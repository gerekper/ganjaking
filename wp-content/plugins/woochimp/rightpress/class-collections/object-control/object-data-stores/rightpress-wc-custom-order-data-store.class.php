<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'interfaces/rightpress-wc-custom-order-data-store-interface.php';

/**
 * WooCommerce Custom Order Data Store
 *
 * @class RightPress_WC_Custom_Order_Data_Store
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WC_Custom_Order_Data_Store extends WC_Order_Data_Store_CPT implements WC_Object_Data_Store_Interface, WC_Order_Data_Store_Interface, RightPress_WC_Custom_Order_Data_Store_Interface
{

    // Define custom internal meta key and property key pairs
    protected $rightpress_meta_keys_to_property_keys = array(

    );

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Add custom internal meta keys
        $this->internal_meta_keys = array_merge($this->internal_meta_keys, array_keys($this->rightpress_meta_keys_to_property_keys));
    }

    /**
     * Read order data
     *
     * @access protected
     * @param object $order
     * @param object $post_object
     * @return void
     */
    protected function read_order_data(&$order, $post_object)
    {

        // Call parent method
        parent::read_order_data($order, $post_object);

        // Store properties to set
        $properties = array();

        // Iterate over property keys
        foreach ($this->rightpress_meta_keys_to_property_keys as $internal_meta_key => $property_key) {

            // Get property value to set
            $properties[$property_key] = get_post_meta($order->get_id(), $internal_meta_key, true);
        }

        // Set properties
        $order->set_props($properties);
    }

    /**
     * Update post meta
     *
     * @access protected
     * @param object $order
     * @return void
     */
    protected function update_post_meta(&$order)
    {

        // Call parent method
        parent::update_post_meta($order);

        // Get properties to update
        $properties_to_update = $this->get_props_to_update($order, $this->rightpress_meta_keys_to_property_keys);

        // Iterate over properties to update
        foreach ($properties_to_update as $internal_meta_key => $property_key) {

            // Get value
            $value = $order->{"get_$property_key"}('edit');

            // Update post meta
            update_post_meta($order->get_id(), $internal_meta_key, $value);
        }
    }





}
