<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post\RelatedMeta;

use ACP\Search\Query\Bindings;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class UserField extends AbstractModel implements QueryBindings
{

    private $field;

    private $meta_key;

    public function __construct(string $field, string $meta_key)
    {
        parent::__construct();

        $this->field = $field;
        $this->meta_key = $meta_key;
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
			    LEFT JOIN $wpdb->users AS $alias ON $alias.ID = $wpdb->postmeta.meta_value 
			    ",
                $this->meta_key
            )
        );
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create("$alias.$this->field", (string)$order)
        );

        return $bindings;
    }

}