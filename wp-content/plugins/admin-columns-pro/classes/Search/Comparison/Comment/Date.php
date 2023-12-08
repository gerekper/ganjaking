<?php

namespace ACP\Search\Comparison\Comment;

use AC\Helper\Select\Options;
use ACP\Search\Comparison;
use ACP\Search\Helper\Select\Comment\DateOptionsFactory;
use ACP\Search\Operators;

abstract class Date extends Comparison\Date implements Comparison\RemoteValues
{

    private $value_factory;

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
                Operators::TODAY,
                Operators::EQ_MONTH,
                Operators::EQ_YEAR,
                Operators::GT_DAYS_AGO,
                Operators::LT_DAYS_AGO,
            ])
        );

        $this->value_factory = new DateOptionsFactory();
    }

    abstract protected function get_field(): string;

    protected function get_column(): string
    {
        global $wpdb;

        return sprintf('%s.%s', $wpdb->comments, $this->get_field());
    }

    public function format_label(string $value): string
    {
        return $this->value_factory->create_label($value);
    }

    public function get_values(): Options
    {
        return $this->value_factory->create_options($this->get_column());
    }

}