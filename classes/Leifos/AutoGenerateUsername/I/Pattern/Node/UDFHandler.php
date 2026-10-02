<?php

namespace Leifos\AutoGenerateUsername\I\Pattern\Node;

use ilObjUser;
use Leifos\AutoGenerateUsername\I\Pattern\Node\Handler as lfAGUPatternNodeInterface;

interface UDFHandler extends lfAGUPatternNodeInterface
{
    public function withUser(
        ilObjUser $user
    ): UDFHandler;

    public function withFieldId(
        int $field_id
    ): UDFHandler;
}
