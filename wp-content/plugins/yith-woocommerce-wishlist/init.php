<?php
/**
 * Plugin Name: YITH WooCommerce Wishlist
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-wishlist/
 * Description: <code><strong>YITH WooCommerce Wishlist</strong></code> gives your users the possibility to create, fill, manage and share their wishlists allowing you to analyze their interests and needs to improve your marketing strategies. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce on <strong>YITH</strong></a>
 * Version: 3.0.14
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-wishlist
 * Domain Path: /languages/
 * WC requires at least: 4.2.0
 * WC tested up to: 4.5
 *
 * @author YITHEMES
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

/*  Copyright 2013  Your Inspiration Themes  (email : plugins@yithemes.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301 USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

if ( ! defined( 'YITH_WCWL' ) ) {
	define( 'YITH_WCWL', true );
}

if ( ! defined( 'YITH_WCWL_URL' ) ) {
	define( 'YITH_WCWL_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'YITH_WCWL_DIR' ) ) {
	define( 'YITH_WCWL_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_WCWL_INC' ) ) {
	define( 'YITH_WCWL_INC', YITH_WCWL_DIR . 'includes/' );
}

if ( ! defined( 'YITH_WCWL_INIT' ) ) {
	define( 'YITH_WCWL_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_WCWL_FREE_INIT' ) ) {
    define( 'YITH_WCWL_FREE_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_WCWL_SLUG' ) ) {
	define( 'YITH_WCWL_SLUG', 'yith-woocommerce-wishlist' );
}

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCWL_DIR . 'plugin-fw/init.php' ) ) {
	require_once( YITH_WCWL_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WCWL_DIR  );

if( ! function_exists( 'yith_wishlist_constructor' ) ) {
	function yith_wishlist_constructor() {

		load_plugin_textdomain( 'yith-woocommerce-wishlist', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

        // Load required classes and functions
	    require_once( YITH_WCWL_INC . 'data-stores/class.yith-wcwl-wishlist-data-store.php' );
	    require_once( YITH_WCWL_INC . 'data-stores/class.yith-wcwl-wishlist-item-data-store.php' );
        require_once( YITH_WCWL_INC . 'functions.yith-wcwl.php' );
	    require_once( YITH_WCWL_INC . 'legacy/functions.yith-wcwl-legacy.php' );
	    require_once( YITH_WCWL_INC . 'class.yith-wcwl-exception.php' );
	    require_once( YITH_WCWL_INC . 'class.yith-wcwl-form-handler.php' );
	    require_once( YITH_WCWL_INC . 'class.yith-wcwl-ajax-handler.php' );
	    require_once( YITH_WCWL_INC . 'class.yith-wcwl-session.php' );
	    require_once( YITH_WCWL_INC . 'class.yith-wcwl-cron.php' );
	    require_once( YITH_WCWL_INC . 'class.yith-wcwl-wishlist.php' );
	    require_once( YITH_WCWL_INC . 'class.yith-wcwl-wishlist-item.php' );
	    require_once( YITH_WCWL_INC . 'class.yith-wcwl-wishlist-factory.php' );
        require_once( YITH_WCWL_INC . 'class.yith-wcwl.php' );
	    require_once( YITH_WCWL_INC . 'class.yith-wcwl-frontend.php' );
        require_once( YITH_WCWL_INC . 'class.yith-wcwl-install.php' );
	    require_once( YITH_WCWL_INC . 'class.yith-wcwl-shortcode.php' );

        if ( is_admin() ) {
	        require_once( YITH_WCWL_INC . 'class.yith-wcwl-admin.php' );
        }

        // Let's start the game!

	    /**
	     * @deprecated
	     */
	    global $yith_wcwl;
	    $yith_wcwl = YITH_WCWL();
    }
}
add_action( 'yith_wcwl_init', 'yith_wishlist_constructor' );

if( ! function_exists( 'yith_wishlist_install' ) ) {
    function yith_wishlist_install() {

        if ( ! function_exists( 'is_plugin_active' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }

        if ( ! function_exists( 'WC' ) ) {
            add_action( 'admin_notices', 'yith_wcwl_install_woocommerce_admin_notice' );
        }
        elseif( defined( 'YITH_WCWL_PREMIUM' ) ) {
            add_action( 'admin_notices', 'yith_wcwl_install_free_admin_notice' );
            deactivate_plugins( plugin_basename( __FILE__ ) );
        }
        else {
            do_action( 'yith_wcwl_init' );
        }
    }
}
add_action( 'plugins_loaded', 'yith_wishlist_install', 11 );

if( ! function_exists( 'yith_wcwl_install_woocommerce_admin_notice' ) ) {
    function yith_wcwl_install_woocommerce_admin_notice() {
        ?>
        <div class="error">
            <p><?php echo 'YITH WooCommerce Wishlist ' . __( 'is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-wishlist' ); ?></p>
        </div>
    <?php
    }
}

if( ! function_exists( 'yith_wcwl_install_free_admin_notice' ) ){
    function yith_wcwl_install_free_admin_notice() {
        ?>
        <div class="error">
            <p><?php echo __( 'You can\'t activate the free version of', 'yith-woocommerce-wishlist' ) . 'YITH WooCommerce Wishlist' . __( 'while you are using the premium one.', 'yith-woocommerce-wishlist' ); ?></p>
        </div>
    <?php
    }
}
