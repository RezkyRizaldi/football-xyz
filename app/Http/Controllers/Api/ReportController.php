<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __invoke(): JsonResponse
    {
        try {
            $games = Game::get();
            $players = Player::selectRaw("players.team_id, players.name, teams.name as team_name, COUNT('goal_scorers.player_id') as total_goals")
                ->join('teams', 'players.team_id', '=', 'teams.id')
                ->join('goal_scorers', 'players.id', '=', 'goal_scorers.player_id')
                ->orderByDesc('total_goals')
                ->groupBy('goal_scorers.player_id')
                ->get();

            if (!empty($games) && !empty($players)) {
                foreach ($games as $key => $value) {
                    foreach ($players as $player) {
                        if ($value->date <= now()->format('Y-m-d H:i:s')) {
                            $games[$key]->status = 'Finished';
                            $games[$key]->top_scorer = $players[0];

                            if ($value->home_score >  $value->away_score) {
                                if ($value->team_home_id == $player->team_id) {
                                    $games[$key]->winner = $player->team_name;
                                } else {
                                    $games[$key]->loser = $player;
                                }
                            } elseif ($value->home_score < $value->away_score) {
                                if ($value->team_away_id == $player->team_id) {
                                    $games[$key]->winner = $player->team_name;
                                } else {
                                    $games[$key]->loser = $player->team_name;
                                }
                            } else {
                                $games[$key]->winner = 'Draw';
                                $games[$key]->loser = 'Draw';
                            }
                        } else {
                            $games[$key]->status = 'Upcoming';
                        }
                    }
                }

                return response()->json([
                    'data'    => $games,
                    'success'  => true,
                ], JsonResponse::HTTP_OK);
            } else {
                return response()->json([
                    'message' => 'Data gagal diambil.',
                    'success'  => false,
                ], JsonResponse::HTTP_FORBIDDEN);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => "error: {$th->getMessage()}",
                'success'  => false,
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }
}
