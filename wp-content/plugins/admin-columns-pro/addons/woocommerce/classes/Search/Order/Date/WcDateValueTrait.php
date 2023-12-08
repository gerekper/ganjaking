<?php

namespace ACA\WC\Search\Order\Date;

use ACP\Search\Helper\DateValueFactory;
use ACP\Search\Operators;
use ACP\Search\Value;
use DateTime;

trait WcDateValueTrait
{

    public function get_wc_formatted_date_comparison_value($operator, Value $value): string
    {
        $date = new DateTime();

        switch ($operator) {
            case Operators::TODAY:
                return $date->format('Y-m-d');

            case Operators::LT_DAYS_AGO:
                return '>' . $date->modify(sprintf('-%s days', $value->get_value()))->format('Y-m-d');

            case Operators::GT_DAYS_AGO:
                return '<' . $date->modify(sprintf('-%s days', $value->get_value()))->format('Y-m-d');

            case Operators::BETWEEN:
                $value_range = is_array($value->get_value()) && count($value->get_value()) === 2 ? $value->get_value(
                ) : [0, 0];

                return $value_range[0] . '...' . $value_range[1];

            case Operators::PAST:
                return '<' . $date->format('U');

            case Operators::FUTURE:
                return '>' . $date->format('U');
            case Operators::EQ_MONTH:
                $value_range = (new DateValueFactory($value->get_type()))->create_range_month(
                    (string)$value->get_value()
                )->get_value();

                return $value_range[0] . '...' . $value_range[1];
            case Operators::EQ_YEAR:
                $value_range = (new DateValueFactory($value->get_type()))->create_range_year(
                    (int)$value->get_value()
                )->get_value();

                return $value_range[0] . '...' . $value_range[1];

            case Operators::WITHIN_DAYS:
                $to_date = clone $date;

                return $date->format('U') . '...' . $to_date->modify(sprintf('+%s days', $value->get_value()))->format(
                        'U'
                    );

            case Operators::GT:
            case Operators::GTE:
            case Operators::LT:
            case Operators::LTE:
                return sprintf('%s%s', $operator, $value->get_value());

            case Operators::EQ:
            default:
                return $value->get_value();
        }
    }

}