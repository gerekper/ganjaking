<?php

declare(strict_types=1);

namespace ACP\Search;

use ACP\Query\Bindings;
use LogicException;

abstract class Comparison
{

    /**
     * @var Operators
     */
    protected $operators;

    /**
     * @var string
     */
    protected $value_type;

    /**
     * @var Labels
     */
    protected $labels;

    public function __construct(Operators $operators, string $value_type = null, Labels $labels = null)
    {
        if (null === $labels) {
            $labels = new Labels();
        }

        if (null === $value_type) {
            $value_type = Value::STRING;
        }

        $this->labels = $labels;
        $this->value_type = $value_type;
        $this->operators = $operators;

        $this->validate_value_type();
    }

    private function validate_value_type(): void
    {
        $value_types = [
            Value::DATE,
            Value::INT,
            Value::DECIMAL,
            Value::STRING,
        ];

        if ( ! in_array($this->value_type, $value_types, true)) {
            throw new LogicException(sprintf('Unsupported value type found: %s', $this->value_type));
        }
    }

    public function get_operators(): Operators
    {
        return $this->operators;
    }

    public function get_value_type(): string
    {
        return $this->value_type;
    }

    public function get_labels(): array
    {
        $labels = [];

        foreach ($this->get_operators() as $operator) {
            $labels[$operator] = $this->labels->get_offset($operator);
        }

        return $labels;
    }

    final public function get_query_bindings(string $operator, Value $value): Bindings
    {
        if ($this->operators->search($operator) === false) {
            throw new LogicException(
                sprintf('Unsupported operator %s found.', sprintf('"%s"', $operator))
            );
        }

        if ($this->value_type !== $value->get_type()) {
            throw new LogicException('Value types are not identical.');
        }

        return $this->create_query_bindings($operator, $value);
    }

    abstract protected function create_query_bindings(string $operator, Value $value): Bindings;

}