<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ColorSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            ['name' => 'Brown', 'code' => '#A52A2A'],
            ['name' => 'Cyan', 'code' => '#00FFFF'],
            ['name' => 'Magenta', 'code' => '#FF00FF'],
            ['name' => 'Lime', 'code' => '#00FF00'],
            ['name' => 'Teal', 'code' => '#008080'],
            ['name' => 'Maroon', 'code' => '#800000'],
            ['name' => 'Navy', 'code' => '#000080'],
            ['name' => 'Olive', 'code' => '#808000'],
            ['name' => 'Silver', 'code' => '#C0C0C0'],
            ['name' => 'SkyBlue', 'code' => '#87CEEB'],
            ['name' => 'DarkBlue', 'code' => '#00008B'],
            ['name' => 'Indigo', 'code' => '#4B0082'],
            ['name' => 'LightGreen', 'code' => '#90EE90'],
            ['name' => 'Violet', 'code' => '#EE82EE'],
            ['name' => 'Tan', 'code' => '#D2B48C'],
            ['name' => 'Coral', 'code' => '#FF7F50'],
            ['name' => 'Aquamarine', 'code' => '#7FFFD4'],
            ['name' => 'Salmon', 'code' => '#FA8072'],
            ['name' => 'Khaki', 'code' => '#F0E68C'],
            ['name' => 'Gold', 'code' => '#FFD700'],
            ['name' => 'Tomato', 'code' => '#FF6347'],
            ['name' => 'Orchid', 'code' => '#DA70D6'],
            ['name' => 'RosyBrown', 'code' => '#BC8F8F'],
            ['name' => 'Thistle', 'code' => '#D8BFD8'],
            ['name' => 'Peru', 'code' => '#CD853F'],
            ['name' => 'SlateBlue', 'code' => '#6A5ACD'],
            ['name' => 'MediumSlateBlue', 'code' => '#7']
        ];

        foreach ($colors as $color) {
            \App\Models\Color::create($color);
        }
    }
}
