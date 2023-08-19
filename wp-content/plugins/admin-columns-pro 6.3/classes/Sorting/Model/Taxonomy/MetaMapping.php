<?php

namespace ACP\Sorting\Model\Taxonomy;

use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class MetaMapping extends Meta
{

    protected $fields;

    public function __construct(string $meta_key, array $fields)
    {
        parent::__construct($meta_key);

        $this->fields = $fields;
    }

    protected function get_order_by(string $alias, Order $order): string
    {
        return SqlOrderByFactory::create_with_field(
            "$alias.meta_value",
            $this->fields,
            (string)$order,
            $this->data_type
        );
    }

}