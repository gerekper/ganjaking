<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Parent condition class
 *
 * @class RightPress_Condition
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition extends RightPress_Item
{

    protected $item_key = 'condition';

    // Properties to be defined at condition group level
    protected $group_key        = null;
    protected $group_position   = null;

    // Properties to be defined at condition level
    protected $key      = null;
    protected $position = null;
    protected $method   = null;

    // Properties with default values that can be overriden at either level
    protected $main_field   = null;
    protected $is_cart      = false;
    protected $is_customer  = false;
    protected $contexts     = array();
    protected $fields       = array();

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Check if library was implemented properly
        add_filter('init', array($this, 'validate_library_implementation'));

        // Call parent constructor
        parent::__construct();
    }

    /**
     * Check if library was implemented properly
     *
     * @access public
     * @return void
     */
    public function validate_library_implementation()
    {

        // Check if required condition properties were set by child classes
        foreach (array('group_key', 'group_position', 'key', 'position', 'method') as $required_property) {
            if (!isset($required_property)) {
                RightPress_Help::doing_it_wrong(__METHOD__, ('Property ' . $required_property . ' must be defined by classes implementing abstract class RightPress_Condition.'), '1.0');
                exit;
            }
        }
    }

    /**
     * Get main field key to compare value against
     *
     * @access public
     * @return string
     */
    public function get_main_field()
    {

        // Main field defined
        if (isset($this->main_field)) {
            return $this->main_field;
        }

        // At least one field defined
        if (is_array($this->fields)) {
            foreach (array('after', 'before') as $position) {
                if (!empty($this->fields[$position])) {
                    return $this->fields[$position][0];
                }
            }
        }

        return null;
    }

    /**
     * Check against condition
     *
     * @access public
     * @param array $params
     * @return bool
     */
    public function check($params)
    {

        try {

            // Get condition methods controller
            $condition_methods_controller = $this->get_controller()->get_condition_methods_controller();

            // Load condition method
            if ($method = $condition_methods_controller->get_item($this->method)) {

                // Get value to compare against condition
                $value = $this->get_value($params);

                // Get condition value
                $condition_value = $this->get_condition_value($params);

                // Compare values if value is set
                return $method->check($params['condition']['method_option'], $value, $condition_value);
            }
        }
        catch (RightPress_Condition_Exception $e) {}

        // Condition check failed
        return false;
    }

    /**
     * Get value to compare against condition
     *
     * Warning! Child get_value() methods must successfully return a value event under unexpected circumstances,
     * e.g. when cart subtotal is supposed to be returned but request comes from the backend and there is no cart.
     * In such cases empty value must be returned (numeric zero, empty array etc).
     *
     * @access public
     * @param array $params
     * @return mixed
     */
    public function get_value($params)
    {

        // Child classes must override this method - if this method was called, condition check must fail
        RightPress_Help::doing_it_wrong((get_called_class() . '::' . __FUNCTION__), 'Child classes must override this method.', '1.0');
        throw new RightPress_Condition_Exception('rightpress_condition_value_error', 'RightPress Condition: Parent get_value() method must not be called.');
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

                    // Reference value
                    $value = $params['condition'][$field_key];

                    // Field supports hierarchy
                    if ($field->supports_hierarchy()) {
                        return $field->get_children($value);
                    }
                    // Field does not support hierarchy
                    else {
                        return $value;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Check if condition is cart condition
     *
     * @access public
     * @return bool
     */
    public function is_cart()
    {

        return $this->is_cart;
    }

    /**
     * Check if condition is customer condition
     *
     * @access public
     * @return bool
     */
    public function is_customer()
    {

        return $this->is_customer;
    }

    /**
     * Get condition fields
     *
     * @access public
     * @return array
     */
    public function get_fields()
    {

        return $this->fields;
    }

    /**
     * Get condition method
     *
     * @access public
     * @return string
     */
    public function get_method()
    {

        return $this->method;
    }

    /**
     * Get cart items
     *
     * Note: This method might be called in the backend or during API calls where cart is not available
     * and must continue checking for cases like that to prevent errors
     *
     * @access public
     * @param array $params
     * @return array
     */
    public function get_cart_items($params)
    {

        // Get cart items
        $cart_items = isset($params['cart_items']) ? $params['cart_items'] : RightPress_Help::get_wc_cart_items();

        // Filter out bundle cart items
        $cart_items = RightPress_Help::filter_out_bundle_cart_items($cart_items);

        // Return remaining items
        return $cart_items;
    }

    /**
     * Get order ids by timeframe
     *
     * @access public
     * @param string $timeframe_key
     * @return array
     */
    public function get_order_ids_by_timeframe($timeframe_key)
    {

        $config = array();

        // Since specific date
        if ($timeframe_key !== 'all_time') {

            // Get date from timeframe
            $config['date'] = RightPress_Conditions_Timeframes::get_date_from_timeframe($timeframe_key);
        }

        // Return matching order ids
        return RightPress_Conditions::get_order_ids($config);
    }

    /**
     * Get order
     *
     * Throws RightPress_Condition_Exception if order can't be loaded
     *
     * Only to be used in conditions' get_value() methods (otherwise errors may not be properly handled)
     *
     * @access public
     * @param array $params
     * @return object
     */
    public function get_order($params)
    {

        // Check if order id is set
        if (!empty($params['item_id'])) {

            // Attempt to load order
            if ($order = wc_get_order($params['item_id'])) {

                // Return order
                return $order;
            }
        }

        // Unable to load order
        throw new RightPress_Condition_Exception('rightpress_condition_value_error', 'RightPress Condition: Order is not defined or invalid.');
    }

    /**
     * Get product
     *
     * Throws RightPress_Condition_Exception if product can't be loaded
     *
     * Only to be used in conditions' get_value() methods (otherwise errors may not be properly handled)
     *
     * @access public
     * @param array $params
     * @param bool $ignore_variation
     * @return object
     */
    public function get_product($params, $ignore_variation = false)
    {

        // Check if at least one id is set
        if (!empty($params['item_id']) || (!$ignore_variation && !empty($params['child_id']))) {

            // Select correct product id
            $product_id = (!$ignore_variation && !empty($params['child_id'])) ? $params['child_id'] : $params['item_id'];

            // Load product object
            if ($product = wc_get_product($product_id)) {

                // Return product object
                return $product;
            }
        }

        // Unable to load product
        throw new RightPress_Condition_Exception('rightpress_condition_value_error', 'RightPress Condition: Product is not defined or invalid.');
    }





}
