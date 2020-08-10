<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Legacy code support
 *
 * @class RightPress_Legacy
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Legacy
{

    // Legacy filters to be overriden by child classes (new_filter => old_filter)
    protected $legacy_filters = array();

    // Legacy actions to be overriden by child classes (new_action => old_action)
    protected $legacy_actions = array();

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Set up legacy filters and actions
        $this->set_up_legacy_filters_and_actions();
    }

    /**
     * Set up legacy filters and actions
     *
     * @access protected
     * @return void
     */
    protected function set_up_legacy_filters_and_actions()
    {

        // Set up legacy filters
        foreach ($this->legacy_filters as $new_filter => $old_filter) {
            add_filter($new_filter, array($this, 'legacy_filter_and_action_handler'), 5, 99);
        }

        // Set up legacy actions
        foreach ($this->legacy_actions as $new_action => $old_action) {
            add_filter($new_action, array($this, 'legacy_filter_and_action_handler'), 5, 99);
        }
    }

    /**
     * Legacy filter and action handler
     *
     * @access public
     * @return mixed
     */
    public function legacy_filter_and_action_handler()
    {

        // Get current filter/action name
        $new = current_filter();

        // Check if this is filter or action
        $is_action = isset($this->legacy_actions[$new]);

        // Get old filter/action name
        $old = $is_action ? $this->legacy_actions[$new] : $this->legacy_filters[$new];

        // Get arguments
        $args = func_get_args();

        // Add old filter/action name to the beginning of arguments list
        array_unshift($args, $old);

        // Call apply_filters or do_action with old filter/action name and all arguments passed to new filter/action
        return call_user_func_array(($is_action ? 'do_action' : 'apply_filters'), $args);
    }





}
