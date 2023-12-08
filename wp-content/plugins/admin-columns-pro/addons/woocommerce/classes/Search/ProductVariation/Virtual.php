<?php

namespace ACA\WC\Search\ProductVariation;

use AC\Helper\Select\Options;
use ACP;

class Virtual extends ACP\Search\Comparison\Meta implements ACP\Search\Comparison\Values
{

    public function __construct()
    {
        parent::__construct(new ACP\Search\Operators([ACP\Search\Operators::EQ]), '_virtual');
    }

    public function get_values(): Options
    {
        return Options::create_from_array([
            'yes' => __('Is Virtual', 'codepress-admin-columns'),
            'no'  => sprintf(__('Exclude %s', 'codepress-admin-columns'), __('Virtual', 'woocommerce')),
        ]);
    }

}