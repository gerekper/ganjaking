<?php

namespace DynamicOOOS\PHPHtmlParser\Contracts\Selector;

use DynamicOOOS\PHPHtmlParser\DTO\Selector\RuleDTO;
use DynamicOOOS\PHPHtmlParser\Exceptions\ChildNotFoundException;
interface SeekerInterface
{
    /**
     * Attempts to find all children that match the rule
     * given.
     *
     * @throws ChildNotFoundException
     */
    public function seek(array $nodes, RuleDTO $rule, array $options) : array;
}
