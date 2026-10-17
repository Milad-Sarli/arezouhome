<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Default category tree for a home-appliance store.
     */
    public function run(): void
    {
        $sort = 0;
        foreach ($this->data() as $title => $children) {
            $sort++;
            $parent = Category::firstOrCreate(
                ['slug' => $this->slug($title)],
                ['title' => $title, 'sort' => $sort, 'is_important' => true],
            );
            $defaultImages = $this->defaultImages();

            if (! $parent->image && isset($defaultImages[$parent->slug])) {
                $parent->update(['image' => $defaultImages[$parent->slug]]);
            }

            $childSort = 0;
            foreach ($children as $childTitle => $grandchildren) {
                if (is_int($childTitle)) {
                    $childTitle = $grandchildren;
                    $grandchildren = [];
                }
                $childSort++;
                $child = Category::firstOrCreate(
                    ['slug' => $this->slug($childTitle)],
                    ['title' => $childTitle, 'parent_id' => $parent->id, 'sort' => $childSort],
                );

                $grandSort = 0;
                foreach ($grandchildren as $grandTitle) {
                    $grandSort++;
                    Category::firstOrCreate(
                        ['slug' => $this->slug($grandTitle)],
                        ['title' => $grandTitle, 'parent_id' => $child->id, 'sort' => $grandSort],
                    );
                }
            }
        }
    }

    private function slug(string $title): string
    {
        return str_replace(' ', '-', $title);
    }

    private function defaultImages(): array
    {
        return [
            'لوازم-برقی-بزرگ' => 'categories/cat-big.webp',
            'صوتی-و-تصویری' => 'categories/cat-tv.webp',
            'لوازم-برقی-کوچک-آشپزخانه' => 'categories/cat-small.webp',
            'نظافت-برقی' => 'categories/cat-clean.webp',
            'زیبایی-و-سلامت-برقی' => 'categories/cat-beauty.webp',
            'پنکه-و-تهویه' => 'categories/cat-fan.webp',
            'ظروف-پخت‌وپز' => 'categories/cat-cook.webp',
            'ظروف-سرو-و-پذیرایی' => 'categories/cat-dinner.webp',
        ];
    }

    /**
     * @return array<string, array>
     */
    private function data(): array
    {
        return [
            'لوازم برقی بزرگ' => [
                'یخچال و فریزر' => ['ساید بای ساید', 'یخچال دوقلو', 'یخچال کمبی', 'فریزر صندوقی', 'یخچال کوچک و هتلی'],
                'ماشین لباسشویی' => ['لباسشویی درب از جلو', 'لباسشویی درب از بالا', 'لباسشویی دوقلو'],
                'ماشین ظرفشویی' => ['ظرفشویی مبله', 'ظرفشویی رومیزی'],
                'اجاق گاز' => ['اجاق گاز فردار', 'اجاق گاز صفحه‌ای', 'اجاق گاز رومیزی', 'فر توکار'],
                'هود آشپزخانه' => [],
                'کولر گازی' => ['کولر گازی دیواری', 'کولر گازی ایستاده', 'کولر گازی پرتابل'],
                'کولر آبی' => [],
                'بخاری و گرمایش' => ['بخاری گازی', 'بخاری برقی', 'شوفاژ برقی'],
                'آبگرمکن و پکیج' => [],
            ],
            'صوتی و تصویری' => [
                'تلویزیون' => ['تلویزیون ال‌ای‌دی', 'تلویزیون کیو‌ال‌ای‌دی', 'تلویزیون او‌ال‌ای‌دی'],
                'سینمای خانگی و ساندبار' => [],
            ],
            'لوازم برقی کوچک آشپزخانه' => [
                'چای‌ساز و کتری برقی' => [],
                'سماور برقی' => [],
                'قهوه‌ساز و اسپرسوساز' => [],
                'مخلوط‌کن و آسیاب' => [],
                'غذاساز' => [],
                'آبمیوه‌گیری' => [],
                'مایکروویو و سولاردام' => [],
                'سرخ‌کن و هواپز' => [],
                'پلوپز، زودپز و آرام‌پز' => [],
                'توستر، ساندویچ‌ساز و وافل‌ساز' => [],
                'چرخ‌گوشت' => [],
                'همزن برقی' => [],
                'آون توستر' => [],
                'بخارپز' => [],
                'آبسردکن' => [],
            ],
            'نظافت برقی' => [
                'جاروبرقی' => ['جاروبرقی کیسه‌ای', 'جاروبرقی مخزن‌دار', 'جاروبرقی رباتیک', 'جاروبرقی شارژی و عصایی'],
                'بخارشوی' => [],
                'اتو' => ['اتو بخار', 'اتو پرسی', 'اتو مسافرتی'],
            ],
            'زیبایی و سلامت برقی' => [
                'سشوار' => [],
                'اتو و حالت‌دهنده مو' => [],
                'ماشین اصلاح و ریش‌تراش' => [],
                'اپیلیدی' => [],
                'ترازو' => [],
            ],
            'پنکه و تهویه' => [
                'پنکه سقفی' => [],
                'پنکه ایستاده' => [],
                'پنکه رومیزی' => [],
                'تصفیه‌کننده هوا' => [],
            ],
            'ظروف پخت‌وپز' => [
                'قابلمه و تابه' => ['قابلمه گرانیتی', 'قابلمه چدن', 'قابلمه استیل', 'قابلمه مسی', 'قابلمه سرامیکی'],
                'سرویس قابلمه' => [],
                'زودپز گازی' => [],
                'کتری و قوری' => [],
                'قالب و سینی فر' => [],
            ],
            'ظروف سرو و پذیرایی' => [
                'سرویس غذاخوری' => ['سرویس غذاخوری چینی', 'سرویس غذاخوری آرکوپال', 'سرویس غذاخوری سرامیکی', 'سرویس غذاخوری اوپال', 'سرویس غذاخوری ملامین'],
                'سرویس قاشق، چنگال و کارد' => [],
                'سرویس چاقوی آشپزخانه' => [],
                'لیوان، استکان و پارچ' => [],
                'سینی و دیس پذیرایی' => [],
                'ظروف نگهدارنده و بانکه' => [],
                'فلاسک و کلمن' => [],
            ],
        ];
    }
}
