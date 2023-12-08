<?php

declare(strict_types=1);

namespace ACP\ConditionalFormat;

use AC\Iterator;
use ACP\ConditionalFormat\Entity\Rule;

final class RuleCollection extends Iterator
{

    public function __construct(array $data = [])
    {
        array_map([$this, 'add'], $data);
    }

    public function add(Rule $rule): void
    {
        $this->data[] = $rule;
    }

    public function current(): Rule
    {
        return parent::current();
    }

}