<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'interfaces/rightpress-object-interface.php';

/**
 * RightPress_Object
 *  > RightPress_WP_Object
 *     > RightPress_WP_Custom_Object
 *     > RightPress_WP_Custom_Post_Object
 *        > RightPress_WP_Log_Entry
 *  > RightPress_WC_Object
 *     > RightPress_WC_Product_Object
 *     > RightPress_WC_Custom_Order_Object
 * RightPress_WC_Custom_Order extends WC_Order
 */

// TODO: Any way to prevent multiple requests/processes modifying the same object at the same time and overwriting each others' changes? E.g. throw exception from save() after comparing current objects initial data to newly fetched data?

/**
 * Generic Object Class
 *
 * @class RightPress_Object
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Object implements RightPress_Object_Interface
{

    protected $id           = 0;
    protected $data_ready   = false;
    protected $default_data = array();
    protected $changes      = array();
    protected $meta_data    = null;
    protected $controller   = null;
    protected $data_store   = null;
    protected $log_entry    = null;

    // Properties with default values
    protected $data = array();

    // Properties flagged as datetime
    protected $datetime_properties = array();

    // Common properties
    protected $common_data                  = array();
    protected $common_datetime_properties   = array();

    // DateTime class to use
    protected $datetime_class = 'RightPress_DateTime';

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

        // Merge common data
        $this->data                 = array_merge($this->common_data, $this->data);
        $this->datetime_properties  = array_merge($this->common_datetime_properties, $this->datetime_properties);

        // Set default data
        $this->default_data = $this->data;

        // Set data store
        $this->set_data_store($data_store);

        // Set controller
        $this->set_controller($controller);

        // Set id
        if (is_numeric($object) && $object > 0) {
            $this->set_id($object);
        }
        else if (is_a($object, get_class($this))) {
            $this->set_id($object->get_id());
        }
        else {
            $this->set_data_ready(true);
        }

        // Read data
        if ($this->get_id() > 0) {
            $this->data_store->read($this);
        }
    }

    // TODO: Maybe add some magic methods (e.g. isset could check if property is set under data)

    /**
     * Set data store
     *
     * @access protected
     * @param object $data_store
     * @return void
     */
    protected function set_data_store($data_store)
    {

        $this->data_store = $data_store;
    }

    /**
     * Get data store
     *
     * @access public
     * @return object
     */
    public function get_data_store()
    {

        return $this->data_store;
    }

    /**
     * Set controller
     *
     * @access protected
     * @param object $controller
     * @return void
     */
    public function set_controller($controller)
    {

        $this->controller = $controller;
    }

    /**
     * Get controller
     *
     * @access public
     * @return object
     */
    public function get_controller()
    {

        return $this->controller;
    }

    /**
     * Set id
     *
     * @access public
     * @param int $id
     * @return void
     */
    public function set_id($id)
    {

        $this->id = absint($id);
    }

    /**
     * Get id
     *
     * @access public
     * @return int
     */
    public function get_id()
    {

        return $this->id;
    }

    /**
     * Set data ready
     *
     * @access public
     * @param bool $ready
     * @return void
     */
    public function set_data_ready($ready)
    {

        $this->data_ready = (bool) $ready;
    }

    /**
     * Check if data is ready
     *
     * @access public
     * @return bool
     */
    public function is_data_ready()
    {

        return $this->data_ready;
    }

    /**
     * Get default data
     *
     * @access public
     * @return array
     */
    public function get_default_data()
    {

        return $this->default_data;
    }

    /**
     * Get all data
     *
     * @access public
     * @return array
     */
    public function get_data()
    {

        // Merge properties and return
        return array_merge(array('id' => $this->get_id()), $this->data, array('meta_data' => $this->get_meta_data()));
    }

    /**
     * Get data keys
     *
     * @access public
     * @return array
     */
    public function get_data_keys()
    {

        return array_keys($this->data);
    }

    /**
     * Reset all data
     *
     * @access public
     * @return array
     */
    public function reset_data()
    {

        $this->data = $this->get_default_data();
        $this->changes = array();
        $this->set_data_ready(false);
    }

    /**
     * Get changes
     *
     * @access public
     * @return array
     */
    public function get_changes()
    {

        return $this->changes;
    }

    /**
     * Apply changes
     *
     * @access public
     * @return void
     */
    public function apply_changes()
    {

        // Merge data
        $this->data = array_replace_recursive($this->data, $this->changes);

        // Reset changes array
        $this->changes = array();
    }

    /**
     * Get property
     *
     * Accepted $context values:
     * - view    get value with filters applied
     * - edit    get value without filters applied
     * - store   get value prepared to be stored in database
     *
     * @access public
     * @param string $key
     * @param string $context
     * @param array $args
     * @return mixed
     */
    public function get_property($key, $context = 'view', $args = array())
    {

        $value = null;

        // Check if requested key exists
        if (array_key_exists($key, $this->data)) {

            // Get value from changes if set, otherwise get from main data array
            $value = array_key_exists($key, $this->changes) ? $this->changes[$key] : $this->data[$key];

            // Allow developers to override
            if ($context === 'view') {
                $value = apply_filters($this->get_controller()->prefix_public_hook('get_property_' . $key), $value, $this);
            }
        }

        return $value;
    }

    /**
     * Get datetime property
     *
     * Clones object to avoid accidental changes after value is returned (objects are passed by reference)
     *
     * @access public
     * @param string $key
     * @param string $context
     * @param array $args
     * @return object|string
     */
    public function get_datetime_property($key, $context = 'view', $args = array())
    {

        // Import arguments
        extract(RightPress_Help::filter_by_keys_with_defaults($args, array('is_gmt' => true)));

        // Get value
        $value = $this->get_property($key, $context, $args);

        // Maybe prepare value to be stored in database
        if ($context === 'store' && is_a($value, 'DateTime')) {
            $value = $is_gmt ? gmdate('Y-m-d H:i:s', $value->getTimestamp()) : $value->format('Y-m-d H:i:s');
        }

        // Clone object
        if (is_object($value)) {
            $value = clone $value;
        }

        // Return value
        return $value;
    }

    /**
     * Set property
     *
     * Throws RightPress_Exception in case of an error
     *
     * @access protected
     * @param string $key
     * @param mixed $value
     * @return void
     */
    protected function set_property($key, $value)
    {

        // Allow developers to override value
        $value = apply_filters($this->get_controller()->prefix_public_hook('pre_set_property'), $value, $key, $this);
        $value = apply_filters($this->get_controller()->prefix_public_hook('pre_set_property_' . $key), $value, $this);

        // Property is defined
        if (array_key_exists($key, $this->data)) {

            // Setting new value after initial object load
            if ($this->is_data_ready()) {

                // Get current value
                $current_value = array_key_exists($key, $this->changes) ? $this->changes[$key] : $this->data[$key];

                // New value is different from current value
                if ($value !== $current_value) {

                    // Set new value
                    $this->changes[$key] = $value;

                    // Trigger action
                    do_action($this->get_controller()->prefix_public_hook('set_property_' . $key), $value, $this);
                }
            }
            // Setting existing value during initial object load
            else {
                $this->data[$key] = $value;
            }
        }
    }

    /**
     * Set multiple properties
     *
     * Throws RightPress_Exception in case of an error
     *
     * @access public
     * @param array $properties
     * @return void
     */
    public function set_properties($properties)
    {

        // Iterate over properties
        foreach ($properties as $key => $value) {

            // Property does not exist
            if (!array_key_exists($key, $this->data)) {
                throw new RightPress_Exception($this->get_controller()->prefix_error_code('property_does_not_exist'), 'Property "' . strval($key) . '" does not exist.');
            }

            // Set property
            $setter = 'set_' . $key;
            $this->{$setter}($value);
        }
    }

    /**
     * Check if property is of datetime type
     *
     * @access public
     * @param string $key
     * @return bool
     */
    public function is_property_datetime($key)
    {

        return in_array($key, $this->datetime_properties, true);
    }

    /**
     * Save object
     *
     * @access public
     * @return int
     */
    public function save()
    {

        // Trigger before save action
        do_action($this->get_controller()->prefix_public_hook('before_save'), $this);

        // Existing object
        if ($this->get_id() > 0) {

            // Trigger before update action
            do_action($this->get_controller()->prefix_public_hook('before_update'), $this);

            // Update existing object
            $this->data_store->update($this);

            // Trigger updated action
            do_action($this->get_controller()->prefix_public_hook('updated'), $this);
        }
        // New object
        else {

            // Trigger before create action
            do_action($this->get_controller()->prefix_public_hook('before_create'), $this);

            // Create new object
            $this->data_store->create($this);

            // Trigger created action
            do_action($this->get_controller()->prefix_public_hook('created'), $this);
        }

        // Trigger saved action
        do_action($this->get_controller()->prefix_public_hook('saved'), $this);

        // Return object id
        return $this->get_id();
    }

    /**
     * Delete object
     *
     * @access public
     * @param bool $permanently
     * @return void
     */
    public function delete($permanently = false)
    {

        // Trigger before delete action
        do_action($this->get_controller()->prefix_public_hook('before_delete'), $this);

        // Delete entry
        $this->data_store->delete($this, array('permanently' => $permanently));

        // Trigger deleted action
        do_action($this->get_controller()->prefix_public_hook('deleted'), $this);

        // Reset id
        $this->set_id(0);
    }

    /**
     * Get all meta data
     *
     * @access public
     * @return array
     */
    public function get_meta_data()
    {
        // Maybe read meta data
        $this->maybe_read_meta_data();

        // Return all meta data that have value set
        return array_filter($this->meta_data, function($meta) {
            return $meta->value !== null;
        });
    }

    /**
     * Save all meta data
     *
     * @access public
     * @return void
     */
    public function save_meta_data()
    {
        // Meta not supported
        if (!$this->get_controller()->supports_metadata()) {
            return;
        }

        // Meta not loaded
        if ($this->meta_data === null) {
            return;
        }

        // Iterate over meta
        foreach ($this->meta_data as $index => $meta) {

            // Delete meta entry
            if ($meta->value === null) {

                // Check if meta entry was previously stored
                if ($meta->id !== null) {
                    $this->data_store->delete_meta($this, $meta);
                    unset($this->meta_data[$index]);
                }
            }
            // Add new meta entry
            else if ($meta->id === null) {
                $meta->id = $this->data_store->add_meta($this, $meta);
                $meta->apply_changes();
            }
            // Update existing meta entry
            else if ($meta->get_changes()) {
                $this->data_store->update_meta($this, $meta);
                $meta->apply_changes();
            }
        }
    }

    /**
     * Maybe read meta data
     *
     * @access protected
     * @return void
     */
    protected function maybe_read_meta_data()
    {
        if ($this->meta_data === null) {
            $this->read_meta_data();
        }
    }

    /**
     * Read meta data
     *
     * @access public
     * @return void
     */
    public function read_meta_data()
    {
        $this->meta_data = array();

        // Meta not supported
        if (!$this->get_controller()->supports_metadata()) {
            return;
        }

        // Object is new
        if (!$this->get_id()) {
            return;
        }

        // Read meta data
        $this->meta_data = $this->data_store->read_meta($this);
    }

    /**
     * Add meta
     *
     * @access public
     * @param string $key
     * @param mixed $value
     * @return bool $unique
     * @return void
     */
    public function add_meta($key, $value, $unique = false)
    {

        // Check metadata support
        if (!$this->check_metadata_support()) {
            return;
        }

        // Maybe read meta data
        $this->maybe_read_meta_data();

        // Handle unique meta
        if ($unique) {
            $this->delete_meta($key);
        }

        // Add meta
        $this->meta_data[] = new RightPress_Meta(array(
            'key'   => $key,
            'value' => $value,
        ));
    }

    /**
     * Update meta
     *
     * @access public
     * @param string $key
     * @param mixed $value
     * @param int $meta_id
     * @return void
     */
    public function update_meta($key, $value, $meta_id = null)
    {
        // Check metadata support
        if (!$this->check_metadata_support()) {
            return;
        }

        // Maybe read meta data
        $this->maybe_read_meta_data();

        // Get meta index if meta id was provided
        if ($meta_id !== null) {
            $meta_data_keys = array_keys(wp_list_pluck($this->meta_data, 'id'), $meta_id);
            $index = current($meta_data_keys);
        }
        else {
            $index = null;
        }

        // Update single meta entry by index
        if (is_numeric($index)) {
            $meta = $this->meta_data[$index];
            $meta->key = $key;
            $meta->value = $value;
        }
        // Update meta by replacing any existing entries with new one
        else {
            $this->add_meta($key, $value, true);
        }
    }

    /**
     * Get meta
     *
     * @access public
     * @param string $key
     * @param bool $single
     * @param string $context
     * @return mixed
     */
    public function get_meta($key, $single = true, $context = 'view')
    {

        // Default value
        $value = $single ? '' : array();

        // Check metadata support
        if (!$this->check_metadata_support()) {
            return $value;
        }

        // Get all meta data
        $meta_data = $this->get_meta_data();

        // Get meta indexes by key
        $indexes = array_keys(wp_list_pluck($meta_data, 'key'), $key);

        // Check if meta exists
        if (!empty($indexes)) {

            // Get single value
            if ($single) {
                $value = $meta_data[current($indexes)]->value;
            }
            // Get multiple values as array
            else {
                $value = array_intersect_key($meta_data, array_flip($indexes));
            }
        }

        // Allow developers to override and return
        return apply_filters($this->get_controller()->prefix_public_hook('get_meta_' . $key), $value, $single, $context, $this);
    }

    /**
     * Delete meta
     *
     * @access public
     * @param string $key
     * @return void
     */
    public function delete_meta($key)
    {
        // Check metadata support
        if (!$this->check_metadata_support()) {
            return;
        }

        // Maybe read meta data
        $this->maybe_read_meta_data();

        // Get meta indexes by key
        $indexes = array_keys(wp_list_pluck($this->meta_data, 'key'), $key);

        // Unset meta by indexes
        foreach ($indexes as $index) {
            $this->meta_data[$index]->value = null;
        }
    }

    /**
     * Check if meta exists
     *
     * @access public
     * @param string $key
     * @return bool
     */
    public function meta_exists($key)
    {
        // Check metadata support
        if (!$this->check_metadata_support()) {
            return false;
        }

        // Check metadata support
        $this->check_metadata_support();

        // Maybe read meta data
        $this->maybe_read_meta_data();

        // Check if meta key exists
        return in_array($key, wp_list_pluck($this->get_meta_data(), 'key'), true);
    }

    /**
     * Check if object supports metadata
     *
     * Throws exception if metadata is not supported
     *
     * @access public
     * @return bool
     */
    public function check_metadata_support()
    {
        // Check metadata support
        $supports_metadata = $this->get_controller()->supports_metadata();

        // Add warning if metadata is not supported but someone is trying to use it
        if (!$supports_metadata) {
            RightPress_Help::doing_it_wrong((get_class($this) . '::' . __FUNCTION__), 'Metadata is not supported for objects of class ' . get_class($this) . '.', '1.0');
        }

        return $supports_metadata;
    }


    /**
     * =================================================================================================================
     * GENERIC OBJECT PROPERTY SANITIZERS
     * =================================================================================================================
     */

    /**
     * Sanitize int
     *
     * Throws error in case value is neither whole number, nor null
     *
     * @access public
     * @param mixed $value
     * @param string $key
     * @return mixed
     */
    public function sanitize_int($value, $key)
    {

        // Convert empty string to null
        if ($value === '') {
            $value = null;
        }

        // Value is whole number
        if (RightPress_Help::is_whole_number($value)) {

            // Trim string
            if (is_string($value)) {
                $value = trim($value);
            }

            // Cast to int
            $value = (int) $value;
        }
        // Value is neither whole number, nor null
        else if ($value !== null) {
            throw new RightPress_Exception($this->get_controller()->prefix_error_code("invalid_integer_property_value"), "Invalid integer value for property {$key}.");
        }

        return $value;
    }

    /**
     * Sanitize float
     *
     * Throws error in case value is neither number, nor null
     *
     * @access public
     * @param mixed $value
     * @param string $key
     * @return mixed
     */
    public function sanitize_float($value, $key)
    {

        // Convert empty string to null
        if ($value === '') {
            $value = null;
        }

        // Value is numeric
        if (is_numeric($value)) {

            // Sanitize numeric string
            if (is_string($value)) {

                // Trim spaces
                $value = trim($value);

                // Normalize decimal expression
                $value = RightPress_Help::normalize_string_decimal($value);
            }

            // Cast to float
            $value = (float) $value;
        }
        // Value is neither numeric, nor null
        else if ($value !== null) {
            throw new RightPress_Exception($this->get_controller()->prefix_error_code("invalid_float_property_value"), "Invalid float value for property {$key}.");
        }

        return $value;
    }

    /**
     * Sanitize string
     *
     * Throws error in case value is object, array or boolean
     *
     * @access public
     * @param mixed $value
     * @param string $key
     * @return mixed
     */
    public function sanitize_string($value, $key)
    {

        // Value is object, array or boolean
        if (in_array(gettype($value), array('object', 'array', 'boolean'), true)) {
            throw new RightPress_Exception($this->get_controller()->prefix_error_code("invalid_string_property_value"), "Invalid string value for property {$key}.");
        }

        // Trim spaces
        if (is_string($value)) {
            $value = trim($value);
        }

        // Cast to string
        if ($value !== null) {
            $value = (string) $value;
        }

        return $value;
    }

    /**
     * Sanitize datetime
     *
     * Accepts as value:
     * - RightPress_DateTime object with a correct timezone already set
     * - Unix timestamp
     * - ISO 8601 string which must define timezone offset, e.g. '2018-06-17T23:17:28+01:00'
     * - MySQL datetime which must be in GMT, e.g. '2018-06-17 23:17:28'
     *
     * Throws error in case value is not valid datetime representation
     *
     * @access public
     * @param mixed $value
     * @param string $key
     * @return object|null
     */
    public function sanitize_datetime($value, $key)
    {

        // Convert empty string and '0000-00-00 00:00:00' to null
        if ($value === '' || $value === '0000-00-00 00:00:00') {
            $value = null;
        }

        // Convert non-empty value to datetime object
        if ($value !== null) {

            // Value is RightPress_DateTime
            // Note: We clone provided datetime object value to avoid accidental changes since objects are passed by reference
            if (is_a($value, 'RightPress_DateTime')) {
                $value = clone $value;
            }
            // Value is ISO 8601 string
            else if (RightPress_Help::is_date($value, DATE_ATOM)) {
                $value = new $this->datetime_class($value);
            }
            // Value is MySQL datetime (must be in GMT)
            else if (RightPress_Help::is_date($value, 'Y-m-d H:i:s')) {
                $value = new $this->datetime_class($value);
            }
            // Value is timestamp
            else if (is_numeric($value)) {
                $value = new $this->datetime_class('@' . $value);
            }
            // Value is not supported
            else {
                throw new RightPress_Exception($this->get_controller()->prefix_error_code("invalid_$key"), "Value for property $key is not of supported format.");
            }
        }

        return $value;
    }

    /**
     * Sanitize future datetime
     *
     * Alias for sanitize_datetime() with extra check for future datetime
     *
     * Throws error in case value is not valid datetime representation
     *
     * @access public
     * @param mixed $value
     * @param string $key
     * @return object|null
     */
    public function sanitize_future_datetime($value, $key)
    {

        // Sanitize datetime
        $value = $this->sanitize_datetime($value, $key);

        // Value must be in the future
        // Note: We only check this on new changes since when loading existing object data future datetime may become past datetime after some time
        if ($this->is_data_ready() && $value !== null && $value <= (new $this->datetime_class())) {
            throw new RightPress_Exception($this->get_controller()->prefix_error_code("invalid_$key"), "Datetime value for property $key must be in the future.");
        }

        return $value;
    }

    /**
     * Sanitize past datetime
     *
     * Alias for sanitize_datetime() with extra check for past datetime
     *
     * For practical purposes current datetime is also treated as valid (since it will be in the past momentarily)
     *
     * Throws error in case value is not valid datetime representation
     *
     * @access public
     * @param mixed $value
     * @param string $key
     * @return object|null
     */
    public function sanitize_past_datetime($value, $key)
    {

        // Sanitize datetime
        $value = $this->sanitize_datetime($value, $key);

        // Value must not be in the future
        if ($value !== null && $value > (new $this->datetime_class())) {
            throw new RightPress_Exception($this->get_controller()->prefix_error_code("invalid_$key"), "Datetime value for property $key must not be in the future.");
        }

        return $value;
    }


    /**
     * =================================================================================================================
     * LOG ENTRY METHODS
     * =================================================================================================================
     */

    /**
     * Set log entry object
     *
     * @access public
     * @param object $log_entry
     * @return void
     */
    public function set_log_entry($log_entry)
    {

        // Invalid log entry object
        if (!is_a($log_entry, 'RightPress_WP_Log_Entry')) {
            return;
        }

        // Set log entry object
        $this->log_entry = $log_entry;
    }

    /**
     * Unset log entry object
     *
     * @access public
     * @return void
     */
    public function unset_log_entry()
    {

        $this->log_entry = null;
    }

    /**
     * Get log entry object
     *
     * @access public
     * @return object
     */
    public function get_log_entry()
    {

        return $this->log_entry;
    }

    /**
     * Check if object has log entry set
     *
     * @access public
     * @return bool
     */
    public function has_log_entry()
    {

        return (bool) $this->log_entry;
    }

    /**
     * Update log entry status
     *
     * @access public
     * @param string $status
     * @return void
     */
    public function update_log_entry_status($status)
    {

        // Check if log entry is set
        if ($this->get_log_entry()) {

            // Update status
            $this->get_log_entry()->update_status($status);
        }
    }

    /**
     * Add log entry note
     *
     * Alias for add_log_entry_property() for adding notes
     *
     * @access public
     * @param array|string $note
     * @return void
     */
    public function add_log_entry_note($note)
    {

        $this->add_log_entry_property('note', $note);
    }

    /**
     * Add log entry property
     *
     * Logger must have method add_{$key}
     *
     * @access public
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function add_log_entry_property($key, $value)
    {

        // Check if log entry is set
        if ($this->get_log_entry()) {

            // Format method name
            $method = 'add_' . $key;

            // Method is not defined
            if (!method_exists($this->get_log_entry(), $method)) {
                throw new RightPress_Exception($this->get_controller()->prefix_error_code("invalid_log_entry_property_key"), "Undefined log entry property key {$key}.");
            }

            // Add log entry property
            $this->get_log_entry()->$method($value);
        }
    }





}
