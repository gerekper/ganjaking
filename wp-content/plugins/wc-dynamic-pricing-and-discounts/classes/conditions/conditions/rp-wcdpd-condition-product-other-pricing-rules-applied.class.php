<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition: Product Other - Pricing Rules Applied
 *
 * @class RP_WCDPD_Condition_Product_Other_Pricing_Rules_Applied
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Condition_Product_Other_Pricing_Rules_Applied extends RightPress_Condition_Product_Other
{

    protected $key      = 'pricing_rules_applied';
    protected $method   = 'boolean';
    protected $position = 10;

    protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX;

    protected $contexts = array(
        'cart_discounts_product'
    );

    // Singleton instance
    protected static $instance = false;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        parent::__construct();

        $this->hook();
    }

    /**
     * Get label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {

        return esc_html__('Any pricing rule applied', 'rp_wcdpd');
    }

    /**
     * Get value to compare against condition
     *
     * @access public
     * @param array $params
     * @return mixed
     */
    public function get_value($params)
    {

        // Check if cart item key is set
        if (!empty($params['cart_item']['key'])) {

            // Get cart item price changes
            $price_changes = RightPress_Product_Price_Cart::get_cart_item_price_changes($params['cart_item']['key']);

            // Check if any rules are applicable
            if (!empty($price_changes['all_changes']['rp_wcdpd'])) {
                return true;
            }
        }

        return false;
    }





}

RP_WCDPD_Condition_Product_Other_Pricing_Rules_Applied::get_instance();
