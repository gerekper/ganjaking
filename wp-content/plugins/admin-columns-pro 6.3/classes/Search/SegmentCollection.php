<?php

declare(strict_types=1);

namespace ACP\Search;

use ACP\Search\Entity\Segment;
use ACP\Search\Type\SegmentKey;
use Countable;
use Iterator;

final class SegmentCollection implements Iterator, Countable
{

    private $position;

    /**
     * @var Segment[]
     */
    private $data = [];

    public function __construct(array $data = [])
    {
        $this->rewind();

        array_map([$this, 'add'], $data);
    }

    public function add(Segment $item): void
    {
        $this->data[] = $item;
    }

    public function remove(SegmentKey $key): void
    {
        $data = [];

        foreach ($this->data as $segment) {
            if ( ! $segment->get_key()->equals($key)) {
                $data[] = $segment;
            }
        }

        $this->data = $data;

        $this->rewind();
    }

    public function current(): Segment
    {
        return $this->data[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->data[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function count(): int
    {
        return count($this->data);
    }

}