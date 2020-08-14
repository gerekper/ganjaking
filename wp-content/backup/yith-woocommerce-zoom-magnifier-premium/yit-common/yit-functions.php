<?php
/**
 * Your Inspiration Themes common functions
 *
 * @author Your Inspiration Themes
 * @version 0.0.1
 */

define( 'YITH_FUNCTIONS', true);

/* === Include Common Framework File === */
require_once( 'google_fonts.php' );
require_once( 'yith-panel.php' );

if ( ! function_exists( 'yit_is_woocommerce_active' ) ) {
    /**
     * WC Detection
     */
    function yit_is_woocommerce_active() {
        $active_plugins = (array) get_option( 'active_plugins', array() );

        if ( is_multisite() ) {
            $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        }

        $woo = yit_get_plugin_basename_from_slug( 'woocommerce' );
        return in_array( $woo, $active_plugins ) || array_key_exists( $woo, $active_plugins );
    }
}

if( ! function_exists( 'yit_get_plugin_basename_from_slug' ) ) {
    /**
     * Helper function to extract the file path of the plugin file from the
     * plugin slug, if the plugin is installed.
     *
     * @param string $slug Plugin slug (typically folder name) as provided by the developer
     * @return string Either file path for plugin if installed, or just the plugin slug
     */
    function yit_get_plugin_basename_from_slug( $slug ) {
        include_once ABSPATH . '/wp-admin/includes/plugin.php';

        $keys = array_keys( get_plugins() );

        foreach ( $keys as $key ) {
            if ( preg_match( '|^' . $slug .'|', $key ) )
                return $key;
        }

        return $slug;
    }
}

if( ! function_exists( 'yith_debug') ) {
    /**
     * Debug helper function.  This is a wrapper for var_dump() that adds
     * the <pre /> tags, cleans up newlines and indents, and runs
     * htmlentities() before output.
     *
     * @param  mixed  $var   The variable to dump.
     * @param  mixed  $var2  The second variable to dump
     * @param  ...
     * @return string
     */
    function yith_debug() {
        $args = func_get_args();
        if( !empty( $args ) ) {
            foreach( $args as $k=>$arg ) {
                // var_dump the variable into a buffer and keep the output
                ob_start();
                var_dump($arg);
                $output = ob_get_clean();

                // neaten the newlines and indents
                $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);

                if(!extension_loaded('xdebug')) {
                    $output = htmlspecialchars($output, ENT_QUOTES);
                }

                $output = '<pre class="yit-debug">'
                    . '<strong>$param_' . ($k+1) . ": </strong>"
                    . $output
                    . '</pre>';
                echo $output;
            }
        } else {
            trigger_error("yit_debug() expects at least 1 parameter, 0 given.", E_USER_WARNING);
        }

        return $args;
    }
}


if( ! function_exists('yit_get_options_from_prefix') ) {
    /**
     * Returns an array of all options that starts with a prefix
     *
     * @param string $prefix
     * @return array
     */
    function yit_get_options_from_prefix( $prefix ) {
        if( !$prefix ) return array();

        global $wpdb;

        $sql = "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '{$prefix}%'";
        $options =  $wpdb->get_col( $sql );
        $return = array();

        foreach( $options as $option ) {
            $return[$option] = get_option( $option );
        }

        return $return;
    }
}

if( !function_exists('yit_wp_roles') ) {
    /**
     * Returns the roles of the site.
     *
     * @return array
     * @since 1.0.0
     */
    function yit_wp_roles() {
        global $wp_roles;

        if ( ! isset( $wp_roles ) ) $wp_roles = new WP_Roles();

        $roles = array();
        foreach( $wp_roles->roles as $k=>$role ) {
            $roles[$k] = $role['name'];
        }

        return $roles;
    }
}

if( !function_exists('yit_user_roles') ) {
    /**
     * Returns the roles of the user
     *
     * @param int $user_id (Optional) The ID of a user. Defaults to the current user.
     * @return array()
     * @since 1.0.0
     */
    function yit_user_roles( $user_id = null ) {
        if ( is_numeric( $user_id ) )
            $user = get_userdata( $user_id );
        else
            $user = wp_get_current_user();

        if ( empty( $user ) )
            return false;

        return (array) $user->roles;
    }
}


// ADMIN
if( !function_exists('yit_typo_option_to_css') ) {
    /**
     * Change the typography option saved in database to attributes for css
     *
     * @param array $option The option as saved in the database
     * @return string
     * @since 1.0.0
     */
    function yit_typo_option_to_css( $option ) {
        $attrs = $variant = array();

        extract( $option );
        $attrs[] = "color: $color;";
        $attrs[] = "font-size: {$size}{$unit};";
        $attrs[] = "font-family: '{$family}';";
        switch ( $style ) {
            case 'regular':
                $attrs[] = 'font-weight: 400;';
                $attrs[] = 'font-style: normal;';
                $variant = 400;
                break;
            case 'bold':
                $attrs[] = 'font-weight: 700;';
                $attrs[] = 'font-style: normal;';
                $variant = 700;
                break;
            case 'extra-bold':
                $attrs[] = 'font-weight: 800;';
                $attrs[] = 'font-style: normal;';
                $variant = 800;
                break;
            case 'italic':
                $attrs[] = 'font-weight: 400;';
                $attrs[] = 'font-style: italic;';
                $variant = 400;
                break;
            case 'bold-italic':
                $attrs[] = 'font-weight: 700;';
                $attrs[] = 'font-style: italic;';
                $variant = 700;
                break;
        }

        yith_add_google_font( $family, $variant );

        return implode( "\n", $attrs ) . "\n";
    }
}


if( !function_exists('yit_curPageURL') ) {
    /**
     * Retrieve the current complete url
     *
     * @since 1.0
     */
    function yit_curPageURL() {
        $pageURL = 'http';
        if ( isset( $_SERVER["HTTPS"] ) AND $_SERVER["HTTPS"] == "on" )
            $pageURL .= "s";

        $pageURL .= "://";

        if ( isset( $_SERVER["SERVER_PORT"] ) AND $_SERVER["SERVER_PORT"] != "80" )
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        else
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

        return $pageURL;
    }
}