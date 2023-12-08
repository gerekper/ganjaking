<?php

namespace ACA\WC\Search\ProductVariation\Parent;

use ACP\Search\Operators;
use ACP\Search\Value;

class MetaNumber extends Meta
{

    public function __construct(string $meta_key)
    {
        parent::__construct(
            $meta_key,
            new Operators([
                Operators::EQ,
                Operators::LTE,
                Operators::GTE,
                Operators::BETWEEN,
            ], false),
            Value::INT
        );

        $this->meta_key = $meta_key;
    }

}