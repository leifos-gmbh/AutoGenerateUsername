<?php

namespace Leifos\AutoGenerateUsername\Pattern;

use ILIAS\User\Profile\Profile as UserProfile;
use Leifos\AutoGenerateUsername\I\DB\Settings\Handler as lfAGUDBSettingsInterface;
use Leifos\AutoGenerateUsername\I\Pattern\Factory as lfAGUPatternFactoryInterface;
use Leifos\AutoGenerateUsername\I\Pattern\Handler as lfAGUPatternInterface;
use Leifos\AutoGenerateUsername\I\Pattern\Node\Factory as lfAGUPatternNodeFactoryInterface;
use Leifos\AutoGenerateUsername\Pattern\Handler as lfAGUPattern;
use Leifos\AutoGenerateUsername\Pattern\Node\Factory as lfAGUPatternNodeFactory;

readonly class Factory implements lfAGUPatternFactoryInterface
{
    public function __construct(
        protected lfAGUDBSettingsInterface $settings,
        protected UserProfile              $profile
    ) {
    }

    public function node(): lfAGUPatternNodeFactoryInterface
    {
        return new lfAGUPatternNodeFactory(
            $this->profile
        );
    }

    public function handler(): lfAGUPatternInterface
    {
        return new lfAGUPattern(
            $this->settings,
            $this->node()
        );
    }
}
