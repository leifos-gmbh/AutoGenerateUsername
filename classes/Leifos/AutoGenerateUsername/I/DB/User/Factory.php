<?php

namespace Leifos\AutoGenerateUsername\I\DB\User;

use Leifos\AutoGenerateUsername\I\DB\User\Handler as lfAGUDBUserInterface;
interface Factory
{
    public function handler(): lfAGUDBUserInterface;
}