<?php
/**
 * Plugin Name: YITH WordPress Title Bar Effects Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-wordpress-title-bar-effects
 * Description: <code><strong>YITH WordPress Title Bar Effects</strong></code> catches your users' eye when they leave the site and open a new tab on your browser. Thanks to the animated TITLE, your users will go back to your site. Don't miss the chance to avoid them to leave it so easily. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce on <strong>YITH</strong></a>
 * Version: 1.1.9
 * Author: YITH
 * Author URI: http://yithemes.com/
 * Text Domain: yith-wordpress-title-bar-effects
 * Domain Path: /languages/
 *
 * @author  YITH
 * @package YITH WordPress Title Bar Effect Premium
 * @version 1.1.9
 */
/*  Copyright 2016-2020  YITH  (email : plugins@yithemes.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if ( !function_exists( 'is_plugin_active' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

if ( !function_exists( 'yith_plugin_registration_hook' ) ) {
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );


if ( !defined( 'YITH_WTBE_VERSION' ) ) {
    define( 'YITH_WTBE_VERSION', '1.1.9' );
}

if ( !defined( 'YITH_WTBE_PREMIUM' ) ) {
    define( 'YITH_WTBE_PREMIUM', '1' );
}

if ( !defined( 'YITH_WTBE_INIT' ) ) {
    define( 'YITH_WTBE_INIT', plugin_basename( __FILE__ ) );
}

if ( !defined( 'YITH_WTBE' ) ) {
    define( 'YITH_WTBE', true );
}

if ( !defined( 'YITH_WTBE_FILE' ) ) {
    define( 'YITH_WTBE_FILE', __FILE__ );
}

if ( !defined( 'YITH_WTBE_URL' ) ) {
    define( 'YITH_WTBE_URL', plugin_dir_url( __FILE__ ) );
}

if ( !defined( 'YITH_WTBE_DIR' ) ) {
    define( 'YITH_WTBE_DIR', plugin_dir_path( __FILE__ ) );
}

if ( !defined( 'YITH_WTBE_ASSETS_URL' ) ) {
    define( 'YITH_WTBE_ASSETS_URL', YITH_WTBE_URL . 'assets' );
}

if ( !defined( 'YITH_WTBE_ASSETS_PATH' ) ) {
    define( 'YITH_WTBE_ASSETS_PATH', YITH_WTBE_DIR . 'assets' );
}

if ( !defined( 'YITH_WTBE_INCLUDES_PATH' ) ) {
    define( 'YITH_WTBE_INCLUDES_PATH', YITH_WTBE_DIR . 'includes' );
}

if ( !defined( 'YITH_WTBE_SLUG' ) ) {
    define( 'YITH_WTBE_SLUG', 'yith-wordpress-title-bar-effects' );
}
if ( !defined( 'YITH_WTBE_SECRET_KEY' ) ) {
    define( 'YITH_WTBE_SECRET_KEY', '2DqVoPrzZrRk3cTljNcM' );
}

if ( !defined( 'YITH_WTBE_DEBUG' ) ) {
    define( 'YITH_WTBE_DEBUG', false );
}

if( !defined( 'YITH_YWBPG_ASSETS_DIR' ) ){
    define( 'YITH_YWBPG_ASSETS_DIR', YITH_WTBE_DIR . 'assets' );
}

function yith_wtbe_init(){
    load_plugin_textdomain( 'yith-wordpress-title-bar-effects', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    require_once ( 'includes/class.yith-wtbe.php' );
    require_once ( 'includes/class.yith-wtbe-admin.php' );
    yith_wtbe();
}

add_action('plugins_loaded','yith_wtbe_init',11);

/* Plugin Framework Version Check */
if ( !function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php' ) ) {
    require_once( plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( plugin_dir_path( __FILE__ ) );