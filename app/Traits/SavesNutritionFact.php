<?php

namespace App\Traits;

use App\Models\Recipe;
use App\Models\NutritionFact;
use App\Http\Requests\NutritionFactRequest;

trait SavesNutritionFact
{
    private function saveNutritionFact(Recipe $recipe, NutritionFactRequest $request)
    {
        $validatedData = $request->validated();

        if (!empty($validatedData['nutrition_fact'])) {
            $nutritionFact = $recipe->nutritionFact ?? new NutritionFact();
            $nutritionFact->recipe()->associate($recipe);
            $nutritionFact->save();

            foreach ($validatedData['nutrition_fact'] as $locale => $data) {
                $nutritionFact->translateOrNew($locale)->fill([
                    'calories' => $data['calories'],
                    'protein' => $data['protein'],
                    'fat' => $data['fat'],
                ]);
            }
            $nutritionFact->save();
        }
    }
}