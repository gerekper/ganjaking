<?php

namespace ACP\Filtering;

use AC\Helper\Select\Options;
use ACP;
use ACP\Query\Bindings;
use ACP\Search\Operators;
use ACP\Search\Value;

/**
 * @deprecated NEWVERSION
 */
abstract class Model extends ACP\Search\Comparison
{

    protected $column;

    public function __construct($column)
    {
        $this->column = $column;

        parent::__construct(new Operators([Operators::EQ]));
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        return new Bindings();
    }

    public function format_label(): string
    {
        return '';
    }

    public function get_values(): Options
    {
        return Options::create_from_array([]);
    }

    public function set_data_type(): self
    {
        return $this;
    }

    public function get_data_type(): string
    {
        return '';
    }

    public function set_ranged($is_ranged): void
    {
    }

    public function is_ranged(): bool
    {
        return false;
    }

}