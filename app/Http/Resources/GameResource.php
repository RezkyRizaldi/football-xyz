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
        $is_winner = "-";
        $is_lose = "-";
        if($isFinished){
            if($this->home_score >  $this->away_score){
                $is_winner = "Team Home Winner";
                $is_lose = "Team Away Lose";
            }elseif($this->home_score < $this->away_score){
                $is_winner = "Team Away Winner";
                $is_lose = "Team Home Lose";
            }else{
                $is_winner = "Draw";
                $is_lose = "Draw";
            }
        }

        return [
            'id' => $this->id,
            'team_home' => $teamHome,
            'team_away' => $teamAway,
            'date' => $this->date,
            'status' => $this->when($isFinished, 'finished', 'upcoming'),
            'home_score' => $this->when($isFinished, $this->home_score, "0"),
            'away_score' => $this->when($isFinished, $this->away_score, "0"),
            'is_winner' => $is_winner,
            'is_lose' => $is_lose,
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
