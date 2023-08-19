<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post\RelatedMeta;

use ACP\Search\Query\Bindings;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class UserMeta extends AbstractModel implements QueryBindings
{

    private $meta_field;

    private $meta_key;

    public function __construct(string $meta_field, string $meta_key)
    {
        parent::__construct();

        $this->meta_key = $meta_key;
        $this->meta_field = $meta_field;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $alias = $bindings->get_unique_alias('usermeta');

        $bindings->join(
            $wpdb->prepare(
                "
                LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id AND $wpdb->postmeta.meta_key = %s
                LEFT JOIN $wpdb->users ON $wpdb->users.ID = $wpdb->postmeta.meta_value
                LEFT JOIN $wpdb->usermeta AS $alias ON $alias.user_id = $wpdb->users.ID AND $alias.meta_key = %s
			",
                $this->meta_key,
                $this->meta_field
            )
        );
        $bindings->order_by(
            SqlOrderByFactory::create("$alias.meta_value", (string)$order)
        );

        return $bindings;
    }

}