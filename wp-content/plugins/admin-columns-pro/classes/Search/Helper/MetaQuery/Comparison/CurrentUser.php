<?php

namespace ACP\Search\Helper\MetaQuery\Comparison;

use ACP\Search\Helper\MetaQuery;
use ACP\Search\Helper\UserValueFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

class CurrentUser extends MetaQuery\Comparison
{

    public function __construct(string $key, Value $value)
    {
        $factory = new UserValueFactory();

        parent::__construct($key, Operators::EQ, $factory->create_current_user());
    }

}