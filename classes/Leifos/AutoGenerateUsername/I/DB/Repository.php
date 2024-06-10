<?php

namespace Leifos\AutoGenerateUsername\I\DB;

use Leifos\AutoGenerateUsername\I\DB\User\Handler as lfAGUDBUserInterface;

interface Repository
{
    public function generateLogin(lfAGUDBUserInterface $db_user): lfAGUDBUserInterface;

    public function updateLogin(lfAGUDBUserInterface $db_user): void;
}