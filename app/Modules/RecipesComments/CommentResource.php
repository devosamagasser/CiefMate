<?php

namespace App\Modules\RecipesComments;

use App\Modules\Users\UserResources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'comment' => $this->comment,
            'create_at' => $this->created_at->diffForHumans(),
            'user' => new UserResources($this->user)
        ];
    }
}
