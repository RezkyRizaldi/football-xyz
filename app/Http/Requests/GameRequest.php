<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class GameRequest extends FormRequest
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
            'home_score' => ['required', 'max:255', 'string'],
            'away_score' => ['required', 'max:255', 'string'],
        ];

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $rules['goal_scorers'] = ['required', 'array'];
        }

        if ($this->method() === 'POST') {
            $rules['team_home_id'] = ['required', 'integer'];
            $rules['team_away_id'] = ['required', 'integer'];
            $rules['date'] = ['required', 'date'];
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
