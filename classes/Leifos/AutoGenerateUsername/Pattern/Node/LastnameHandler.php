<?php

namespace Leifos\AutoGenerateUsername\Pattern\Node;

use ilObjUser;
use Leifos\AutoGenerateUsername\I\Pattern\Node\LastnameHandler as lfAGUPatternNodeLastnameInterface;

class LastnameHandler implements lfAGUPatternNodeLastnameInterface
{
    protected ilObjUser $user;

    public function toString(): string
    {
        return " " . ($this->user->lastname ?? "");
    }

    public function withUser(ilObjUser $user): lfAGUPatternNodeLastnameInterface
    {
        $clone = clone $this;
        $clone->user = $user;
        return $clone;
    }

    public function formattContent(): bool
    {
        return true;
    }
}