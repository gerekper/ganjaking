<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-object.class.php';
require_once 'interfaces/rightpress-wp-object-interface.php';

/**
 * WordPress Object Class
 *
 * @class RightPress_WP_Object
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WP_Object extends RightPress_Object implements RightPress_WP_Object_Interface
{

    // Common properties
    protected $common_data = array(
        'plugin_version'    => null,
        'status'            => null,
        'created'           => null,
        'updated'           => null,
        'status_since'      => null,
    );

    // Common datetime properties
    protected $common_datetime_properties = array(
        'created',
        'updated',
        'status_since',
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

        // Get object class
        $object_class = $controller->get_object_class();

        // Identifier not valid
        if (!is_a($object, $object_class) && !is_numeric($object)) {
            throw new RightPress_Exception($this->get_controller()->prefix_error_code('invalid_object_identifier'), 'RightPress: Identifier for object of class ' . $object_class . ' is not valid.');
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
     * Get status
     *
     * @access public
     * @param string $context
     * @param array $args
     * @return string
     */
    public function get_status($context = 'view', $args = array())
    {

        // Get status
        $status = $this->get_property('status', $context, $args);

        // Set default status if object has no status
        if ($status === null && $this->is_data_ready()) {

            // Set default status
            // Note: We are not calling set_status() here since this would cause circular reference
            // as set_status() calls get_status() for some checks when transitioning from one status to another
            $this->set_property('status', $this->get_controller()->get_default_status());

            // Get status again
            $status = $this->get_property('status', $context, $args);
        }

        // Prefix status for storage
        if ($status && $context === 'store') {
            $status = $this->get_controller()->prefix_status($status);
        }

        // Return status
        return $status;
    }

    /**
     * Get created datetime
     *
     * @access public
     * @param string $context
     * @param array $args
     * @return string
     */
    public function get_created($context = 'view', $args = array())
    {

        return $this->get_datetime_property('created', $context, $args);
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
     * Get status since datetime
     *
     * @access public
     * @param string $context
     * @param array $args
     * @return string
     */
    public function get_status_since($context = 'view', $args = array())
    {

        return $this->get_datetime_property('status_since', $context, $args);
    }


    /**
     * =================================================================================================================
     * SETTERS
     * =================================================================================================================
     */

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
     * Set status
     *
     * Throws RightPress_Exception in case of an error
     *
     * @access public
     * @param string $new_status
     * @return void
     */
    public function set_status($new_status)
    {

        // Sanitize and validate value
        $new_status = $this->sanitize_status($new_status);

        // Get current status
        $current_status = $this->get_status('edit');

        // Status has not changed, nothing to do
        if ($new_status === $current_status) {
            return;
        }

        // Set property
        $this->set_property('status', $new_status);

        // Update status since datetime
        $this->set_status_since(time());
    }

    /**
     * Set created datetime
     *
     * Throws RightPress_Exception in case of an error
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function set_created($value)
    {

        // Sanitize and validate value
        $value = $this->sanitize_created($value);

        // Set property
        $this->set_property('created', $value);
    }

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
     * Set status since datetime
     *
     * Note:Status since datetime should only be updated through set_status()
     *
     * Throws RightPress_Exception in case of an error
     *
     * @access public
     * @param mixed $value
     * @return void
     */
    public function set_status_since($value)
    {

        // Sanitize and validate value
        $value = $this->sanitize_status_since($value);

        // Set property
        $this->set_property('status_since', $value);
    }


    /**
     * =================================================================================================================
     * SANITIZERS - VALIDATORS
     * =================================================================================================================
     */

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
     * Sanitize and validate status
     *
     * Throws RightPress_Exception in case of an error
     *
     * @access public
     * @param string $value
     * @return string
     */
    public function sanitize_status($value)
    {

        // Unprefix status string
        $value = $this->get_controller()->unprefix_status((string) $value);

        // Get status keys
        $status_keys = array_keys($this->get_controller()->get_status_list());

        // Validate value
        if (!in_array($value, array_merge($status_keys, array('trash', 'draft', 'auto-draft')), true)) {
            throw new RightPress_Exception($this->get_controller()->prefix_error_code('invalid_status'), 'Status "' . $value . '" is not valid.');
        }

        // Return sanitized value
        return $value;
    }

    /**
     * Sanitize and validate created datetime
     *
     * Throws RightPress_Exception in case of an error
     *
     * @access public
     * @param string $value
     * @return string
     */
    public function sanitize_created($value)
    {

        // Sanitize datetime
        $value = $this->sanitize_past_datetime($value, 'created');

        // Return sanitized value
        return $value;
    }

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
     * Sanitize status since datetime
     *
     * Throws RightPress_Exception in case of an error
     *
     * @access public
     * @param mixed $value
     * @return mixed
     */
    public function sanitize_status_since($value)
    {

        // Sanitize datetime
        $value = $this->sanitize_past_datetime($value, 'status_since');

        // Return sanitized value
        return $value;
    }


    /**
     * =================================================================================================================
     * OTHER METHODS
     * =================================================================================================================
     */

    /**
     * Check if property is taxonomy
     *
     * @access public
     * @param string $key
     * @return bool
     */
    public function is_property_taxonomy($key)
    {

        // Get all taxonomies for this object type
        $taxonomies = $this->get_controller()->get_taxonomies_with_terms();

        // Check if provided property is taxonomy
        return isset($taxonomies[$key]);
    }

    /**
     * Check if status matches one of the provided values
     *
     * @access public
     * @param array|string $statuses
     * @return bool
     */
    public function has_status($statuses = array())
    {

        return in_array($this->get_status(), (array) $statuses, true);
    }

    /**
     * Get status label
     *
     * @access public
     * @return string
     */
    public function get_status_label()
    {

        // Get all statuses
        $statuses = $this->get_controller()->get_status_list();

        // Return status label
        return apply_filters(($this->get_controller()->get_object_key() . '_status_label'), $statuses[$this->get_status('edit')]['label'], $this);
    }





}
