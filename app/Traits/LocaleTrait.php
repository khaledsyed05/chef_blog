<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait LocaleTrait
{
    protected $locale;

    public function callAction($method, $parameters)
    {
        $this->locale = request()->header('Accept-Language', App::getLocale());
        App::setLocale($this->locale);

        return parent::callAction($method, $parameters);
    }
}