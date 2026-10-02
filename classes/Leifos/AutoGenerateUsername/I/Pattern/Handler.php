<?php

namespace Leifos\AutoGenerateUsername\I\Pattern;

use ilObjUser;

interface Handler
{
    public function withPattern(
        string $pattern
    ): Handler;

    public function buildName(
        ilObjUser $user,
        bool $demo
    ): string;

    public function cleanPattern(): string;

    public function valid(): bool;
}
