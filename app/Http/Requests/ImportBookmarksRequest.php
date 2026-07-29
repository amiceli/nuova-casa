<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ImportBookmarksRequest extends FormRequest {
    private const DEFAULT_TAG = 'Bookmarks';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Links at the root have no folder, the UI names the tag they land in.
     */
    protected function prepareForValidation(): void {
        $defaultTag = trim((string) $this->input('default_tag'));

        $this->merge(array(
            'default_tag' => $defaultTag !== '' ? $defaultTag : self::DEFAULT_TAG,
        ));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return array(
            'bookmarks' => array(
                'required',
                'file',
                'extensions:html,htm',
                'mimetypes:text/html,text/plain',
                'max:10240',
            ),
            'default_tag' => array('required', 'string', 'max:255'),
        );
    }

    /**
     * Error codes, translated by the UI layer.
     *
     * @return array<string, string>
     */
    public function messages(): array {
        return array(
            'bookmarks.required' => 'import_file_required',
            'bookmarks.file' => 'import_file_invalid',
            'bookmarks.extensions' => 'import_file_invalid',
            'bookmarks.mimetypes' => 'import_file_invalid',
            'bookmarks.max' => 'import_file_too_large',
        );
    }
}
