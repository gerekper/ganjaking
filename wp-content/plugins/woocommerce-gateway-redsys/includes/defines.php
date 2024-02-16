<?php
/**
 * Defines Redsys Gateway for WooCommerce
 *
 * @package WooCommerce Redsys Gateway
 * @since 1.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @link https://redsys.joseconti.com
 * @link https://woo.com/products/redsys-gateway/
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2024 José Conti.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! defined( 'REDSYS_PLUGIN_API_REDSYS_PATH' ) ) {
	define( 'REDSYS_PLUGIN_API_REDSYS_PATH', REDSYS_PLUGIN_PATH_P . 'includes/api-redsys/' );
}

if ( ! defined( 'REDSYS_PLUGIN_CLASS_PATH_P' ) ) {
	define( 'REDSYS_PLUGIN_CLASS_PATH_P', REDSYS_PLUGIN_PATH_P . 'classes/' );
}

if ( ! defined( 'REDSYS_PLUGIN_METABOXES_PATH' ) ) {
	define( 'REDSYS_PLUGIN_METABOXES_PATH', REDSYS_PLUGIN_PATH_P . 'includes/metabox/' );
}

if ( ! defined( 'REDSYS_PLUGIN_STATUS_PATH' ) ) {
	define( 'REDSYS_PLUGIN_STATUS_PATH', REDSYS_PLUGIN_PATH_P . 'includes/woo-status/' );
}

if ( ! defined( 'REDSYS_PLUGIN_NOTICE_PATH_P' ) ) {
	define( 'REDSYS_PLUGIN_NOTICE_PATH_P', REDSYS_PLUGIN_PATH_P . 'includes/notices/' );
}

if ( ! defined( 'REDSYS_PLUGIN_DATA_PATH_P' ) ) {
	define( 'REDSYS_PLUGIN_DATA_PATH_P', REDSYS_PLUGIN_PATH_P . 'includes/data/' );
}

if ( ! defined( 'REDSYS_PLUGIN_DATA_URL' ) ) {
	define( 'REDSYS_PLUGIN_DATA_URL', REDSYS_PLUGIN_URL_P . 'includes/data/' );
}

if ( ! defined( 'REDSYS_CHECK_WOO_CONNECTION' ) ) {
	define( 'REDSYS_CHECK_WOO_CONNECTION', true );
}

if ( ! defined( 'REDSYS_POST_PSD2_URL' ) ) {
	define( 'REDSYS_POST_PSD2_URL', 'https://redsys.joseconti.com/2019/09/05/redsys-y-psd2-o-sca/' );
}

if ( ! defined( 'REDSYS_INSTALL_URL_P' ) ) {
	define( 'REDSYS_INSTALL_URL_P', 'https://redsys.joseconti.com/primeros-pasos-con-redsys-y-woocommerce/' );
}

if ( ! defined( 'REDSYS_TELEGRAM_SIGNUP_P' ) ) {
	define( 'REDSYS_TELEGRAM_SIGNUP_P', 'https://t.me/wooredsys' );
}

if ( ! defined( 'REDSYS_REVIEW_P' ) ) {
	define( 'REDSYS_REVIEW_P', 'https://woo.com/products/redsys-gateway/' );
}

if ( ! defined( 'REDSYS_TICKET' ) ) {
	define( 'REDSYS_TICKET', 'https://woo.com/my-account/contact-support/' );
}

if ( ! defined( 'REDSYS_ADD_LICENSE' ) ) {
	define( 'REDSYS_ADD_LICENSE', 'https://redsys.joseconti.com/product/plugin-woocommerce-redsys-gateway/' );
}

if ( ! defined( 'REDSYS_PRODUCT_ID_WOO' ) ) {
	define( 'REDSYS_PRODUCT_ID_WOO', 187871 );
}

if ( ! defined( 'REDSYS_BLOCKS_PATH' ) ) {
	define( 'REDSYS_BLOCKS_PATH', REDSYS_PLUGIN_PATH_P . 'bloques-redsys/' );
}

if ( ! defined( 'REDSYS_BLOCKS_URL' ) ) {
	define( 'REDSYS_BLOCKS_URL', REDSYS_PLUGIN_URL_P . 'bloques-redsys/' );
}
