<?php

namespace ACP\Search\Comparison\User;

use AC\Helper\Select\Options;
use ACP\Search\Comparison;
use ACP\Search\Helper\Select\User\DateOptionsFactory;
use ACP\Search\Operators;

abstract class Date extends Comparison\Date implements Comparison\RemoteValues
{

    private $value_factory;

    public function __construct(Operators $operators)
    {
        parent::__construct($operators);

        $this->value_factory = new DateOptionsFactory();
    }

    abstract protected function get_field(): string;

    protected function get_column(): string
    {
        global $wpdb;

        return sprintf('%s.%s', $wpdb->users, $this->get_field());
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