<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SizeGown;
use App\Models\SizeGownImage;
use App\Models\SizeGownTranslation;
use App\Models\SizeGownOption;
use App\Models\SizeGownOptionTranslation;

class SizeGownSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // test size gown 1
        $SizeGown1 = SizeGown::create([
            'type'  => 'options'
        ]);

        SizeGownImage::create([
            'size_gown_id'  => $SizeGown1->id,
            'image' => 'default.png'
        ]);

        SizeGownTranslation::create([
            'size_gown_id'  => $SizeGown1->id,
            'name' => 'Neck shape',
            'locale'  => 'en'
        ]);    
        
        SizeGownTranslation::create([
            'size_gown_id'  => $SizeGown1->id,
            'name' => 'شكل الرقبة',
            'locale'  => 'ar'
        ]);  
        
        $sizeGownOption1 = SizeGownOption::create([
            'size_gown_id'  => $SizeGown1->id,
            'image' => 'default.png'
        ]); 

        SizeGownOptionTranslation::create([
            'size_gown_option_id'  => $sizeGownOption1->id,
            'name' => 'Upside down',
            'locale'  => 'en'
        ]);          

        SizeGownOptionTranslation::create([
            'size_gown_option_id'  => $sizeGownOption1->id,
            'name' => 'مقلوب',
            'locale'  => 'ar'
        ]);  
        
        $sizeGownOption11 = SizeGownOption::create([
            'size_gown_id'  => $SizeGown1->id,
            'image' => 'default.png'
        ]); 

        SizeGownOptionTranslation::create([
            'size_gown_option_id'  => $sizeGownOption11->id,
            'name' => 'Upside down 2',
            'locale'  => 'en'
        ]);          

        SizeGownOptionTranslation::create([
            'size_gown_option_id'  => $sizeGownOption11->id,
            'name' => '2 مقلوب',
            'locale'  => 'ar'
        ]);
        
        // test size gown 2
        $SizeGown2 = SizeGown::create([
            'type'  => 'options'
        ]);

        SizeGownTranslation::create([
            'size_gown_id'  => $SizeGown2->id,
            'name' => 'Mobile pocket shape',
            'locale'  => 'en'
        ]);    
        
        SizeGownTranslation::create([
            'size_gown_id'  => $SizeGown2->id,
            'name' => 'شكل جيب الجوال',
            'locale'  => 'ar'
        ]);  
        
        $sizeGownOption2 = SizeGownOption::create([
            'size_gown_id'  => $SizeGown2->id
        ]); 

        SizeGownOptionTranslation::create([
            'size_gown_option_id'  => $sizeGownOption2->id,
            'name' => 'Right mobile pocket',
            'locale'  => 'en'
        ]);          

        SizeGownOptionTranslation::create([
            'size_gown_option_id'  => $sizeGownOption2->id,
            'name' => 'جيب جوال يمين',
            'locale'  => 'ar'
        ]);  
        
        $sizeGownOption22 = SizeGownOption::create([
            'size_gown_id'  => $SizeGown2->id
        ]); 

        SizeGownOptionTranslation::create([
            'size_gown_option_id'  => $sizeGownOption22->id,
            'name' => 'Left mobile pocket',
            'locale'  => 'en'
        ]);          

        SizeGownOptionTranslation::create([
            'size_gown_option_id'  => $sizeGownOption22->id,
            'name' => 'جيب جوال يسار',
            'locale'  => 'ar'
        ]);

        // test size gown 3
        $SizeGown3 = SizeGown::create([
            'type'  => 'text'
        ]);

        SizeGownImage::create([
            'size_gown_id'  => $SizeGown3->id,
            'image' => 'default4.png'
        ]);

        SizeGownImage::create([
            'size_gown_id'  => $SizeGown3->id,
            'image' => 'default2.png'
        ]);

        SizeGownImage::create([
            'size_gown_id'  => $SizeGown3->id,
            'image' => 'default3.png'
        ]);

        SizeGownTranslation::create([
            'size_gown_id'  => $SizeGown3->id,
            'name' => 'Button shape',
            'locale'  => 'en'
        ]);    
        
        SizeGownTranslation::create([
            'size_gown_id'  => $SizeGown3->id,
            'name' => 'شكل الازرار',
            'locale'  => 'ar'
        ]);  
        
        $sizeGownOption3 = SizeGownOption::create([
            'size_gown_id'  => $SizeGown3->id
        ]); 

        SizeGownoptionTranslation::create([
            'size_gown_option_id'  => $sizeGownOption3->id,
            'name' => 'Pocket button number',
            'locale'  => 'en'
        ]);          

        SizeGownOptionTranslation::create([
            'size_gown_option_id'  => $sizeGownOption3->id,
            'name' => 'رقم زر الجيب',
            'locale'  => 'ar'
        ]);  
        
        $sizeGownOption33 = SizeGownOption::create([
            'size_gown_id'  => $SizeGown3->id
        ]); 

        SizeGownOptionTranslation::create([
            'size_gown_option_id'  => $sizeGownOption33->id,
            'name' => 'Neck button number',
            'locale'  => 'en'
        ]);          

        SizeGownOptionTranslation::create([
            'size_gown_option_id'  => $sizeGownOption33->id,
            'name' => 'رقم زر الرقبة',
            'locale'  => 'ar'
        ]); 
        
        $sizeGownOption333 = SizeGownOption::create([
            'size_gown_id'  => $SizeGown3->id
        ]); 

        SizeGownOptionTranslation::create([
            'size_gown_option_id'  => $sizeGownOption333->id,
            'name' => 'The number of neck buttons',
            'locale'  => 'en'
        ]);          

        SizeGownOptionTranslation::create([
            'size_gown_option_id'  => $sizeGownOption333->id,
            'name' => 'عدد زر الرقبة',
            'locale'  => 'ar'
        ]); 
    }
}
