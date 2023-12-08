<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post\RelatedMeta;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class UserMeta implements QueryBindings
{

    private $meta_field;

    private $meta_key;

    public function __construct(string $meta_field, string $meta_key)
    {
        $this->meta_key = $meta_key;
        $this->meta_field = $meta_field;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $users_alias = $bindings->get_unique_alias('users');
        $usermeta_alias = $bindings->get_unique_alias('usermeta');
        $postmeta_alias = $bindings->get_unique_alias('postmeta');

        $bindings->join(
            $wpdb->prepare(
                "
                LEFT JOIN $wpdb->postmeta AS $postmeta_alias ON $wpdb->posts.ID = $postmeta_alias.post_id AND $postmeta_alias.meta_key = %s
                LEFT JOIN $wpdb->users AS $users_alias ON $users_alias.ID = $postmeta_alias.meta_value
                LEFT JOIN $wpdb->usermeta AS $usermeta_alias ON $usermeta_alias.user_id = $users_alias.ID AND $usermeta_alias.meta_key = %s
			",
                $this->meta_key,
                $this->meta_field
            )
        );
        $bindings->order_by(
            SqlOrderByFactory::create("$usermeta_alias.meta_value", (string)$order)
        );

        return $bindings;
    }

}