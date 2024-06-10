<?php

namespace Leifos\AutoGenerateUsername\I\Pattern\Node;

use Leifos\AutoGenerateUsername\I\DB\Settings\Handler as lfAGUDBSettingsInterface;
use Leifos\AutoGenerateUsername\I\Pattern\Node\Handler as lfAGUPatternNodeInterface;

interface NumberHandler extends lfAGUPatternNodeInterface
{
    public function withSettings(lfAGUDBSettingsInterface $settings): NumberHandler;

    public function withLength(int $repeats): NumberHandler;

    public function withAdd(int $add): NumberHandler;

    public function withDemo(bool $enabled): NumberHandler;
}