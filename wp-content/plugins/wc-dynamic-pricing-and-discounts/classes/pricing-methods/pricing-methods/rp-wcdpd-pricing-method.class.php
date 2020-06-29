<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Parent pricing method class
 *
 * @class RP_WCDPD_Pricing_Method
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
abstract class RP_WCDPD_Pricing_Method extends RightPress_Item
{

    protected $plugin_prefix    = RP_WCDPD_PLUGIN_PRIVATE_PREFIX;
    protected $item_key         = 'pricing_method';

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        parent::__construct();
    }

    /**
     * Calculate adjustment value
     *
     * @access public
     * @param float $setting
     * @param float $amount
     * @param array $adjustment
     * @return float
     */
    public function calculate($setting, $amount = 0, $adjustment = null)
    {
        return RightPress_Help::get_amount_in_currency($setting, array('aelia', 'wpml'));
    }

    /**
     * Adjust amount
     *
     * @access public
     * @param float $amount
     * @param float $setting
     * @return float
     */
    public function adjust($amount, $setting)
    {
        $amount += $this->calculate($setting, $amount);
        return (float) ($amount >= 0 ? $amount : 0);
    }





}
