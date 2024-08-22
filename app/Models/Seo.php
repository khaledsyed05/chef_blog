<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Seo extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    public $translatedAttributes = ['meta_title', 'meta_description', 'og_title', 'og_description'];

    protected $fillable = ['seoable_id', 'seoable_type'];

    public function seoable()
    {
        return $this->morphTo();
    }
}
