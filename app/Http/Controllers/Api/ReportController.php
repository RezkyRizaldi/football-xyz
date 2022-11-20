<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GameResource;
use App\Http\Resources\ReportResource;
use App\Models\Game;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __invoke(): ReportResource|JsonResponse
    {
        $games = Game::with(['teamHome', 'teamAway', 'goalScorers.player'])->get();

        try {
            return new ReportResource(['games' => GameResource::collection($games)]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => "error: {$th->getMessage()}",
                'success' => FALSE,
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }
}
