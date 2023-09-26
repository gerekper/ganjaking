<?php

declare(strict_types=1);

namespace ACP\Search\Type;

final class SegmentId
{

    private $identity;

    public function __construct(int $identity)
    {
        $this->identity = $identity;
    }

    public function get_id(): int
    {
        return $this->identity;
    }

    public function __toString(): string
    {
        return (string)$this->identity;
    }

}