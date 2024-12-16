<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            ['name' => 'seeker', 'description' => 'Соискатель'],
            ['name' => 'employer', 'description' => 'Работодатель'],
            ['name' => 'moderator', 'description' => 'Модератор'],
            ['name' => 'admin', 'description' => 'Администратор'],
        ]);
    }
}


