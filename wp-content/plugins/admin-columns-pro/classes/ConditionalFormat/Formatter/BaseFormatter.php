<?php

declare(strict_types=1);

namespace ACP\ConditionalFormat\Formatter;

use AC\Column;
use ACP\ConditionalFormat\Formatter;
use InvalidArgumentException;

abstract class BaseFormatter implements Formatter
{

    private $type;

    public function __construct(string $type)
    {
        $this->type = $type;

        $this->validate();
    }

    protected function validate(): void
    {
        $valid_types = [
            self::DATE,
            self::FLOAT,
            self::INTEGER,
            self::STRING,
        ];

        if ( ! in_array($this->type, $valid_types, true)) {
            throw new InvalidArgumentException(sprintf('Invalid value type (%s) for formatting.', $this->type));
        }
    }

    public function get_type(): string
    {
        return $this->type;
    }

    public function format(string $value, $id, Column $column, string $operator_group): string
    {
        return $value;
    }

}