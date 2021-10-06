<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RP_WCDPD_Method_Product_Pricing_Volume')) {
    require_once('rp-wcdpd-method-product-pricing-volume.class.php');
}

/**
 * Product Pricing Method: Volume Bulk
 *
 * @class RP_WCDPD_Method_Product_Pricing_Volume_Bulk
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Method_Product_Pricing_Volume_Bulk extends RP_WCDPD_Method_Product_Pricing_Volume
{

    protected $key      = 'bulk';
    protected $position = 10;

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
        return esc_html__('Bulk pricing', 'rp_wcdpd');
    }

    /**
     * Get matching quantity range keys with allocated cart item quantities
     *
     * @access public
     * @param array $rule
     * @param int $quantity_group
     * @param string $quantity_group_key
     * @return array
     */
    public function get_quantity_ranges_with_allocated_quantities($rule, $quantity_group, $quantity_group_key)
    {
        $matched = array();

        // Get total quantity
        $total_quantity = array_sum($quantity_group);

        // Iterate over quantity ranges
        foreach ($rule['quantity_ranges'] as $quantity_range_key => $quantity_range) {

            // Format unique range key
            $unique_range_key = $quantity_range_key . '_' . $quantity_group_key;

            // Check if total quantity falls into current range
            if ($quantity_range['from'] <= $total_quantity && ($quantity_range['to'] === null || $total_quantity <= $quantity_range['to'])) {
                $matched[$unique_range_key]['quantity_range_key'] = $quantity_range_key;
                $matched[$unique_range_key]['quantity_group_key'] = $quantity_group_key;
                $matched[$unique_range_key]['quantities'] = $quantity_group;
                break;
            }
        }

        return $matched;
    }


}

RP_WCDPD_Method_Product_Pricing_Volume_Bulk::get_instance();
