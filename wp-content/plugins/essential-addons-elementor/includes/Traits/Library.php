<?php

namespace Essential_Addons_Elementor\Pro\Traits;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/**
 * Core function library
 *
 * @since 3.0.0
 */
trait Library
{
    /**
     * Remove files in a dir
     *
     * @since 3.0.0
     */
    public function empty_dir($path)
    {
        if (!is_dir($path) || !file_exists($path)) {
            return;
        }

        foreach (scandir($path) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            unlink($path . DIRECTORY_SEPARATOR . $item);
        }
    }

    /**
     * Get plugin data from WP.org repository
     *
     * @since 3.0.0
     */
    public function get_plugin_data($slug = '')
    {
        $args = array(
            'slug' => $slug,
            'fields' => array(
                'version' => false,
            ),
        );

        $response = wp_remote_post(
            'http://api.wordpress.org/plugins/info/1.0/',
            array(
                'body' => array(
                    'action' => 'plugin_information',
                    'request' => serialize((object) $args),
                ),
            )
        );

        if (is_wp_error($response)) {
            return false;
        } else {
            $response = unserialize(wp_remote_retrieve_body($response));

            if ($response) {
                return $response;
            } else {
                return false;
            }
        }
    }

    /**
     * Check if a plugin is installed
     *
     * @since 3.0.0
     */
    public function is_plugin_installed($basename)
    {
        if (!function_exists('get_plugins')) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $plugins = get_plugins();

        return isset($plugins[$basename]);
    }

    /**
     * Check if a plugin is installed
     *
     * @since 3.0.0
     */
    public function get_plugin_version($basename)
    {
        if (!function_exists('get_plugins')) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $plugins = get_plugins();

        return $plugins[$basename]['Version'];
    }

    /**
     * Install plugin from url
     *
     * @since 3.0.0
     */
    public function install_plugin($plugin_url)
    {
        include_once ABSPATH . 'wp-admin/includes/file.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once ABSPATH . 'wp-admin/includes/class-automatic-upgrader-skin.php';

        $skin = new \Automatic_Upgrader_Skin;
        $upgrader = new \Plugin_Upgrader($skin);
        $upgrader->install($plugin_url);

        // activate plugin
        activate_plugin($upgrader->plugin_info(), '', false, true);

        return $skin->result;
    }

    /**
     * Upgrade plugin
     *
     * @since 3.0.0
     */
    public function upgrade_plugin($basename)
    {
        include_once ABSPATH . 'wp-admin/includes/file.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once ABSPATH . 'wp-admin/includes/class-automatic-upgrader-skin.php';

        $skin = new \Automatic_Upgrader_Skin;
        $upgrader = new \Plugin_Upgrader($skin);
        $upgrader->upgrade($basename);

        return $skin->result;
    }

    /**
     * Generate safe path
     *
     * @since v3.0.0
     */
    public function safe_path($path)
    {
        $path = str_replace(['//', '\\\\'], ['/', '\\'], $path);

        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }

	/**
	 * check is plugin active or not
	 *
	 * @since v4.3.5
	 * @param $plugin
	 * @return bool
	 */
    public function is_plugin_active($plugin) {
	    if ( !function_exists( 'is_plugin_active' ) ){
		    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        }

	    return is_plugin_active( $plugin );
    }

}
