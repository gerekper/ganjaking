<?php

declare(strict_types=1);

namespace ACP\Filtering;

trait FilteringDateSettingTrait
{

    public function get_filtering_date_setting(): ?string
    {
        return $this->options['filter_format'] ?? null;
    }

}