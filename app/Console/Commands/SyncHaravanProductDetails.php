<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncHaravanProductDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'haravan:sync-product-details
                            {--base=https://thegioitraicay.net : Base URL}
                            {--delay-ms=250 : Delay between requests in milliseconds}
                            {--timeout=25 : HTTP timeout per request in seconds}
                            {--limit=0 : Limit number of products to process (0 = all)}
                            {--slug= : Sync only one product slug}
                            {--only-missing : Update only products missing description/summary/gallery}
                            {--insecure : Disable TLS cert verification (Windows dev only)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync product detail HTML, summary, prices and gallery images from Haravan product pages';

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
        $slug = trim((string) $this->option('slug'));
        $onlyMissing = (bool) $this->option('only-missing');

        $query = Product::query()
            ->where('is_active', true)
            ->orderBy('id');

        if ($slug !== '') {
            $query->where('slug', $slug);
        }

        if ($onlyMissing) {
            $query->where(function ($builder) {
                $builder->whereNull('description')
                    ->orWhere('description', '')
                    ->orWhereNull('short_desc')
                    ->orWhere('short_desc', '')
                    ->orWhereDoesntHave('images');
            });
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        $products = $query->get([
            'id',
            'slug',
            'name',
            'unit',
            'stock',
            'price',
            'sale_price',
            'thumb',
            'short_desc',
            'description',
            'has_gear_detail',
        ]);

        if ($products->isEmpty()) {
            $this->warn('No products to sync.');
            return 0;
        }

        $this->info('Products to process: ' . $products->count());

        $updated = 0;
        $unchanged = 0;
        $failed = 0;
        $syncedImages = 0;

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        $total = $products->count();

        foreach ($products as $index => $product) {
            $url = $base . '/products/' . ltrim((string) $product->slug, '/');
            $html = $this->fetchHtml($url);

            if ($html === null) {
                $failed++;
                $bar->advance();
                if ($delayMs > 0 && $index + 1 < $total) {
                    usleep($delayMs * 1000);
                }
                continue;
            }

            $detail = $this->extractDetailPayload($html, $base);
            if ($detail === null) {
                $this->newLine();
                $this->warn('Skip (cannot parse detail): ' . $product->slug);
                $failed++;
                $bar->advance();
                if ($delayMs > 0 && $index + 1 < $total) {
                    usleep($delayMs * 1000);
                }
                continue;
            }

            $result = $this->applyDetailPayload($product, $detail, $onlyMissing);

            if ($result['updated']) {
                $updated++;
            } else {
                $unchanged++;
            }

            if ($result['images_synced']) {
                $syncedImages++;
            }

            $bar->advance();

            if ($delayMs > 0 && $index + 1 < $total) {
                usleep($delayMs * 1000);
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('Detail sync completed.');
        $this->line('Updated products: ' . $updated);
        $this->line('Unchanged products: ' . $unchanged);
        $this->line('Products with gallery re-sync: ' . $syncedImages);
        $this->line('Failed products: ' . $failed);

        return 0;
    }

    private function fetchHtml(string $url): ?string
    {
        $timeout = max(5, (int) $this->option('timeout'));

        try {
            $client = Http::timeout($timeout)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) LaravelDetailSync/1.0',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ]);

            if ((bool) $this->option('insecure')) {
                $client = $client->withoutVerifying();
            }

            $response = $client->get($url);
            if (!$response->successful()) {
                $this->newLine();
                $this->warn('HTTP ' . $response->status() . ' for ' . $url);
                return null;
            }

            return (string) $response->body();
        } catch (\Throwable $e) {
            $this->newLine();
            $this->warn('Error fetching ' . $url . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * @return array<string,mixed>|null
     */
    private function extractDetailPayload(string $html, string $base): ?array
    {
        $productJson = $this->extractProductJson($html);
        $xpath = $this->createXPath($html);

        $descriptionHtml = '';
        if (is_array($productJson)) {
            $descriptionHtml = trim((string) ($productJson['description'] ?? ''));
        }

        if ($descriptionHtml === '' && $xpath) {
            $descriptionHtml = $this->extractDescriptionFromDom($xpath);
        }

        $shortDesc = $xpath ? $this->extractSummaryText($xpath) : '';
        if ($shortDesc === '' && $descriptionHtml !== '') {
            $shortDesc = Str::limit($this->normalizeText(strip_tags($descriptionHtml)), 320, '...');
        }

        $images = [];
        if (is_array($productJson)) {
            $images = $this->extractImagesFromJson($productJson, $base);
        }

        if (empty($images) && $xpath) {
            $images = $this->extractImagesFromDom($xpath, $base);
        }

        $pricePayload = $this->extractPricePayload($productJson, $xpath);

        $unit = '';
        $hasGearDetail = false;
        $stock = null;

        if (is_array($productJson)) {
            $variants = collect($productJson['variants'] ?? [])
                ->filter(fn ($item) => is_array($item))
                ->values()
                ->all();

            $hasGearDetail = count($variants) > 1;

            $selectedVariant = $productJson['selected_or_first_available_variant'] ?? null;
            if (is_array($selectedVariant)) {
                $unit = trim((string) ($selectedVariant['public_title'] ?? $selectedVariant['title'] ?? $selectedVariant['option1'] ?? ''));
            }

            if ($unit === '' && !empty($variants)) {
                $firstVariant = $variants[0];
                $unit = trim((string) ($firstVariant['public_title'] ?? $firstVariant['title'] ?? $firstVariant['option1'] ?? ''));
            }

            if (Str::lower($unit) === 'default title') {
                $unit = '';
            }

            if (array_key_exists('available', $productJson)) {
                $stock = (bool) $productJson['available'] ? 100 : 0;
            }
        }

        if ($descriptionHtml === '' && $shortDesc === '' && empty($images) && $pricePayload['price'] === null) {
            return null;
        }

        return [
            'description_html' => $descriptionHtml,
            'short_desc' => $shortDesc,
            'images' => $images,
            'price' => $pricePayload['price'],
            'sale_price' => $pricePayload['sale_price'],
            'unit' => $unit,
            'has_gear_detail' => $hasGearDetail,
            'stock' => $stock,
        ];
    }

    /**
     * @param array<string,mixed> $payload
     * @return array{updated:bool,images_synced:bool}
     */
    private function applyDetailPayload(Product $product, array $payload, bool $onlyMissing): array
    {
        $updated = false;
        $imagesSynced = false;

        $incomingDescription = trim((string) ($payload['description_html'] ?? ''));
        if ($incomingDescription !== '' && (!$onlyMissing || trim((string) $product->description) === '')) {
            if ((string) $product->description !== $incomingDescription) {
                $product->description = $incomingDescription;
                $updated = true;
            }
        }

        $incomingShortDesc = trim((string) ($payload['short_desc'] ?? ''));
        if ($incomingShortDesc !== '' && (!$onlyMissing || trim((string) $product->short_desc) === '')) {
            if ((string) $product->short_desc !== $incomingShortDesc) {
                $product->short_desc = $incomingShortDesc;
                $updated = true;
            }
        }

        $incomingPrice = $payload['price'] ?? null;
        if (is_int($incomingPrice) && $incomingPrice > 0 && (!$onlyMissing || (int) $product->price <= 0)) {
            if ((int) $product->price !== $incomingPrice) {
                $product->price = $incomingPrice;
                $updated = true;
            }
        }

        $incomingSalePrice = $payload['sale_price'] ?? null;
        if (is_int($incomingSalePrice) && $incomingSalePrice > 0) {
            if (!$onlyMissing || !is_int($product->sale_price) || (int) $product->sale_price <= 0 || (int) $product->sale_price >= (int) $product->price) {
                if ((int) $product->sale_price !== $incomingSalePrice) {
                    $product->sale_price = $incomingSalePrice;
                    $updated = true;
                }
            }
        } elseif (!$onlyMissing && $product->sale_price !== null) {
            $product->sale_price = null;
            $updated = true;
        }

        $incomingUnit = trim((string) ($payload['unit'] ?? ''));
        if ($incomingUnit !== '' && (!$onlyMissing || trim((string) $product->unit) === '')) {
            if ((string) $product->unit !== $incomingUnit) {
                $product->unit = $incomingUnit;
                $updated = true;
            }
        }

        $incomingStock = $payload['stock'] ?? null;
        if (is_int($incomingStock) && (!$onlyMissing || (int) $product->stock <= 0)) {
            if ((int) $product->stock !== $incomingStock) {
                $product->stock = $incomingStock;
                $updated = true;
            }
        }

        $incomingHasGearDetail = (bool) ($payload['has_gear_detail'] ?? false);
        if ($incomingHasGearDetail && !$product->has_gear_detail) {
            $product->has_gear_detail = true;
            $updated = true;
        }

        $images = collect($payload['images'] ?? [])
            ->filter(fn ($url) => is_string($url) && trim($url) !== '')
            ->map(fn ($url) => trim((string) $url))
            ->unique()
            ->values();

        $hasCurrentImages = $product->images()->exists();
        $shouldSyncImages = $images->isNotEmpty() && (!$onlyMissing || !$hasCurrentImages);

        if ($shouldSyncImages) {
            $firstImage = (string) $images->first();
            if ($firstImage !== '' && (!$onlyMissing || trim((string) $product->thumb) === '')) {
                if ((string) $product->thumb !== $firstImage) {
                    $product->thumb = $firstImage;
                    $updated = true;
                }
            }
        }

        if ($updated) {
            $product->save();
        }

        if ($shouldSyncImages) {
            $product->images()->delete();

            foreach ($images as $index => $url) {
                $product->images()->create([
                    'url' => $url,
                    'sort_order' => $index,
                ]);
            }

            $imagesSynced = true;
            $updated = true;
        }

        return [
            'updated' => $updated,
            'images_synced' => $imagesSynced,
        ];
    }

    /**
     * @return array<string,mixed>|null
     */
    private function extractProductJson(string $html): ?array
    {
        $jsonString = $this->extractBalancedJsonAfterMarker($html, 'var productJson');
        if ($jsonString === null) {
            return null;
        }

        $decoded = json_decode($jsonString, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return null;
        }

        return $decoded;
    }

    private function extractBalancedJsonAfterMarker(string $content, string $marker): ?string
    {
        $markerPos = strpos($content, $marker);
        if ($markerPos === false) {
            return null;
        }

        $slice = substr($content, $markerPos);
        if ($slice === false) {
            return null;
        }

        $equalPos = strpos($slice, '=');
        if ($equalPos === false) {
            return null;
        }

        $bracePos = strpos($slice, '{', $equalPos);
        if ($bracePos === false) {
            return null;
        }

        $jsonCandidate = substr($slice, $bracePos);
        if ($jsonCandidate === false) {
            return null;
        }

        return $this->extractBalancedObject($jsonCandidate);
    }

    private function extractBalancedObject(string $value): ?string
    {
        $length = strlen($value);
        $depth = 0;
        $inString = false;
        $escaped = false;
        $start = null;

        for ($i = 0; $i < $length; $i++) {
            $char = $value[$i];

            if ($inString) {
                if ($escaped) {
                    $escaped = false;
                    continue;
                }

                if ($char === '\\') {
                    $escaped = true;
                    continue;
                }

                if ($char === '"') {
                    $inString = false;
                }

                continue;
            }

            if ($char === '"') {
                $inString = true;
                continue;
            }

            if ($char === '{') {
                if ($depth === 0) {
                    $start = $i;
                }
                $depth++;
                continue;
            }

            if ($char === '}') {
                if ($depth === 0) {
                    return null;
                }

                $depth--;

                if ($depth === 0 && $start !== null) {
                    return substr($value, $start, $i - $start + 1);
                }
            }
        }

        return null;
    }

    private function createXPath(string $html): ?\DOMXPath
    {
        $dom = new \DOMDocument();

        $previous = libxml_use_internal_errors(true);
        $loaded = $dom->loadHTML($html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (!$loaded) {
            return null;
        }

        return new \DOMXPath($dom);
    }

    private function extractDescriptionFromDom(\DOMXPath $xpath): string
    {
        $candidates = [
            "//*[@id='tab-1']//*[contains(concat(' ', normalize-space(@class), ' '), ' rte ')]",
            "//*[contains(concat(' ', normalize-space(@class), ' '), ' product_getcontent ')]",
            "//*[contains(concat(' ', normalize-space(@class), ' '), ' tab-content ')]//*[contains(concat(' ', normalize-space(@class), ' '), ' rte ')]",
        ];

        foreach ($candidates as $query) {
            $nodes = $xpath->query($query);
            $node = $nodes ? $nodes->item(0) : null;
            if ($node instanceof \DOMNode) {
                $html = trim($this->innerHtml($node));
                if ($html !== '') {
                    return $html;
                }
            }
        }

        return '';
    }

    private function extractSummaryText(\DOMXPath $xpath): string
    {
        $queries = [
            "//*[contains(concat(' ', normalize-space(@class), ' '), ' product-summary ')]//*[contains(concat(' ', normalize-space(@class), ' '), ' description ')]",
            "//*[contains(concat(' ', normalize-space(@class), ' '), ' product-summary ')]",
            "//*[@itemprop='description']",
        ];

        foreach ($queries as $query) {
            $nodes = $xpath->query($query);
            $node = $nodes ? $nodes->item(0) : null;
            if ($node instanceof \DOMNode) {
                $text = $this->normalizeText((string) $node->textContent);
                if ($text !== '') {
                    return Str::limit($text, 320, '...');
                }
            }
        }

        return '';
    }

    /**
     * @param array<string,mixed> $productJson
     * @return array<int,string>
     */
    private function extractImagesFromJson(array $productJson, string $base): array
    {
        $images = [];

        foreach ((array) ($productJson['images'] ?? []) as $item) {
            if (is_string($item)) {
                $images[] = $this->normalizeMediaUrl($item, $base);
                continue;
            }

            if (is_array($item)) {
                $candidate = $item['src'] ?? $item['url'] ?? null;
                if (is_string($candidate)) {
                    $images[] = $this->normalizeMediaUrl($candidate, $base);
                }
            }
        }

        if (empty($images)) {
            $image = $productJson['image'] ?? null;
            if (is_array($image) && isset($image['src']) && is_string($image['src'])) {
                $images[] = $this->normalizeMediaUrl($image['src'], $base);
            }
        }

        return collect($images)
            ->filter(fn ($url) => is_string($url) && trim($url) !== '')
            ->map(fn ($url) => trim((string) $url))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<int,string>
     */
    private function extractImagesFromDom(\DOMXPath $xpath, string $base): array
    {
        $images = [];

        $queries = [
            "//*[@id='gallery_01']//a[@data-image]",
            "//*[@id='gallery_01']//a[@href]",
            "//*[contains(concat(' ', normalize-space(@class), ' '), ' large-image ')]//a[@href]",
        ];

        foreach ($queries as $query) {
            $nodes = $xpath->query($query);
            if (!$nodes || $nodes->length === 0) {
                continue;
            }

            foreach ($nodes as $node) {
                if (!($node instanceof \DOMElement)) {
                    continue;
                }

                $raw = trim((string) ($node->getAttribute('data-image') ?: $node->getAttribute('href')));
                if ($raw === '' || Str::startsWith($raw, 'javascript:')) {
                    continue;
                }

                $images[] = $this->normalizeMediaUrl($raw, $base);
            }
        }

        return collect($images)
            ->filter(fn ($url) => is_string($url) && trim($url) !== '')
            ->map(fn ($url) => trim((string) $url))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param array<string,mixed>|null $productJson
     * @return array{price:int|null,sale_price:int|null}
     */
    private function extractPricePayload(?array $productJson, ?\DOMXPath $xpath): array
    {
        $price = null;
        $salePrice = null;

        if (is_array($productJson)) {
            $priceMin = $this->normalizeHaravanMoney($productJson['price_min'] ?? $productJson['price'] ?? null);
            $compareAtPriceMin = $this->normalizeHaravanMoney($productJson['compare_at_price_min'] ?? $productJson['compare_at_price'] ?? null);

            if ($priceMin !== null) {
                if ($compareAtPriceMin !== null && $compareAtPriceMin > $priceMin) {
                    $price = $compareAtPriceMin;
                    $salePrice = $priceMin;
                } else {
                    $price = $priceMin;
                }
            }
        }

        if (($price === null || $price <= 0) && $xpath) {
            $priceNodes = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' price-box ')]//*[contains(concat(' ', normalize-space(@class), ' '), ' product-price ')]");
            $priceNode = $priceNodes ? $priceNodes->item(0) : null;
            if ($priceNode instanceof \DOMNode) {
                $price = $this->parseMoneyFromText((string) $priceNode->textContent);
            }

            $oldPriceNodes = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' price-box ')]//*[contains(concat(' ', normalize-space(@class), ' '), ' product-price-old ')]");
            $oldPriceNode = $oldPriceNodes ? $oldPriceNodes->item(0) : null;
            if ($oldPriceNode instanceof \DOMNode) {
                $oldPrice = $this->parseMoneyFromText((string) $oldPriceNode->textContent);
                if ($oldPrice !== null && $price !== null && $oldPrice > $price) {
                    $salePrice = $price;
                    $price = $oldPrice;
                }
            }
        }

        if ($salePrice !== null && $price !== null && $salePrice >= $price) {
            $salePrice = null;
        }

        return [
            'price' => $price,
            'sale_price' => $salePrice,
        ];
    }

    private function normalizeHaravanMoney($value): ?int
    {
        if (!is_numeric($value)) {
            return null;
        }

        $raw = (float) $value;
        if ($raw <= 0) {
            return null;
        }

        // Haravan JSON commonly stores VND in cent format (x100).
        if ($raw >= 5000000) {
            return (int) round($raw / 100);
        }

        return (int) round($raw);
    }

    private function parseMoneyFromText(string $text): ?int
    {
        $digits = preg_replace('/\D+/', '', $text);
        if (!is_string($digits) || $digits === '') {
            return null;
        }

        return (int) $digits;
    }

    private function normalizeMediaUrl(string $url, string $base): string
    {
        $trimmed = trim($url);
        if ($trimmed === '') {
            return '';
        }

        if (Str::startsWith($trimmed, '//')) {
            $trimmed = 'https:' . $trimmed;
        } elseif (!Str::startsWith($trimmed, ['http://', 'https://'])) {
            if (Str::startsWith($trimmed, '/')) {
                $trimmed = $base . $trimmed;
            } else {
                $trimmed = $base . '/' . ltrim($trimmed, '/');
            }
        }

        $parts = parse_url($trimmed);
        if (!is_array($parts) || !isset($parts['scheme'], $parts['host'])) {
            return $trimmed;
        }

        $path = $parts['path'] ?? '';

        return sprintf('%s://%s%s', $parts['scheme'], $parts['host'], $path);
    }

    private function normalizeText(string $value): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', $value));
    }

    private function innerHtml(\DOMNode $node): string
    {
        $document = $node->ownerDocument;
        if (!$document) {
            return '';
        }

        $html = '';
        foreach ($node->childNodes as $child) {
            $html .= $document->saveHTML($child);
        }

        return $html;
    }
}
