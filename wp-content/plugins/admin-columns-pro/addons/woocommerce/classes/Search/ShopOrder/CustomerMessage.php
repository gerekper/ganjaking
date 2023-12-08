<?php

namespace ACA\WC\Search\ShopOrder;

use ACP;
use ACP\Search\Operators;

class CustomerMessage extends ACP\Search\Comparison\Post\PostField
{

    public function __construct()
    {
        parent::__construct(
            new Operators([
                Operators::IS_EMPTY,
                Operators::NOT_IS_EMPTY,
            ]),
            null,
            new ACP\Search\Labels([
                Operators::IS_EMPTY     => __('Empty', 'codepress-admin-columns'),
                Operators::NOT_IS_EMPTY => __('Has customer message', 'codepress-admin-columns'),
            ])
        );
    }

    protected function get_field(): string
    {
        return 'post_excerpt';
    }

}