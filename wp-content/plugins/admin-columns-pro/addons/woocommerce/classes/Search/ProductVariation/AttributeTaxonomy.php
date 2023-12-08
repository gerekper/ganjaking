<?php

namespace ACA\WC\Search\ProductVariation;

use AC\Helper\Select\Options\Paginated;
use ACP\Helper\Select;
use ACP\Helper\Select\Taxonomy\PaginatedFactory;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class AttributeTaxonomy extends Comparison\Meta implements Comparison\SearchableValues
{

    /**
     * @var string
     */
    protected $taxonomy;

    public function __construct($taxonomy)
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::NEQ,
        ]);

        $this->taxonomy = $taxonomy;

        parent::__construct($operators, 'attribute_' . $taxonomy);
    }

    private function get_label_formatter(): Select\Taxonomy\LabelFormatter
    {
        return new Select\Taxonomy\LabelFormatter\TermName();
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
        ]);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        $term = get_term($value->get_value());

        return parent::create_query_bindings(
            $operator,
            new Value(
                $term->slug ?? '',
                Value::STRING
            )
        );
    }

}