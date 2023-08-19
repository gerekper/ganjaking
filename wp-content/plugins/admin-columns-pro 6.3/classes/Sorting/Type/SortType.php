<?php

namespace ACP\Sorting\Type;

use ACP\Sorting\NativeSortable;
use InvalidArgumentException;

class SortType
{

    private $order_by;

    private $order;

    public function __construct(string $order_by, string $order)
    {
        if ('asc' !== $order) {
            $order = 'desc';
        }

        $this->order_by = $order_by;
        $this->order = $order;

        $this->validate();
    }

    private function validate(): void
    {
        if ( ! is_string($this->order_by)) {
            throw new InvalidArgumentException('Expected a string for order by.');
        }
    }

    public function get_order_by(): string
    {
        return $this->order_by;
    }

    public function get_order(): string
    {
        return $this->order;
    }

    public function equals(SortType $sort_type): bool
    {
        return $sort_type->get_order() === $this->order && $sort_type->get_order_by() === $this->order_by;
    }

    public static function create_by_request(NativeSortable\Request\Sort $request): self
    {
        return new self(
            (string)$request->get_order_by(),
            (string)$request->get_order()
        );
    }

}