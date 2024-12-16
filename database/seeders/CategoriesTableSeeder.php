<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'IT',
            'Администрация',
            'Строительство',
            'Бухгалтерия',
            'Гостинично-ресторанный бизнес',
            'СМИ',
            'Логистика',
            'Медицина',
            'Образование',
            'Охрана',
            'Продажа',
            'Розничная торговля',
            'Сфера обслуживания',
            'Сельское хозяйство',
            'Телекоммуникации',
            'Транспорт',
            'Управление персоналом',
            'Финансы'
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'name' => $category
            ]);
        }
    }
}
