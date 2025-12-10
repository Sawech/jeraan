<?php

namespace Database\Seeders;

use App\Models\Design;
use App\Models\DesignImage;
use App\Models\DesignTranslation;
use Illuminate\Database\Seeder;

class DesignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $design1 = Design::create([
        ]);
        DesignImage::create([
            'design_id' => $design1->id,
            'image' => 'default.png'
        ]);
        DesignImage::create([
            'design_id' => $design1->id,
            'image' => 'default2.png'
        ]);
        DesignTranslation::create([
            'design_id' => $design1->id,
            'name' => 'Dress design',
            'description' => 'Test description here',
            'locale' => 'en',
        ]);
        DesignTranslation::create([
            'design_id' => $design1->id,
            'name' => 'تصميم ثوب',
            'description' => 'نموذج الوصف هنا',
            'locale' => 'ar',
        ]);
    }
}
