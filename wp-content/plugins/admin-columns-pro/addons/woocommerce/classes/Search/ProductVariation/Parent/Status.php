<?php

namespace ACA\WC\Search\ProductVariation\Parent;

use AC\Helper\Select\Options;
use ACP\Helper\Select\OptionsFactory\PostStatus;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class Status extends Field implements Comparison\Values
{

    public function __construct()
    {
        parent::__construct(
            'post_status',
            new Operators([
                Operators::EQ,
                Operators::NEQ,
            ], false)
        );
    }

    public function get_values(): Options
    {
        return (new PostStatus())->create('product');
    }

}