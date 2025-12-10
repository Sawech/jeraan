<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            CategorySeeder::class,
            DesignSeeder::class,
            FabricSeeder::class,
            SizeGownSeeder::class,
            SizeTypeSeeder::class,
            SizeTypeCategorySeeder::class,
            SizeTypeCategoryUserSeeder::class,
            UserSeeder::class,
            SiteInfoSeeder::class,
            SettingSeeder::class,
            LanguageSeeder::class
        ]);
    }
}
