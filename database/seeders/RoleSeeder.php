<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\RoleTranslation;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role1 = Role::create([
            'type'  => 'user'
        ]);
        RoleTranslation::create([
            'role_id'  => $role1->id,
            'name'  => 'User',
            'locale'  => 'en',
        ]);
        RoleTranslation::create([
            'role_id'  => $role1->id,
            'name'  => 'مستخدم',
            'locale'  => 'ar',
        ]);       
        $role2 = Role::create([
            'type'  => 'admin'
        ]);
        RoleTranslation::create([
            'role_id'  => $role2->id,
            'name'  => 'Admin',
            'locale'  => 'en',
        ]);
        RoleTranslation::create([
            'role_id'  => $role2->id,
            'name'  => 'مدير النظام',
            'locale'  => 'ar',
        ]);          
        $role3 = Role::create([
            'type'  => 'seller'
        ]);
        RoleTranslation::create([
            'role_id'  => $role3->id,
            'name'  => 'Seller',
            'locale'  => 'en',
        ]);
        RoleTranslation::create([
            'role_id'  => $role3->id,
            'name'  => 'بائع',
            'locale'  => 'ar',
        ]);          
        $role4 = Role::create([
            'type'  => 'shear_factor'
        ]);
        RoleTranslation::create([
            'role_id'  => $role4->id,
            'name'  => 'Shear factor',
            'locale'  => 'en',
        ]);
        RoleTranslation::create([
            'role_id'  => $role4->id,
            'name'  => 'عامل قص',
            'locale'  => 'ar',
        ]);  
        $role5 = Role::create([
            'type'  => 'sewing_worker'
        ]);
        RoleTranslation::create([
            'role_id'  => $role5->id,
            'name'  => 'Sewing worker',
            'locale'  => 'en',
        ]);
        RoleTranslation::create([
            'role_id'  => $role5->id,
            'name'  => 'عامل خياطه',
            'locale'  => 'ar',
        ]);          
        $role6 = Role::create([
            'type'  => 'button_operator'
        ]);
        RoleTranslation::create([
            'role_id'  => $role6->id,
            'name'  => 'Button operator',
            'locale'  => 'en',
        ]);
        RoleTranslation::create([
            'role_id'  => $role6->id,
            'name'  => 'عامل ازرار',
            'locale'  => 'ar',
        ]);         
    }
}
