<?php

namespace ACA\WC\Search\ProductVariation\Parent;

use ACP\Search\Operators;

class Title extends Field
{

    public function __construct()
    {
        parent::__construct(
            'post_title',
            new Operators([
                Operators::CONTAINS,
                Operators::NOT_CONTAINS,
                Operators::EQ,
                Operators::BEGINS_WITH,
                Operators::ENDS_WITH,
            ], false)
        );
    }

}