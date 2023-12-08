<?php

namespace ACP\Sorting\Model\User;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Model\SqlTrait;
use ACP\Sorting\Type\ComputationType;
use ACP\Sorting\Type\Order;

class PostCount implements QueryBindings
{

    use SqlTrait;

    private $post_types;

    private $post_status;

    public function __construct(array $post_types = null, array $post_status = null)
    {
        $this->post_types = $post_types;
        $this->post_status = $post_status;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $alias = $bindings->get_unique_alias('pcount');

        $join = "LEFT JOIN $wpdb->posts AS $alias ON $alias.post_author = $wpdb->users.ID";

        if ($this->post_status) {
            $join .= sprintf(
                "\nAND $alias.post_status IN ( %s )",
                $this->esc_sql_array($this->post_status)
            );
        }

        if ($this->post_types) {
            $join .= sprintf(
                "\nAND $alias.post_type IN ( %s )",
                $this->esc_sql_array($this->post_types)
            );
        }

        $bindings->join($join);
        $bindings->group_by("$wpdb->users.ID");
        $bindings->order_by(
            SqlOrderByFactory::create_with_computation(
                new ComputationType(ComputationType::COUNT),
                "$alias.ID",
                (string)$order,
                true
            )
        );

        return $bindings;
    }

}