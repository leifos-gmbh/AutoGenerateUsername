<?php

namespace Leifos\AutoGenerateUsername;

use ilDBInterface;
use ILIAS\User\Profile\Profile as UserProfile;
use ilLanguage;
use Leifos\AutoGenerateUsername\DB\Factory as lfAGUDBFactory;
use Leifos\AutoGenerateUsername\I\DB\Factory as lfAGUDBFactoryInterface;
use Leifos\AutoGenerateUsername\I\Factory as lfAGUFactoryInterface;
use Leifos\AutoGenerateUsername\I\Pattern\Factory as lfAGUPatternFactoryInterface;
use Leifos\AutoGenerateUsername\Pattern\Factory as lfAGUPatternFactory;

readonly class Factory implements lfAGUFactoryInterface
{
    protected ilLanguage $lng;
    protected ilDBInterface $db;
    protected UserProfile $profile;

    public function __construct() {
        global $DIC;
        $this->lng = $DIC->language();
        $this->db = $DIC->database();
        $this->profile = $DIC['user']->getProfile();
    }

    public function db(): lfAGUDBFactoryInterface
    {
        return new lfAGUDBFactory($this->lng, $this->db);
    }

    public function pattern(): lfAGUPatternFactoryInterface
    {
        return new lfAGUPatternFactory(
            $this->db()->settings()->handler(),
            $this->profile
        );
    }
}
