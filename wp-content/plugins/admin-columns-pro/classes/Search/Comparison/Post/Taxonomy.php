<?php

namespace ACP\Search\Comparison\Post;

use AC\Helper\Select\Options\Paginated;
use ACP\Helper\Select;
use ACP\Helper\Select\Taxonomy\LabelFormatter\TermName;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Helper\TaxQuery\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

class Taxonomy extends Comparison
    implements Comparison\SearchableValues
{

    protected $taxonomy;

    public function __construct(string $taxonomy)
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
            'hide_empty' => ! is_taxonomy_hierarchical($this->taxonomy),
        ];

        $args = (array)apply_filters('acp/filtering/terms_args', $args);

        return (new Select\Taxonomy\PaginatedFactory())->create(
            array_merge(
                [
                    'search' => $search,
                    'page'   => $page,
                ],
                $args
            )
        );
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        $tax_query = ComparisonFactory::create(
            $this->taxonomy,
            $operator,
            $value
        );

        $bindings = new Bindings\Post();
        $bindings->tax_query($tax_query->get_expression());

        return $bindings;
    }

}