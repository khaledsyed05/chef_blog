<?php

namespace App\Models;

use App\Traits\HasSeo;
use App\Traits\Toggleable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
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
    public function category()
    {
        return $this->belongsTo(Category::class); // many Recipes have (belongs to) one Category
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
    return $this->morphMany(Comment::class, 'commentable');
}
    public function scopeSearch(Builder $query, string $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->whereHas('translations', function ($query) use ($search) {
                $query->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('content', 'LIKE', "%{$search}%");
            })
            ->orWhereHas('tags', function ($query) use ($search) {
                $query->whereHas('translations', function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%");
                });
            });
        });
    }
}
