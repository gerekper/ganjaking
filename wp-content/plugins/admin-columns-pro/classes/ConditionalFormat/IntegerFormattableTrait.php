<?php

declare(strict_types=1);

namespace ACP\ConditionalFormat;

use ACP\ConditionalFormat\Formatter\IntegerFormatter;

trait IntegerFormattableTrait
{

    public function conditional_format(): ?FormattableConfig
    {
        return new FormattableConfig(new IntegerFormatter());
    }

}