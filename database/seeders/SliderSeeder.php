<?php

namespace Database\Seeders;

use App\Models\Slide;
use App\Models\Slider;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    public function run(): void
    {
        $slider = Slider::firstOrCreate(['title' => 'اسلایدر اصلی'], ['status' => true, 'sort' => 1]);

        $slides = [
            ['image' => 'sliders/hero-real-1.webp', 'title' => 'آشپزخانه هوشمند', 'desc' => 'نتیجه‌ای خوشمزه — با آرزو', 'link' => '/products', 'btn' => 'مشاهده محصولات'],
            ['image' => 'sliders/hero-real-2.webp', 'title' => 'جهیزیه کامل', 'desc' => 'با بهترین قیمت و گارانتی', 'link' => '/products', 'btn' => 'دیدن کالاها'],
            ['image' => 'sliders/hero-real-3.webp', 'title' => 'تخفیف شگفت‌انگیز', 'desc' => 'تا ۲۵٪ تخفیف واقعی', 'link' => '/amazings', 'btn' => 'جشنواره'],
            ['image' => 'sliders/hero-real-4.webp', 'title' => 'ارسال به سراسر کشور', 'desc' => 'تیپاکس و باربری برای کالاهای بزرگ', 'link' => '/products', 'btn' => 'خرید کن'],
            ['image' => 'sliders/hero-real-5.webp', 'title' => 'گارانتی شرکتی', 'desc' => 'نصب رایگان + ۷ روز مرجوعی', 'link' => '/warranty', 'btn' => 'بیشتر بدان'],
        ];

        foreach ($slides as $i => $s) {
            Slide::updateOrCreate(
                ['slider_id' => $slider->id, 'sort' => $i],
                [
                    'image' => $s['image'],
                    'title' => $s['title'],
                    'description' => $s['desc'],
                    'link' => $s['link'],
                    'button_title' => $s['btn'],
                    'button_link' => $s['link'],
                    'status' => true,
                ]
            );
        }
    }
}
