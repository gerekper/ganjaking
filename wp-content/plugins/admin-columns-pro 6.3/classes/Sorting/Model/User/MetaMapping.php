<?php

namespace ACP\Sorting\Model\User;

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

    protected function get_order_by(Order $order): string
    {
        return SqlOrderByFactory::create_with_field(
            "acsort_usermeta.meta_value",
            $this->fields,
            (string)$order,
            $this->data_type
        );
    }

}