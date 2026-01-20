<?php

namespace Leifos\AutoGenerateUsername\Pattern\Node;

use ilObjUser;
use Leifos\AutoGenerateUsername\I\Pattern\Node\LoginHandler as lfAGUPatternNodeLoginInterface;

class LoginHandler implements lfAGUPatternNodeLoginInterface
{
    protected ilObjUser $user;

    public function toString(): string
    {
        return $this->user->login ?? "";
    }

    public function withUser(
        ilObjUser $user
    ): lfAGUPatternNodeLoginInterface {
        $clone = clone $this;
        $clone->user = $user;
        return $clone;
    }

    public function formattContent(): bool
    {
        return true;
    }
}
