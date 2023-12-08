<?php

declare(strict_types=1);

namespace ACP\Filtering\ApplyFilter;

use ACP\Search\Comparison;

class CacheDuration
{

    private $comparison;

    public function __construct(Comparison $comparison)
    {
        $this->comparison = $comparison;
    }

    public function apply_filters(int $seconds): int
    {
        return (int)apply_filters('acp/filtering/cache/seconds', $seconds, $this->comparison);
    }

}