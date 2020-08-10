<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-cart.class.php';

/**
 * Condition: Cart - Subtotal
 *
 * @class RightPress_Condition_Cart_Subtotal
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Cart_Subtotal extends RightPress_Condition_Cart
{

    protected $key      = 'subtotal';
    protected $method   = 'numeric';
    protected $fields   = array(
        'after' => array('decimal'),
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

        return __('Cart subtotal', 'rightpress');
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

        // Check if amounts include tax
        $include_tax = $this->get_controller()->amounts_include_tax();

        // Calculate subtotal
        $value = RightPress_Help::calculate_subtotal($include_tax);

        // Process currency manipulations
        return RightPress_Help::get_amount_in_currency($value, array('realmag777', 'wpml'));
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
