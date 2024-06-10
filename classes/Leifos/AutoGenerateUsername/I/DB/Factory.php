<?php

namespace Leifos\AutoGenerateUsername\I\DB;

use Leifos\AutoGenerateUsername\I\DB\Settings\Factory as lfAGUDBSettingsFactoryInterface;
use Leifos\AutoGenerateUsername\I\DB\User\Factory as lfAGUDBUserFactoryInterface;
use Leifos\AutoGenerateUsername\I\DB\Repository as lfAGUDBRepositoryInterface;

interface Factory
{
    public function settings(): lfAGUDBSettingsFactoryInterface;

    public function user(): lfAGUDBUserFactoryInterface;

    public function repository(): lfAGUDBRepositoryInterface;
}