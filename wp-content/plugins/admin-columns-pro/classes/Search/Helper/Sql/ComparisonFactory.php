<?php

namespace ACP\Search\Helper\Sql;

use ACP\Search\Operators;
use ACP\Search\Value;
use LogicException;

final class ComparisonFactory
{

    public static function create(string $column, string $operator, Value $value): Comparison
    {
        $operators = [
            Operators::EQ           => '=',
            Operators::NEQ          => '!=',
            Operators::LT           => '<',
            Operators::LTE          => '<=',
            Operators::GT           => '>',
            Operators::GTE          => '>=',
            Operators::IS_EMPTY     => '=',
            Operators::NOT_IS_EMPTY => '!=',
        ];

        if (array_key_exists($operator, $operators)) {
            return new Comparison($column, $operators[$operator], $value);
        }

        $operators = [
            Operators::CONTAINS     => Comparison\Contains::class,
            Operators::NOT_CONTAINS => Comparison\NotContains::class,
            Operators::BEGINS_WITH  => Comparison\BeginsWith::class,
            Operators::ENDS_WITH    => Comparison\EndsWith::class,
            Operators::IN           => Comparison\In::class,
            Operators::NOT_IN       => Comparison\NotIn::class,
            Operators::BETWEEN      => Comparison\Between::class,
            Operators::TODAY        => Comparison\Today::class,
            Operators::FUTURE       => Comparison\Future::class,
            Operators::PAST         => Comparison\Past::class,
            Operators::LT_DAYS_AGO  => Comparison\LtDaysAgo::class,
            Operators::GT_DAYS_AGO  => Comparison\GtDaysAgo::class,
            Operators::WITHIN_DAYS  => Comparison\WithinDays::class,
            Operators::CURRENT_USER => Comparison\CurrentUser::class,
            Operators::EQ_MONTH     => Comparison\EqMonth::class,
            Operators::EQ_YEAR      => Comparison\EqYear::class,
        ];

        if ( ! array_key_exists($operator, $operators)) {
            throw new LogicException('Invalid operator found.');
        }

        return new $operators[$operator]($column, $value);
    }

}