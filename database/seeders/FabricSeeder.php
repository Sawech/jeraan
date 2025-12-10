<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fabric;
use App\Models\FabricTranslation;

class FabricSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fabric1 = Fabric::create([
            'image'  => 'default.png'
        ]);
        FabricTranslation::create([
            'fabric_id'  => $fabric1->id,
            'name'  => 'Polyester fabric',
            'description'  => 'Test description here',
            'locale'  => 'en',
        ]);
        FabricTranslation::create([
            'fabric_id'  => $fabric1->id,
            'name'  => 'قماش بوليستر',
            'description'  => 'نموذج الوصف هنا',
            'locale'  => 'ar',
        ]);  

        $fabric2 = Fabric::create([
            'image'  => 'default2.png'
        ]);
        FabricTranslation::create([
            'fabric_id'  => $fabric2->id,
            'name'  => 'Natural cotton fabric',
            'description'  => 'Test description here2',
            'locale'  => 'en',
        ]);
        FabricTranslation::create([
            'fabric_id'  => $fabric2->id,
            'name'  => 'قماش قطن طبيعى',
            'description'  => '2نموذج الوصف هنا',
            'locale'  => 'ar',
        ]);  
    }
}
