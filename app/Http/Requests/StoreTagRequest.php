<?php

namespace App\Http\Requests;

use App\Rules\UniqueTagName;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    protected function prepareForValidation(): void {
        $this->merge(array(
            'name' => trim((string) $this->input('name')),
        ));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return array(
            'icon' => 'required|string',
            'name' => array(
                'required',
                'string',
                new UniqueTagName(),
            ),
        );
    }

    /**
     * Error codes, translated by the UI layer.
     *
     * @return array<string, string>
     */
    public function messages(): array {
        return array(
            'name.required' => 'tag_name_required',
            'name.string' => 'tag_name_invalid',
            'icon.required' => 'tag_icon_required',
            'icon.string' => 'tag_icon_invalid',
        );
    }
}
