<?php

namespace ACP\Search\Helper\MetaQuery;

use ACP\Search\Helper\MetaQuery;
use ACP\Search\Operators;
use ACP\Search\Value;
use LogicException;

final class ComparisonFactory
{

    public static function create(string $key, string $operator, Value $value): Comparison
    {
        $operators = [
            Operators::EQ           => '=',
            Operators::NEQ          => '!=',
            Operators::CONTAINS     => 'LIKE',
            Operators::NOT_CONTAINS => 'NOT LIKE',
            Operators::BETWEEN      => 'BETWEEN',
            Operators::GT           => '>',
            Operators::GTE          => '>=',
            Operators::LT           => '<',
            Operators::LTE          => '<=',
        ];

        if (array_key_exists($operator, $operators)) {
            return new Comparison($key, $operators[$operator], $value);
        }

        $operators = [
            Operators::BEGINS_WITH  => MetaQuery\Comparison\BeginsWith::class,
            Operators::ENDS_WITH    => MetaQuery\Comparison\EndsWith::class,
            Operators::IS_EMPTY     => MetaQuery\Comparison\IsEmpty::class,
            Operators::NOT_IS_EMPTY => MetaQuery\Comparison\NotEmpty::class,
            Operators::TODAY        => MetaQuery\Comparison\Today::class,
            Operators::FUTURE       => MetaQuery\Comparison\Future::class,
            Operators::PAST         => MetaQuery\Comparison\Past::class,
            Operators::GT_DAYS_AGO  => MetaQuery\Comparison\GtDaysAgo::class,
            Operators::LT_DAYS_AGO  => MetaQuery\Comparison\LtDaysAgo::class,
            Operators::WITHIN_DAYS  => MetaQuery\Comparison\WithinDays::class,
            Operators::EQ_MONTH     => MetaQuery\Comparison\EqMonth::class,
            Operators::EQ_YEAR      => MetaQuery\Comparison\EqYear::class,
            Operators::CURRENT_USER => MetaQuery\Comparison\CurrentUser::class,
        ];

        if ( ! array_key_exists($operator, $operators)) {
            throw new LogicException('Invalid operator found.');
        }

        return new $operators[$operator]($key, $value);
    }

}