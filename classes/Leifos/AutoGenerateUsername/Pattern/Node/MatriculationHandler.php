<?php

namespace Leifos\AutoGenerateUsername\Pattern\Node;

use ilObjUser;
use Leifos\AutoGenerateUsername\I\Pattern\Node\MatriculationHandler as lfAGUPatternNodeMatriculationInterface;

class MatriculationHandler implements lfAGUPatternNodeMatriculationInterface
{
    protected ilObjUser $user;

    public function toString(): string
    {
        return $this->user->matriculation ?? "";
    }

    public function withUser(
        ilObjUser $user
    ): lfAGUPatternNodeMatriculationInterface {
        $clone = clone $this;
        $clone->user = $user;
        return $clone;
    }

    public function formattContent(): bool
    {
        return true;
    }
}
