<?php

namespace Leifos\AutoGenerateUsername\I\DB\Settings;

use Leifos\AutoGenerateUsername\I\DB\Settings\Handler as lfAGUDBSettingsInterface;

interface Factory
{
    public function handler(): lfAGUDBSettingsInterface;
}