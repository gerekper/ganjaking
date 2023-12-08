<?php

namespace ACA\MetaBox\Sorting\Model\Taxonomy;

use ACA\MetaBox\Sorting\TableOrderByFactory;
use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\Order;

class Table implements QueryBindings
{

    protected $table_name;

    protected $meta_key;

    protected $data_type;

    public function __construct(string $table_name, string $meta_key, DataType $data_type = null)
    {
        $this->table_name = $table_name;
        $this->meta_key = $meta_key;
        $this->data_type = $data_type;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        $bindings = new Bindings();

        $bindings->join(
            sprintf(
                "LEFT JOIN %s AS acsort_ct ON acsort_ct.ID = t.term_id",
                esc_sql($this->table_name)
            )
        );
        $bindings->order_by(
            TableOrderByFactory::create($this->meta_key, $order, $this->data_type)
        );

        return $bindings;
    }

}