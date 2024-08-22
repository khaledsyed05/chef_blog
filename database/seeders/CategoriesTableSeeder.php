<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'slug' => 'vegan',
                'translations' => [
                    'en' => [
                        'name' => 'vegan',
                        'description' => 'vegan diet includes only plant foods—fruits, vegetables, beans, grains, nuts, and seeds'
                    ],
                    'ar' => [
                        'name' => 'نباني',
                        'description' => 'يشمل النظام الغذائي النباتي الأطعمة النباتية فقط - الفواكه والخضروات والفاصوليا والحبوب والمكسرات والبذور'
                    ],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::create(['slug' => $categoryData['slug']]);

            foreach ($categoryData['translations'] as $locale => $translation) {
                $category->translateOrNew($locale)->name = $translation['name'];
                $category->translateOrNew($locale)->description = $translation['description'];
            }

            $category->save();
        }
    }
}