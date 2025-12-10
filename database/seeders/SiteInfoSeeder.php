<?php

namespace Database\Seeders;
use App\Models\SiteInfo;
use Illuminate\Database\Seeder;

class SiteInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SiteInfo::create([
            'attribute' => 'email',
            'value' => 'info@jeeranapp.com'
        ]);

        SiteInfo::create([
            'attribute' => 'phone',
            'value' => 0554444444
        ]);

        SiteInfo::create([
            'attribute' => 'whatsapp',
            'value' => 0554444444
        ]);
    }
}
