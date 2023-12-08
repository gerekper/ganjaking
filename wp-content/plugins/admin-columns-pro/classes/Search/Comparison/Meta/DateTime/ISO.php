<?php

namespace ACP\Search\Comparison\Meta\DateTime;

use AC\Helper\Select\Options;
use AC\Meta\Query;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\RemoteValues;
use ACP\Search\Helper\Select\Meta\DateOptionsFactory;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;

class ISO extends Meta implements RemoteValues
{

    private $value_factory;

    public function __construct(string $meta_key, Query $query)
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::GT,
            Operators::LT,
            Operators::GTE,
            Operators::LTE,
            Operators::BETWEEN,
            Operators::TODAY,
            Operators::PAST,
            Operators::FUTURE,
            Operators::WITHIN_DAYS,
            Operators::LT_DAYS_AGO,
            Operators::GT_DAYS_AGO,
            Operators::EQ_YEAR,
            Operators::EQ_MONTH,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($operators, $meta_key, Value::DATE, new Labels\Date());

        $this->value_factory = new DateOptionsFactory($query, 'Y-m-d H:i:s');
    }

    protected function get_meta_query(string $operator, Value $value): array
    {
        if (Operators::EQ === $operator) {
            $value = new Value(
                [
                    $value->get_value() . ' 00:00:00',
                    $value->get_value() . ' 23:59:59',
                ],
                Value::DATE
            );
            $operator = Operators::BETWEEN;
        }

        return parent::get_meta_query($operator, $value);
    }

    public function format_label(string $value): string
    {
        return $this->value_factory->create_label($value);
    }

    public function get_values(): Options
    {
        return $this->value_factory->create_options();
    }

}