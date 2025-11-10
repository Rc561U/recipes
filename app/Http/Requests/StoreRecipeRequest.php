<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Store Recipe Request - Validation logic (Single Responsibility Principle)
 * Separates validation concerns from controller
 */
class StoreRecipeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'cuisine_type' => 'required|string|max:255',
            'ingredients' => 'required|string',
            'steps' => 'required|string',
            'picture' => 'nullable|image|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Recipe name is required.',
            'cuisine_type.required' => 'Cuisine type is required.',
            'ingredients.required' => 'Ingredients are required.',
            'steps.required' => 'Cooking steps are required.',
            'picture.image' => 'The file must be an image.',
            'picture.max' => 'The image must not be larger than 2MB.',
        ];
    }
}
