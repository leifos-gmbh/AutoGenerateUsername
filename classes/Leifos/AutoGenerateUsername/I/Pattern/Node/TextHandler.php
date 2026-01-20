<?php

namespace Leifos\AutoGenerateUsername\I\Pattern\Node;

use Leifos\AutoGenerateUsername\I\Pattern\Node\Handler as lfAGUPatternNodeInterface;

interface TextHandler extends lfAGUPatternNodeInterface
{
    public function withText(
        string $text
    ): TextHandler;
}
