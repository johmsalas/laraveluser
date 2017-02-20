<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'admin',
            'email' => 'admin@mail.com',
            'password' => bcrypt('admin'),
            'active' => true,
        ]);
        User::find(1)->roles()->attach(Role::where('name', 'administrator')->first()->id);

        DB::table('users')->insert([
            'id' => 2,
            'name' => 'Agent Name',
            'email' => 'agent@mail.com',
            'password' => bcrypt('agent'),
            'active' => true,
        ]);
        User::find(2)->roles()->attach(Role::where('name', 'agent')->first()->id);

        DB::table('users')->insert([
            'id' => 3,
            'name' => 'Customer Name',
            'email' => 'customer@mail.com',
            'password' => bcrypt('customer'),
            'active' => true,
        ]);
        User::find(3)->roles()->attach(Role::where('name', 'customer')->first()->id);
    }
}
