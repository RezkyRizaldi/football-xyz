<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class GameResource extends JsonResource
{
    public function toArray($request): array|Arrayable|JsonSerializable
    {
        $isFinished = $this->date <= now()->format('Y-m-d H:i:s');
        $winner = '-';
        $loser = '-';

        if ($isFinished) {
            if ($this->home_score > $this->away_score) {
                $winner = 'Home Team';
                $loser = 'Away Team';
            } elseif ($this->home_score < $this->away_score) {
                $winner = 'Away Team';
                $loser = 'Home Team';
            } else {
                $winner = 'Draw';
                $loser = 'Draw';
            }
        }

        return [
            'id' => $this->id,
            'team_home' => new TeamResource($this->whenLoaded('teamHome')),
            'team_away' => new TeamResource($this->whenLoaded('teamAway')),
            'date' => $this->date,
            'status' => $this->when($isFinished, 'finished', 'upcoming'),
            'home_score' => $this->when($isFinished, $this->home_score, '0'),
            'away_score' => $this->when($isFinished, $this->away_score, '0'),
            'winner' => $winner,
            'loser' => $loser,
            'goal_scorers' => $this->when($isFinished, GoalScorerResource::collection($this->whenLoaded('goalScorers')), []),
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
