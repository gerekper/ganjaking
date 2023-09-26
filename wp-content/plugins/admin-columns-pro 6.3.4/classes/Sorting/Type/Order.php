<?php

declare(strict_types=1);

namespace ACP\Sorting\Type;

use InvalidArgumentException;

class Order
{

    private $order;

    public function __construct(string $order)
    {
        $this->order = $order;

        $this->validate();
    }

    private function validate(): void
    {
        if ( ! in_array($this->order, ['ASC', 'DESC'], true)) {
            throw new InvalidArgumentException('Invalid order');
        }
    }

    public static function create_by_string(string $order): self
    {
        $order = strtoupper($order);

        if ('ASC' !== $order) {
            $order = 'DESC';
        }

        return new self($order);
    }

    public function __toString(): string
    {
        return $this->order;
    }

}