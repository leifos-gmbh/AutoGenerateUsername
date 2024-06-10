<?php

namespace Leifos\AutoGenerateUsername\Pattern;

use Leifos\AutoGenerateUsername\I\Pattern\Factory as lfAGUPatternFactoryInterface;
use Leifos\AutoGenerateUsername\I\Pattern\Handler as lfAGUPatternInterface;
use Leifos\AutoGenerateUsername\Pattern\Handler as lfAGUPattern;
use Leifos\AutoGenerateUsername\I\Pattern\Node\Factory as lfAGUPatternNodeFactoryInterface;
use Leifos\AutoGenerateUsername\Pattern\Node\Factory as lfAGUPatternNodeFactory;
use Leifos\AutoGenerateUsername\I\DB\Settings\Handler as lfAGUDBSettingsInterface;

class Factory implements lfAGUPatternFactoryInterface
{
    protected lfAGUDBSettingsInterface $settings;

    public function __construct(lfAGUDBSettingsInterface $settings)
    {
        $this->settings = $settings;
    }

    public function node(): lfAGUPatternNodeFactoryInterface
    {
        return new lfAGUPatternNodeFactory();
    }

    public function handler(): lfAGUPatternInterface
    {
        return new lfAGUPattern(
            $this->settings,
            $this->node()
        );
    }
}