<?php

namespace App\Modules\Equipments;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentsResources extends JsonResource
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
            'warehouse' => $this->warehouse?->title ?? null,
            'workspace' => $this->workspace?->name,
            'workspace_id' => $this->workspace?->id,
            'cover' => config('app.url') . '/images/' . $this->cover
        ];
    }
}
