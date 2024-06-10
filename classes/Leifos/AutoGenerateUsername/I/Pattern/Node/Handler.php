<?php

namespace Leifos\AutoGenerateUsername\I\Pattern\Node;

use Leifos\AutoGenerateUsername\I\DB\Settings\Handler as lfAGUDBSettingsInterface;

interface Handler
{
    public function toString(): string;

    public function formattContent(): bool;
}