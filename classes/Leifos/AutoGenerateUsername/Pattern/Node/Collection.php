<?php

namespace Leifos\AutoGenerateUsername\Pattern\Node;

use Leifos\AutoGenerateUsername\I\Pattern\Node\Collection as lfAGUPatternNodeCollectionInterface;
use Leifos\AutoGenerateUsername\I\Pattern\Node\Handler as lfAGUPatternNodeInterface;

class Collection implements lfAGUPatternNodeCollectionInterface
{
    /**
     * @var lfAGUPatternNodeInterface[]
     */
    protected array $elements;
    protected int $index;

    public function __construct()
    {
        $this->elements = [];
        $this->index = 0;
    }

    public function withNode(
        lfAGUPatternNodeInterface $node
    ): lfAGUPatternNodeCollectionInterface {
        $clone = clone $this;
        $clone->elements[] = $node;
        return $clone;
    }

    public function current(): lfAGUPatternNodeInterface
    {
        return $this->elements[$this->index];
    }

    public function key(): int
    {
        return $this->index;
    }

    public function next(): void
    {
        $this->index++;
    }

    public function rewind(): void
    {
        $this->index = 0;
    }

    public function valid(): bool
    {
        return isset($this->elements[$this->index]);
    }

    public function count(): int
    {
        return count($this->elements);
    }
}
