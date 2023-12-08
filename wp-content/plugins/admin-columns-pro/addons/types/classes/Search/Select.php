<?php

namespace ACA\Types\Search;

use AC;
use AC\Helper\Select\Options;
use ACP;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class Select extends ACP\Search\Comparison\Meta
    implements Comparison\Values
{

    private $options;

    public function __construct(string $meta_key, array $options, string $value_type = null)
    {
        $this->options = $options;

        $operators = new Operators([
            Operators::EQ,
            Operators::NEQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($operators, $meta_key, $value_type);
    }

    public function get_values(): Options
    {
        return AC\Helper\Select\Options::create_from_array($this->options);
    }

}