<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;
use App\Models\LanguageTranslation;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $language1 = Language::create([
            'language_universal'  => 'en',
            'rtl' => 'inactive',
            'status' => 'active'
        ]);
        LanguageTranslation::create([
            'language_id'  => $language1->id,
            'languageName'  => 'English',
            'locale'  => 'en',
        ]);
        LanguageTranslation::create([
            'language_id'  => $language1->id,
            'languageName'  => 'الإنجليزية',
            'locale'  => 'ar',
        ]); 
        
        $language2 = Language::create([
            'language_universal'  => 'ar',
            'rtl' => 'active',
            'status' => 'active'
        ]);
        LanguageTranslation::create([
            'language_id'  => $language2->id,
            'languageName'  => 'Arabic',
            'locale'  => 'en',
        ]);
        LanguageTranslation::create([
            'language_id'  => $language2->id,
            'languageName'  => 'العربية',
            'locale'  => 'ar',
        ]); 
    }
}
