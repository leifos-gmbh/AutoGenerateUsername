<?php

namespace Leifos\AutoGenerateUsername\I\DB\User;

interface Handler
{
    public function withName(string $name): Handler;

    public function getName(): string;

    public function withId(int $id): Handler;

    public function getId(): int;

    public function withLogin(string $login): Handler;

    public function getLogin(): string;
}