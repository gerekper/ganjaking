<?php

namespace ACA\WC\Search\ShopCoupon;

use AC\Helper\Select\Options\Paginated;
use ACP\Helper\Select\Taxonomy\LabelFormatter\TermName;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;
use WP_Term;

class Categories extends Comparison\Meta
    implements Comparison\SearchableValues
{

    public function __construct($meta_key)
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($operators, $meta_key);
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
        return (new \ACP\Helper\Select\Taxonomy\PaginatedFactory())->create([
            'search'   => $search,
            'page'     => $page,
            'taxonomy' => 'product_cat',
        ]);
    }

    protected function get_meta_query(string $operator, Value $value): array
    {
        if (Operators::EQ === $operator) {
            return [
                'key'     => $this->get_meta_key(),
                'value'   => serialize(absint($value->get_value())),
                'compare' => 'LIKE',
            ];
        }

        return parent::get_meta_query($operator, $value);
    }

}