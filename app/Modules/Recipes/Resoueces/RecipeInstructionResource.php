<?php

namespace App\Modules\Recipes\Resoueces;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeInstructionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'order' => $this->order,
            'description' => $this->description,
            'timer' => $this->timer,
            'media' => $this->media ? url($this->media) : null,
        ];
    }
}
