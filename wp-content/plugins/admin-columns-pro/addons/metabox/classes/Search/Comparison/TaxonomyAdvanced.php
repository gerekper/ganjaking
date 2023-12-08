<?php

namespace ACA\MetaBox\Search\Comparison;

use AC\Helper\Select\Options\Paginated;
use ACP;
use ACP\Helper\Select\Taxonomy\PaginatedFactory;
use ACP\Search\Operators;

class TaxonomyAdvanced extends ACP\Search\Comparison\Meta
    implements ACP\Search\Comparison\SearchableValues
{

    /**
     * @var string
     */
    protected $taxonomy;

    public function __construct(array $taxonomy, string $meta_key)
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::NEQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        $this->taxonomy = $taxonomy;

        parent::__construct($operators, $meta_key);
    }

    private function get_label_formatter(): ACP\Helper\Select\Taxonomy\LabelFormatter
    {
        return new ACP\Helper\Select\Taxonomy\LabelFormatter\TermName();
    }

    public function format_label($value): string
    {
        $term = get_term($value);

        return $term instanceof \WP_Term
            ? $this->get_label_formatter()->format_label($term)
            : $value;
    }

    public function get_values(string $search, int $page): Paginated
    {
        return (new PaginatedFactory())->create([
            'search'   => $search,
            'page'     => $page,
            'taxonomy' => $this->taxonomy,
        ], $this->get_label_formatter());
    }

}