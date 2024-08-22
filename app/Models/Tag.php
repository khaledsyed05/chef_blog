<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model implements TranslatableContract
{
    use HasFactory, Translatable, SoftDeletes;

    // store data for columns that translated by locale
    public $translatedAttributes = [
        'name'
    ];
    protected $fillable = ['slug'];
    public function recipes()
    {
        return $this->belongsToMany(Recipe::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }
}
