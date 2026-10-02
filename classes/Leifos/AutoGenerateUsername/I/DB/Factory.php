<?php

namespace Leifos\AutoGenerateUsername\I\DB;

use Leifos\AutoGenerateUsername\I\DB\Repository as lfAGUDBRepositoryInterface;
use Leifos\AutoGenerateUsername\I\DB\Settings\Factory as lfAGUDBSettingsFactoryInterface;
use Leifos\AutoGenerateUsername\I\DB\User\Factory as lfAGUDBUserFactoryInterface;

interface Factory
{
    public function settings(): lfAGUDBSettingsFactoryInterface;

    public function user(): lfAGUDBUserFactoryInterface;

    public function repository(): lfAGUDBRepositoryInterface;
}
