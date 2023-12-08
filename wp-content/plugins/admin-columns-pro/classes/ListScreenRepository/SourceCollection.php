<?php

declare(strict_types=1);

namespace ACP\ListScreenRepository;

use AC\Type\ListScreenId;
use Countable;
use InvalidArgumentException;
use Iterator;

final class SourceCollection implements Iterator, Countable
{

    /**
     * @var string[]
     */
    private $data = [];

    public function __construct(array $data = [])
    {
        array_map([$this, 'add'], $data);
    }

    public function add(ListScreenId $id, string $source): void
    {
        $this->data[(string)$id] = $source;
    }

    public function remove(ListScreenId $id): void
    {
        unset($this->data[(string)$id]);
    }

    public function contains(ListScreenId $id): bool
    {
        return isset($this->data[(string)$id]);
    }

    public function get(ListScreenId $id): string
    {
        if ( ! $this->contains($id)) {
            throw new InvalidArgumentException(sprintf('No source found for id %s.', $id));
        }

        return $this->data[(string)$id];
    }

    public function current(): string
    {
        return current($this->data);
    }

    public function next(): void
    {
        next($this->data);
    }

    public function key(): ListScreenId
    {
        return new ListScreenId(key($this->data));
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