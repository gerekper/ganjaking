<?php

namespace ACP\Search\Comparison;

use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Helper\DateValueFactory;
use ACP\Search\Helper\Sql;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;
use DateTime;
use RuntimeException;

abstract class Date extends Comparison
{

    /**
     * DB column for SQL clause
     */
    abstract protected function get_column(): string;

    public function __construct(Operators $operators)
    {
        parent::__construct($operators, Value::DATE, new Labels\Date());
    }

    protected function get_sql_comparison(string $operator, Value $value): Sql\Comparison
    {
        if (Operators::EQ === $operator) {
            $value_factory = new DateValueFactory($value->get_type());

            $date = DateTime::createFromFormat('Y-m-d', $value->get_value());

            if (false === $date) {
                throw new RuntimeException('Invalid date format.');
            }

            return ComparisonFactory::create(
                $this->get_column(),
                Operators::BETWEEN,
                $value_factory->create_range_single_day($date)
            );
        }

        return ComparisonFactory::create(
            $this->get_column(),
            $operator,
            $value
        );
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        $bindings = new Bindings();

        $bindings->where(
            $this->get_sql_comparison($operator, $value)->prepare()
        );

        return $bindings;
    }

}