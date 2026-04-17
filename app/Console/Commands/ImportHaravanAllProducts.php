<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportHaravanAllProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'haravan:import-all
                            {--base=https://thegioitraicay.net : Base URL}
                            {--path=/collections/all : Collection path}
                            {--max-pages=0 : 0 = auto detect from pagination}
                            {--delay-ms=400 : Delay between requests}
                            {--insecure : Disable TLS cert verification (Windows dev only)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from Haravan collection /collections/all into local DB';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $base = rtrim((string) $this->option('base'), '/');
        $path = (string) $this->option('path');
        $delayMs = (int) $this->option('delay-ms');

        $firstUrl = $base . $path;
        $this->info('Fetch: ' . $firstUrl);

        $html = $this->fetchHtml($firstUrl);
        if ($html === null) {
            $this->error('Fetch failed.');
            return 1;
        }

        $detectedMaxPages = $this->detectMaxPages($html);
        $maxPagesOpt = (int) $this->option('max-pages');
        $maxPages = $maxPagesOpt > 0 ? $maxPagesOpt : max(1, $detectedMaxPages);

        $this->line('Max pages: ' . $maxPages . ($maxPagesOpt > 0 ? ' (from option)' : ' (auto)'));

        $totalUpserted = 0;
        for ($page = 1; $page <= $maxPages; $page++) {
            $url = $page === 1 ? $firstUrl : ($firstUrl . '?page=' . $page);
            $this->info('Page ' . $page . ': ' . $url);

            $pageHtml = $page === 1 ? $html : $this->fetchHtml($url);
            if ($pageHtml === null) {
                $this->warn('Skip page ' . $page . ' (fetch failed).');
                continue;
            }

            $items = $this->parseProductsFromCollectionHtml($pageHtml, $base);
            $this->line('Found products: ' . count($items));

            foreach ($items as $p) {
                $product = Product::query()->withTrashed()->firstOrNew([
                    'slug' => $p['slug'],
                ]);

                if ($product->exists && $product->trashed()) {
                    $product->restore();
                }

                $product->name = $p['name'];

                // Keep existing category_id intact. New records remain uncategorized until sync command maps them.
                if (!$product->exists && !$product->category_id) {
                    $product->category_id = null;
                }

                $product->unit = $p['unit'];
                $product->stock = 100;
                $product->price = $p['price'] ?? 0;
                $product->sale_price = $p['sale_price'];
                $product->thumb = $p['thumb'];
                $product->short_desc = null;
                $product->description = null;
                $product->is_active = true;
                $product->has_gear_detail = $p['has_gear_detail'] ?? false;
                $product->save();

                $totalUpserted++;
            }

            if ($page < $maxPages && $delayMs > 0) {
                usleep($delayMs * 1000);
            }
        }

        $this->info('Done. Upserted: ' . $totalUpserted);
        return 0;
    }

    private function fetchHtml(string $url): ?string
    {
        try {
            $client = Http::timeout(20)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) LaravelImporter/1.0',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ]);

            if ((bool) $this->option('insecure')) {
                $client = $client->withoutVerifying();
            }

            $resp = $client->get($url);

            if (!$resp->successful()) {
                $this->warn('HTTP ' . $resp->status());
                return null;
            }
            return (string) $resp->body();
        } catch (\Throwable $e) {
            $this->warn('Error: ' . $e->getMessage());
            return null;
        }
    }

    private function detectMaxPages(string $html): int
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        $numbers = [];
        foreach ($xpath->query("//ul[contains(concat(' ', normalize-space(@class), ' '), ' pagination ')]//a") as $a) {
            $t = trim($a->textContent ?? '');
            if (ctype_digit($t)) {
                $numbers[] = (int) $t;
            }
        }
        return $numbers ? max($numbers) : 1;
    }

    /**
     * Parse product cards on /collections/all page.
     *
      * @return array<int, array{name:string,slug:string,unit:?string,thumb:?string,price:?int,sale_price:?int,has_gear_detail:bool}>
     */
    private function parseProductsFromCollectionHtml(string $html, string $base): array
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        $out = [];
        $cards = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' product-box ')]");
        foreach ($cards as $card) {
            $a = $xpath->query(".//h3[contains(concat(' ', normalize-space(@class), ' '), ' product-name ')]//a", $card)->item(0)
                ?? $xpath->query(".//a[@href and contains(@href, '/products/')]", $card)->item(0);
            if (!$a || !($a instanceof \DOMElement)) {
                continue;
            }

            $href = (string) $a->getAttribute('href');
            if (!Str::startsWith($href, '/products/')) {
                continue;
            }
            $slug = trim(Str::after($href, '/products/'));
            if ($slug === '') {
                continue;
            }

            $name = trim($a->getAttribute('title') ?: ($a->textContent ?? ''));
            if ($name === '') {
                $name = $slug;
            }

            $img = $xpath->query(".//img", $card)->item(0);
            $thumb = null;
            if ($img instanceof \DOMElement) {
                $thumb = $img->getAttribute('data-lazyload') ?: $img->getAttribute('src');
                $thumb = $this->normalizeAssetUrl($thumb, $base);
            }

            $priceText = $this->textOrNull($xpath->query(".//span[contains(@class,'product-price') and not(contains(@class,'old'))]", $card)->item(0));
            $oldText = $this->textOrNull($xpath->query(".//span[contains(@class,'product-price-old')]", $card)->item(0));

            $price = $this->parseVnd($priceText);
            $old = $this->parseVnd($oldText);

            $salePrice = null;
            $basePrice = null;
            if ($price !== null && $old !== null && $old > 0 && $price < $old) {
                $basePrice = $old;
                $salePrice = $price;
            } else {
                $basePrice = $price;
            }

            $unit = null;
            if (Str::contains($name, ' - ')) {
                $unit = trim(Str::afterLast($name, ' - '));
            }

            $hasGearDetail = $this->detectGearDetailFromCard($xpath, $card);
            $out[] = [
                'name' => $name,
                'slug' => $slug,
                'unit' => $unit ?: null,
                'thumb' => $thumb,
                'price' => $basePrice,
                'sale_price' => $salePrice,
                'has_gear_detail' => $hasGearDetail,
            ];
        }

        // de-dupe by slug
        $uniq = [];
        foreach ($out as $p) {
            $uniq[$p['slug']] = $p;
        }
        return array_values($uniq);
    }

    private function textOrNull(?\DOMNode $node): ?string
    {
        if (!$node) return null;
        $t = trim($node->textContent ?? '');
        return $t === '' ? null : $t;
    }

    private function parseVnd(?string $text): ?int
    {
        if ($text === null) return null;
        $t = trim($text);
        if ($t === '' || mb_stripos($t, 'liên hệ') !== false) return null;
        $digits = preg_replace('/[^\d]/u', '', $t);
        if (!$digits) return null;
        return (int) $digits;
    }

    private function normalizeAssetUrl(?string $url, string $base): ?string
    {
        if ($url === null) return null;
        $u = trim($url);
        if ($u === '' || Str::contains($u, 'rolling.svg')) return null;
        if (Str::startsWith($u, '//')) return 'https:' . $u;
        if (Str::startsWith($u, '/')) return $base . $u;
        return $u;
    }

    private function detectGearDetailFromCard(\DOMXPath $xpath, \DOMNode $card): bool
    {
        $actionNode = $xpath->query(".//*[contains(concat(' ', normalize-space(@class), ' '), ' product-action ')]", $card)->item(0);

        if (!$actionNode || !($actionNode instanceof \DOMElement) || !$actionNode->ownerDocument) {
            return false;
        }

        $actionHtml = (string) $actionNode->ownerDocument->saveHTML($actionNode);
        $normalized = mb_strtolower((string) preg_replace('/\s+/u', ' ', trim($actionHtml)), 'UTF-8');

        return Str::contains($normalized, [
            'fa-gear',
            'name="variantid"',
            'title="chọn sản phẩm"',
            'title="chon san pham"',
            'onclick="window.location.href=',
        ]);
    }
}
