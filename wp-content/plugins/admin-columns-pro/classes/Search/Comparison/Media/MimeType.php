<?php

namespace ACP\Search\Comparison\Media;

use AC\Helper\Select\Options;
use ACP\Helper\Select\OptionsFactory;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class MimeType extends Comparison
    implements Comparison\RemoteValues
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::EQ,
        ]);

        parent::__construct($operators);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        return (new Bindings\Media())->mime_types($value->get_value());
    }

    public function format_label(string $value): string
    {
        return $value;
    }

    public function get_values(): Options
    {
        return (new OptionsFactory\MimeType())->create('attachment');
    }

}