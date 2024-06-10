<?php

namespace Leifos\AutoGenerateUsername\I;

use Leifos\AutoGenerateUsername\I\DB\Factory as lfAGUDBFactoryInterface;
use Leifos\AutoGenerateUsername\I\Pattern\Factory as lfAGUPatternFactoryInterface;

interface Factory
{
    public function db(): lfAGUDBFactoryInterface;

    public function pattern(): lfAGUPatternFactoryInterface;
}