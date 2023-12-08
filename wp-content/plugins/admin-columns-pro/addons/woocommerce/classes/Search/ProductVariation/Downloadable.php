<?php

namespace ACA\WC\Search\ProductVariation;

use AC\Helper\Select\Options;
use ACP;
use ACP\Search\Operators;

class Downloadable extends ACP\Search\Comparison\Meta implements ACP\Search\Comparison\Values
{

    public function __construct()
    {
        parent::__construct(new Operators([Operators::EQ]), '_downloadable');
    }

    public function get_values(): Options
    {
        return Options::create_from_array([
            'yes' => __('Is Downloadable', 'codepress-admin-columns'),
            'no'  => sprintf(__('Exclude %s', 'codepress-admin-columns'), __('Downloadable', 'woocommerce')),
        ]);
    }

}