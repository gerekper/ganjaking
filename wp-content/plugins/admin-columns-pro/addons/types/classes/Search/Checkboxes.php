<?php

namespace ACA\Types\Search;

use AC;
use AC\Helper\Select\Options;
use ACP;
use ACP\Search\Comparison;
use ACP\Search\Helper\MetaQuery\SerializedComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

class Checkboxes extends ACP\Search\Comparison\Meta
    implements Comparison\Values
{

    /**
     * @var array options
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

    protected function get_meta_query(string $operator, Value $value): array
    {
        $comparison = SerializedComparisonFactory::create(
            $this->get_meta_key(),
            $operator,
            $value
        );

        return $comparison();
    }

}
