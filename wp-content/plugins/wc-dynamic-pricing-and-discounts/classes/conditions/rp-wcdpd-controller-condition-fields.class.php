<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition fields controller
 *
 * @class RP_WCDPD_Controller_Condition_Fields
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Controller_Condition_Fields extends RightPress_Controller_Condition_Fields
{

    protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX;

    // Conditions are second level items in this plugin (inside rules)
    protected $is_second_level_item = true;

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

RP_WCDPD_Controller_Condition_Fields::get_instance();
