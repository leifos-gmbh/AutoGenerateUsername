<?php

namespace Leifos\AutoGenerateUsername\DB\User;

use Leifos\AutoGenerateUsername\I\DB\User\Handler as lfAGUDBUserInterface;
use Leifos\AutoGenerateUsername\DB\User\Handler as lfAGUDBUser;
use Leifos\AutoGenerateUsername\I\DB\User\Factory as lfAGUDBUserFactoryInterface;

class Factory implements lfAGUDBUserFactoryInterface
{
    public function handler(): lfAGUDBUserInterface
    {
        return new lfAGUDBUser();
    }
}