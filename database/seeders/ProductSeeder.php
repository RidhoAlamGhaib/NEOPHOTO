<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $data = [[
            'name' => 'Standar 2 Lembar',
            'type' => '104',
            'price' => '50000',
            'description' => 'For the both of us',
            'img' => '2lmbrS.png',
        ],[
            'name' => 'Standar 4 Foto',
            'type' => '103',
            'price' => '45000',
            'description' => 'Closer than ever',
            'img' => '4Foto.png',
        ],[
            'name' => 'Standar 6 Foto',
            'type' => '105',
            'price' => '45000',
            'description' => 'The more the better',
            'img' => '6Foto.png',
        ],[
            'name' => 'High Angle',
            'type' => '104',
            'price' => '60000',
            'description' => 'A view from above',
            'img' => 'High-Angle.png',
            ],
            [
                'name' => 'YearBook',
                'type' => '104',
                'price' => '60000',
                'description' => 'School memories',
                'img' => 'Yearbook.png',
            ],
            [
                'name' => 'Extra Print Standar',
                'type' => 'addon',
                'price' => '15000',
                'description' => '',
                'img' => '',
            ],
            [
                'name' => 'Extra Print Special',
                'type' => 'addon',
                'price' => '20000',
                'description' => '',
                'img' => '',
            ],
            [
                'name' => 'keychain',
                'type' => 'addon',
                'price' => '5000',
                'description' => '',
                'img' => '',
            ],
            [
                'name' => 'mini album',
                'type' => 'addon',
                'price' => '20000',
                'description' => '',
                'img' => '',
            ],
            [
                'name' => 'Transparant Print',
                'type' => 'addon',
                'price' => '15000',
                'description' => '',
                'img' => '',
            ],
            ];
        product::insert($data);
    }
}
