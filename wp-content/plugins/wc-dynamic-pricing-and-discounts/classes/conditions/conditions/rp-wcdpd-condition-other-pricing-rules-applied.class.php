<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition: Other - Pricing Rules Applied
 *
 * @class RP_WCDPD_Condition_Other_Pricing_Rules_Applied
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Condition_Other_Pricing_Rules_Applied extends RightPress_Condition_Other
{

    protected $key      = 'pricing_rules_applied';
    protected $method   = 'boolean';
    protected $position = 10;
    protected $is_cart  = true;

    protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX;

    protected $contexts = array(
        'cart_discounts',
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

        // Frontend requests only
        if (RightPress_Help::is_request('frontend')) {

            // Get cart item price changes
            $price_changes = RightPress_Product_Price_Cart::get_cart_item_price_changes();

            // Iterate over cart items and check if any rules are applicable
            foreach (RightPress_Help::get_wc_cart_items() as $cart_item_key => $cart_item) {
                if (!empty($price_changes[$cart_item_key]['all_changes']['rp_wcdpd'])) {
                    return true;
                }
            }
        }

        return false;
    }





}

RP_WCDPD_Condition_Other_Pricing_Rules_Applied::get_instance();
