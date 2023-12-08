<?php

namespace ACA\Pods\Search;

use AC;
use AC\Helper\Select\Options;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\Values;
use ACP\Search\Operators;

class Pick extends Meta implements Values
{

    /**
     * @var array
     */
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