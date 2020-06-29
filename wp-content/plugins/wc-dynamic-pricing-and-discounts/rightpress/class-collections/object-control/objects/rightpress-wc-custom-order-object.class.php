<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-wc-object.class.php';

/**
 * WooCommerce Custom Order Object
 *
 * @class RightPress_WC_Custom_Order_Object
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WC_Custom_Order_Object extends RightPress_WC_Object
{

    /**
     * Constructor
     *
     * @access public
     * @param mixed $object
     * @param object $data_store
     * @param object $controller
     * @return void
     */
    public function __construct($object, $data_store, $controller)
    {

        $order_id = false;

        // Existing WooCommerce order of own type was provided
        if (is_a($object, 'WC_Order') && $object->get_id() && $object->get_type() === $controller->get_order_type()) {
            $order_id = $object->get_id();
        }
        // Existing object of own class was provided
        else if (is_a($object, get_class($this)) && $object->get_id()) {
            $order_id = $object->get_id();
        }
        // Numeric identifier was provided
        else if (is_numeric($object) && RightPress_Help::is_whole_number($object) && $object >= 0) {
            $order_id = $object;
        }

        // Load existing WooCommerce order
        if ($order_id !== false && $order_id > 0) {

            // Load order
            $wc_order = WC()->order_factory->get_order($order_id);

            // Set WooCommerce object property
            $this->wc_object = $wc_order;
        }
        // Create new WooCommerce order
        else if ($order_id !== false) {

            // Get order type data
            if ($order_type_data = wc_get_order_type($controller->get_order_type())) {

                // Create new order of own type
                $wc_order = new $order_type_data['class_name'];

                // Set WooCommerce object property
                $this->wc_object = $wc_order;
            }
        }

        // We must have a valid WooCommerce order object by now
        if (!is_a($this->get_wc_object(), 'WC_Order') || $this->get_wc_object()->get_type() !== $controller->get_order_type()) {
            throw new RightPress_Exception($controller->prefix_error_code('unable_to_load_wc_order'), 'Unable to load WooCommerce order object of required type.');
        }

        // Call parent constructor
        parent::__construct($object, $data_store, $controller);
    }


    /**
     * =================================================================================================================
     * GETTERS
     * =================================================================================================================
     */

    /**
     * Get WooCommerce order
     *
     * Alias for get_wc_object()
     *
     * @access public
     * @return object
     */
    public function get_wc_order()
    {

        return $this->get_wc_object();
    }

    /**
     * Get order status
     *
     * @access public
     * @param string $context
     * @return string
     */
    public function get_status($context = 'view')
    {

        return $this->get_wc_order()->get_status($context);
    }

    /**
     * Checks the order status against a passed in status
     *
     * @access public
     * @param array|string $status
     * @return bool
     */
    public function has_status($status)
    {

        return $this->get_wc_order()->has_status($status);
    }

    /**
     * Get customer id
     *
     * @access public
     * @param string $context
     * @return string
     */
    public function get_customer_id($context = 'view')
    {

        return $this->get_wc_order()->get_customer_id($context);
    }

    /**
     * Get customer note
     *
     * @access public
     * @param string $context
     * @return string
     */
    public function get_customer_note($context = 'view')
    {

        return $this->get_wc_order()->get_customer_note($context);
    }

    /**
     * Get billing first name
     *
     * @access public
     * @param string $context
     * @return string
     */
    public function get_billing_first_name($context = 'view')
    {

        return $this->get_wc_order()->get_billing_first_name($context);
    }

    /**
     * Get billing last name
     *
     * @access public
     * @param string $context
     * @return string
     */
    public function get_billing_last_name($context = 'view')
    {

        return $this->get_wc_order()->get_billing_last_name($context);
    }

    /**
     * Get billing email
     *
     * @access public
     * @param string $context
     * @return string
     */
    public function get_billing_email($context = 'view')
    {

        return $this->get_wc_order()->get_billing_email($context);
    }

    /**
     * Get billing phone
     *
     * @access public
     * @param string $context
     * @return string
     */
    public function get_billing_phone($context = 'view')
    {

        return $this->get_wc_order()->get_billing_phone($context);
    }

    /**
     * Get formatted billing full name
     *
     * @access public
     * @return string
     */
    public function get_formatted_billing_full_name()
    {

        return $this->get_wc_order()->get_formatted_billing_full_name();
    }

    /**
     * Get items
     *
     * @access public
     * @param array|string $types
     * @return array
     */
    public function get_items($types = 'line_item')
    {

        return $this->get_wc_order()->get_items($types);
    }

    /**
     * Get totals for display on pages and in emails
     *
     * @access public
     * @param string $tax_display
     * @return array
     */
    public function get_order_item_totals($tax_display = '')
    {

        return $this->get_wc_order()->get_order_item_totals($tax_display);
    }

    /**
     * Gets line subtotal - formatted for display
     *
     * @access public
     * @param object $item
     * @param string $tax_display
     * @return string
     */
    public function get_formatted_line_subtotal($item, $tax_display = '')
    {

        return $this->get_wc_order()->get_formatted_line_subtotal($item, $tax_display);
    }

    /**
     * Get a formatted billing address for the order
     *
     * @access public
     * @param string $empty_content
     * @return string
     */
    public function get_formatted_billing_address($empty_content = '')
    {

        return $this->get_wc_order()->get_formatted_billing_address($empty_content);
    }

    /**
     * Get a formatted shipping address for the order
     *
     * @access public
     * @param string $empty_content
     * @return string
     */
    public function get_formatted_shipping_address($empty_content = '')
    {

        return $this->get_wc_order()->get_formatted_shipping_address($empty_content);
    }

    /**
     * Checks if an order needs display the shipping address, based on shipping method
     *
     * @access public
     * @return bool
     */
    public function needs_shipping_address()
    {

        return $this->get_wc_order()->needs_shipping_address();
    }

    /**
     * Get downloadable items
     *
     * @access public
     * @return array
     */
    public function get_downloadable_items()
    {

        return $this->get_wc_order()->get_downloadable_items();
    }

    /**
     * Returns true if the order contains a downloadable product
     *
     * @access public
     * @return bool
     */
    public function has_downloadable_item()
    {

        return $this->get_wc_order()->has_downloadable_item();
    }

    /**
     * Checks if product download is permitted
     *
     * @access public
     * @return bool
     */
    public function is_download_permitted()
    {

        return $this->get_wc_order()->is_download_permitted();
    }


    /**
     * =================================================================================================================
     * SETTERS
     * =================================================================================================================
     */


    /**
     * =================================================================================================================
     * SANITIZERS - VALIDATORS
     * =================================================================================================================
     */


    /**
     * =================================================================================================================
     * OTHER METHODS
     * =================================================================================================================
     */

    /**
     * Add order cross reference
     *
     * Note: Own object has to be saved explicitly after calling this method; WooCommerce order is saved from here
     *
     * @access protected
     * @param int $order_id
     * @return void
     */
    protected function add_order_cross_reference($order_id)
    {

        // Add order id to subscription meta
        $this->add_meta('related_order', $order_id);

        // Add subscription id to order meta
        RightPress_WC::order_add_meta_data($order_id, $this->get_controller()->prefix_database_key('related_subscription'), $this->get_id());
    }





}
