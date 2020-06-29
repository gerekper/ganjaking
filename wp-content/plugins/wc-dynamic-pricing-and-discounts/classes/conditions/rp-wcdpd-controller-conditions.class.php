<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Conditions controller
 *
 * @class RP_WCDPD_Controller_Conditions
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Controller_Conditions extends RightPress_Controller_Conditions
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

        // Assign fields and methods controllers
        $this->condition_fields_controller  = RP_WCDPD_Controller_Condition_Fields::get_instance();
        $this->condition_methods_controller = RP_WCDPD_Controller_Condition_Methods::get_instance();

        // Construct parent
        parent::__construct();
    }

    /**
     * Generate custom taxonomy conditions
     *
     * @access public
     * @return void
     */
    public function generate_custom_taxonomy_conditions()
    {

        // Get enabled taxonomies
        if ($enabled_taxonomies = RP_WCDPD_Settings::get('conditions_custom_taxonomies')) {

            // Generate conditions
            $this->generate_custom_taxonomy_conditions_internal($enabled_taxonomies, 'RP_WCDPD_Condition_Custom_Taxonomy_Product', 'RP_WCDPD_Condition_Field_Multiselect_Custom_Taxonomy');
        }
    }

    /**
     * Get sum of cart item quantities filtered by product conditions
     *
     * @access public
     * @param array $conditions
     * @param int $one_per_line   If set to true, will only count one quantity unit per line item, that is will only count maching cart lines
     * @return int
     */
    public static function get_sum_of_cart_item_quantities_by_product_conditions($conditions = array(), $one_per_line = false)
    {
        $sum = 0;

        // Get cart items filtered by product conditions
        $cart_items = RP_WCDPD_Controller_Conditions::get_cart_items_by_product_conditions($conditions);

        // Filter out bundled cart items
        $cart_items = RightPress_Help::filter_out_bundle_cart_items($cart_items);

        // Add all quantities
        foreach ($cart_items as $cart_item) {
            $sum += ($one_per_line ? 1 : $cart_item['quantity']);
        }

        return $sum;
    }

    /**
     * Get sum of cart item subtotals filtered by product conditions
     *
     * @access public
     * @param array $conditions
     * @param bool $include_tax
     * @return float
     */
    public static function get_sum_of_cart_item_subtotals_by_product_conditions($conditions = array(), $include_tax = true)
    {
        $sum = 0;

        // Get cart items filtered by product conditions
        $cart_items = RP_WCDPD_Controller_Conditions::get_cart_items_by_product_conditions($conditions);

        // Filter out bundled cart items
        $cart_items = RightPress_Help::filter_out_bundle_cart_items($cart_items);

        // Add all quantities
        foreach ($cart_items as $cart_item) {

            // Add subtotal
            $sum += $cart_item['line_subtotal'];

            // Add subtotal tax
            if ($include_tax) {
                $sum += $cart_item['line_subtotal_tax'];
            }
        }

        return $sum;
    }

    /**
     * Check if cart item or product needs to be excluded by exclude rules
     *
     * @access public
     * @param array $exclude_rules
     * @param array $params
     * @return bool
     */
    public static function exclude_item_by_rules($exclude_rules, $params)
    {

        // Filter exclude rules by conditions
        $filtered_exclude_rules = RP_WCDPD_Controller_Conditions::filter_objects($exclude_rules, $params);

        // Remaining exclude rules mean that this item must be excluded from any other rules
        return !empty($filtered_exclude_rules);
    }

    /**
     * Check if amounts in conditions include tax
     *
     * @access public
     * @return bool
     */
    public static function amounts_include_tax()
    {

        return (wc_tax_enabled() && RP_WCDPD_Settings::get('condition_amounts_include_tax'));
    }

    /**
     * Get list of all custom product taxonomies
     *
     * @access public
     * @param array $blacklist
     * @return void
     */
    public static function get_all_custom_taxonomies($blacklist = array())
    {

        // Do not include taxonomies that we already have as conditions
        $blacklist = array('product_cat', 'product_tag', 'product_shipping_class');

        // Get taxonomies
        return parent::get_all_custom_taxonomies($blacklist);
    }





}

RP_WCDPD_Controller_Conditions::get_instance();
