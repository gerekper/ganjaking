<?php

namespace ACP\ConditionalFormat;

use ACP\ConditionalFormat\Formatter\StringFormatter;

final class FormattableConfig
{

    /**
     * @var Formatter
     */
    private $formatter;

    public function __construct(Formatter $formatter = null)
    {
        if (null === $formatter) {
            $formatter = new StringFormatter();
        }

        $this->formatter = $formatter;
    }

    public function get_value_formatter(): Formatter
    {
        return $this->formatter;
    }

}