<?php

/**
 * Standalone Init File
 *
 * Initializes the file for the add-on version of the plugin.
 *
 * @author      WP Admin Pages PRO
 * @category    Admin
 * @package     WP_Ultimo
 * @version     0.0.1
 */

if ( !function_exists( 'wu_apc_init' ) ) {
    /**
     * Initialize the Plugin
     */
    add_action( 'plugins_loaded', 'wu_apc_init', 1 );
    /**
     * Initializes the plugin
     *
     * @return void
     */
    function wu_apc_init()
    {
        // Set global
        $GLOBALS['WP_Ultimo_APC'] = WP_Ultimo_APC();
        require_once plugin_dir_path( __FILE__ ) . 'inc/class-wapp-admin-notices.php';
        require_once plugin_dir_path( __FILE__ ) . 'inc/class-wapp-pages.php';
        require_once plugin_dir_path( __FILE__ ) . 'inc/class-wapp-pages-getting-started.php';
    }
    
    // end wu_apc_init;
}

