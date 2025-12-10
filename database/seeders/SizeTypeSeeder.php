<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SizeType;
use App\Models\SizeTypeTranslation;

class SizeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $type1 = SizeType::create([]);
        SizeTypeTranslation::create([
            'size_type_id'  => $type1->id,
            'name'  => 'Height',
            'locale'  => 'en',
        ]);
        SizeTypeTranslation::create([
            'size_type_id'  => $type1->id,
            'name'  => 'الطول',
            'locale'  => 'ar',
        ]); 

        $type2 = SizeType::create([]);
        SizeTypeTranslation::create([
            'size_type_id'  => $type2->id,
            'name'  => 'Chest width',
            'locale'  => 'en',
        ]);
        SizeTypeTranslation::create([
            'size_type_id'  => $type2->id,
            'name'  => 'عرض الصدر',
            'locale'  => 'ar',
        ]); 

        $type3 = SizeType::create([]);
        SizeTypeTranslation::create([
            'size_type_id'  => $type3->id,
            'name'  => 'Center width',
            'locale'  => 'en',
        ]);
        SizeTypeTranslation::create([
            'size_type_id'  => $type3->id,
            'name'  => 'عرض الوسط',
            'locale'  => 'ar',
        ]); 

        $type4 = SizeType::create([]);
        SizeTypeTranslation::create([
            'size_type_id'  => $type4->id,
            'name'  => 'Shoulder width',
            'locale'  => 'en',
        ]);
        SizeTypeTranslation::create([
            'size_type_id'  => $type4->id,
            'name'  => 'عرض الكتف',
            'locale'  => 'ar',
        ]); 
    }
}
