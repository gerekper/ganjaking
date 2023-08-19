<?php

namespace ACA\WC\Search\Order;

use AC;
use ACA\WC\Search;
use ACP;
use ACP\Search\Operators;
use ACP\Search\Value;

class ProductTaxonomy extends ACP\Search\Comparison implements ACP\Search\Comparison\SearchableValues
{

    private $taxonomy;

    public function __construct(string $taxonomy)
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
            ]),
            Value::DECIMAL
        );

        $this->taxonomy = $taxonomy;
    }

    public function get_values($s, $paged)
    {
        $entities = new ACP\Helper\Select\Entities\Taxonomy([
            's'        => $s,
            'page'     => $paged,
            'taxonomy' => [$this->taxonomy],
        ]);

        return new AC\Helper\Select\Options\Paginated(
            $entities,
            new ACP\Helper\Select\Formatter\TermName($entities)
        );
    }

    protected function create_query_bindings($operator, Value $value)
    {
        $bindings = new ACP\Search\Query\Bindings\QueryArguments();

        $ids = $this->get_orders_ids_by_product_cat((int)$value->get_value());
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

    protected function get_orders_ids_by_product_cat($term_id)
    {
        global $wpdb;

        $product_ids = get_posts([
            'post_type'       => 'product',
            'fields'          => 'ids',
            'posts_per_field' => -1,
            'tax_query'       => [
                [
                    'taxonomy' => $this->taxonomy,
                    'terms'    => $term_id,
                ],
            ],
        ]);

        $product_ids = ! empty($product_ids) ? implode(',', $product_ids) : 0;

        $sql = sprintf(
            "SELECT DISTINCT( order_id )
			FROM {$wpdb->prefix}wc_order_product_lookup
			WHERE product_id IN ( %s ) OR variation_id IN( %s )"
            ,
            $product_ids,
            $product_ids
        );

        return $wpdb->get_col($sql);
    }

}