<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Main site controller
 *
 * @class RightPress_Main_Site_Controller
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Main_Site_Controller
{

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Maybe print main site URL mismatch notification
        RightPress_Help::add_early_action('admin_notices', array($this, 'maybe_print_url_mismatch_notification'));

        // Maybe process mismatch notification action
        add_action('init', array($this, 'maybe_process_url_mismatch_notification_action'));
    }

    /**
     * Check if this is main site
     *
     * Some actions must not be processed on development/staging websites, e.g. automatic payments or emails to customers
     *
     * @access public
     * @return bool
     */
    public static function is_main_site()
    {

        // Get called class
        $called_class = get_called_class();

        // Get instance
        $instance = $called_class::get_instance();

        // Prepare urls for comparison
        $main_site_url      = preg_replace('#^\w+://#', '', trim($instance->get_main_site_url(), '/'));
        $current_site_url   = preg_replace('#^\w+://#', '', trim($instance->get_current_site_url(), '/'));

        // Check if current site is main site
        $is_main_site = $main_site_url === $current_site_url;

        // Allow developers to override and return
        return apply_filters(($instance->get_plugin_public_prefix() . 'is_main_site'), $is_main_site);
    }

    /**
     * Get main site url
     *
     * @access public
     * @return string|false
     */
    public function get_main_site_url()
    {

        // Get value from database
        $main_site_url = get_option(($this->get_plugin_private_prefix() . 'main_site_url'), false);

        // Value exists
        if ($main_site_url !== false) {

            // Decode value
            $main_site_url = base64_decode($main_site_url);
        }
        // Value does not exist
        else {

            // Set main site url to current site url
            $main_site_url = $this->get_current_site_url();

            // Update value in database
            $this->update_main_site_url($main_site_url);
        }

        return $main_site_url;
    }

    /**
     * Update main site url
     *
     * @access public
     * @param string $main_site_url
     * @return void
     */
    public function update_main_site_url($main_site_url)
    {

        // Encode value
        $main_site_url = base64_encode($main_site_url);

        // Save value to database
        update_option(($this->get_plugin_private_prefix() . 'main_site_url'), $main_site_url);
    }

    /**
     * Get current site url
     *
     * @access public
     * @return string
     */
    public function get_current_site_url()
    {

        return get_site_url();
    }

    /**
     * Maybe print main site URL mismatch notification
     *
     * @access public
     * @return void
     */
    public function maybe_print_url_mismatch_notification()
    {

        // Current site is not the main one and is not RightPress demo site
        if (!$this->is_main_site() && !RightPress_Help::is_demo()) {

            // Check if notification is not ignored
            if (get_option(($this->get_plugin_private_prefix() . 'ignored_mismatch_url')) !== $this->get_current_site_url()) {

                // Print notification
                $this->print_url_mismatch_notification();
            }
        }
    }

    /**
     * Maybe process mismatch notification action
     *
     * @access public
     * @return void
     */
    public function maybe_process_url_mismatch_notification_action()
    {

        // Check if any action was requested
        if (isset($_REQUEST[($this->get_plugin_public_prefix() . 'url_mismatch_action')])) {

            // Switch main site url
            if ($_REQUEST[($this->get_plugin_public_prefix() . 'url_mismatch_action')] === 'change') {

                // Get current site url
                $current_site_url = $this->get_current_site_url();

                // Set current site url as a new main site url
                $this->update_main_site_url($current_site_url);
            }
            // Ignore URL mismatch notification
            else if ($_REQUEST[($this->get_plugin_public_prefix() . 'url_mismatch_action')] === 'ignore') {

                // Save preference
                update_option(($this->get_plugin_private_prefix() . 'ignored_mismatch_url'), $this->get_current_site_url());
            }
        }
    }





}
