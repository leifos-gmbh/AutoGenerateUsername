<?php

namespace Leifos\AutoGenerateUsername\Pattern\Node;

use ilObjUser;
use Leifos\AutoGenerateUsername\I\Pattern\Node\EmailHandler as lfAGUPatternNodeEmailInterface;

class EmailHandler implements lfAGUPatternNodeEmailInterface
{
    protected ilObjUser $user;

    public function withUser(ilObjUser $user): lfAGUPatternNodeEmailInterface
    {
        $clone = clone $this;
        $clone->user = $user;
        return $clone;
    }

    public function toString(): string
    {
        return " " . ($this->user->getEmail() ?? "");
    }

    public function formattContent(): bool
    {
        return true;
    }
}