<?php

namespace ACA\WC\Search\ProductVariation\Parent;

use ACP\Search\Operators;

class MetaText extends Meta
{

    public function __construct(string $meta_key)
    {
        parent::__construct(
            $meta_key,
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