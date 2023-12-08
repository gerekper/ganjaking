<?php

namespace ACA\WC\Search\ProductSubscription;

use AC;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class Options extends Comparison\Meta
    implements Comparison\Values
{

    /**
     * @var array
     */
    private $options;

    public function __construct(string $meta_key, array $options)
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::NEQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        $this->options = $options;

        parent::__construct($operators, $meta_key);
    }

    public function get_values(): \AC\Helper\Select\Options
    {
        return AC\Helper\Select\Options::create_from_array($this->options);
    }

}