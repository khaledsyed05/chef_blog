<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model implements TranslatableContract
{
    use HasFactory, Translatable, SoftDeletes ;

        // store data for columns that translated by locale
    public $translatedAttributes = ['name', 'description'];

        // fill static columns dosen't translated by locale 
    protected $fillable = ['slug'];

    public function recipes()
    {
        return $this->hasMany(Recipe::class); // one Category has many Recipes
    }
}
