<?php

namespace Leifos\AutoGenerateUsername;

use ilLanguage;
use Leifos\AutoGenerateUsername\I\DB\Factory as lfAGUDBFactoryInterface;
use Leifos\AutoGenerateUsername\DB\Factory as lfAGUDBFactory;
use Leifos\AutoGenerateUsername\I\Factory as lfAGUFactoryInterface;
use Leifos\AutoGenerateUsername\I\Pattern\Factory as lfAGUPatternFactoryInterface;
use Leifos\AutoGenerateUsername\Pattern\Factory as lfAGUPatternFactory;
use Leifos\AutoGenerateUsername\I\DB\Settings\Handler as lfAGUDBSettingsInterface;
use ilDBInterface;

class Factory implements lfAGUFactoryInterface
{
    protected ilLanguage $lng;
    protected ilDBInterface $db;

    public function __construct(
        ilLanguage $lng,
        ilDBInterface $db
    ) {
        $this->lng = $lng;
        $this->db = $db;
    }

    public function db(): lfAGUDBFactoryInterface
    {
        return new lfAGUDBFactory($this->lng, $this->db);
    }

    public function pattern(): lfAGUPatternFactoryInterface
    {
        return new lfAGUPatternFactory($this->db()->settings()->handler());
    }
}