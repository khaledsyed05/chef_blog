<?php

namespace App\Traits;

use App\Models\Seo;

trait HasSeo
{
    public function seo()
    {
        return $this->morphOne(Seo::class, 'seoable');
    }

    public function saveSeo($seoData)
    {
        $seo = $this->seo ?? new Seo();
        $seo->seoable()->associate($this);
        $seo->save();

        foreach ($seoData as $field => $translations) {
            foreach ($translations as $locale => $value) {
                $seo->translateOrNew($locale)->{$field} = $value;
            }
        }
        $seo->save();
    }
}