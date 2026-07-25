<?php

namespace App\Rules;

use App\Models\Tag;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueTagName implements ValidationRule {
    /**
     * A tag name must be unique for a given user, whatever its case.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (Tag::nameAlreadyUsed((string) $value)) {
            $fail('tag_name_already_used');
        }
    }
}
