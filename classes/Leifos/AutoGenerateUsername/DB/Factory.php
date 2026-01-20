<?php

namespace Leifos\AutoGenerateUsername\DB;

use Leifos\AutoGenerateUsername\I\DB\Factory as lfAGUDBFactoryInterface;
use Leifos\AutoGenerateUsername\I\DB\Repository as lfAGUDBRepositoryInterface;
use Leifos\AutoGenerateUsername\DB\Repository as lfAGUDBRepository;
use Leifos\AutoGenerateUsername\I\DB\Settings\Factory as lfAGUDBSettingsFactoryInterface;
use Leifos\AutoGenerateUsername\DB\Settings\Factory as lfAGUDBSettingsFactory;
use ilLanguage;
use Leifos\AutoGenerateUsername\I\DB\User\Factory as lfAGUDBUserFactoryInterface;
use Leifos\AutoGenerateUsername\DB\User\Factory as lfAGUDBUserFactory;
use ilDBInterface;

class Factory implements lfAGUDBFactoryInterface
{
    public function __construct(
        protected ilLanguage $lng,
        protected ilDBInterface $db
    ) {
    }

    public function settings(): lfAGUDBSettingsFactoryInterface
    {
        return new lfAGUDBSettingsFactory(
            $this->lng
        );
    }

    public function user(): lfAGUDBUserFactoryInterface
    {
        return new lfAGUDBUserFactory();
    }

    public function repository(): lfAGUDBRepositoryInterface
    {
        return new lfAGUDBRepository($this->db);
    }
}
