<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Parent method class
 *
 * @class RP_WCDPD_Method
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
abstract class RP_WCDPD_Method extends RightPress_Item
{

    protected $plugin_prefix    = RP_WCDPD_PLUGIN_PRIVATE_PREFIX;
    protected $item_key         = 'method';

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
     * Get adjustments
     *
     * @access public
     * @param array $rule
     * @param array $cart_items
     * @return array
     */
    public function get_adjustments($rule, $cart_items = null)
    {
        $adjustments = array();

        // Check if rule conditions are matched
        if (RP_WCDPD_Controller_Conditions::object_conditions_are_matched($rule, array('context' => $this->context, 'cart_items' => $cart_items))) {

            // Add to adjustments array
            $adjustments[] = array(
                'rule'              => $rule,
                'reference_amount'  => $this->get_reference_amount(array('rule' => $rule)),
            );
        }

        return $adjustments;
    }

    /**
     * Get reference amount
     *
     * @access public
     * @param array $adjustment
     * @param float $base_amount
     * @return mixed
     */
    public function get_reference_amount($adjustment, $base_amount = null)
    {
        // Get base amount if not passed in
        if ($base_amount === null) {
            $base_amount = $this->get_cart_subtotal();
        }

        // Get rule selection method
        $selection_method = RP_WCDPD_Settings::get($this->context . '_rule_selection_method');

        // Calculate reference amount
        if (in_array($selection_method, array('bigger_discount', 'smaller_discount', 'bigger_fee', 'smaller_fee'), true)) {
            $reference_amount = (float) RP_WCDPD_Pricing::get_adjustment_value($adjustment['rule']['pricing_method'], $adjustment['rule']['pricing_value'], $base_amount, $adjustment);
            return abs($reference_amount);
        }
        // Reference amount is not needed
        else {
            return null;
        }
    }

    /**
     * Get method key
     *
     * @access public
     * @return string
     */
    public function get_key()
    {
        return $this->key;
    }


}
