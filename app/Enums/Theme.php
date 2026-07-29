<?php

namespace App\Enums;

enum Theme: string {
    case Light = 'light';

    case Dark = 'dark';

    case System = 'system';

    /**
     * @return array<int, string>
     */
    public static function values(): array {
        return array_map(
            function ($case) {
                return $case->value;
            },
            self::cases()
        );
    }
}
