<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SizeTypeCategoryUser;

class SizeTypeCategoryUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $SizeTypeCategoryUser1 = SizeTypeCategoryUser::create([
            'size_type_category_id'  => 1,
            'user_id'  => 1,
            'value' => 150
        ]);
        $SizeTypeCategoryUser2 = SizeTypeCategoryUser::create([
            'size_type_category_id'  => 2,
            'user_id'  => 1,
            'value' => 130
        ]);

        $SizeTypeCategoryUser3 = SizeTypeCategoryUser::create([
            'size_type_category_id'  => 5,
            'user_id'  => 1,
            'value' => 120
        ]);
        $SizeTypeCategoryUser4 = SizeTypeCategoryUser::create([
            'size_type_category_id'  => 6,
            'user_id'  => 1,
            'value' => 110
        ]);

        $SizeTypeCategoryUser5 = SizeTypeCategoryUser::create([
            'size_type_category_id'  => 8,
            'user_id'  => 1,
            'value' => 113
        ]);
        $SizeTypeCategoryUser6 = SizeTypeCategoryUser::create([
            'size_type_category_id'  => 9,
            'user_id'  => 1,
            'value' => 124
        ]);
        $SizeTypeCategoryUser7 = SizeTypeCategoryUser::create([
            'size_type_category_id'  => 3,
            'user_id'  => 1,
            'value' => 150
        ]);
        $SizeTypeCategoryUser8 = SizeTypeCategoryUser::create([
            'size_type_category_id'  => 4,
            'user_id'  => 1,
            'value' => 125
        ]);
    }
}
