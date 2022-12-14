<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlayerRequest;
use App\Http\Resources\PlayerResource;
use App\Models\Player;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller
{
    public function index(): AnonymousResourceCollection|JsonResponse
    {
        try {
            return PlayerResource::collection(Player::with(['team', 'goalScorers'])->paginate(10));
        } catch (\Throwable $th) {
            return response()->json([
                'message' => "error: {$th->getMessage()}",
                'success' => false,
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function store(PlayerRequest $request): PlayerResource|JsonResponse
    {
        DB::beginTransaction();

        try {
            $players = Player::create($request->validated());

            DB::commit();

            return new PlayerResource($players);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => "error: {$th->getMessage()}",
                'success' => false,
            ], JsonResponse::HTTP_FORBIDDEN);
        }
    }

    public function show(Player $player): PlayerResource|JsonResponse
    {
        try {
            return new PlayerResource($player->load(['team', 'goalScorers']));
        } catch (\Throwable $th) {
            return response()->json([
                'message' => "error: {$th->getMessage()}",
                'success' => false,
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function update(PlayerRequest $request, Player $player): PlayerResource|JsonResponse
    {
        DB::beginTransaction();

        try {
            $player->update($request->validated());

            DB::commit();

            return new PlayerResource($player);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => "error: {$th->getMessage()}",
                'success' => false,
            ], JsonResponse::HTTP_FORBIDDEN);
        }
    }

    public function destroy(Player $player): JsonResponse
    {
        try {
            $player->delete();

            return response()->json([
                'message' => 'Data berhasil Dihapus.',
                'status' => true,
            ], JsonResponse::HTTP_NO_CONTENT);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => "error: {$th->getMessage()}",
                'success' => false,
            ], JsonResponse::HTTP_FORBIDDEN);
        }
    }
}
