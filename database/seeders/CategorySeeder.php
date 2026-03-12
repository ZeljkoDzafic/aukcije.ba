<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Elektronika', 'icon' => 'device-phone-mobile', 'children' => [
                'Mobiteli i tableti', 'Laptopi i računari', 'TV i audio', 'Gaming', 'Foto i video',
            ]],
            ['name' => 'Automobili i vozila', 'icon' => 'truck', 'children' => [
                'Osobni automobili', 'Motocikli', 'Kamioni i kombiji', 'Dijelovi i oprema',
            ]],
            ['name' => 'Kolekcionarstvo', 'icon' => 'star', 'children' => [
                'Stari novac i marke', 'Vintage satovi', 'Umetnine', 'Stare knjige',
            ]],
            ['name' => 'Kuća i bašta', 'icon' => 'home', 'children' => [
                'Namještaj', 'Kuhinja', 'Alati', 'Dekoracija',
            ]],
            ['name' => 'Odjeća i obuća', 'icon' => 'shopping-bag', 'children' => [
                'Muška odjeća', 'Ženska odjeća', 'Dječija odjeća', 'Sportska odjeća',
            ]],
            ['name' => 'Sport i rekreacija', 'icon' => 'trophy', 'children' => [
                'Fitnes oprema', 'Bicikli', 'Zimski sportovi', 'Vodeni sportovi',
            ]],
            ['name' => 'Igračke i igre', 'icon' => 'puzzle-piece', 'children' => [
                'LEGO i konstruktori', 'Video igre', 'Društvene igre', 'Lutke i figurice',
            ]],
            ['name' => 'Nakit i satovi', 'icon' => 'sparkles', 'children' => [
                'Zlatni nakit', 'Srebrni nakit', 'Satovi', 'Moderni nakit',
            ]],
        ];

        foreach ($categories as $sortOrder => $cat) {
            $parent = Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'icon' => $cat['icon'],
                'sort_order' => $sortOrder,
                'is_active' => true,
            ]);

            foreach ($cat['children'] as $childOrder => $childName) {
                Category::create([
                    'parent_id' => $parent->id,
                    'name' => $childName,
                    'slug' => Str::slug($childName),
                    'sort_order' => $childOrder,
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('Categories seeded.');
    }
}
