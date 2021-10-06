<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-cart.class.php';

/**
 * Condition: Cart - Coupons
 *
 * @class RightPress_Condition_Cart_Coupons
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Cart_Coupons extends RightPress_Condition_Cart
{

    protected $key      = 'coupons';
    protected $method   = 'coupons';
    protected $fields   = array(
        'after' => array('coupons'),
    );
    protected $position = 50;

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

        $coupon_ids = RightPress_Help::get_wc_cart_applied_coupon_ids();

        // Remove coupons with empty id - they are not real coupons and most probably are our cart discounts
        foreach ($coupon_ids as $coupon_id_key => $coupon_id) {
            if ($coupon_id === 0) {
                unset($coupon_ids[$coupon_id_key]);
            }
        }

        return $coupon_ids;
    }





}
