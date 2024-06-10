<?php

namespace Leifos\AutoGenerateUsername\I\Pattern;

use Leifos\AutoGenerateUsername\I\Pattern\Handler as lfAGUPatternInterface;
use Leifos\AutoGenerateUsername\I\Pattern\Node\Factory as lfAGUPatternNodeFactoryInterface;

interface Factory
{
    public function node(): lfAGUPatternNodeFactoryInterface;

    public function handler(): lfAGUPatternInterface;

}