<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Tools',
                'description' => 'Hand tools, power tools, and equipment for repairs and projects.',
            ],
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and accessories available for borrowing or rental.',
            ],
            [
                'name' => 'Sports Equipment',
                'description' => 'Sports gear and equipment for recreational and athletic activities.',
            ],
            [
                'name' => 'Camping & Outdoor',
                'description' => 'Camping gear and equipment for outdoor activities.',
            ],
            [
                'name' => 'Household',
                'description' => 'Common household items and equipment.',
            ],
            [
                'name' => 'Party & Events',
                'description' => 'Items used for parties, celebrations, and events.',
            ],
            [
                'name' => 'Gardening',
                'description' => 'Gardening tools, equipment, and supplies.',
            ],
            [
                'name' => 'Photography',
                'description' => 'Cameras, tripods, lighting equipment, and photography accessories.',
            ],
            [
                'name' => 'Others',
                'description' => 'Items that do not belong to the other available categories.',
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'description' => $category['description'],
                'status' => 'active',
            ]);
        }
    }
}