<?php

namespace ACA\WC\Search\ShopOrder;

use ACP;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class HasFees extends Comparison
{

    public function __construct()
    {
        $operators = new Operators(
            [
                Operators::IS_EMPTY,
                Operators::NOT_IS_EMPTY,
            ]
        );

        parent::__construct(
            $operators,
            null,
            new ACP\Search\Labels([
                Operators::IS_EMPTY     => sprintf(
                    __('Without %s', 'codepress-admin-columns'),
                    __('Fees', 'codepress-admin-columns')
                ),
                Operators::NOT_IS_EMPTY => sprintf(
                    __('Has %s', 'codepress-admin-columns'),
                    __('Fees', 'codepress-admin-columns')
                ),
            ])
        );
    }
    
    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $in_type = $operator === Operators::IS_EMPTY ? 'NOT IN' : 'IN';

        $sql = "
			SELECT DISTINCT order_id
			FROM {$wpdb->prefix}woocommerce_order_items
			WHERE order_item_type = 'fee'
			";

        $bindings->where(sprintf($wpdb->posts . '.ID ' . $in_type . '( %s )', $sql));

        return $bindings;
    }

}