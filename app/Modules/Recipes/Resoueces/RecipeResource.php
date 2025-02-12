<?php

namespace App\Modules\Recipes\Resoueces;

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
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'cover' => $this->cover ? url('images/'.$this->cover) : null,
            'preparation_time' => $this->preparation_time,
            'calories' => $this->calories,
            'protein' => $this->protein,
            'fats' => $this->fats,
            'carbs' => $this->carbs,
            'status' => $this->status,
            'category' => $this->category->title,
            'instructions' => RecipeInstructionResource::collection($this->instructions),
            'ingredients' => RecipeIngredientResource::collection($this->ingredients),
            'equipments' => RecipeEquipmentResource::collection($this->equipments),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
