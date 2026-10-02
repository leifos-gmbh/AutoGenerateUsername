<?php

namespace Leifos\AutoGenerateUsername\I\Pattern\Node;

interface Handler
{
    public function toString(): string;

    public function formattContent(): bool;
}
