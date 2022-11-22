<?php

namespace App\Http\Requests;

use App\Enums\PlayerPosition;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class PlayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'team_id' => ['required', 'integer'],
            'name' => ['required', 'max:255', 'string'],
            'height' => ['required', 'integer'],
            'weight' => ['required', 'integer'],
            'position' => ['required', new Enum(PlayerPosition::class), 'string'],
            'back_number' => ['required', Rule::unique('players')->where(fn ($query) => $query->where(
                'team_id',
                '=',
                $this->team_id,
            )), 'integer'],
        ];

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $rules['back_number'] = ['required', Rule::unique('players')->ignore($this->player->id), 'integer'];
        }

        return $rules;
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation errors',
            'success' => false,
            'data' => $validator->errors(),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
