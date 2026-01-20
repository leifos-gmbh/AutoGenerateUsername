<?php

namespace Leifos\AutoGenerateUsername\Pattern;

use ilObjUser;
use Leifos\AutoGenerateUsername\I\DB\Settings\Handler as lfAGUDBSettingsInterface;
use Leifos\AutoGenerateUsername\I\Pattern\Handler as lfAGUPatternInterface;
use Leifos\AutoGenerateUsername\I\Pattern\Node\Collection as lfAGUPatternNodeCollectionInterface;
use Leifos\AutoGenerateUsername\Pattern\Segments as lfAGUPatternSegments;
use Leifos\AutoGenerateUsername\I\Pattern\Node\Factory as lfAGUPatternNodeFactoryInterface;
use Leifos\AutoGenerateUsername\DB\Settings\Settings;

class Handler implements lfAGUPatternInterface
{
    public const PATTERN_ALLOWED_CHARACTERS = "/[^a-zA-Z0-9_.+@!$%~\[\]\- -]/";
    protected const DEFAULT_NAME = "invalid_login";
    protected string $pattern;

    public function __construct(
        protected lfAGUDBSettingsInterface $settings,
        protected lfAGUPatternNodeFactoryInterface $node_factory
    ) {
    }

    public function withPattern(
        string $pattern
    ): lfAGUPatternInterface {
        $clone = clone $this;
        $clone->pattern = $pattern;
        return $clone;
    }

    public function buildName(
        ilObjUser $user,
        bool $demo = false
    ): string {
        if (!$this->valid()) {
            return self::DEFAULT_NAME;
        }
        $matches = [];
        $segment_pattern = '/([a-zA-Z0-9_.+*@!$%~\- ]+)|(\[[a-z]+((\:|\+|\_)[0-9]+)?\])/';
        if (preg_match_all($segment_pattern, $this->pattern, $matches) !== 1) {
            // Do nothing if matches is zero
        }
        $segments = array_filter($matches[0]);
        $nodes = $this->createSegmentNodes($segments, $user, $demo);
        $formatted_node_content = [];
        foreach ($nodes as $node) {
            $formatted_node_content[] = str_replace(' ', '',
                $node->formattContent()
                    ? $this->applyEnabledTransformation($node->toString())
                    : $node->toString());
        }
        $result = trim(implode("", $formatted_node_content));
        $clean_result = preg_replace(
            self::PATTERN_ALLOWED_CHARACTERS,
            "",
            $result
        ) ?? "";
        return $clean_result;
    }

    public function cleanPattern(): string {
        if (!isset($this->pattern)) {
            return "";
        }
        return $this->applyEnabledTransformation($this->pattern);
    }

    public function valid(): bool
    {
        $clean_pattern = $this->cleanPattern();
        return strlen($clean_pattern) > 3;
    }

    protected function applyEnabledTransformation(
        string $input
    ): string {
        $input =  $this->umlauts($input);
        if (
            $this->settings->readAsBool(Settings::STRING_TO_LOWER) ||
            $this->settings->readAsBool(Settings::CAMEL_CASE)
        ) {
            $input = $this->strToLower($input);
        }
        if ($this->settings->readAsBool(Settings::CAMEL_CASE)) {
            $input = $this->camelCase($input);
        }
        return $input;
    }

    protected function camelCase(
        string $a_string
    ): string {
        return ucwords($a_string);
    }

    protected function strToLower(
        string $a_string
    ): string {
        if (function_exists("mb_strtolower")) {
            return mb_strtolower($a_string, "UTF-8");
        } else {
            return strtolower($a_string);
        }
    }

    protected function umlauts(
        string $a_string
    ): string {
        return iconv("utf-8", "ASCII//TRANSLIT", $a_string);
    }

    protected function createSegmentNodes(
        array $segments,
        ilObjUser $user,
        bool $demo
    ): lfAGUPatternNodeCollectionInterface {
        $nodes = $this->node_factory->collection();
        foreach ($segments as $segment) {
            $handler = null;
            $matches = [];
            if (preg_match("/" . lfAGUPatternSegments::EMAIL . "/", $segment, $matches) === 1) {
                $handler = $this->node_factory->email()->withUser($user);
            }
            if (preg_match("/" . lfAGUPatternSegments::FIRSTNAME . "/", $segment, $matches) === 1) {
                $handler = $this->node_factory->firstname()->withUser($user);
            }
            if (preg_match("/" . lfAGUPatternSegments::HASH . "/", $segment, $matches) === 1) {
                $handler = $this->node_factory->hash();
            }
            if (preg_match("/" . lfAGUPatternSegments::LASTNAME . "/", $segment, $matches) === 1) {
                $handler = $this->node_factory->lastname()->withUser($user);
            }
            if (preg_match("/" . lfAGUPatternSegments::LOGIN . "/", $segment, $matches) === 1) {
                $handler = $this->node_factory->login()->withUser($user);
            }
            if (preg_match("/" . lfAGUPatternSegments::MATRICULATION . "/", $segment, $matches) === 1) {
                $handler = $this->node_factory->matriculation()->withUser($user);
            }
            if (preg_match("/" . lfAGUPatternSegments::NUMBER . "/", $segment, $matches) === 1) {
                $handler = $this->node_factory->number()
                    ->withDemo($demo)
                    ->withSettings($this->settings)
                    ->withAdd((int) ($matches[5] ?? 0))
                    ->withLength((int) ($matches[3] ?? 0));
            }
            if (preg_match("/" . lfAGUPatternSegments::UDF . "/", $segment, $matches) === 1) {
                $handler = $this->node_factory->udf()
                    ->withFieldId((int) $matches[1])
                    ->withUser($user);
            }
            if (is_null($handler)) {
                $handler = $this->node_factory->text()->withText($segment);
            }
            $nodes = $nodes->withNode($handler);
        }
        return $nodes;
    }
}
