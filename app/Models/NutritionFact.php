<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NutritionFact extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    public $translatedAttributes = ['calories', 'protein', 'fat'];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
