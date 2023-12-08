<?php

namespace ACA\Pods\Search;

use AC\Helper\Select\Options\Paginated;
use ACP\Helper\Select;
use ACP\Helper\Select\Taxonomy\LabelFormatter\TermName;
use ACP\Helper\Select\Taxonomy\PaginatedFactory;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class PickTaxonomy extends Meta
    implements SearchableValues
{

    private $taxonomy;

    public function __construct(string $meta_key, array $taxonomy, string $value_type = null)
    {
        $this->taxonomy = $taxonomy;

        $operators = new Operators([
            Operators::EQ,
            Operators::NEQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($operators, $meta_key, $value_type);
    }

    public function format_label($value): string
    {
        $term = get_term($value);

        return $term
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

}