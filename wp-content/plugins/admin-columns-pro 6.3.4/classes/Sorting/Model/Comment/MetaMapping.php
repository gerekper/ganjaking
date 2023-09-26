<?php

namespace ACP\Sorting\Model\Comment;

use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\Order;

class MetaMapping extends Meta
{

    private $fields;

    public function __construct(string $meta_key, array $fields)
    {
        parent::__construct($meta_key);

        $this->fields = $fields;
    }

    protected function get_order_by(Order $order): string
    {
        return SqlOrderByFactory::create_with_field(
            "acsort_commentmeta.meta_value",
            $this->fields,
            (string)$order,
            new DataType(DataType::STRING)
        );
    }

}