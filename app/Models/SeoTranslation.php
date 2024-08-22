<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoTranslation extends Model
{
    use HasFactory;
    public $timestamps = false;

    // fill columns that translated by locale
    protected $fillable = [
        'meta_title',
        'meta_description',
        'og_title',
        'og_description'
    ];
}
