<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-wp-custom-post-object.class.php';

/**
 * WordPress Custom Post Based Log Entry Object
 *
 * @class RightPress_WP_Log_Entry
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WP_Log_Entry extends RightPress_WP_Custom_Post_Object
{

    protected $is_logging = false;

    // Define generic properties available for all log entries
    // Note: Child classes should re-define all these items when overriding $data
    protected $data = array(
        'actor_id'      => null,        // ID of user who performed the action; value is null in case action was performed by system
        'event_type'    => null,
        'notes'         => array(),
        'error_details' => null,
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
     * Get actor id
     *
     * @access public
     * @param string $context
     * @param array $args
     * @return int
     */
    public function get_actor_id($context = 'view', $args = array())
    {

        return $this->get_property('actor_id', $context, $args);
    }

    /**
     * Get event type
     *
     * @access public
     * @param string $context
     * @param array $args
     * @return string
     */
    public function get_event_type($context = 'view', $args = array())
    {

        return $this->get_property('event_type', $context, $args);
    }

    /**
     * Get notes
     *
     * @access public
     * @param string $context
     * @param array $args
     * @return array
     */
    public function get_notes($context = 'view', $args = array())
    {

        return $this->get_property('notes', $context, $args);
    }

    /**
     * Get error details
     *
     * @access public
     * @param string $context
     * @param array $args
     * @return string
     */
    public function get_error_details($context = 'view', $args = array())
    {

        return $this->get_property('error_details', $context, $args);
    }


    /**
     * =================================================================================================================
     * SETTERS
     * =================================================================================================================
     */

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

        // Get all statuses
        $statuses = $this->get_controller()->get_status_list();

        // Get current status
        $from_status = $this->get_status('edit');

        // Status change is not allowed
        if ($from_status !== null && !in_array($new_status, $statuses[$from_status]['system_change_to'], true)) {
            return;
        }

        // Call parent method
        parent::set_status($new_status);
    }

    /**
     * Set actor id
     *
     * Throws RightPress_Exception in case of an error
     *
     * @access public
     * @param int $value
     * @return void
     */
    public function set_actor_id($value)
    {

        // Sanitize and validate value
        $value = $this->sanitize_actor_id($value);

        // Set property
        $this->set_property('actor_id', $value);
    }

    /**
     * Set event type
     *
     * Throws RightPress_Exception in case of an error
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function set_event_type($value)
    {

        // Sanitize and validate value
        $value = $this->sanitize_event_type($value);

        // Set property
        $this->set_property('event_type', $value);
    }

    /**
     * Set notes
     *
     * Throws RightPress_Exception in case of an error
     *
     * @access public
     * @param array $value
     * @return void
     */
    public function set_notes($value)
    {

        // Sanitize and validate value
        $value = $this->sanitize_notes($value);

        // Set property
        $this->set_property('notes', $value);
    }

    /**
     * Set error details
     *
     * Throws RightPress_Exception in case of an error
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function set_error_details($value)
    {

        // Sanitize and validate value
        $value = $this->sanitize_error_details($value);

        // Set property
        $this->set_property('error_details', $value);
    }


    /**
     * =================================================================================================================
     * SANITIZERS - VALIDATORS
     * =================================================================================================================
     */

    /**
     * Sanitize and validate actor id
     *
     * Throws RightPress_Exception in case of an error
     *
     * @access public
     * @param int $value
     * @return string
     */
    public function sanitize_actor_id($value)
    {

        // Sanitize integer
        $value = $this->sanitize_int($value, 'actor_id');

        // No such user
        if ($value !== null && !get_userdata($value)) {
            throw new RightPress_Exception($this->get_controller()->prefix_error_code('invalid_actor_id'), esc_html__('Invalid log entry actor id.', 'rightpress'));
        }

        // Return sanitized value
        return $value;
    }

    /**
     * Sanitize and validate event type
     *
     * Throws RightPress_Exception in case of an error
     *
     * @access public
     * @param string $value
     * @return string
     */
    public function sanitize_event_type($value)
    {

        // Get all event types
        $event_types = $this->get_controller()->get_event_types();

        // Event type is not defined
        if (!isset($event_types[$value])) {
            throw new RightPress_Exception($this->get_controller()->prefix_error_code('invalid_event_type'), esc_html__('Invalid log entry event type.', 'rightpress'));
        }

        // Return sanitized value
        return $value;
    }

    /**
     * Sanitize and validate notes
     *
     * Throws RightPress_Exception in case of an error
     *
     * @access public
     * @param array $value
     * @return string
     */
    public function sanitize_notes($value)
    {

        // Cast value to array
        $value = (array) $value;

        // Return sanitized value
        return $value;
    }

    /**
     * Sanitize and validate error details
     *
     * Throws RightPress_Exception in case of an error
     *
     * @access public
     * @param string $value
     * @return string
     */
    public function sanitize_error_details($value)
    {

        // Sanitize string
        $value = $this->sanitize_string($value, 'error_details');

        // Return sanitized value
        return $value;
    }


    /**
     * =================================================================================================================
     * LOGGING METHODS
     * =================================================================================================================
     */

    /**
     * Start logging
     *
     * @access public
     * @return void
     */
    public function start_logging()
    {

        // Set flag
        $this->is_logging = true;

        // Add shutdown handler
        add_action('shutdown', array( $this, 'handle_unexpected_shutdown'));
    }

    /**
     * End logging
     *
     * @access public
     * @param object $object
     * @return void
     */
    public function end_logging($object = null)
    {

        // Set status to success if current status is still processing
        if ($this->has_status('processing')) {
            $this->set_status('success');
        }

        // Save any potentially unsaved data
        $this->save();

        // Unset log entry from object
        if (is_a($object, 'RightPress_Object')) {
            $object->unset_log_entry();
        }

        // Unset flag
        $this->is_logging = false;

        // Remove shutdown handler
        remove_action('shutdown', array( $this, 'handle_unexpected_shutdown'));
    }

    /**
     * Handle unexpected shutdown
     *
     * @access public
     * @return void
     */
    public function handle_unexpected_shutdown()
    {

        // No longer logging
        if (!$this->is_logging) {
            return;
        }

        // Update status
        $this->update_status('error');

        $error_details = null;

        // Get actual error message if possible
        if ($error = error_get_last()) {
            if (in_array($error['type'], array(E_ERROR, E_PARSE, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR))) {
                $error_details = sprintf(esc_html__('PHP Fatal error %s in %s on line %s.', 'rightpress'), $error['message'], $error['file'], $error['line']);
            }
        }

        // Add notes
        $this->add_note(esc_html__('Execution ended prematurely.', 'rightpress') . (!$error_details ? (' ' . esc_html__('Reason unknown.', 'rightpress')) : ''));

        // Add error message
        if ($error_details) {
            $this->add_error_details($error_details);
        }

        // End logging
        $this->end_logging();
    }

    /**
     * Update status
     *
     * Sets status and saves object
     *
     * @access public
     * @param string $status
     * @return void
     */
    public function update_status($status)
    {

        $this->set_status($status);
        $this->save();
    }

    /**
     * Add note(s)
     *
     * Adds note(s) to notes array and saves object
     *
     * @access public
     * @param string|array $note
     * @return void
     */
    public function add_note($note)
    {

        $this->set_notes(array_merge($this->get_notes('edit'), (array) $note));
        $this->save();
    }

    /**
     * Add error details
     *
     * Sets status and saves error details
     *
     * @access public
     * @param string $error_details
     * @return void
     */
    public function add_error_details($error_details)
    {

        $this->set_error_details($error_details);
        $this->save();
    }

    /**
     * Handle caught exception
     *
     * @access public
     * @param object $exception
     * @param string $note
     * @param string $status
     * @return void
     */
    public function handle_caught_exception($exception, $note = null, $status = 'error')
    {

        // Set status
        $this->set_status($status);

        // Check if exception is RightPress_Exception
        $is_own_exception = is_a($exception, 'RightPress_Exception');

        // Get note if it was not passed in
        if ($note === null) {
            $note = $is_own_exception ? $exception->getMessage() : esc_html__('Unexpected error occurred.', 'rightpress');
        }

        // Add note to log entry
        $this->add_note($note);

        // Add error details if exception is not RightPress_Exception
        if (!$is_own_exception) {
            $this->add_error_details($exception->getTraceAsString());
        }

        // Save log entry
        $this->save();
    }


    /**
     * =================================================================================================================
     * OTHER METHODS
     * =================================================================================================================
     */

    /**
     * Get event type label
     *
     * @access public
     * @return string|null
     */
    public function get_event_type_label()
    {

        // Get event type
        if ($event_type = $this->get_event_type()) {

            // Get all event types
            $event_types = $this->get_controller()->get_event_types();

            // Check if event type is defined
            if (isset($event_types[$event_type])) {

                // Return label
                return $event_types[$event_type]['label'];
            }
        }

        // Event type not set
        return null;
    }

    /**
     * Get actor name
     *
     * @access public
     * @return string
     */
    public function get_actor_name()
    {

        // Actor is user
        if ($this->get_actor_id()) {

            // Load user
            if ($user = get_user_by('id', $this->get_actor_id())) {
                $name = $user->display_name;
            }
            // No such user
            else {
                $name = sprintf(esc_html__('user #%d', 'rightpress'), $this->get_actor_id());
            }
        }
        // Actor is system
        else {
            $name = esc_html__('system', 'rightpress');
        }

        return apply_filters($this->get_controller()->prefix_public_hook('actor_name'), $name, $this);
    }

    /**
     * Get formatted actor name
     *
     * Adds link to user profile if user exists
     *
     * @access public
     * @return string
     */
    public function get_formatted_actor_name()
    {

        // Get actor name
        $formatted_actor_name = $this->get_actor_name();

        // Actor is user
        if ($this->get_actor_id()) {

            // User exists
            if (get_user_by('id', $this->get_actor_id())) {

                // Add link to user profile
                $formatted_actor_name = '<a href="' . esc_url(add_query_arg('user_id', $this->get_actor_id(), admin_url('user-edit.php'))) . '" title="' . esc_attr__('User profile', 'rightpress') . '">' . $formatted_actor_name . '</a>';
            }
        }

        return $formatted_actor_name;
    }





}
