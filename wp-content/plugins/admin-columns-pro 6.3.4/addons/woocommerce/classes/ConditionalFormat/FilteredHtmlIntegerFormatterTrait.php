<?php

declare(strict_types=1);

namespace ACA\WC\ConditionalFormat;

use ACP\ConditionalFormat\FormattableConfig;
use ACP\ConditionalFormat\Formatter;
use ACP\ConditionalFormat\Formatter\FilterHtmlFormatter;

trait FilteredHtmlIntegerFormatterTrait
{

    public function conditional_format(): ?FormattableConfig
    {
        return new FormattableConfig(
            new FilterHtmlFormatter(new Formatter\IntegerFormatter())
        );
    }

}