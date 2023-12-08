<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\User\RelatedMeta;

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
        $this->meta_field = $meta_field;
        $this->meta_key = $meta_key;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias = $bindings->get_unique_alias('umeta');

        $bindings->join(
            $wpdb->prepare(
                "
                LEFT JOIN $wpdb->usermeta AS acsort_usermeta ON acsort_usermeta.user_id = $wpdb->users.ID
                    AND acsort_usermeta.meta_key = %s
                LEFT JOIN $wpdb->users AS acsort_users ON acsort_users.ID = acsort_usermeta.meta_value
                LEFT JOIN $wpdb->usermeta AS $alias ON $alias.user_id = acsort_users.ID
                    AND $alias.meta_key = %s
                ",
                $this->meta_key,
                $this->meta_field
            )
        );
        $bindings->group_by("$wpdb->users.ID");
        $bindings->order_by(
            SqlOrderByFactory::create("$alias.meta_value", (string)$order)
        );

        return $bindings;
    }

}