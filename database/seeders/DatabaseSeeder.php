<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Role;
use App\Models\Admin;
use App\Models\GroupAdminType;

use App\Models\User;
use App\Models\Permission;
use App\Models\Module;
// use App\Models\Ecom\FoodCategory;
use App\Models\Ecom\Supplier;
use App\Models\Restaurant\Restaurant;
use App\Models\AdminType;
use App\Models\SubModule;

use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $modulePermission = [];

        
        

        // Module insert
        $ModuleList =
        [ 
            [
                'name' => 'Role Access System',
                'sub_child' => ['Role Access']
            ],
            [
                'name' => 'Staff Account Management',
                'sub_child' => ['Staff Account']
            ],
            [
                'name' => 'Customer Management System',
                'sub_child' => ['Customer Management']
            ]   ,
            [
                'name' => 'Sales Management System',
                'sub_child' => ['Transaction', 'Invoice']
            ]   ,
            [
                'name' => 'Campaign Management System',
                'sub_child' => ['Campaign Management']
            ]   ,
            [
                'name' => 'Strategy Management System',
                'sub_child' => ['Strategy Management']
            ]   ,
            [
                'name' => 'Symbol Creation System',
                'sub_child' => ['Symbol Creation']
            ]    ,
            [
                'name' => 'Server Management System',
                'sub_child' => ['Server Management']
            ]    ,
            [
                'name' => 'Credit Management System',
                'sub_child' => ['Credit Management']
            ]    ,
            [
                'name' => 'Notification Management System',
                'sub_child' => ['Notification Management']
            ]        ,
            [
                'name' => 'Recent Logs System',
                'sub_child' => ['Recent Logs']
            ]    
        ];
        
        foreach ($ModuleList as $key => $module) {
            $mod = Module::create([
                'module_name' => $module['name'],
                'module_slug' => Str::slug($module['name']),
            ]);

            foreach ($module['sub_child'] as $key => $child) {
                SubModule::create([
                    'module_id' => $mod['id'],
                    'name' => $child,
                    'slug' => Str::slug($child),
                ]);
            }
            
        }


        // Main Admin Type Role insert
        $AdmintypeList = ['Super Admin', 'Admin', 'Staff', 'Other'];
        foreach ($AdmintypeList as $key => $module) {
            AdminType::create([
                'name' => $module,
                'slug' => Str::slug($module)
            ]);
        }

        // Admin role create
        // Role::insert(
        //     [
        //         'role_name' => 'Admin',
        //         'role_slug' => 'admin',
        //         'module_permissions' => implode(',', $modulePermission)
        //     ]
        // );

        // admin insert
        Admin::insert([
            [
                'name' => 'Super admin',
                'admin_type_id' => 1,
                'email' => 'superadmin@gmail.com',
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
            ]

        ]);

        // app user 
        User::insert([
            [
                'name' => 'user',
                'email' => 'user@gmail.com',
                'phone' => '9999999999',
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
            ]

        ]);

        // User::factory(10)->create();
    }
}
