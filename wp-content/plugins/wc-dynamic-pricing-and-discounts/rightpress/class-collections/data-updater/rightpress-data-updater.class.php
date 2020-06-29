<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-data-updater-interface.php';

/**
 * Data Updater
 *
 * @class RightPress_Data_Updater
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Data_Updater implements RightPress_Data_Updater_Interface
{

    protected $is_installation = false;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        add_action('init', array($this, 'check'), 5);
    }

    /**
     * Check if plugin update procedure needs to be executed
     *
     * @access public
     * @return void
     */
    public function check()
    {

        // Get plugin version
        $plugin_version = $this->get_plugin_version();

        // Get plugin private prefix
        $prefix = $this->get_plugin_private_prefix();

        // Get previous plugin version
        $previous_version = get_option($prefix . 'version');

        // Update procedure is executed on each version change
        if (!defined('IFRAME_REQUEST') && $previous_version !== $plugin_version) {

            // Maybe set installation flag
            if ($previous_version === false) {
                $this->is_installation = true;
            }

            // Execute update procedure
            $this->execute();

            // Update stored version number
            update_option(($prefix . 'version'), $plugin_version);

            // Let other classes know
            do_action($prefix . 'updated');
        }
    }

    /**
     * Execute update procedure
     *
     * @access protected
     * @return void
     */
    protected function execute()
    {

        // Create or update database tables
        $this->create_database_tables();

        // Add custom taxonomy terms
        $this->add_terms();

        // Add custom capabilities on installation
        if ($this->is_installation) {
            $this->add_capabilities();
        }

        // Allow plugins to run custom data update procedures
        $this->execute_custom();
    }

    /**
     * Create database tables
     *
     * @access protected
     * @return void
     */
    protected function create_database_tables()
    {

        global $wpdb;

        // Get database properties
        $table_prefix = $wpdb->prefix;
        $collate = $wpdb->has_cap('collation') ? $wpdb->get_charset_collate() : '';

        // Get custom tables sql
        if ($sql = $this->get_custom_tables_sql($table_prefix, $collate)) {

            // Load dependencies
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            // Create or modify custom tables
            dbDelta($sql);
        }
    }

    /**
     * Add custom taxonomy terms
     *
     * @access protected
     * @return void
     */
    protected function add_terms()
    {

        // Get custom taxonomy terms
        if ($taxonomy_terms = $this->get_custom_terms()) {

            // Iterate over taxonomies
            foreach ($taxonomy_terms as $taxonomy_key => $terms) {

                // Iterate over taxonomy terms
                foreach ($terms as $term_key => $term) {

                    // Check if term is missing
                    if (!term_exists($term_key, $taxonomy_key)) {

                        // Add term
                        wp_insert_term($term['title'], $taxonomy_key, array(
                            'slug' => $term_key,
                        ));
                    }
                }
            }
        }
    }

    /**
     * Add custom capabilities to administrator and shop_manager roles
     *
     * @access protected
     * @return void
     */
    protected function add_capabilities()
    {

        global $wp_roles;

        if (!class_exists('WP_Roles')) {
            return;
        }

        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        // Get custom capabilities
        $capabilities = $this->get_custom_capabilities();

        // Generate capabilities from capability types
        foreach ($this->get_capability_types() as $capability_type) {

            // get_capability_types() is always supposed to return array with two strings - [0] is singular, [1] is plural
            // See https://developer.wordpress.org/reference/functions/get_post_type_capabilities/
            $singular   = $capability_type[0];
            $plural     = $capability_type[1];

            // Add to capabilities array
            $capabilities[$singular] = array(

                // Post type
                "edit_{$singular}",
                "read_{$singular}",
                "delete_{$singular}",
                "edit_{$plural}",
                "edit_others_{$plural}",
                "publish_{$plural}",
                "read_private_{$plural}",
                "delete_{$plural}",
                "delete_private_{$plural}",
                "delete_published_{$plural}",
                "delete_others_{$plural}",
                "edit_private_{$plural}",
                "edit_published_{$plural}",

                // Terms
                "manage_{$singular}_terms",
                "edit_{$singular}_terms",
                "delete_{$singular}_terms",
                "assign_{$singular}_terms",
            );
        }

        // Add capabilities to specific roles
        foreach ($capabilities as $capability_group) {
            foreach ($capability_group as $capability) {
                $wp_roles->add_cap('administrator', $capability);
                $wp_roles->add_cap('shop_manager', $capability);
            }
        }
    }

    /**
     * Get capability types
     *
     * @access public
     * @return array
     */
    public function get_capability_types()
    {

        return array();
    }





}
