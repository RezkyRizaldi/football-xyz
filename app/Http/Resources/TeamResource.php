<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class TeamResource extends JsonResource
{
    public function toArray($request): array|Arrayable|JsonSerializable
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'logo' => $this->when(!empty($this->logo), url("storage/team/{$this->logo}")),
            'since' => $this->since,
            'address' => $this->address,
            'city' => $this->city,
            'players' => PlayerResource::collection($this->whenLoaded('players')),
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
