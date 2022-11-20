<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class PlayerResource extends JsonResource
{
    public function toArray($request): array|Arrayable|JsonSerializable
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'height' => $this->height,
            'weight' => $this->weight,
            'position' => $this->position,
            'back_number' => $this->back_number,
            'team' => new TeamResource($this->whenLoaded('team')),
            'goals' => GoalScorerResource::collection($this->whenLoaded('goalScorers')),
            'created_at' => $this->whenNotNull($this->created_at),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function with($request): array
    {
        return [
            'success' => TRUE,
        ];
    }
}
