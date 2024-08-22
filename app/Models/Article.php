<?php

namespace App\Models;

use App\Traits\HasSeo;
use App\Traits\Toggleable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Article extends Model implements TranslatableContract
{
    use HasFactory, Translatable, SoftDeletes, HasSeo, Toggleable;

    // columns that translated by locale
    public $translatedAttributes = ['title', 'content'];

    // static columns dosen't translated by locale 
    protected $fillable = [
        'user_id',
        'slug',
        'featured',
        'published',
        'cover_image',
    ];

    // Realations between columns
    public function user()
    {
        return $this->belongsTo(User::class); // one user have (belongs to)many articles
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

    public function tags()
    {
        return $this->belongsToMany(Tag::class); //many articles have many tags
    }

    public function comments()
    {
        return $this->hasMany(Comment::class); // one article has many comments
    }
}
