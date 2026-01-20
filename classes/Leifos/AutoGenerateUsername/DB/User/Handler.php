<?php

namespace Leifos\AutoGenerateUsername\DB\User;

use Leifos\AutoGenerateUsername\I\DB\User\Handler as lfAGUDBUserInterface;

class Handler implements lfAGUDBUserInterface
{
    protected string $name;
    protected string $login;
    protected int $id;

    public function withName(
        string $name
    ): lfAGUDBUserInterface {
        $clone = clone $this;
        $clone->name = $name;
        return $clone;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function withId(
        int $id
    ): lfAGUDBUserInterface {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function withLogin(
        string $login
    ): lfAGUDBUserInterface {
        $clone = clone $this;
        $clone->login = $login;
        return $clone;
    }

    public function getLogin(): string
    {
        return $this->login;
    }
}
