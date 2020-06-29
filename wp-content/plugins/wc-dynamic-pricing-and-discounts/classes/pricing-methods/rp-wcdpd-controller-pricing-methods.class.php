<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Pricing methods controller
 *
 * @class RP_WCDPD_Controller_Pricing_Methods
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Controller_Pricing_Methods extends RightPress_Item_Controller
{

    protected $plugin_prefix        = RP_WCDPD_PLUGIN_PRIVATE_PREFIX;
    protected $item_key             = 'pricing_method';
    protected $items_are_grouped    = true;

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
    }




}

RP_WCDPD_Controller_Pricing_Methods::get_instance();
