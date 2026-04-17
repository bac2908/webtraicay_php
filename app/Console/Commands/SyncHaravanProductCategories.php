<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncHaravanProductCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'haravan:sync-categories
                            {--base=https://thegioitraicay.net : Base URL}
                            {--delay-ms=250 : Delay between requests}
                            {--limit=0 : Limit number of products to process (0 = all)}
                            {--all : Process all products, including already categorized ones}
                            {--fallback-slug= : Fallback category slug when breadcrumb category is missing}
                            {--insecure : Disable TLS cert verification (Windows dev only)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill product category_id from Haravan breadcrumb collection links';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $base = rtrim((string) $this->option('base'), '/');
        $delayMs = max(0, (int) $this->option('delay-ms'));
        $limit = max(0, (int) $this->option('limit'));
        $syncAll = (bool) $this->option('all');
        $fallbackSlug = trim((string) $this->option('fallback-slug'));

        $query = Product::query()->orderBy('id');

        if (!$syncAll) {
            $query->whereNull('category_id');
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        $products = $query->get(['id', 'slug', 'name', 'category_id']);

        if ($products->isEmpty()) {
            $this->info('No products to sync.');
            return 0;
        }

        $this->info('Products to process: ' . $products->count());

        $categoryIdBySlug = Category::query()->pluck('id', 'slug')->all();
        $fallbackCategoryId = null;

        if ($fallbackSlug !== '') {
            $fallbackCategory = Category::query()
                ->where('slug', $fallbackSlug)
                ->first();

            if (!$fallbackCategory) {
                $this->error('Fallback category slug not found: ' . $fallbackSlug);
                return 1;
            }

            $fallbackCategoryId = (int) $fallbackCategory->id;
            $this->line('Fallback category: ' . $fallbackCategory->name . ' (' . $fallbackSlug . ')');
        }

        $updated = 0;
        $unchanged = 0;
        $missingCategory = 0;
        $failedFetch = 0;
        $createdCategories = 0;
        $fallbackAssigned = 0;

        $total = $products->count();

        foreach ($products as $index => $product) {
            $this->line(sprintf('[%d/%d] %s', $index + 1, $total, $product->slug));

            $productUrl = $base . '/products/' . ltrim((string) $product->slug, '/');
            $html = $this->fetchHtml($productUrl);

            if ($html === null) {
                $failedFetch++;
                continue;
            }

            $parsed = $this->extractCategoryFromBreadcrumb($html);
            if ($parsed === null) {
                if ($fallbackCategoryId !== null) {
                    if ((int) $product->category_id !== $fallbackCategoryId) {
                        $product->category_id = $fallbackCategoryId;
                        $product->save();
                        $updated++;
                        $fallbackAssigned++;
                    } else {
                        $unchanged++;
                    }

                    continue;
                }

                $missingCategory++;
                continue;
            }

            [$categorySlug, $categoryName] = $parsed;

            if ($categorySlug === '' || $categorySlug === 'all') {
                if ($fallbackCategoryId !== null) {
                    if ((int) $product->category_id !== $fallbackCategoryId) {
                        $product->category_id = $fallbackCategoryId;
                        $product->save();
                        $updated++;
                        $fallbackAssigned++;
                    } else {
                        $unchanged++;
                    }

                    continue;
                }

                $missingCategory++;
                continue;
            }

            $categoryId = $categoryIdBySlug[$categorySlug] ?? null;
            if (!$categoryId) {
                $category = Category::query()->firstOrCreate(
                    ['slug' => $categorySlug],
                    [
                        'name' => $categoryName,
                        'parent_id' => null,
                        'sort_order' => 0,
                        'is_active' => true,
                    ]
                );

                $categoryId = $category->id;
                $categoryIdBySlug[$categorySlug] = $categoryId;
                $createdCategories++;
            }

            if ((int) $product->category_id !== (int) $categoryId) {
                $product->category_id = $categoryId;
                $product->save();
                $updated++;
            } else {
                $unchanged++;
            }

            if ($delayMs > 0 && $index + 1 < $total) {
                usleep($delayMs * 1000);
            }
        }

        $this->newLine();
        $this->info('Category sync completed.');
        $this->line('Updated products: ' . $updated);
        $this->line('Unchanged products: ' . $unchanged);
        $this->line('Missing breadcrumb category: ' . $missingCategory);
        $this->line('Fetch failures: ' . $failedFetch);
        $this->line('Created categories: ' . $createdCategories);
        $this->line('Fallback-assigned products: ' . $fallbackAssigned);

        return 0;
    }

    private function fetchHtml(string $url): ?string
    {
        try {
            $client = Http::timeout(20)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) LaravelCategorySync/1.0',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ]);

            if ((bool) $this->option('insecure')) {
                $client = $client->withoutVerifying();
            }

            $response = $client->get($url);
            if (!$response->successful()) {
                $this->warn('HTTP ' . $response->status() . ' for ' . $url);
                return null;
            }

            return (string) $response->body();
        } catch (\Throwable $e) {
            $this->warn('Error fetching ' . $url . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * @return array{0:string,1:string}|null
     */
    private function extractCategoryFromBreadcrumb(string $html): ?array
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        $nodes = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' breadcrumb ')]//a[contains(@href,'/collections/')]");
        if (!$nodes || $nodes->length === 0) {
            return null;
        }

        $last = null;
        foreach ($nodes as $node) {
            if ($node instanceof \DOMElement) {
                $last = $node;
            }
        }

        if (!$last || !($last instanceof \DOMElement)) {
            return null;
        }

        $href = trim((string) $last->getAttribute('href'));
        if ($href === '') {
            return null;
        }

        $path = (string) (parse_url($href, PHP_URL_PATH) ?? '');
        if ($path === '') {
            $path = $href;
        }

        $path = rawurldecode($path);

        if (!preg_match('#/collections/([^/]+)#i', $path, $matches)) {
            return null;
        }

        $categorySlug = Str::slug(trim((string) ($matches[1] ?? '')));
        if ($categorySlug === '') {
            return null;
        }

        $categoryName = trim((string) $last->textContent);
        if ($categoryName === '') {
            $categoryName = Str::title(str_replace('-', ' ', $categorySlug));
        }

        return [$categorySlug, $categoryName];
    }
}
