<?php

namespace ACA\MetaBox\Search\Comparison;

use AC\Helper\Select\Options\Paginated;
use ACP;
use ACP\Helper\Select\Taxonomy\LabelFormatter\TermName;
use ACP\Query\Bindings;
use ACP\Search\Operators;
use ACP\Search\Value;

class Taxonomy extends ACP\Search\Comparison implements ACP\Search\Comparison\SearchableValues
{

    public function __construct(array $taxonomy)
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::NEQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        $this->taxonomy = $taxonomy;

        parent::__construct($operators, Value::INT);
    }

    private function get_term_by_id($term_id)
    {
        global $wpdb;

        $_tax = $wpdb->get_row(
            $wpdb->prepare(
                "
			SELECT t.* 
			FROM $wpdb->term_taxonomy AS t 
			WHERE t.term_id = %s 
			LIMIT 1"
                ,
                $term_id
            )
        );

        if ( ! $_tax || is_wp_error($_tax)) {
            return false;
        }

        return get_term($term_id, $_tax->taxonomy);
    }

    public function get_formatter(): TermName
    {
        return new TermName();
    }

    public function format_label($value): string
    {
        $term = get_term($value);

        return $term
            ? $this->get_formatter()->format_label($term)
            : '';
    }

    public function get_values(string $search, int $page): Paginated
    {
        $args = [
            'taxonomy'   => $this->taxonomy,
            'hide_empty' => false,
        ];

        return (new ACP\Helper\Select\Taxonomy\PaginatedFactory())->create(
            array_merge(
                [
                    'search' => $search,
                    'page'   => $page,
                ],
                $args
            )
        );
    }

    protected function create_query_bindings(string $operator, ACP\Search\Value $value): Bindings
    {
        $bindings = new ACP\Query\Bindings\Post();
        $term = $this->get_term_by_id($value->get_value());

        if ($term) {
            $tax_query = ACP\Search\Helper\TaxQuery\ComparisonFactory::create(
                $term->taxonomy,
                $operator,
                $value
            );

            $bindings = new ACP\Query\Bindings\Post();
            $bindings->tax_query($tax_query->get_expression());
        }

        return $bindings;
    }

}