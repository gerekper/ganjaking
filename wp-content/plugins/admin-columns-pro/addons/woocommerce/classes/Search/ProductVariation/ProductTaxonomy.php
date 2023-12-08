<?php

namespace ACA\WC\Search\ProductVariation;

use AC\Helper\Select\Options\Paginated;
use ACP;
use ACP\Helper\Select\Taxonomy\PaginatedFactory;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Value;

class ProductTaxonomy extends Comparison
    implements Comparison\SearchableValues
{

    /**
     * @var string
     */
    private $taxonomy;

    public function __construct($taxonomy)
    {
        $this->taxonomy = $taxonomy;

        $operators = new ACP\Search\Operators(
            [
                ACP\Search\Operators::EQ,
            ]
        );

        parent::__construct($operators);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        return (new Bindings())->where($this->get_where($value->get_value()));
    }

    /**
     * @param int $product_id
     *
     * @return string
     */
    public function get_where($product_id)
    {
        global $wpdb;

        $products = $this->get_product_ids_by_term_id($product_id);

        if (empty($products)) {
            $products = [-1];
        }

        return sprintf("$wpdb->posts.post_parent IN( %s )", implode(',', $products));
    }

    private function get_label_formatter(): ACP\Helper\Select\Taxonomy\LabelFormatter
    {
        return new ACP\Helper\Select\Taxonomy\LabelFormatter\TermName();
    }

    public function format_label($value): string
    {
        $term = get_term($value);

        return $term ? $this->get_label_formatter()->format_label($term) : $value;
    }

    public function get_values(string $search, int $page): Paginated
    {
        return (new PaginatedFactory())->create([
            'search'   => $search,
            'page'     => $page,
            'taxonomy' => $this->taxonomy,
        ],
            $this->get_label_formatter());
    }

    protected function get_product_ids_by_term_id($term_id): array
    {
        return get_posts([
            'post_type'      => 'product',
            'fields'         => 'ids',
            'posts_per_page' => -1,
            'tax_query'      => [
                [
                    'taxonomy' => $this->taxonomy,
                    'terms'    => $term_id,
                ],
            ],
        ]);
    }

}