<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition: Custom Taxonomy - Product
 *
 * This is a special condition - it is instantiated with different settings for
 * each custom taxonomy that is enabled
 *
 * @class RP_WCDPD_Condition_Custom_Taxonomy_Product
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Condition_Custom_Taxonomy_Product extends RightPress_Condition_Custom_Taxonomy_Product
{

    protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX;

    protected $contexts = array(
        'product_pricing_product',
        'product_pricing_bogo_product',
        'product_pricing_group_product',
        'cart_discounts_product',
        'checkout_fees_product',
    );

    /**
     * Constructor
     *
     * @access public
     * @param string $key
     * @param array $fields
     * @return void
     */
    public function __construct($key, $fields, $label)
    {

        parent::__construct($key, $fields, $label);
    }





}
