<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GameRequest;
use App\Http\Resources\GameResource;
use App\Models\Game;
use App\Models\GoalScorer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    public function index(): AnonymousResourceCollection|JsonResponse
    {
        try {
            return GameResource::collection(
                Game::with(['teamHome', 'teamAway', 'goalScorers.player.team'])
                    ->paginate(10)
            );
        } catch (\Throwable $th) {
            return response()->json([
                'message' => "error: {$th->getMessage()}",
                'success' => FALSE,
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function store(GameRequest $request): GameResource|JsonResponse
    {
        DB::beginTransaction();

        try {
            $games = Game::create($request->validated());

            DB::commit();

            return new GameResource($games);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => "error: {$th->getMessage()}",
                'success' => FALSE,
            ], JsonResponse::HTTP_FORBIDDEN);
        }
    }

    public function show(Game $game): GameResource|JsonResponse
    {
        try {
            return new GameResource($game);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => "error: {$th->getMessage()}",
                'success' => FALSE,
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function update(GameRequest $request, Game $game): GameResource|JsonResponse
    {
        DB::beginTransaction();

        try {
            $game->update($request->safe()->only(['home_score', 'away_score']));

            if (!empty($game->id) && !empty($request->safe()->only(['goal_scorers']))) {
                $data = [];
                foreach ($request->safe()->only(['goal_scorers']) as $key) {
                    $obj = array();
                    $obj['game_id'] = $game->id;
                    foreach ($key as $k) {
                        $obj['player_id'] = $k['player_id'];
                        $obj['goal_time'] = $k['goal_time'];

                        array_push($data, $obj);
                    }
                }

                GoalScorer::insert($data);

                DB::commit();

                return new GameResource($game->load(['goalScorers']));
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => "error: {$th->getMessage()}",
                'success' => FALSE,
            ], JsonResponse::HTTP_FORBIDDEN);
        }
    }

    public function destroy(Game $game): JsonResponse
    {
        try {
            if (!empty($game)) {
                $id = explode(',', $game->id);

                GoalScorer::whereIn('game_id', $id)->delete();

                $game->delete();
            }

            return response()->json(["status" => TRUE, "message" => "Berhasil Di Hapus"], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => "error: {$th->getMessage()}",
                'success' => FALSE,
            ], JsonResponse::HTTP_FORBIDDEN);
        }
    }
}
