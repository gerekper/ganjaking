<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RP_WCDPD_Method')) {
    require_once('rp-wcdpd-method.class.php');
}

/**
 * Checkout Fee Method
 *
 * @class RP_WCDPD_Method_Checkout_Fee
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
abstract class RP_WCDPD_Method_Checkout_Fee extends RP_WCDPD_Method
{

    protected $context = 'checkout_fees';

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
     * Get cart subtotal
     *
     * @access public
     * @param bool $for_limit
     * @return float
     */
    public function get_cart_subtotal($for_limit = false)
    {

        $base_includes_tax = false;

        // Check if fees are taxable
        $fees_taxable = RP_WCDPD_Controller_Methods_Checkout_Fee::fees_are_taxable();

        if ($for_limit) {

            // Check if limit is percentage
            $is_percentage = RP_WCDPD_Settings::check('checkout_fees_total_limit', 'total_percentage');

            // Fix percentage limits
            if ($is_percentage) {

                // Base amount should include taxes under specific conditions
                if (!$fees_taxable) {

                    $base_includes_tax = true;
                }
            }
        }
        else {

            // Base amount should include taxes under specific conditions
            if (!$fees_taxable) {

                $base_includes_tax = true;
            }
        }

        // Get cart subtotal
        return RightPress_Help::get_wc_cart_subtotal($base_includes_tax);
    }





}
