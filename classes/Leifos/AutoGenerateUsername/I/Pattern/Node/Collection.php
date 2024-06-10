<?php

namespace Leifos\AutoGenerateUsername\I\Pattern\Node;

use Leifos\AutoGenerateUsername\I\Pattern\Node\Handler as lfAGUPatternNodeInterface;
use Countable;
use Iterator;

interface Collection extends Countable, Iterator
{
    public function withNode(lfAGUPatternNodeInterface $node): Collection;

    public function current() : lfAGUPatternNodeInterface;

    public function key() : int;

    public function next() : void;

    public function rewind() : void;

    public function valid() : bool;

    public function count() : int;
}