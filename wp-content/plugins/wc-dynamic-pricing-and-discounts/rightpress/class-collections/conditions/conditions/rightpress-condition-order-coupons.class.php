<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-order.class.php';

/**
 * Condition: Order - Coupons
 *
 * @class RightPress_Condition_Order_Coupons
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Order_Coupons extends RightPress_Condition_Order
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

        $coupons = array();

        // Get used coupons
        if ($used_coupons = (RightPress_Helper::wc_version_gte('3.7') ? $this->get_order($params)->get_coupon_codes() : $this->get_order($params)->get_used_coupons())) {

            // Iterate over used coupons
            foreach ($used_coupons as $coupon_code) {

                // Get coupon id
                if ($coupon_id = RightPress_Help::get_wc_coupon_id_from_code($coupon_code)) {

                    // Add to list
                    $coupons[] = $coupon_id;
                }
            }
        }

        return $coupons;
    }





}
