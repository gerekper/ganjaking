<?php

namespace ACA\WC\Search\Order;

use AC\Helper\Select\Options\Paginated;
use ACA\WC\Search;
use ACP;
use ACP\Helper\Select\Taxonomy\LabelFormatter\TermName;
use ACP\Helper\Select\Taxonomy\PaginatedFactory;
use ACP\Query\Bindings;
use ACP\Search\Operators;
use ACP\Search\Value;
use WP_Term;

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

    public function format_label($value): string
    {
        $term = get_term($value);

        return $term instanceof WP_Term
            ? (new TermName())->format_label($term)
            : '';
    }

    public function get_values(string $search, int $page): Paginated
    {
        return (new PaginatedFactory())->create([
            'search'   => $search,
            'page'     => $page,
            'taxonomy' => $this->taxonomy,
        ]);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;
        $bindings = new Bindings\QueryArguments();

        $ids = $this->get_orders_ids_by_product_cat((int)$value->get_value());
        $ids = empty($ids) ? [0] : array_map('absint', $ids);

        $bindings->where(sprintf('%s IN(%s)', $wpdb->prefix . 'wc_orders.id', implode(',', $ids)));

        return $bindings;
    }

    protected function get_orders_ids_by_product_cat(int $term_id): array
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

        $product_ids = ! empty($product_ids)
            ? implode(',', $product_ids)
            : 0;

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