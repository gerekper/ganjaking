<?php

namespace ACA\WC\Search\Order;

use ACA\WC\Helper\Select;
use ACA\WC\Search;
use ACP;
use ACP\Search\Operators;
use ACP\Search\Value;

class Product extends ACP\Search\Comparison implements ACP\Search\Comparison\SearchableValues
{

    public function __construct()
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
                Operators::LT,
                Operators::GT,
                Operators::BETWEEN,
            ]),
            Value::DECIMAL
        );
    }

    public function get_values($s, $paged)
    {
        return new Select\Paginated\Products((string)$s, (int)$paged, ['product', 'product_variation']);
    }

    protected function create_query_bindings($operator, Value $value)
    {
        $bindings = new ACP\Search\Query\Bindings\QueryArguments();

        $ids = $this->get_orders_for_used_product((int)$value->get_value());
        $ids = empty($ids) ? [0] : $ids;

        $bindings->query_arguments([
            'field_query' => [
                [
                    'field'   => 'id',
                    'value'   => $ids,
                    'compare' => 'IN',
                ],
            ],
        ]);

        return $bindings;
    }

    private function get_orders_for_used_product(int $product_id)
    {
        global $wpdb;

        $sql = sprintf(
            "SELECT DISTINCT( order_id)
			FROM {$wpdb->prefix}wc_order_product_lookup
			WHERE product_id = %d OR variation_id = %d"
            ,
            $product_id,
            $product_id
        );

        return $wpdb->get_col($sql);
    }

}