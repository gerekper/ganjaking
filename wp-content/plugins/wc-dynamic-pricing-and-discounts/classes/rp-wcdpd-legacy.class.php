<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Legacy functionality support
 *
 * @class RP_WCDPD_Legacy
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Legacy
{

    // Singleton control
    protected static $instance = false; public static function get_instance() { return self::$instance ? self::$instance : (self::$instance = new self()); }

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Maybe allow changing prices before cart is loaded
        add_filter('rightpress_product_price_shop_change_prices_before_cart_is_loaded', array($this, 'maybe_allow_changing_prices_before_cart_is_loaded'), 99);

        // Maybe change price decimals
        add_filter('rightpress_product_price_decimals', array($this, 'maybe_change_price_decimals'), 99);

        // Maybe change rounded price
        add_filter('rightpress_product_price_rounded_price', array($this, 'maybe_change_rounded_price'), 99, 3);

        // Maybe change price breakdown average price
        add_filter('rightpress_product_price_breakdown_average_price', array($this, 'maybe_change_price_breakdown_average_price'), 99, 4);

        // Bundle cart item filter flags filter
        add_filter('rightpress_bundle_cart_item_filter_flags', array($this, 'legacy_bundle_cart_item_filter_flags'));

        // Supported object types for custom taxonomy conditions
        add_filter('rightpress_custom_condition_taxonomy_object_types', array($this, 'custom_condition_taxonomy_object_types'));

        // Taxonomies for custom taxonomy conditions
        add_filter('rightpress_custom_condition_taxonomies', array($this, 'custom_condition_taxonomies'));

        // Conditions timeframes
        add_filter('rightpress_conditions_timeframes', array($this, 'conditions_timeframes'));
    }

    /**
     * Maybe allow changing prices before cart is loaded
     *
     * @access public
     * @param bool $allow
     * @return bool
     */
    public function maybe_allow_changing_prices_before_cart_is_loaded($allow)
    {

        return $allow ? $allow : apply_filters('rp_wcdpd_allow_price_override_before_cart_is_loaded', false);
    }

    /**
     * Maybe change price decimals
     *
     * @access public
     * @param int $decimals
     * @return int
     */
    public function maybe_change_price_decimals($decimals)
    {

        return apply_filters('rp_wcdpd_decimals', $decimals);
    }

    /**
     * Maybe change rounded price
     *
     * @access public
     * @param float $rounded_price
     * @param float $price
     * @param int $decimals
     * @return float
     */
    public function maybe_change_rounded_price($rounded_price, $price, $decimals)
    {

        return apply_filters('rp_wcdpd_rounded_amount', $rounded_price, $price, $decimals);
    }

    /**
     * Maybe change price breakdown average price
     *
     * @access public
     * @param float $average_price
     * @param array $price_ranges
     * @param object $product
     * @param array $cart_item
     * @return float
     */
    public function maybe_change_price_breakdown_average_price($average_price, $price_ranges, $product, $cart_item = null)
    {

        return apply_filters('rp_wcdpd_product_pricing_adjusted_price', $average_price, $price_ranges, $product, $cart_item);
    }

    /**
     * Bundle cart item filter flags filter
     *
     * @access public
     * @param array $flags
     * @return array
     */
    public function legacy_bundle_cart_item_filter_flags($flags)
    {

        return apply_filters('rp_wcdpd_bundle_cart_item_filter_flags', $flags);
    }

    /**
     * Supported object types for custom taxonomy conditions
     *
     * @access public
     * @param array $object_types
     * @return array
     */
    public function custom_condition_taxonomy_object_types($object_types)
    {

        return apply_filters('rp_wcdpd_custom_condition_taxonomy_object_types', $object_types);
    }

    /**
     * Taxonomies for custom taxonomy conditions
     *
     * @access public
     * @param array $taxonomies
     * @return array
     */
    public function custom_condition_taxonomies($taxonomies)
    {

        return apply_filters('rp_wcdpd_custom_condition_taxonomies', $taxonomies);
    }

    /**
     * Conditions timeframes
     *
     * @access public
     * @param array $timeframes
     * @return array
     */
    public function conditions_timeframes($timeframes)
    {

        return apply_filters('rp_wcdpd_timeframes', $timeframes);
    }





}

RP_WCDPD_Legacy::get_instance();



/**
 * LEGACY CLASS NAMES THAT COULD HAVE BEEN USED BY 3RD PARTY DEVELOPERS
 */

abstract class RP_WCDPD_Condition_Cart_Item_Quantities      extends RightPress_Condition_Cart_Item_Quantities       { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }
abstract class RP_WCDPD_Condition_Cart_Item_Subtotals       extends RightPress_Condition_Cart_Item_Subtotals        { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }
abstract class RP_WCDPD_Condition_Cart_Items                extends RightPress_Condition_Cart_Items                 { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }
abstract class RP_WCDPD_Condition_Cart                      extends RightPress_Condition_Cart                       { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }
abstract class RP_WCDPD_Condition_Checkout                  extends RightPress_Condition_Checkout                   { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }
abstract class RP_WCDPD_Condition_Custom_Taxonomy           extends RightPress_Condition_Custom_Taxonomy            { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }
abstract class RP_WCDPD_Condition_Customer_Value            extends RightPress_Condition_Customer_Value             { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }
abstract class RP_WCDPD_Condition_Customer                  extends RightPress_Condition_Customer                   { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }
abstract class RP_WCDPD_Condition_Other                     extends RightPress_Condition_Other                      { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }
abstract class RP_WCDPD_Condition_Product_Other             extends RightPress_Condition_Product_Other              { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }
abstract class RP_WCDPD_Condition_Product_Property          extends RightPress_Condition_Product_Property           { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }
abstract class RP_WCDPD_Condition_Product                   extends RightPress_Condition_Product                    { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }
abstract class RP_WCDPD_Condition_Purchase_History_Quantity extends RightPress_Condition_Purchase_History_Quantity  { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }
abstract class RP_WCDPD_Condition_Purchase_History_Value    extends RightPress_Condition_Purchase_History_Value     { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }
abstract class RP_WCDPD_Condition_Purchase_History          extends RightPress_Condition_Purchase_History           { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }
abstract class RP_WCDPD_Condition_Shipping                  extends RightPress_Condition_Shipping                   { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }
abstract class RP_WCDPD_Condition_Time                      extends RightPress_Condition_Time                       { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }
abstract class RP_WCDPD_Condition                           extends RightPress_Condition                            { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }

abstract class RP_WCDPD_Condition_Field_Decimal             extends RightPress_Condition_Field_Decimal              { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }
abstract class RP_WCDPD_Condition_Field_Multiselect         extends RightPress_Condition_Field_Multiselect          { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }
abstract class RP_WCDPD_Condition_Field_Number              extends RightPress_Condition_Field_Number               { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }
abstract class RP_WCDPD_Condition_Field_Select              extends RightPress_Condition_Field_Select               { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }
abstract class RP_WCDPD_Condition_Field_Select_Timeframe    extends RightPress_Condition_Field_Select_Timeframe     { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }
abstract class RP_WCDPD_Condition_Field_Text                extends RightPress_Condition_Field_Text                 { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }
abstract class RP_WCDPD_Condition_Field                     extends RightPress_Condition_Field                      { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }

abstract class RP_WCDPD_Condition_Method                    extends RightPress_Condition_Method                     { protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX; }
