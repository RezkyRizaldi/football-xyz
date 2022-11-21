<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamRequest;
use App\Http\Resources\TeamResource;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    public function index(): AnonymousResourceCollection|JsonResponse
    {
        try {
            return TeamResource::collection(Team::with(['players'])->paginate(10));
        } catch (\Throwable $th) {
            return response()->json([
                'message' => "error: {$th->getMessage()}",
                'success' => FALSE,
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function store(TeamRequest $request): TeamResource|JsonResponse
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $path = $file->storeAs('public/team', $file->hashName());

                $validated['logo'] = basename($path);
            }
            $teams = Team::create($validated);

            DB::commit();

            return new TeamResource($teams);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => "error: {$th->getMessage()}",
                'success' => FALSE,
            ], JsonResponse::HTTP_FORBIDDEN);
        }
    }

    public function show(Team $team): TeamResource|JsonResponse
    {
        try {
            return new TeamResource(Team::with(['players'])->find($team->id));
        } catch (\Throwable $th) {
            return response()->json([
                'message' => "error: {$th->getMessage()}",
                'success' => FALSE,
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function update(TeamRequest $request, Team $team): TeamResource|JsonResponse
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            if ($request->hasFile('logo')) {
                if (Storage::disk('public')->exists('team') && !empty($team->logo)) {
                    Storage::disk('public')->delete("team/{$team->logo}");
                }

                $file = $request->file('logo');
                $path = $file->storeAs('public/team', $file->hashName());

                $validated['logo'] = basename($path);
            }

            $team->update($validated);

            DB::commit();

            return new TeamResource($team);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => "error: {$th->getMessage()}",
                'success' => FALSE,
            ], JsonResponse::HTTP_FORBIDDEN);
        }
    }

    public function destroy(Team $team): JsonResponse
    {
        try {
            if (!empty($team)) {
                $id = explode(',', $team->id);

                Player::whereIn('team_id', $id)->delete();

                $team->delete();
                return response()->json(["status" => TRUE, "message" => "Berhasil Di Hapus"], JsonResponse::HTTP_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => "error: {$th->getMessage()}",
                'success' => FALSE,
            ], JsonResponse::HTTP_FORBIDDEN);
        }
    }
}
