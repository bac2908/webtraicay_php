<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SyncHaravanCategoryParents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'haravan:sync-category-parents
                            {--dry-run : Preview parent updates without writing to DB}
                            {--all-categories : Include categories with zero products}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Normalize flat collection categories into root category tree for correct listing coverage';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = (bool) $this->option('dry-run');
        $includeAllCategories = (bool) $this->option('all-categories');

        $rootSlugToId = [
            'trai-cay-viet-nam' => null,
            'trai-cay-nhap-khau' => null,
            'trai-cay-thai-lan' => null,
            'gio-qua-va-set-qua' => null,
            'qua-cuoi-va-mam-cung' => null,
            'combo-sieu-tiet-kiem' => null,
            'hang-vao-mua' => null,
            'san-pham-ban-chay' => null,
            'thuc-pham' => null,
        ];

        foreach (array_keys($rootSlugToId) as $slug) {
            $categoryId = Category::query()->where('slug', $slug)->value('id');

            if (!$categoryId) {
                $this->error('Missing root category slug: ' . $slug);
                return 1;
            }

            $rootSlugToId[$slug] = (int) $categoryId;
        }

        $explicitSlugToRootSlug = [
            'trai-cay-tet' => 'gio-qua-va-set-qua',
            'nhom-quang-cao-gs' => 'trai-cay-nhap-khau',
            'nho-khong-hat' => 'trai-cay-nhap-khau',
            'tao' => 'trai-cay-nhap-khau',
            'kiwi-nhap-khau' => 'trai-cay-nhap-khau',
            'dau' => 'trai-cay-nhap-khau',
            'sau-rieng' => 'trai-cay-viet-nam',
            'tao-envy' => 'trai-cay-nhap-khau',
            'cha-la-nhap-khau' => 'trai-cay-nhap-khau',
            'cam' => 'trai-cay-viet-nam',
            'dua' => 'trai-cay-viet-nam',
            'viet-quat-nhap-khau' => 'trai-cay-nhap-khau',
            'vu-sua' => 'trai-cay-viet-nam',
            'xoai' => 'trai-cay-viet-nam',
            'trai-vai' => 'trai-cay-viet-nam',
            'mit' => 'trai-cay-viet-nam',
            'buoi-da-xanh' => 'trai-cay-viet-nam',
            'chuoi' => 'trai-cay-viet-nam',
            'bo' => 'trai-cay-viet-nam',
            'thanh-long' => 'trai-cay-viet-nam',
            'le-nhap-khau' => 'trai-cay-nhap-khau',
            'dao-nhap-khau' => 'trai-cay-nhap-khau',
            'chom-chom' => 'trai-cay-viet-nam',
            'mang-cut' => 'trai-cay-thai-lan',
            'best-seller' => 'san-pham-ban-chay',
        ];

        $categoriesQuery = Category::query()
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->whereNotIn('slug', array_keys($rootSlugToId));

        if (!$includeAllCategories) {
            $categoriesQuery->whereIn('id', function ($sub) {
                $sub->from((new Product())->getTable())
                    ->select('category_id')
                    ->where('is_active', true)
                    ->whereNotNull('category_id')
                    ->whereNull('deleted_at')
                    ->groupBy('category_id');
            });
        }

        $categories = $categoriesQuery
            ->orderBy('slug')
            ->get(['id', 'slug', 'name', 'parent_id']);

        if ($categories->isEmpty()) {
            $this->info('No categories to process.');
            return 0;
        }

        $updated = 0;
        $unchanged = 0;
        $skipped = 0;

        foreach ($categories as $category) {
            $targetRootSlug = $this->resolveRootSlug((string) $category->slug, $explicitSlugToRootSlug);

            if ($targetRootSlug === null) {
                $skipped++;
                $this->line(sprintf('[SKIP] %s (%s)', $category->slug, $category->name));
                continue;
            }

            $targetParentId = (int) $rootSlugToId[$targetRootSlug];

            if ((int) $category->parent_id === $targetParentId) {
                $unchanged++;
                continue;
            }

            $this->line(sprintf('[MAP] %s -> %s', $category->slug, $targetRootSlug));

            if (!$isDryRun) {
                $category->parent_id = $targetParentId;
                $category->save();
            }

            $updated++;
        }

        $this->newLine();
        $this->info('Parent sync completed' . ($isDryRun ? ' (dry-run)' : '') . '.');
        $this->line('Updated categories: ' . $updated);
        $this->line('Unchanged categories: ' . $unchanged);
        $this->line('Skipped categories: ' . $skipped);

        return 0;
    }

    /**
     * Resolve a root category slug for a leaf/category slug.
     */
    private function resolveRootSlug(string $slug, array $explicitSlugToRootSlug): ?string
    {
        $slug = Str::lower(trim($slug));

        if ($slug === '') {
            return null;
        }

        if (isset($explicitSlugToRootSlug[$slug])) {
            return $explicitSlugToRootSlug[$slug];
        }

        if ($this->containsAny($slug, ['gio-qua', 'gio-trai-cay', 'hop-qua', 'set-qua'])) {
            return 'gio-qua-va-set-qua';
        }

        if ($this->containsAny($slug, ['qua-cuoi', 'mam-', 'mam-dia', 'cung', 'kinh-vieng', 'kinh-le', 'di-dam'])) {
            return 'qua-cuoi-va-mam-cung';
        }

        if ($this->containsAny($slug, ['thuc-pham', 'kem-', 'sua-', 'yen-sao', 'tau-hu', 'nuoc-ep'])) {
            return 'thuc-pham';
        }

        if ($this->containsAny($slug, ['best-seller', 'ban-chay'])) {
            return 'san-pham-ban-chay';
        }

        if ($this->containsAny($slug, ['vao-mua'])) {
            return 'hang-vao-mua';
        }

        if ($this->containsAny($slug, ['combo'])) {
            return 'combo-sieu-tiet-kiem';
        }

        if ($this->containsAny($slug, ['thai', 'mongthoong', 'na-thai', 'mit-thai', 'me-thai', 'bon-bon-thai'])) {
            return 'trai-cay-thai-lan';
        }

        if ($this->containsAny($slug, [
            'han-quoc',
            'my',
            'uc',
            'newzealand',
            'new-zealand',
            'nam-phi',
            'chile',
            'peru',
            'canada',
            'nhat',
            'dai-loan',
            'israel',
            'an-do',
            'phap',
            'tay-ban-nha',
            'nhap-khau',
        ])) {
            return 'trai-cay-nhap-khau';
        }

        if ($this->containsAny($slug, [
            'viet-nam',
            'mien-tay',
            'da-lat',
            'moc-chau',
            'cao-lanh',
            'soc-trang',
            'ben-tre',
            'ninh-thuan',
            'tan-trieu',
            'an-phuoc',
            'luc-ngan',
            'nghe-an',
        ])) {
            return 'trai-cay-viet-nam';
        }

        return null;
    }

    /**
     * Check if slug contains at least one keyword.
     */
    private function containsAny(string $slug, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (Str::contains($slug, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
