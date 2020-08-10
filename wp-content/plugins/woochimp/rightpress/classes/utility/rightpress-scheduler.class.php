<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Scheduler
 *
 * Just a wrapper for the Action Scheduler library included in WooCommerce
 *
 * Child classes must implement the following methods for each action:
 *  - schedule_{action}
 *  - unschedule_{action}
 *  - {prefix}_scheduled_{action}
 *
 * @class RightPress_Scheduler
 * @package RightPress
 * @author RightPress
 */
class RightPress_Scheduler
{

    protected $group;
    protected $prefix;

    protected $actions = null;

    protected $unscheduling = false;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Set up action handlers
        foreach ($this->get_actions() as $action => $action_data) {
            add_action($this->prefix_hook($action), array($this, ('scheduled_' . $action)));
        }

        // Prevent actions from being cancelled or deleted by anything but this scheduler class
        add_action('action_scheduler_canceled_action', array($this, 'maybe_prevent_scheduled_action_removal'));
        add_action('action_scheduler_deleted_action', array($this, 'maybe_prevent_scheduled_action_removal'));
    }

    /**
     * Prefix hook name
     *
     * @access protected
     * @param string $action
     * @return string
     */
    protected function prefix_hook($action)
    {

        return $this->get_prefix() . 'scheduled_' . $action;
    }

    /**
     * Unprefix hook name
     *
     * @access protected
     * @param string $prefixed_action
     * @return string
     */
    protected function unprefix_hook($prefixed_action)
    {

        return RightPress_Help::unprefix_string($prefixed_action, ($this->get_prefix() . 'scheduled_'));
    }

    /**
     * Get hook prefix
     *
     * @access protected
     * @return string
     */
    protected function get_prefix()
    {

        return $this->prefix;
    }

    /**
     * Get actions
     *
     * @access public
     * @return array
     */
    public function get_actions()
    {

        if ($this->actions === null) {
            $this->actions = $this->register_actions();
        }

        return $this->actions;
    }

    /**
     * Register actions
     *
     * @access public
     * @return array
     */
    public function register_actions()
    {

        // To be implemented by child classes
        return array();
    }


    /**
     * =================================================================================================================
     * PROXY METHODS TO ACTION SCHEDULER LIBRARY
     * =================================================================================================================
     */

    /**
     * Schedule single action
     *
     * @access public
     * @param object|int $datetime
     * @param string $hook
     * @param array $args
     * @param string $group
     * @return int
     */
    public function schedule_single($datetime, $hook, $args = array(), $group = '')
    {

        $timestamp = is_a($datetime, 'DateTime') ? $datetime->getTimestamp() : $datetime;

        return as_schedule_single_action($timestamp, $hook, $args, $group);
    }

    /**
     * Schedule recurring action
     *
     * @access public
     * @param object|int $datetime
     * @param int $interval_in_seconds
     * @param string $hook
     * @param array $args
     * @param string $group
     * @return int
     */
    public function schedule_recurring($datetime, $interval_in_seconds, $hook, $args = array(), $group = '')
    {

        $timestamp = is_a($datetime, 'DateTime') ? $datetime->getTimestamp() : $datetime;

        return as_schedule_recurring_action($timestamp, $interval_in_seconds, $hook, $args, $group);
    }

    /**
     * Unschedule action
     *
     * @access public
     * @param string $hook
     * @param array $args
     * @param string $group
     * @return void
     */
    public function unschedule($hook, $args = array(), $group = '')
    {

        // Set flag
        $this->unscheduling = $hook;

        // Unschedule action
        as_unschedule_all_actions($hook, $args, $group);

        // Unset flag
        $this->unscheduling = false;
    }

    /**
     * Get timestamp of next scheduled action
     *
     * @access public
     * @param string $hook
     * @param array $args
     * @param string $group
     * @return int|bool
     */
    public function next_scheduled($hook, $args = null, $group = '')
    {

        return as_next_scheduled_action($hook, $args, $group);
    }

    /**
     * Find scheduled actions
     *
     * @access public
     * @param array $args
     * @param string $return_format
     * @return array
     */
    public function find_scheduled($args = array(), $return_format = OBJECT)
    {

        return as_get_scheduled_actions($args, $return_format);
    }


    /**
     * =================================================================================================================
     * OTHER METHODS
     * =================================================================================================================
     */

    /**
     * Prevent actions from being cancelled or deleted by anything but this scheduler class
     *
     * @access public
     * @param int $action_id
     * @return void
     */
    public function maybe_prevent_scheduled_action_removal($action_id)
    {

        // Get action
        $action = ActionScheduler::store()->fetch_action($action_id);

        // Check if this action belongs to us
        if ($action->get_group() === $this->group) {

            // Make sure we are not unscheduling this action by ourselves
            if ($action->get_hook() !== $this->unscheduling) {

                // Get action args
                $args = $action->get_args();

                // Check if subscription id is set
                if (is_array($args) && !empty($args['subscription_id'])) {

                    // Load subscription object
                    if ($subscription = subscriptio_get_subscription($args['subscription_id'])) {

                        // Get clean action name
                        $action_name = $this->unprefix_hook($action->get_hook());

                        // Check if action is supposed to be scheduled
                        if ($datetime = $subscription->{"get_scheduled_{$action_name}"}()) {

                            // Reschedule event
                            $this->{"schedule_{$action_name}"}($subscription, $datetime);
                        }
                    }
                }
            }
        }
    }





}
