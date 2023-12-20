<?php

namespace WPDeveloper\BetterDocsPro\Utils;

class Helper {
    public static function get_plugins( $plugin_basename = null ) {
        if ( ! function_exists( 'get_plugins' ) ) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $plugins = get_plugins();
        return $plugin_basename == null ? $plugins : isset( $plugins[$plugin_basename] );
    }

    public static function is_plugin_active( $plugin_basename ) {
        if ( ! function_exists( 'is_plugin_active' ) ) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        return is_plugin_active( $plugin_basename );
    }
}
