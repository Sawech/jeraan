<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SizeTypeCategory;

class SizeTypeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $SizeTypeCategory1 = SizeTypeCategory::create([
            'category_id'  => 1,
            'size_type_id'  => 1,
        ]);

        $SizeTypeCategory2 = SizeTypeCategory::create([
            'category_id'  => 1,
            'size_type_id'  => 2,
        ]);

        $SizeTypeCategory3 = SizeTypeCategory::create([
            'category_id'  => 1,
            'size_type_id'  => 3,
        ]);

        $SizeTypeCategory4 = SizeTypeCategory::create([
            'category_id'  => 1,
            'size_type_id'  => 4,
        ]);  
        
        $SizeTypeCategory5 = SizeTypeCategory::create([
            'category_id'  => 2,
            'size_type_id'  => 1,
        ]);   
        
        $SizeTypeCategory6 = SizeTypeCategory::create([
            'category_id'  => 2,
            'size_type_id'  => 2,
        ]);   

        $SizeTypeCategory7 = SizeTypeCategory::create([
            'category_id'  => 2,
            'size_type_id'  => 3,
        ]);
       
        $SizeTypeCategory8 = SizeTypeCategory::create([
            'category_id'  => 3,
            'size_type_id'  => 2,
        ]);   

        $SizeTypeCategory9 = SizeTypeCategory::create([
            'category_id'  => 3,
            'size_type_id'  => 4,
        ]);         
    }
}
