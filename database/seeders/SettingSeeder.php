<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use App\Models\SettingTranslation;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $setting1 = Setting::create([
            'attribute'  => 'about_us'
        ]);
        SettingTranslation::create([
            'setting_id'  => $setting1->id,
            'value'  => 'Description here',
            'locale'  => 'en',
        ]);
        SettingTranslation::create([
            'setting_id'  => $setting1->id,
            'value'  => 'الوصف هنا',
            'locale'  => 'ar',
        ]);  

        $setting2 = Setting::create([
            'attribute'  => 'terms_conditions'
        ]);
        SettingTranslation::create([
            'setting_id'  => $setting2->id,
            'value'  => 'Description here for terms',
            'locale'  => 'en',
        ]);
        SettingTranslation::create([
            'setting_id'  => $setting2->id,
            'value'  => ' الوصف هنا للشروط والاحكام',
            'locale'  => 'ar',
        ]);   
    }
}
