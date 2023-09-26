<?php

declare(strict_types=1);

namespace ACP\Search\Type;

final class SegmentKey
{

    private $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function equals(SegmentKey $key): bool
    {
        return (string)$key === $this->key;
    }

    public function __toString(): string
    {
        return $this->key;
    }

}