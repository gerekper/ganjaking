<?php

namespace ACA\WC\Search\User;

use AC;
use ACA\WC;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class Products extends Comparison
    implements Comparison\SearchableValues
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::EQ,
        ]);

        parent::__construct($operators);
    }

    protected function create_query_bindings($operator, Value $value)
    {
        global $wpdb;

        $bindings = new Bindings();

        $sub = $wpdb->prepare(
            "
                SELECT wccl.user_id
                FROM {$wpdb->prefix}wc_order_product_lookup AS wcopl
                JOIN {$wpdb->prefix}wc_customer_lookup AS wccl ON wccl.customer_id = wcopl.customer_id
                WHERE ( wcopl.product_id = %d OR wcopl.variation_id = %d )
        ",
            $value->get_value(),
            $value->get_value()
        );

        return $bindings->where($wpdb->users . '.ID IN( ' . $sub . ')');
    }

    public function get_values($s, $paged)
    {
        $entities = new WC\Helper\Select\Entities\Product([
            's'         => $s,
            'paged'     => $paged,
            'post_type' => ['product', 'product_variation'],
        ]);

        return new AC\Helper\Select\Options\Paginated(
            $entities,
            new WC\Helper\Select\Formatter\ProductTitleAndSKU($entities)
        );
    }

}