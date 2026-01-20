<?php

namespace Leifos\AutoGenerateUsername\DB\Settings;

use Leifos\AutoGenerateUsername\I\DB\Settings\Factory as lfAGUDBSettingsFactoryInterface;
use Leifos\AutoGenerateUsername\I\DB\Settings\Handler as lfAGUDBSettingsInterface;
use Leifos\AutoGenerateUsername\DB\Settings\Handler as lfAGUDBSettings;
use ilLanguage;

class Factory implements lfAGUDBSettingsFactoryInterface
{
    public function __construct(
        protected ilLanguage $lng
    ) {
    }

    public function handler(): lfAGUDBSettingsInterface
    {
        return new lfAGUDBSettings(
            $this->lng
        );
    }
}
