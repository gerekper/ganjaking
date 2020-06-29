<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition Field: Multiselect - Capabilities
 *
 * @class RP_WCDPD_Condition_Field_Multiselect_Capabilities
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Condition_Field_Multiselect_Capabilities extends RightPress_Condition_Field_Multiselect_Capabilities
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

    /**
     * Load multiselect options
     *
     * @access public
     * @param array $ids
     * @param string $query
     * @return array
     */
    public function load_multiselect_options($ids = array(), $query = '')
    {

        $all_capabilities = parent::load_multiselect_options($ids, $query);
        return apply_filters('rp_wcdpd_all_capabilities', $all_capabilities);
    }





}

RP_WCDPD_Condition_Field_Multiselect_Capabilities::get_instance();
