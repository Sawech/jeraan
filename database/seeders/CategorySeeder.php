<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\CategoryTranslation;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category1 = Category::create([
            'type'  => 'gown'
        ]);
        CategoryTranslation::create([
            'category_id'  => $category1->id,
            'name'  => 'Saudi dress',
            'locale'  => 'en',
        ]);
        CategoryTranslation::create([
            'category_id'  => $category1->id,
            'name'  => 'ثوب سعودى',
            'locale'  => 'ar',
        ]);  

        $category2 = Category::create([
            'type'  => 'gown'
        ]);
        CategoryTranslation::create([
            'category_id'  => $category2->id,
            'name'  => 'Qatari dress',
            'locale'  => 'en',
        ]);
        CategoryTranslation::create([
            'category_id'  => $category2->id,
            'name'  => 'ثوب قطرى',
            'locale'  => 'ar',
        ]);  

        $category3 = Category::create([
            'type'  => 'gown'
        ]);
        CategoryTranslation::create([
            'category_id'  => $category3->id,
            'name'  => 'Kuwaiti dress',
            'locale'  => 'en',
        ]);
        CategoryTranslation::create([
            'category_id'  => $category3->id,
            'name'  => 'ثوب كويتى',
            'locale'  => 'ar',
        ]);  

        $category4 = Category::create([
            'type'  => 'gown'
        ]);
        CategoryTranslation::create([
            'category_id'  => $category4->id,
            'name'  => 'Emirati dress',
            'locale'  => 'en',
        ]);
        CategoryTranslation::create([
            'category_id'  => $category4->id,
            'name'  => 'ثوب اماراتى',
            'locale'  => 'ar',
        ]);  

        $category5 = Category::create([
            'type'  => 'gown'
        ]);
        CategoryTranslation::create([
            'category_id'  => $category5->id,
            'name'  => 'Nightgown',
            'locale'  => 'en',
        ]);
        CategoryTranslation::create([
            'category_id'  => $category5->id,
            'name'  => 'ثوب نوم',
            'locale'  => 'ar',
        ]);  
        
        $category6 = Category::create([
            'type'  => 'other'
        ]);
        CategoryTranslation::create([
            'category_id'  => $category6->id,
            'name'  => 'Coat',
            'locale'  => 'en',
        ]);
        CategoryTranslation::create([
            'category_id'  => $category6->id,
            'name'  => 'معطف',
            'locale'  => 'ar',
        ]);  
        
        $category7 = Category::create([
            'type'  => 'other'
        ]);
        CategoryTranslation::create([
            'category_id'  => $category7->id,
            'name'  => 'Sedary',
            'locale'  => 'en',
        ]);
        CategoryTranslation::create([
            'category_id'  => $category7->id,
            'name'  => 'سديرى',
            'locale'  => 'ar',
        ]); 
        
        $category8 = Category::create([
            'type'  => 'other'
        ]);
        CategoryTranslation::create([
            'category_id'  => $category8->id,
            'name'  => 'Trouser',
            'locale'  => 'en',
        ]);
        CategoryTranslation::create([
            'category_id'  => $category8->id,
            'name'  => 'بنطلون',
            'locale'  => 'ar',
        ]); 
        
        $category9 = Category::create([
            'type'  => 'other'
        ]);
        CategoryTranslation::create([
            'category_id'  => $category9->id,
            'name'  => 'Shirt',
            'locale'  => 'en',
        ]);
        CategoryTranslation::create([
            'category_id'  => $category9->id,
            'name'  => 'قميص',
            'locale'  => 'ar',
        ]);  
        
        $category10 = Category::create([
            'type'  => 'other'
        ]);
        CategoryTranslation::create([
            'category_id'  => $category10->id,
            'name'  => 'Long pants',
            'locale'  => 'en',
        ]);
        CategoryTranslation::create([
            'category_id'  => $category10->id,
            'name'  => 'سروال طويل',
            'locale'  => 'ar',
        ]); 
        
        $category11 = Category::create([
            'type'  => 'other'
        ]);
        CategoryTranslation::create([
            'category_id'  => $category11->id,
            'name'  => 'Short pants',
            'locale'  => 'en',
        ]);
        CategoryTranslation::create([
            'category_id'  => $category11->id,
            'name'  => 'سروال قصير',
            'locale'  => 'ar',
        ]); 

        $category12 = Category::create([
            'type'  => 'other'
        ]);
        CategoryTranslation::create([
            'category_id'  => $category12->id,
            'name'  => 'Other',
            'locale'  => 'en',
        ]);
        CategoryTranslation::create([
            'category_id'  => $category12->id,
            'name'  => 'اخرى',
            'locale'  => 'ar',
        ]);         
    }
}
