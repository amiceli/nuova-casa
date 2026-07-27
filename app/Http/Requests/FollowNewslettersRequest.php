<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FollowNewslettersRequest extends FormRequest {
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
            'ids' => array('required', 'array', 'min:1'),
            'ids.*' => array('integer', 'exists:available_newsletters,id'),
        );
    }

    /**
     * Error codes, translated by the UI layer.
     *
     * @return array<string, string>
     */
    public function messages(): array {
        return array(
            'ids.required' => 'newsletter_pick_required',
            'ids.min' => 'newsletter_pick_required',
        );
    }
}
