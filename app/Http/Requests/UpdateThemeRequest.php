<?php

namespace App\Http\Requests;

use App\Enums\Theme;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateThemeRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return array(
            'theme' => array('required', Rule::enum(Theme::class)),
        );
    }

    /**
     * Error codes, translated by the UI layer.
     *
     * @return array<string, string>
     */
    public function messages(): array {
        return array(
            'theme.required' => 'theme_required',
            'theme.enum' => 'theme_invalid',
        );
    }
}
