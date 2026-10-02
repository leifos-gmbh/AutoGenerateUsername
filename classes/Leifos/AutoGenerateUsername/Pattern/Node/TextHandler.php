<?php

namespace Leifos\AutoGenerateUsername\Pattern\Node;

use Leifos\AutoGenerateUsername\I\Pattern\Node\TextHandler as lfAGUPatternNodeTextInterface;

class TextHandler implements lfAGUPatternNodeTextInterface
{
    protected string $text;

    public function toString(): string
    {
        return $this->text ?? "";
    }

    public function withText(
        string $text
    ): TextHandler {
        $clone = clone $this;
        $clone->text = $text;
        return $clone;
    }

    public function formattContent(): bool
    {
        return false;
    }
}
