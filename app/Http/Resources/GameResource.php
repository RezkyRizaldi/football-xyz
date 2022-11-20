<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class GameResource extends JsonResource
{
    public function toArray($request): array|Arrayable|JsonSerializable
    {
        $teamHome = new TeamResource($this->whenLoaded('teamHome'));
        $teamAway = new TeamResource($this->whenLoaded('teamAway'));
        $isFinished = $this->date <= now()->format('Y-m-d H:i:s');

        return [
            'id' => $this->id,
            'team_home' => $teamHome,
            'team_away' => $teamAway,
            'date' => $this->date,
            'status' => $this->when($isFinished, 'finished', 'upcoming'),
            'home_score' => $this->when($isFinished, $this->home_score, "0"),
            'away_score' => $this->when($isFinished, $this->away_score, "0"),
            'winner' => $this->when($this->home_score !== $this->away_score, $this->when((int) $this->home_score > (int) $this->away_score, $teamHome, $teamAway), null),
            'goal_scorers' => $this->when($isFinished, GoalScorerResource::collection($this->whenLoaded('goalScorers')), []),
            'created_at' => $this->whenNotNull($this->created_at),
        ];
    }

    public function with($request): array
    {
        return [
            'success' => TRUE,
        ];
    }
}
