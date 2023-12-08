<?php

declare(strict_types=1);

namespace ACP\Search;

use ACP\Search\Entity\Segment;
use ACP\Search\Type\SegmentKey;
use Countable;
use InvalidArgumentException;
use Iterator;

final class SegmentCollection implements Iterator, Countable
{

    /**
     * @var Segment[]
     */
    private $data = [];

    public function __construct(array $data = [])
    {
        array_map([$this, 'add'], $data);
    }

    public function add(Segment $item): void
    {
        $this->data[(string)$item->get_key()] = $item;
    }

    public function remove(SegmentKey $key): void
    {
        unset($this->data[(string)$key]);
    }

    public function contains(SegmentKey $segment_key): bool
    {
        return isset($this->data[(string)$segment_key]);
    }

    public function get(SegmentKey $segment_key): Segment
    {
        if ( ! $this->contains($segment_key)) {
            throw new InvalidArgumentException(sprintf('No segment found for key %s.', $segment_key));
        }

        return $this->data[(string)$segment_key];
    }

    public function current(): Segment
    {
        return current($this->data);
    }

    public function next(): void
    {
        next($this->data);
    }

    public function key(): SegmentKey
    {
        return new SegmentKey(key($this->data));
    }

    public function valid(): bool
    {
        return key($this->data) !== null;
    }

    public function rewind(): void
    {
        reset($this->data);
    }

    public function count(): int
    {
        return count($this->data);
    }

}