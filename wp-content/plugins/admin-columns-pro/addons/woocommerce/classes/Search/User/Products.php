<?php

namespace ACA\WC\Search\User;

use AC\Helper\Select\Options\Paginated;
use ACA\WC\Helper\Select\Product\GroupFormatter\ProductType;
use ACA\WC\Helper\Select\Product\LabelFormatter\ProductTitle;
use ACA\WC\Helper\Select\Product\PaginatedFactory;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Operators;
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

    protected function create_query_bindings(string $operator, Value $value): Bindings
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

    public function get_values(string $search, int $page): Paginated
    {
        return (new PaginatedFactory())->create([
            's'         => $search,
            'paged'     => $page,
            'post_type' => ['product', 'product_variation'],
        ], null, new ProductType());
    }

    public function format_label($value): string
    {
        $product = wc_get_product($value);

        return $product
            ? (new ProductTitle())->format_label($product)
            : '';
    }

}