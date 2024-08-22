<?php

namespace App\Traits;

use App\Http\Requests\SeoRequest;

trait HandlesSeo
{
    protected function validateAndSaveSeo($model, SeoRequest $request)
    {
        $validatedSeoData = $request->validated();

        if (!empty($validatedSeoData['seo'])) {
            $model->saveSeo($validatedSeoData['seo']);
        }
    }
}