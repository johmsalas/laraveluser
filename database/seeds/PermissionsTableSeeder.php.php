<?php

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert([
            'id' => 1,
            'name' => 'see users',
            'label' => 'See users',
        ]);

        DB::table('permissions')->insert([
            'id' => 2,
            'name' => 'edit users',
            'label' => 'Edit users',
        ]);

        DB::table('permissions')->insert([
            'id' => 3,
            'name' => 'delete users',
            'label' => 'Delete users',
        ]);

        DB::table('permissions')->insert([
            'id' => 4,
            'name' => 'edit own user',
            'label' => 'Edit own users',
        ]);

        DB::table('permissions')->insert([
            'id' => 5,
            'name' => 'delete own user',
            'label' => 'Delete own users',
        ]);

        DB::table('permissions')->insert([
            'id' => 6,
            'name' => 'create users',
            'label' => 'Create users',
        ]);
    }
}
