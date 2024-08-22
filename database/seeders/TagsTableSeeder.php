<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagsTableSeeder extends Seeder
{
    public function run()
    {
        $tags = [
            [
                'slug' => 'breakfast',
                'translations' => [
                    'en' => ['name' => 'Breakfast'],
                    'ar' => ['name' => 'فطور'],
                ],
            ],
            [
                'slug' => 'lunch',
                'translations' => [
                    'en' => ['name' => 'Lunch'],
                    'ar' => ['name' => 'غداء'],
                ],
            ],
            [
                'slug' => 'dinner',
                'translations' => [
                    'en' => ['name' => 'Dinner'],
                    'ar' => ['name' => 'عشاء'],
                ],
            ],
            // Add more tags as needed
        ];

        foreach ($tags as $tagData) {
            $tag = Tag::create(['slug' => $tagData['slug']]);
            
            foreach ($tagData['translations'] as $locale => $translation) {
                $tag->translateOrNew($locale)->name = $translation['name'];
            }
            
            $tag->save();
        }
    }
}