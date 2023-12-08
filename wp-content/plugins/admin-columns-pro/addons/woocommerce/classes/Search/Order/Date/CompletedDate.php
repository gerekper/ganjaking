<?php

namespace ACA\WC\Search\Order\Date;

use AC\Helper\Select\Options;
use ACA\WC\Helper\Order\DateOptionsFactory;
use ACA\WC\Scheme\OrderOperationalData;
use ACA\WC\Search;
use ACA\WC\Search\LabelFormatter\DateMonth;
use ACP;
use ACP\Query\Bindings;
use ACP\Query\Bindings\QueryArguments;
use ACP\Search\Operators;
use ACP\Search\Value;

class CompletedDate extends ACP\Search\Comparison implements ACP\Search\Comparison\RemoteValues
{

    use WcDateValueTrait;

    public function __construct()
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
                Operators::GT,
                Operators::LT,
                Operators::GTE,
                Operators::LTE,
                Operators::BETWEEN,
                Operators::GT_DAYS_AGO,
                Operators::LT_DAYS_AGO,
                Operators::TODAY,
                Operators::EQ_MONTH,
                Operators::EQ_YEAR,
            ]),
            Value::DATE,
            new ACP\Search\Labels\Date()
        );
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        return (new QueryArguments())->query_arguments([
            'date_completed' => $this->get_wc_formatted_date_comparison_value($operator, $value),
        ]);
    }

    public function format_label(string $value): string
    {
        return (new DateMonth())->format_label($value);
    }

    public function get_values(): Options
    {
        return (new DateOptionsFactory())->create_operational_data_options(
            OrderOperationalData::DATE_COMPLETED_GMT
        );
    }

}