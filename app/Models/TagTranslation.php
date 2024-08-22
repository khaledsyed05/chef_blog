<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TagTranslation extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamps = false;

        // fill columns that translated by locale
        protected $fillable = [
            'name'
        ];
}