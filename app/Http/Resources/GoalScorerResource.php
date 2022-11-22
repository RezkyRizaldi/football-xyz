<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class GoalScorerResource extends JsonResource
{
    public function toArray($request): array|Arrayable|JsonSerializable
    {
        return [
            'id' => $this->id,
            'goal_time' => $this->goal_time,
            'game' => new GameResource($this->whenLoaded('game')),
            'player' => new PlayerResource($this->whenLoaded('player')),
            'created_at' => $this->whenNotNull($this->created_at),
        ];
    }

    public function with($request): array
    {
        return [
            'success' => true,
        ];
    }
}
