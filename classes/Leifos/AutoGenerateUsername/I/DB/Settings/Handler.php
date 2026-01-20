<?php

namespace Leifos\AutoGenerateUsername\I\DB\Settings;

use Leifos\AutoGenerateUsername\DB\Settings\Settings;

interface Handler
{
    public function deleteAll(): void;

    public function set(
        Settings $setting,
        string $value
    ): void;

    public function read(
        Settings $setting
    ): string;

    public function readAsInt(
        Settings $setting
    ): int;

    public function readAsBool(
        Settings $setting
    ): bool;

    public function readAsArray(
        Settings $setting,
        string $separator = ';'
    ): array;

    public function getNextId(): int;

    public function isValidContext(
        array $a_context
    ): bool;

    public function getStringActiveAuthModes(): array;
}
