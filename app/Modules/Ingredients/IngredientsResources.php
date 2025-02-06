<?php

namespace App\Modules\Ingredients;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IngredientsResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'unit' => $this->unit,
            'quantity' => $this->quantity,
            'warehouse' => $this->warehouse?->title ?? null, // Null-safe access
            'workspace' => $this->workspace?->name ?? null, // Null-safe access
            'workspace_id' => $this->workspace?->id ?? null, // Null-safe access
            'cover' => config('app.url') . '/images/' . $this->cover
        ];
    }
}
