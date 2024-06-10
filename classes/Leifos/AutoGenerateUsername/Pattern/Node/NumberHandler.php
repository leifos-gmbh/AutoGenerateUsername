<?php

namespace Leifos\AutoGenerateUsername\Pattern\Node;

use ILIAS\UI\Implementation\Component\Table\Column\Number;
use Leifos\AutoGenerateUsername\I\DB\Settings\Handler as lfAGUDBSettingsInterface;
use Leifos\AutoGenerateUsername\I\Pattern\Node\Handler;
use Leifos\AutoGenerateUsername\I\Pattern\Node\NumberHandler as lfAGUPatternNodeNumberInterface;

class NumberHandler implements lfAGUPatternNodeNumberInterface
{
    protected lfAGUDBSettingsInterface $settings;
    protected bool $demo_enabled;
    protected int $add;
    protected int $length;

    public function __construct()
    {
        $this->demo_enabled = false;
        $this->add = 0;
        $this->length = 0;
    }

    public function withSettings(lfAGUDBSettingsInterface $settings): NumberHandler
    {
        $clone = clone $this;
        $clone->settings = $settings;
        return $clone;
    }

    public function withLength(int $repeats): NumberHandler
    {
        $clone = clone $this;
        $clone->length = $repeats;
        return $clone;
    }

    public function withAdd(int $add): NumberHandler
    {
        $clone = clone $this;
        $clone->add = $add;
        return $clone;
    }

    public function withDemo(bool $enabled): NumberHandler
    {
        $clone = clone $this;
        $clone->demo_enabled = $enabled;
        return $clone;
    }

    public function toString(): string
    {
        $number = $this->demo_enabled ? 1 : $this->settings->getNextId() ?? 0;
        $number += $this->add;
        $number_str = "" . $number;
        return str_pad($number_str, $this->length, "0", STR_PAD_LEFT);
    }

    public function formattContent(): bool
    {
        return true;
    }
}