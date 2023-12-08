<?php

namespace ACP\Sorting\Model\Taxonomy;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\ComputationType;
use ACP\Sorting\Type\Order;

class MetaCount implements QueryBindings
{

    protected $meta_key;

    public function __construct(string $meta_key)
    {
        $this->meta_key = $meta_key;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias = $bindings->get_unique_alias('metacount');

        $orderby = SqlOrderByFactory::create_with_computation(
            new ComputationType(ComputationType::COUNT),
            "$alias.meta_key",
            $order,
            true
        );

        $join = $wpdb->prepare(
            "
			    LEFT JOIN $wpdb->termmeta AS $alias ON t.term_id = $alias.term_id
				AND $alias.meta_key = %s
		    ",
            $this->meta_key
        );

        return $bindings
            ->join($join)
            ->group_by("t.term_id")
            ->order_by($orderby);
    }

}