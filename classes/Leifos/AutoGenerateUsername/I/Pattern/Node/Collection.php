<?php

namespace Leifos\AutoGenerateUsername\I\Pattern\Node;

use Countable;
use Iterator;
use Leifos\AutoGenerateUsername\I\Pattern\Node\Handler as lfAGUPatternNodeInterface;

interface Collection extends Countable, Iterator
{
    public function withNode(
        lfAGUPatternNodeInterface $node
    ): Collection;

    public function current() : lfAGUPatternNodeInterface;

    public function key() : int;

    public function next() : void;

    public function rewind() : void;

    public function valid() : bool;

    public function count() : int;
}
