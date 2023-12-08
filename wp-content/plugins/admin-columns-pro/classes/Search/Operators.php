<?php

namespace ACP\Search;

use AC\Config;
use LogicException;

final class Operators extends Config
{

    public const EQ = '=';
    public const NEQ = '!=';
    public const GT = '>';
    public const GTE = '>=';
    public const LT = '<';
    public const LTE = '<=';
    public const CONTAINS = 'CONTAINS';
    public const NOT_CONTAINS = 'NOT CONTAINS';
    public const BEGINS_WITH = 'BEGINS WITH';
    public const ENDS_WITH = 'ENDS WITH';
    public const IN = 'IN';
    public const NOT_IN = 'NOT IN';
    public const BETWEEN = 'BETWEEN';
    public const IS_EMPTY = 'IS EMPTY';
    public const NOT_IS_EMPTY = 'NOT IS EMPTY';
    public const TODAY = 'TODAY';
    public const PAST = 'PAST';
    public const FUTURE = 'FUTURE';
    public const EQ_YEAR = 'EQ_YEAR';
    public const EQ_MONTH = 'EQ_MONTH';
    public const LT_DAYS_AGO = 'LT_DAYS_AGO';
    public const GT_DAYS_AGO = 'GT_DAYS_AGO';
    public const WITHIN_DAYS = 'WITHIN_DAYS';
    public const CURRENT_USER = 'CURRENT_USER';

    public function __construct(array $operators, bool $order = true)
    {
        if ($order) {
            $operators = array_intersect($this->get_operators(), $operators);
        }

        parent::__construct($operators);
    }

    protected function get_operators(): array
    {
        return [
            self::EQ,
            self::NEQ,
            self::GT,
            self::GTE,
            self::LT,
            self::LTE,
            self::CONTAINS,
            self::NOT_CONTAINS,
            self::BEGINS_WITH,
            self::ENDS_WITH,
            self::IN,
            self::NOT_IN,
            self::BETWEEN,
            self::IS_EMPTY,
            self::NOT_IS_EMPTY,
            self::TODAY,
            self::PAST,
            self::FUTURE,
            self::EQ_YEAR,
            self::EQ_MONTH,
            self::LT_DAYS_AGO,
            self::GT_DAYS_AGO,
            self::WITHIN_DAYS,
            self::CURRENT_USER,
        ];
    }

    protected function validate_config(): void
    {
        $operators = $this->get_operators();

        foreach ($this as $operator) {
            if ( ! in_array($operator, $operators, true)) {
                throw new LogicException('Invalid operator found.');
            }
        }
    }

}