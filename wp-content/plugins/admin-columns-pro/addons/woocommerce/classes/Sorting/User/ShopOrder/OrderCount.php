<?php

namespace ACA\WC\Sorting\User\ShopOrder;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Model\SqlTrait;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\Order;

class OrderCount implements QueryBindings
{

    use SqlTrait;

    private $status;

    public function __construct(array $status = [])
    {
        $this->status = $status;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $alias = $bindings->get_unique_alias('count');

        $where = '';
        if ($this->status) {
            $where = sprintf(
                "\nAND acsort_posts.post_status IN ( %s )",
                $this->esc_sql_array($this->status)
            );
        }

        $sub_query = "
            SELECT acsort_postmeta.meta_value as user_id, COUNT(acsort_postmeta.meta_value) as count
            FROM $wpdb->posts as acsort_posts
            INNER JOIN $wpdb->postmeta AS acsort_postmeta ON acsort_posts.ID = acsort_postmeta.post_id 
			   AND acsort_postmeta.meta_key = '_customer_user'
            WHERE acsort_posts.post_type = 'shop_order'
                $where
            GROUP BY acsort_postmeta.meta_value
        ";

        $bindings->join("LEFT JOIN ( $sub_query ) as $alias ON $wpdb->users.ID = $alias.user_id");
        $bindings->order_by(
            SqlOrderByFactory::create(
                "$alias.count",
                (string)$order,
                [
                    'cast_type' => CastType::SIGNED,
                ]
            )
        );

        return $bindings;
    }

}