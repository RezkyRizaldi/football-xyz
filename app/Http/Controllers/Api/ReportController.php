<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function report()
    {
        
        try {
            $games = Game::get();
            $players = Player::selectRaw("players.team_id, players.name, teams.name as team_name, COUNT('goal_scorers.player_id') as total_goals")
            ->join("teams", "players.team_id", "=", "teams.id")
            ->join("goal_scorers", "players.id", "=", "goal_scorers.player_id")
            ->orderByDesc("total_goals")
            ->groupBy("goal_scorers.player_id")
            ->get();
            if(!empty($games) && !empty($players)){
                foreach($games as $key => $value){
                    foreach($players as $player){
                        if($value->date <= now()->format('Y-m-d H:i:s')){
                            $games[$key]->status = "Finished";
                            $games[$key]->top_score = $players[0];
                            if($value->home_score >  $value->away_score){
                                if($value->team_home_id == $player->team_id){
                                    $games[$key]->is_winner = $player->team_name;
                                }else{
                                    $games[$key]->is_lose = $player;
                                }
                            }elseif($value->home_score < $value->away_score){
                                if($value->team_away_id == $player->team_id){
                                    $games[$key]->is_winner = $player->team_name;
                                }else{
                                    $games[$key]->is_lose = $player->team_name;
                                }
                            }else{
                            $games[$key]->is_winner = "Draw";
                            $games[$key]->is_lose = "Draw";
                            }
                        }else{
                            $games[$key]->status = "upcoming";

                        }

                    }
                }
                return response()->json([
                    'data'    => $games,
                    'message' => "Data Berhasil",
                    'success'  => TRUE,
                ], JsonResponse::HTTP_OK);
            }else{
                return response()->json([
                    'data'    => [],
                    'message' => "Data Gagal Di ambil",
                    'success'  => FALSE,
                ], JsonResponse::HTTP_OK);
            }   
        } catch (\Throwable $th) {
            return response()->json([
                'message' => "error: {$th->getMessage()}",
                'success'  => FALSE,
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }
}
