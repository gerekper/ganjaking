<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Data Updater Interface
 *
 * @package RightPress
 * @author RightPress
 */
interface RightPress_Data_Updater_Interface
{

    /**
     * Get plugin version
     *
     * @access public
     * @return string
     */
    public function get_plugin_version();

    /**
     * Get plugin private prefix
     *
     * @access public
     * @return string
     */
    public function get_plugin_private_prefix();

    /**
     * Execute custom update procedures
     *
     * @access public
     * @return void
     */
    public function execute_custom();

    /**
     * Get custom capabilities
     *
     * @access public
     * @return array
     */
    public function get_custom_capabilities();

    /**
     * Get custom terms
     *
     * @access public
     * @return array
     */
    public function get_custom_terms();

    /**
     * Get custom tables sql
     *
     * @access public
     * @param string $table_prefix
     * @param string $collate
     * @return string
     */
    public function get_custom_tables_sql($table_prefix, $collate);

    /**
     * Migrate settings
     *
     * @access public
     * @return array
     */
    public static function migrate_settings($stored, $to_settings_version);





}
