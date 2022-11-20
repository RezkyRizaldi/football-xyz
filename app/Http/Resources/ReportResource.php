<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ReportResource extends JsonResource
{
    public function toArray($request): array|Arrayable|JsonSerializable
    {
        return parent::toArray($request);
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