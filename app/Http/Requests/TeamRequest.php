<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class TeamRequest extends FormRequest
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
        if (request()->isMethod('PUT')) {
            return [
                'name' => ['required', 'max:255', 'string'],
                'logo' => ['nullable', 'image', 'max:2048'],
                'since' => ['required', 'digits:4', 'numeric'],
                'address' => ['required', 'max:255', 'string'],
                'city' => ['required', 'max:255', 'string'],
            ];
        }else{
            return [
                'name' => ['required', 'max:255', 'string'],
                'logo' => ['nullable', 'image', 'max:2048'],
                'since' => ['required', 'digits:4', 'numeric'],
                'address' => ['required', 'max:255', 'string'],
                'city' => ['required', 'max:255', 'string'],
            ];

        }
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation errors',
            'success' => FALSE,
            'data' => $validator->errors(),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
