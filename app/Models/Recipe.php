<?php

namespace App\Models;

use App\Traits\HasSeo;
use App\Traits\Toggleable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipe extends Model implements TranslatableContract
{
    use HasFactory, Translatable, SoftDeletes, HasSeo ,Toggleable;

    // store data for columns that translated by locale
    public $translatedAttributes = ['title', 'description', 'ingredients', 'instructions', 'total_time'];

    // fill static columns dosen't translated by locale 
    protected $fillable = [
        'user_id',
        'category_id',
        'youtube_video',
        'cover_image',
        'featured',
        'published'
    ];

    protected $casts = [
        'ingredients' => 'array',
        'instructions' => 'array',
        'ingredients.*' => 'array',
        'instructions.*' => 'array',
        'featured' => 'boolean',
        'published' => 'boolean',
    ];

    public function getIngredientsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getInstructionsAttribute($value)
    {
        return json_decode($value, true);
    }
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
    
    public function likesCount()
    {
        return $this->likes()->where('is_like', true)->count();
    }
    
    public function dislikesCount()
    {
        return $this->likes()->where('is_like', false)->count();
    }
    public function user()
    {
        return $this->belongsTo(User::class); // many Recipes have (belongs to) one User
    }

    public function category()
    {
        return $this->belongsTo(Category::class); // many Recipes have (belongs to) one Category
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class); // Recipe have many (belongs to many) Tags
    }

    public function comments()
    {
        return $this->hasMany(Comment::class); // Recipe has many comment 
    }
    public function nutrition()
    {
        return $this->hasOne(NutritionFact::class);
    }
}
