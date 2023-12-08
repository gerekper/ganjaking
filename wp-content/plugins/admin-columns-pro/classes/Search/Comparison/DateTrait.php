<?php

namespace ACP\Search\Comparison;

use ACP\Search\Helper\DateValueFactory;
use ACP\Search\Helper\Sql;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;
use DateTime;
use RuntimeException;

trait DateTrait
{

    public function get_date_sql_comparison(string $column, string $operator, Value $value): Sql\Comparison
    {
        if (Operators::EQ === $operator) {
            $value_factory = new DateValueFactory($value->get_type());

            $date = DateTime::createFromFormat('Y-m-d', $value->get_value());

            if (false === $date) {
                throw new RuntimeException('Invalid date format.');
            }

            return ComparisonFactory::create(
                $column,
                Operators::BETWEEN,
                $value_factory->create_range_single_day($date)
            );
        }

        return ComparisonFactory::create(
            $column,
            $operator,
            $value
        );
    }
}