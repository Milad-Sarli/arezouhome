<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            'contact_phone' => '',
            'contact_phone_2' => '',
            'contact_email' => '',
            'contact_address' => '',
            'working_hours' => 'شنبه تا پنجشنبه، ۹ صبح تا ۶ عصر',
            'support_info' => '۷ روز هفته، پاسخگویی در ساعات کاری',
            'about' => '',
            'terms' => '',
            'instagram' => '',
            'telegram' => '',
            'whatsapp' => '',
            'store_name' => 'فروشگاه آرزو',
            'economic_code' => '',
            'payment_gateway' => 'saman',
            'saman_merchant' => '',
            'zarinpal_merchant' => '',
            'sms_api_key' => '',
            'sms_sender_number' => '',
            'sms_pattern_order' => '',
            'sms_pattern_status' => '',
            'sms_pattern_login' => '',
        ];

        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
