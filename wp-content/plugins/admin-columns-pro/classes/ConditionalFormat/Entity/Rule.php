<?php
declare(strict_types=1);

namespace ACP\ConditionalFormat\Entity;

use ACP\ConditionalFormat\Type\Format;
use BadMethodCallException;

final class Rule
{

    private $column_name;

    private $format;

    private $operator;

    private $fact;

    public function __construct(string $column_name, Format $format, string $operator, $fact = null)
    {
        $this->column_name = $column_name;
        $this->format = $format;
        $this->operator = $operator;
        $this->fact = $fact;
    }

    public function get_column_name(): string
    {
        return $this->column_name;
    }

    public function get_format(): Format
    {
        return $this->format;
    }

    public function get_operator(): string
    {
        return $this->operator;
    }

    public function has_fact(): bool
    {
        return $this->fact !== null;
    }

    public function get_fact()
    {
        if ( ! $this->has_fact()) {
            throw new BadMethodCallException('Fact is not stated for this rule.');
        }

        return $this->fact;
    }

}