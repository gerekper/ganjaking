<?php

namespace ACP\Sorting\NativeSortable\Request;

class Sort
{

    public const PARAM_ORDERBY = 'orderby';
    public const PARAM_ORDER = 'order';

    private $order_by;

    private $order;

    public function __construct(string $order_by = null, string $order = null)
    {
        $this->order_by = $order_by;
        $this->order = $order;
    }

    public static function create_from_globals(): self
    {
        return new self(
            isset($_GET[self::PARAM_ORDERBY]) ? (string)$_GET[self::PARAM_ORDERBY] : null,
            isset($_GET[self::PARAM_ORDER]) && is_string($_GET[self::PARAM_ORDER]) ? $_GET[self::PARAM_ORDER] : null
        );
    }

    public function get_order_by(): ?string
    {
        return $this->order_by;
    }

    public function get_order(): ?string
    {
        return $this->order;
    }

}