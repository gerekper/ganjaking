<?php
/**
 * Plugin Name: WooCommerce Advanced Notifications
 * Plugin URI: https://woocommerce.com/products/advanced-notifications
 * Description: Add additonal, advanced order and stock notifications to WordPress - ideal for improving store management or for dropshippers.
 * Version: 1.2.32
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Text Domain: woocommerce-advanced-notifications
 * Domain Path: /languages/
 * Tested up to: 5.5
 * WC requires at least: 2.6
 * WC tested up to: 4.5
 * Woo: 18740:112372c44b002fea2640bd6bfafbca27
 *
 * Copyright: © 2021 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package woocommerce-advanced-notifications
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WC_ADVANCED_NOTIFICATIONS_VERSION', '1.2.32' ); // WRCS: DEFINED_VERSION.

/**
 * Localisation
 */
load_plugin_textdomain( 'woocommerce-advanced-notifications', false, plugin_basename( __DIR__ ) . '/languages/' );

/**
 * Init advanced notifications function.
 */
function init_advanced_notifications() {
	if ( class_exists( 'WooCommerce' ) ) {
		include_once 'includes/class-wc-advanced-notifications.php';
		include_once 'includes/class-wc-advanced-notifications-privacy.php';
	} else {
		add_action( 'admin_notices', 'woocommerce_advanced_notifications_woocommerce_deactivated' );
	}
}
add_action( 'plugins_loaded', 'init_advanced_notifications' );

/**
 * WooCommerce Deactivated Notice.
 */
function woocommerce_advanced_notifications_woocommerce_deactivated() {
	/* translators: %s: WooCommerce link */
	echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce Advanced Notifications requires %s to be installed and active.', 'woocommerce-advanced-notifications' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</p></div>';
}

/**
 * Activation
 */
register_activation_hook( __FILE__, 'activate_advanced_notifications' );

/**
 * Advanced notifications activation function.
 */
function activate_advanced_notifications() {
	global $wpdb;

	$wpdb->hide_errors();

	$collate = '';
	if ( $wpdb->has_cap( 'collation' ) ) {
		if ( ! empty( $wpdb->charset ) ) {
			$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$collate .= " COLLATE $wpdb->collate";
		}
	}

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	/**
	 * Table for notifications
	 */
	$sql = "
CREATE TABLE {$wpdb->prefix}advanced_notifications (
notification_id bigint(20) NOT NULL auto_increment,
recipient_name LONGTEXT NULL,
recipient_email LONGTEXT NULL,
recipient_address LONGTEXT NULL,
recipient_phone varchar(240) NULL,
recipient_website varchar(240) NULL,
notification_type varchar(240) NULL,
notification_plain_text int(1) NOT NULL,
notification_totals int(1) NOT NULL,
notification_prices int(1) NOT NULL,
notification_sent_count bigint(20) NOT NULL default 0,
PRIMARY KEY  (notification_id)
) $collate;
";
	dbDelta( $sql );

	$sql = "
CREATE TABLE {$wpdb->prefix}advanced_notification_triggers (
notification_id bigint(20) NOT NULL,
object_id bigint(20) NOT NULL,
object_type varchar(200) NOT NULL,
PRIMARY KEY  (notification_id,object_id)
) $collate;
";
	dbDelta( $sql );
}
