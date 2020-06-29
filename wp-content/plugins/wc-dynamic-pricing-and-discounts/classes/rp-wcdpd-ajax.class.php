<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Methods related to Ajax requests
 *
 * @class RP_WCDPD_Ajax
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Ajax
{

    // Singleton control
    protected static $instance = false; public static function get_instance() { return self::$instance ? self::$instance : (self::$instance = new self()); }

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        add_action('init', array($this, 'define_ajax'), 0);
    }

    /**
     * Adapted from WooCommerce core
     *
     * @access public
     * @return void
     */
    public function define_ajax()
    {
        if (!empty($_REQUEST['rp_wcdpd_ajax']) || !empty($_REQUEST['rightpress_ajax'])) {

            // Define ajax
            if (!defined('DOING_AJAX')) {
                define('DOING_AJAX', true);
            }
            if (!defined('RP_WCDPD_DOING_AJAX')) {
                define('RP_WCDPD_DOING_AJAX', true);
            }

            // Turn off display_errors during AJAX events to prevent malformed JSON
            if (!WP_DEBUG || (WP_DEBUG && !WP_DEBUG_DISPLAY)) {
                @ini_set('display_errors', 0);
            }

            // Hide database errors
            $GLOBALS['wpdb']->hide_errors();
        }
    }

    /**
     * Get Ajax URL
     *
     * @access public
     * @return string
     */
    public static function get_url()
    {
        return admin_url('admin-ajax.php?rp_wcdpd_ajax=1');
    }


}

RP_WCDPD_Ajax::get_instance();
