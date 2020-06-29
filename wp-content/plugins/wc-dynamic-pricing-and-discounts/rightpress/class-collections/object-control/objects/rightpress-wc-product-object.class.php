<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-wc-object.class.php';

/**
 * WooCommerce Product Wrapper Class
 *
 * Used to build our own object (e.g. subscription product, booking product) around a regular WooCommerce product object
 *
 * @class RightPress_WC_Product_Object
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WC_Product_Object extends RightPress_WC_Object
{

    /**
     * Constructor
     *
     * Note: Currently this is not designed to create new WooCommerce products - this only works with WooCommerce products that already exist
     *
     * @access public
     * @param mixed $object
     * @param object $data_store
     * @param object $controller
     * @return void
     */
    public function __construct($object, $data_store, $controller)
    {

        $product_id = false;

        // Existing WooCommerce product object was provided
        if (is_a($object, 'WC_Product')) {
            $product_id = $object->get_id();
        }
        // Existing object of own class was provided
        else if (is_a($object, get_class($this)) && $object->get_id()) {
            $product_id = $object->get_id();
        }
        // Numeric identifier of existing product was provided
        else if (is_numeric($object) && RightPress_Help::is_whole_number($object) && $object > 0) {
            $product_id = $object;
        }

        // Load existing WooCommerce product object
        if ($product_id) {
            $this->wc_object = wc_get_product($product_id);
        }

        // We must have a valid WooCommerce product object by now
        if (!is_a($this->get_wc_object(), 'WC_Product')) {
            throw new RightPress_Exception($controller->prefix_error_code('unable_to_load_wc_product'), 'Unable to load WooCommerce product object.');
        }

        // Make sure product is not variable, grouped etc.
        if (RightPress_Help::wc_product_has_children($this->get_wc_product())) {
            throw new RightPress_Exception($controller->prefix_error_code('product_has_children'), 'RightPress: RightPress_WC_Product_Object must not be constructed from a variable, grouped or similar product that has children. Product #' . $this->get_wc_product()->get_id() . ' provided.');
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
     * Get WooCommerce product
     *
     * Alias for get_wc_object()
     *
     * @access public
     * @return object
     */
    public function get_wc_product()
    {

        return $this->get_wc_object();
    }

    /**
     * Get price
     *
     * @access public
     * @param string $context
     * @return string
     */
    public function get_price($context = 'view')
    {

        return $this->get_wc_product()->get_price($context);
    }

    /**
     * Get sale price
     *
     * @access public
     * @param string $context
     * @return string
     */
    public function get_sale_price($context = 'view')
    {

        return $this->get_wc_product()->get_sale_price($context);
    }

    /**
     * Get regular price
     *
     * @access public
     * @param string $context
     * @return string
     */
    public function get_regular_price($context = 'view')
    {

        return $this->get_wc_product()->get_regular_price($context);
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





}
