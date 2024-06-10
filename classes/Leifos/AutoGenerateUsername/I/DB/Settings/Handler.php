<?php

namespace Leifos\AutoGenerateUsername\I\DB\Settings;

interface Handler
{
    public function deleteAll(): void;

    public function setAllowedContexts(array $allowed_contexts): void;

    public function getAllowedContexts(): array;

    public function setIdSequence(int $id_sequence): void;

    public function getIdSequence(): int;

    public function setLoginTemplate(string $login_template): void;

    public function getLoginTemplate(): string;

    public function setStringToLower(bool $string_to_lower): void;

    public function getStringToLower(): bool;

    public function setUseCamelCase(bool $use_camelCase): void;

    public function getUseCamelCase(): bool;

    public function setActiveUpdateExistingUsers(bool $active_update): void;

    public function getActiveUpdateExistingUsers(): bool;

    public function setAuthModeUpdate(string $mode): void;

    public function getAuthModeUpdate(): string;

    public function getNextId(): int;

    public function isValidContext(array $a_context): bool;

    public function getStringActiveAuthModes(): array;
}