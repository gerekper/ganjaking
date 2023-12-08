<?php

namespace ACP\Search\Comparison\Post\Date;

use AC\Helper\Select\Options;
use ACP\Search\Comparison\Post;
use ACP\Search\Comparison\RemoteValues;
use ACP\Search\Helper\Select\Post\DateOptionsFactory;
use ACP\Search\Operators;

class PostDate extends Post\Date implements RemoteValues
{

    private $value_factory;

    public function __construct(string $post_type)
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
                Operators::PAST,
                Operators::FUTURE,
                Operators::EQ_MONTH,
                Operators::EQ_YEAR,
                Operators::GT_DAYS_AGO,
                Operators::WITHIN_DAYS,
            ], false)
        );

        $this->value_factory = new DateOptionsFactory($post_type);
    }

    public function get_field(): string
    {
        return 'post_date';
    }

    public function format_label(string $value): string
    {
        return $this->value_factory->create_label($value);
    }

    public function get_values(): Options
    {
        return $this->value_factory->create_options($this->get_field());
    }

}