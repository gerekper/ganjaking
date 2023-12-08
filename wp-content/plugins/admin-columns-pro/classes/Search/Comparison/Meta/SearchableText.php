<?php

namespace ACP\Search\Comparison\Meta;

use AC\Helper\Select\Options;
use AC\Helper\Select\Options\Paginated;
use AC\Meta\Query;
use ACP;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class SearchableText extends Meta implements SearchableValues
{

    private $query;

    public function __construct(string $meta_key, Query $query)
    {
        $this->query = $query;

        $operators = new Operators([
            Operators::CONTAINS,
            Operators::NOT_CONTAINS,
            Operators::EQ,
            Operators::NEQ,
            Operators::BEGINS_WITH,
            Operators::ENDS_WITH,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ], false);

        parent::__construct($operators, $meta_key);
    }

    public function format_label($value): string
    {
        return $value;
    }

    public function get_values(string $search, int $page): Paginated
    {
        $this->query->limit(500);

        if ($search) {
            $this->query->where('meta_value', 'LIKE', '%' . esc_sql( $search ) . '%');
        }

        $options = new ACP\Helper\Select\Meta\Text($this->query);

        return new Paginated(
            $options,
            Options::create_from_array($options->get_copy())
        );
    }

}