<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::where('name', 'Super Admin')->firstOrFail();

        User::create([
            'name' => 'Super Admin User',
            'email' => 'super-admin@gmail.com',
            'password' => Hash::make('password'),
            'role_id' => $role->id
        ]);
    }
}
