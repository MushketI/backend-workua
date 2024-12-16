<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            ['name' => 'Киев', 'region' => 'Киевская область'],
            ['name' => 'Харьков', 'region' => 'Харьковская область'],
            ['name' => 'Одесса', 'region' => 'Одесская область'],
            ['name' => 'Львов', 'region' => 'Львовская область'],
            ['name' => 'Днепр', 'region' => 'Днепропетровская область'],
            ['name' => 'Запорожье', 'region' => 'Запорожская область'],
            ['name' => 'Винница', 'region' => 'Винницкая область'],
            ['name' => 'Полтава', 'region' => 'Полтавская область'],
            ['name' => 'Чернигов', 'region' => 'Черниговская область'],
            ['name' => 'Черкассы', 'region' => 'Черкасская область'],
            ['name' => 'Тернополь', 'region' => 'Тернопольская область'],
            ['name' => 'Ровно', 'region' => 'Ровенская область'],
            ['name' => 'Сумы', 'region' => 'Сумская область'],
            ['name' => 'Житомир', 'region' => 'Житомирская область'],
            ['name' => 'Кропивницкий', 'region' => 'Кировоградская область'],
            ['name' => 'Хмельницкий', 'region' => 'Хмельницкая область'],
            ['name' => 'Ивано-Франковск', 'region' => 'Ивано-Франковская область'],
            ['name' => 'Закарпатье', 'region' => 'Закарпатская область'],
            ['name' => 'Черновцы', 'region' => 'Черновицкая область'],
            ['name' => 'Луцк', 'region' => 'Волынская область'],
            ['name' => 'Николаев', 'region' => 'Николаевская область'],
            ['name' => 'Донецк', 'region' => 'Донецкая область'],
            ['name' => 'Луганск', 'region' => 'Луганская область'],
        ];

        DB::table('locations')->insert($locations);
    }
}
