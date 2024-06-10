<?php

namespace Leifos\AutoGenerateUsername\Pattern\Node;

use Leifos\AutoGenerateUsername\I\Pattern\Node\HashHandler as lfAGUPatternNodeHashInterface;

class HashHandler implements lfAGUPatternNodeHashInterface
{
    public function toString(): string
    {
        return strrev(uniqid());
    }

    public function formattContent(): bool
    {
        return true;
    }
}