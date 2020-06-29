<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition Method: List Advanced
 *
 * Note: This is supposed to be used with arrays of numeric ids only (e.g. lists of product ids, category ids etc)
 *
 * @class RP_WCDPD_Condition_Method_List_Advanced
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Condition_Method_List_Advanced extends RightPress_Condition_Method_List_Advanced
{

    protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX;

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

RP_WCDPD_Condition_Method_List_Advanced::get_instance();
