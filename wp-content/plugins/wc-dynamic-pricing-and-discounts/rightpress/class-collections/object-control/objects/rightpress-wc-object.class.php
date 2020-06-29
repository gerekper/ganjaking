<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-object.class.php';

/**
 * WooCommerce Object Wrapper Class
 *
 * @class RightPress_WC_Object
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WC_Object extends RightPress_Object
{

    protected $wc_object = null;

    // Common properties
    protected $common_data = array(
        'updated'           => null,
        'plugin_version'    => null,
    );

    // Common datetime properties
    protected $common_datetime_properties = array(
        'updated',
    );

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

        // Call parent constructor
        parent::__construct($object, $data_store, $controller);
    }


    /**
     * =================================================================================================================
     * GETTERS
     * =================================================================================================================
     */

    /**
     * Get WooCommerce object
     *
     * @access public
     * @return object
     */
    public function get_wc_object()
    {
        return $this->wc_object;
    }

    /**
     * Get id
     *
     * @access public
     * @return int
     */
    public function get_id()
    {
        return $this->get_wc_object()->get_id();
    }

    /**
     * Get updated datetime
     *
     * @access public
     * @param string $context
     * @param array $args
     * @return string
     */
    public function get_updated($context = 'view', $args = array())
    {
        return $this->get_datetime_property('updated', $context, $args);
    }

    /**
     * Get plugin version
     *
     * @access public
     * @param string $context
     * @param array $args
     * @return string
     */
    public function get_plugin_version($context = 'view', $args = array())
    {
        return $this->get_property('plugin_version', $context, $args);
    }

    /**
     * =================================================================================================================
     * SETTERS
     * =================================================================================================================
     */

    /**
     * Set updated datetime
     *
     * Throws RightPress_Exception in case of an error
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function set_updated($value)
    {
        // Sanitize and validate value
        $value = $this->sanitize_updated($value);

        // Set property
        $this->set_property('updated', $value);
    }

    /**
     * Set plugin version
     *
     * Throws RightPress_Exception in case of an error
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function set_plugin_version($value)
    {
        // Sanitize and validate value
        $value = $this->sanitize_plugin_version($value);

        // Set property
        $this->set_property('plugin_version', $value);
    }

    /**
     * =================================================================================================================
     * SANITIZERS - VALIDATORS
     * =================================================================================================================
     */

    /**
     * Sanitize and validate updated datetime
     *
     * Throws RightPress_Exception in case of an error
     *
     * @access public
     * @param string $value
     * @return string
     */
    public function sanitize_updated($value)
    {

        // Sanitize datetime
        $value = $this->sanitize_past_datetime($value, 'updated');

        // Return sanitized value
        return $value;
    }

    /**
     * Sanitize and validate plugin version
     *
     * Throws RightPress_Exception in case of an error
     *
     * @access public
     * @param string $value
     * @return string
     */
    public function sanitize_plugin_version($value)
    {

        // Sanitize string
        $value = $this->sanitize_string($value, 'plugin_version');

        // Return sanitized value
        return $value;
    }

    /**
     * =================================================================================================================
     * OTHER METHODS
     * =================================================================================================================
     */

    /**
     * Save object
     *
     * @access public
     * @return int
     */
    public function save()
    {

        // Check if this is a new object
        $is_new = !$this->get_id();

        // Call parent method
        parent::save();

        // Save WooCommerce object
        $this->get_wc_object()->save();

        // New object handling
        if ($is_new) {

            // Update post title
            wp_update_post(array(
                'ID'            => $this->get_id(),
                'post_title'    => str_replace('_', ' ', $this->get_controller()->get_object_class()),
            ));

            // Update local id
            $this->id = $this->get_id();
        }

        // Return object id
        return $this->get_id();
    }

    /**
     * Clear object
     *
     * @access public
     * @return void
     */
    public function clear()
    {

        // Let developers know
        do_action($this->get_controller()->prefix_public_hook('before_clear'), $this);

        // Delete entry
        $this->data_store->clear($this);

        // Save WooCommerce object
        $this->get_wc_object()->save();
    }





}
