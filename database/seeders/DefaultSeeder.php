<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permissions')->insert(
            [
                'name' => 'Permission',
                'parent_id' => 0,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('roles')->insert(
            [
                'name' => 'Admin',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('roles')->insert(
            [
                'name' => 'Cashier',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
