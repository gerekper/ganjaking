<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition.class.php';

/**
 * Condition Group: Cart Item Subtotals
 *
 * @class RightPress_Condition_Cart_Item_Subtotals
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Cart_Item_Subtotals extends RightPress_Condition
{

    protected $group_key        = 'cart_item_subtotals';
    protected $group_position   = 140;
    protected $is_cart          = true;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        parent::__construct();

        $this->hook_group();
    }

    /**
     * Get group label
     *
     * @access public
     * @return string
     */
    public function get_group_label()
    {

        return __('Cart Items - Subtotal', 'rightpress');
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

        // Check if items are defined
        if (empty($params['condition'][$this->key])) {
            throw new RightPress_Condition_Exception('rightpress_condition_value_error', 'RightPress Condition: Items are not defined.');
        }

        // Get sum of item subtotals
        $sum = RightPress_Help::get_wc_cart_sum_of_item_subtotals(array(
            $this->key => $params['condition'][$this->key],
        ), $this->get_controller()->amounts_include_tax());

        return RightPress_Help::get_amount_in_currency($sum, array('realmag777', 'wpml'));
    }

    /**
     * Get condition value
     *
     * @access public
     * @param array $params
     * @return mixed
     */
    public function get_condition_value($params)
    {

        // Get field key
        if ($field_key = $this->get_main_field()) {

            // Get condition fields controller
            $condition_fields_controller = $this->get_controller()->get_condition_fields_controller();

            // Load field
            if ($field = $condition_fields_controller->get_item($field_key)) {
                if (isset($params['condition'][$field_key])) {
                    return RightPress_Help::get_amount_in_currency($params['condition'][$field_key]);
                }
            }
        }

        return null;
    }





}
