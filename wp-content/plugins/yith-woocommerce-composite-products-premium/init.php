<?php

/**
 * Plugin Name: YITH Composite Products for WooCommerce Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-composite-products/
 * Description: <code><strong>YITH Composite Products for WooCommerce Premium</strong></code> is the plugin that allows your customers to assemble their own product using other products from your store. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 1.1.20
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-composite-products-for-woocommerce
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages/
 * Requires at least: 4.5
 * Tested up to: 5.4
 * WC requires at least: 3.0
 * WC tested up to: 4.2
 *
 * @author  YITH
 * @package YITH WooCommerce Product Add-ons
 *
 * Copyright 2012-2018 - Your Inspiration Themes - All right reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! function_exists( 'yith_wcp_install_premium_woocommerce_admin_notice' ) ) {
    /**
     * Print an admin notice if woocommerce is deactivated
     *
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     * @since  1.0
     * @return void
     * @use admin_notices hooks
     */
    function yith_wcp_install_premium_woocommerce_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'YITH Composite Products for WooCommerce is enabled but not effective. It requires WooCommerce in order to work.', 'yith-composite-products-for-woocommerce' ); ?></p>
        </div>
    <?php
    }
}

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

/* Advanced Option Constant */
! defined( 'YITH_WCP' ) && define( 'YITH_WCP', true );
! defined( 'YITH_WCP_URL' ) && define( 'YITH_WCP_URL', plugin_dir_url( __FILE__ ) );
! defined( 'YITH_WCP_DIR' ) && define( 'YITH_WCP_DIR', plugin_dir_path( __FILE__ ) );
! defined( 'YITH_WCP_NAME' ) && define( 'YITH_WCP_NAME', 'YITH Composite Products for WooCommerce' );
! defined( 'YITH_WCP_TEMPLATE_PATH' ) && define( 'YITH_WCP_TEMPLATE_PATH', YITH_WCP_DIR . 'templates' );
! defined( 'YITH_WCP_TEMPLATE_FRONTEND_PATH' ) && define( 'YITH_WCP_TEMPLATE_ADMIN_PATH', YITH_WCP_TEMPLATE_PATH . '/admin/' );
! defined( 'YITH_WCP_TEMPLATE_FRONTEND_PATH' ) && define( 'YITH_WCP_TEMPLATE_FRONTEND_PATH', YITH_WCP_TEMPLATE_PATH . '/frontend/' );
! defined( 'YITH_WCP_ASSETS_URL' ) && define( 'YITH_WCP_ASSETS_URL', YITH_WCP_URL . 'assets' );
! defined( 'YITH_WCP_VERSION' ) && define( 'YITH_WCP_VERSION', '1.1.20' );
! defined( 'YITH_WCP_DB_VERSION' ) && define( 'YITH_WCP_DB_VERSION', '1.0.1' );
! defined( 'YITH_WCP_PREMIUM' ) && define( 'YITH_WCP_PREMIUM', true );
! defined( 'YITH_WCP_FILE' ) && define( 'YITH_WCP_FILE', __FILE__ );
! defined( 'YITH_WCP_SLUG' ) && define( 'YITH_WCP_SLUG', 'yith-composite-products-for-woocommerce' );
! defined( 'YITH_WCP_SECRET_KEY' ) && define( 'YITH_WCP_SECRET_KEY', 'MLEzG9Jak6H6xX5BAP8f' );
! defined( 'YITH_WCP_INIT' ) && define( 'YITH_WCP_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_WCP_WPML_CONTEXT' ) && define( 'YITH_WCP_WPML_CONTEXT', 'YITH Composite Products for WooCommerce' );

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCP_DIR . 'plugin-fw/init.php' ) ) {
    require_once( YITH_WCP_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WCP_DIR );

if ( ! function_exists( 'YITH_WCP' ) ) {
    /**
     * Unique access to instance of YITH_Vendors class
     *
     * @return YITH_WCP
     * @since 1.0.0
     */
    function YITH_WCP() {
        
        // Load required classes and functions
        require_once( YITH_WCP_DIR . 'includes/class.yith-wcp-product-composite.php' );
        require_once( YITH_WCP_DIR . 'includes/class.yith-wcp.php' );

        return YITH_WCP::instance();
    }
}

/**
 * Require core files
 *
 * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
 * @since  1.0
 * @return void
 * @use Load plugin core
 */
function yith_wcp_premium_init() {

    load_plugin_textdomain( YITH_WCP_SLUG, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    YITH_WCP();

}

add_action( 'yith_wcp_premium_init', 'yith_wcp_premium_init' );

function yith_wcp_premium_install() {

    if ( ! function_exists( 'WC' ) ) {
        add_action( 'admin_notices', 'yith_wcp_install_premium_woocommerce_admin_notice' );
    }
    else {
        do_action( 'yith_wcp_premium_init' );
    }

}

add_action( 'plugins_loaded', 'yith_wcp_premium_install', 12 );
