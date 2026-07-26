<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNewsletterRequest extends FormRequest {
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
            'url' => array(
                'required',
                'string',
                Rule::unique('newsletters')->where(function ($query) {
                    return $query->where('user_id', auth()->user()->id);
                }),
            ),
            'title' => array('nullable', 'string'),
            'available_newsletter_id' => array(
                'nullable',
                'integer',
                'exists:available_newsletters,id',
                Rule::unique('newsletters')->where(function ($query) {
                    return $query->where('user_id', auth()->user()->id);
                }),
            ),
        );
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array {
        return array(
            'url.unique' => 'newsletter_already_followed',
            'available_newsletter_id.unique' => 'newsletter_already_followed',
        );
    }
}
