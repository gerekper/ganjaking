<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post\RelatedMeta;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class UserField implements QueryBindings
{

    private $field;

    private $meta_key;

    public function __construct(string $field, string $meta_key)
    {
        $this->field = $field;
        $this->meta_key = $meta_key;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $usermeta_alias = $bindings->get_unique_alias('usermeta');
        $postmeta_alias = $bindings->get_unique_alias('postmeta');

        $bindings->join(
            $wpdb->prepare(
                "
                LEFT JOIN $wpdb->postmeta AS $postmeta_alias ON $wpdb->posts.ID = $postmeta_alias.post_id AND $postmeta_alias.meta_key = %s
			    LEFT JOIN $wpdb->users AS $usermeta_alias ON $usermeta_alias.ID = $postmeta_alias.meta_value 
			    ",
                $this->meta_key
            )
        );
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create("$usermeta_alias.$this->field", (string)$order)
        );

        return $bindings;
    }

}