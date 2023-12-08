<?php

namespace ACA\WC\Sorting\User\ShopOrder;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Model\SqlTrait;
use ACP\Sorting\Type\Order;

class TotalSales implements QueryBindings
{

    use SqlTrait;

    private $status;

    public function __construct(array $status = ['wc-completed'])
    {
        $this->status = $status;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $status_where = ! empty($this->status)
            ? sprintf("\nAND acsort_posts.post_status IN ( %s )", $this->esc_sql_array($this->status))
            : '';

        $sub_query = "
            SELECT acsort_pm1.meta_value as user_id, SUM( acsort_pm2.meta_value ) as total_sales
            FROM $wpdb->postmeta as acsort_pm1
            INNER JOIN $wpdb->postmeta as acsort_pm2 
                ON acsort_pm1.post_id = acsort_pm2.post_id 
                AND acsort_pm2.meta_key = '_order_total'
            INNER JOIN $wpdb->posts as acsort_posts 
                ON acsort_pm1.post_id = acsort_posts.ID
                WHERE acsort_pm1.meta_key = '_customer_user' 
            AND acsort_posts.post_type = 'shop_order'
            $status_where
            GROUP BY user_id
        ";

        $bindings->join(
            "LEFT JOIN ( $sub_query ) as acsort_user2 on $wpdb->users.ID = acsort_user2.user_id "
        );
        $bindings->order_by(
            SqlOrderByFactory::create('total_sales', (string)$order)
        );

        return $bindings;
    }

}