<?php

/**
 * Plugin Name: YITH WooCommerce Product Add-ons Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-product-add-ons/
 * Description: <code><strong>YITH WooCommerce Product Add-ons</strong></code> is the plugin that allows you to create new options for WooCommerce products. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 1.5.28
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-product-add-ons
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages/
 * Requires at least: 4.5
 * Tested up to: 5.4
 * WC requires at least: 3.0
 * WC tested up to: 4.3
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

defined( 'ABSPATH' ) or exit; 

// Product Add-ons constants
! defined( 'YITH_WAPO' )						&& define( 'YITH_WAPO', true );
! defined( 'YITH_WAPO_FILE' )					&& define( 'YITH_WAPO_FILE', __FILE__ );
! defined( 'YITH_WAPO_URL' )					&& define( 'YITH_WAPO_URL', plugin_dir_url( __FILE__ ) );
! defined( 'YITH_WAPO_DIR' )					&& define( 'YITH_WAPO_DIR', plugin_dir_path( __FILE__ ) );
! defined( 'YITH_WAPO_DIR_NAME' )				&& define( 'YITH_WAPO_DIR_NAME', basename( dirname( __FILE__ ) ) );
! defined( 'YITH_WAPO_NAME' )					&& define( 'YITH_WAPO_NAME', 'YITH WooCommerce Product Add-ons Premium' );
! defined( 'YITH_WAPO_TEMPLATE_PATH' )			&& define( 'YITH_WAPO_TEMPLATE_PATH', YITH_WAPO_DIR . 'templates' );
! defined( 'YITH_WAPO_TEMPLATE_ADMIN_PATH' )	&& define( 'YITH_WAPO_TEMPLATE_ADMIN_PATH', YITH_WAPO_TEMPLATE_PATH . '/admin/' );
! defined( 'YITH_WAPO_TEMPLATE_FRONTEND_PATH' )	&& define( 'YITH_WAPO_TEMPLATE_FRONTEND_PATH', YITH_WAPO_TEMPLATE_PATH . '/frontend/' );
! defined( 'YITH_WAPO_ASSETS_URL' )				&& define( 'YITH_WAPO_ASSETS_URL', YITH_WAPO_URL . 'assets' );
! defined( 'YITH_WAPO_VERSION' )				&& define( 'YITH_WAPO_VERSION', '1.5.28' );
! defined( 'YITH_WAPO_DB_VERSION' )				&& define( 'YITH_WAPO_DB_VERSION', '1.5.28' );
! defined( 'YITH_WAPO_FILE' )					&& define( 'YITH_WAPO_FILE', __FILE__ );
! defined( 'YITH_WAPO_SLUG' )					&& define( 'YITH_WAPO_SLUG', 'yith-woocommerce-advanced-product-options' );
! defined( 'YITH_WAPO_LOCALIZE_SLUG' )			&& define( 'YITH_WAPO_LOCALIZE_SLUG', 'yith-woocommerce-product-add-ons' );
! defined( 'YITH_WAPO_SECRET_KEY' )				&& define( 'YITH_WAPO_SECRET_KEY', 'yCVBJvwjwXe2Z9vlqoWo' );
! defined( 'YITH_WAPO_INIT' )					&& define( 'YITH_WAPO_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_WAPO_WPML_CONTEXT' )			&& define( 'YITH_WAPO_WPML_CONTEXT', 'YITH WooCommerce Product Add-ons' );
! defined( 'YITH_WAPO_PREMIUM' )				&& define( 'YITH_WAPO_PREMIUM', true );

// Check if a free version currently active and try disabling before activating this one
if ( defined( 'YITH_WAPO_PREMIUM' ) ) {
	if ( ! function_exists( 'yit_deactive_free_version' ) ) {
		require_once YITH_WAPO_DIR . 'plugin-fw/yit-deactive-plugin.php';
	}
	yit_deactive_free_version( 'YITH_WCCL_FREE_INIT', plugin_basename( __FILE__ ) );
	yit_deactive_free_version( 'YITH_WAPO_FREE_INIT', plugin_basename( __FILE__ ) );
}

// Plugin Framework Version Check
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WAPO_DIR . 'plugin-fw/init.php' ) ) {
	require_once( YITH_WAPO_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WAPO_DIR );

// Plugin registration
if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once YITH_WAPO_DIR . 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

// Main Product Add-ons functions
require_once( YITH_WAPO_DIR . 'includes/functions/yith-wapo.php' );

// Init
add_action( 'plugins_loaded', 'yith_wapo_init', 12 );
