<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\User;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Model\SqlTrait;
use ACP\Sorting\Type\ComputationType;
use ACP\Sorting\Type\Order;

class MaxPostDate implements QueryBindings
{

    use SqlTrait;

    private $post_type;

    private $post_stati;

    private $oldest_post;

    public function __construct(string $post_type, array $post_stati = [], bool $oldest_post = false)
    {
        $this->post_type = $post_type;
        $this->post_stati = $post_stati;
        $this->oldest_post = $oldest_post;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $alias = $bindings->get_unique_alias('max');

        $join = $wpdb->prepare(
            " 
            LEFT JOIN $wpdb->posts AS $alias ON $wpdb->users.ID = $alias.post_author
                AND $alias.post_type = %s
            ",
            $this->post_type
        );

        if ($this->post_stati) {
            $join .= "AND $alias.post_status IN (" . $this->esc_sql_array($this->post_stati) . ")";
        }

        $bindings->join($join);
        $bindings->group_by("$wpdb->users.ID");
        $bindings->order_by(
            SqlOrderByFactory::create_with_computation(
                new ComputationType($this->oldest_post ? ComputationType::MIN : ComputationType::MAX),
                "$alias.post_date",
                (string)$order
            )
        );

        return $bindings;
    }

}