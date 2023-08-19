<?php

namespace ACP\ConditionalFormat;

use ACP\ConditionalFormat\Formatter\FilterHtmlFormatter;
use ACP\ConditionalFormat\Formatter\StringFormatter;

trait FilteredHtmlFormatTrait
{

    public function conditional_format(): ?FormattableConfig
    {
        return new FormattableConfig(
            new FilterHtmlFormatter(new StringFormatter())
        );
    }

}