<?php

namespace Leifos\AutoGenerateUsername\I\Pattern\Node;

use ilObjUser;
use Leifos\AutoGenerateUsername\I\Pattern\Node\Handler as lfAGUPatternNodeInterface;

interface EmailHandler extends lfAGUPatternNodeInterface
{
    public function withUser(
        ilObjUser $user
    ): EmailHandler;
}
