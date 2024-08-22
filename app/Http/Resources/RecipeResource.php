<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $parent = parent::toArray($request);
        return [
            'parent' => $parent,
            'ingredients' => $this->ingredients, // This will now be an array
            'instructions' => $this->instructions, // This will now be an array
        ];
    }
}
