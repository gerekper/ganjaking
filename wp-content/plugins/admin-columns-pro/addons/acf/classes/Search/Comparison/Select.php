<?php

namespace ACA\ACF\Search\Comparison;

use AC\Helper\Select\Options;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\Values;
use ACP\Search\Operators;

class Select extends Meta
    implements Values
{

    private $choices;

    public function __construct(string $meta_key, array $choices)
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::NEQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        $this->choices = $choices;

        parent::__construct($operators, $meta_key);
    }

    public function get_values(): Options
    {
        return Options::create_from_array($this->choices);
    }

}