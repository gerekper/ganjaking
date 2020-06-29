<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition Field: Multiselect - Custom Taxonomy
 *
 * This is a special condition field - it is instantiated with different
 * settings for each custom taxonomy that is enabled
 *
 * @class RP_WCDPD_Condition_Field_Multiselect_Custom_Taxonomy
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Condition_Field_Multiselect_Custom_Taxonomy extends RightPress_Condition_Field_Multiselect_Custom_Taxonomy
{

    protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX;

    // Singleton instance
    protected static $instance = false;

    /**
     * Constructor
     *
     * @access public
     * @param string $key
     * @param string $taxonomy_key
     * @param bool $supports_hierarchy
     * @return void
     */
    public function __construct($key, $taxonomy_key, $supports_hierarchy)
    {

        parent::__construct($key, $taxonomy_key, $supports_hierarchy);
    }





}
