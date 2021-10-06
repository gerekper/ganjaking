<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-product-other.class.php';

/**
 * Condition: Product Other - WooCommerce Coupons Applied
 *
 * @class RightPress_Condition_Product_Other_WC_Coupons_Applied
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Product_Other_WC_Coupons_Applied extends RightPress_Condition_Product_Other
{

    protected $key      = 'wc_coupons_applied';
    protected $method   = 'coupons';
    protected $fields   = array(
        'after' => array('coupons'),
    );
    protected $position = 10;

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

        return esc_html__('Coupons applied', 'rightpress');
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

        $coupon_ids = array();

        // Circular reference prevention
        do_action('rightpress_conditions_get_coupons_start');

        // Get applied coupons
        $coupons = RightPress_Help::is_request('frontend') ? WC()->cart->get_coupons() : array();

        // Circular reference prevention
        do_action('rightpress_conditions_get_coupons_end');

        // Iterate over applied coupons
        foreach ($coupons as $coupon_code => $coupon) {

            // Add coupons that have id set (i.e. are actual WooCommerce coupons) and are applicable to current cart item
            if ($coupon->get_id() && ((!empty($params['cart_item']) && $coupon->is_valid_for_product($params['cart_item']['data'], $params['cart_item'])) || $coupon->is_valid_for_cart())) {
                $coupon_ids[] = $coupon->get_id();
            }
        }

        return $coupon_ids;
    }





}
