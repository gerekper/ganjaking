<?php

namespace ACA\ACF\Search\Comparison\Repeater;

use ACA\ACF\Search\Comparison;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;
use ACP\Search\UserValuesTrait;

class User extends Comparison\Repeater implements SearchableValues
{

    use UserValuesTrait;

    public function __construct(string $meta_type, string $parent_key, string $sub_key, bool $multiple)
    {
        $operators = new Operators([
            Operators::EQ,
        ]);

        parent::__construct($meta_type, $parent_key, $sub_key, $operators, null, $multiple);
    }

}