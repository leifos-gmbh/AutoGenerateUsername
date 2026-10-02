<?php

namespace Leifos\AutoGenerateUsername\Pattern\Node;

use ilObjUser;
use Leifos\AutoGenerateUsername\I\Pattern\Node\FirstnameHandler as lfAGUPatternNodeFirstNameInterface;

class FirstnameHandler implements lfAGUPatternNodeFirstNameInterface
{
    protected ilObjUser $user;

    public function withUser(
        ilObjUser $user
    ): lfAGUPatternNodeFirstNameInterface {
        $clone = clone $this;
        $clone->user = $user;
        return $clone;
    }

    public function toString(): string
    {
        return str_replace('-', ' ', $this->user->getFirstname()) ?? "";
    }

    public function formattContent(): bool
    {
        return true;
    }
}
