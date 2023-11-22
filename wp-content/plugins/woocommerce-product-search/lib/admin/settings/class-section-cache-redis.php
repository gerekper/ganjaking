<?php
/**
 * class-section-cache-redis.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 5.0.0
 */

namespace com\itthinx\woocommerce\search\engine\admin;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

use com\itthinx\woocommerce\search\engine\Cache;
use com\itthinx\woocommerce\search\engine\Cache_Settings;
use com\itthinx\woocommerce\search\engine\Redis_Cache_Base;
use com\itthinx\woocommerce\search\engine\Settings;

/**
 * Cache Settings.
 */
class Section_Cache_Redis extends \WooCommerce_Product_Search_Admin_Base {

	/**
	 * Records changes made to the settings.
	 */
	public static function save() {
		$settings = Cache_Settings::get_instance();
		$cache_settings = $settings->get();

		$enabled = !empty( $_POST['redis-enabled'] );
		$cache_settings['redis']['enabled'] = $enabled;

		$host = !empty( $_POST['host'] ) ? $_POST['host'] : '';
		$host = trim( stripslashes( $host ) );
		if ( strlen( $host ) === 0 ) {
			$host = null;
		}
		$cache_settings['redis']['host'] = $host;

		$port = !empty( $_POST['port'] ) ? $_POST['port'] : '';
		$port = trim( stripslashes( $port ) );
		if ( strlen( $port ) === 0 || !is_numeric( $port ) ) {
			$port = null;
		} else {
			$port = intval( $port );
			if ( $port < 0 ) {
				$port = null;
			}
		}
		$cache_settings['redis']['port'] = $port;

		$username = !empty( $_POST['username'] ) ? $_POST['username'] : '';
		$username = trim( stripslashes( $username ) );
		if ( strlen( $username ) === 0 ) {
			$username = null;
		}
		$cache_settings['redis']['username'] = $username;

		$password = !empty( $_POST['password'] ) ? $_POST['password'] : '';
		$password = trim( stripslashes( $password ) );
		if ( strlen( $password ) === 0 ) {
			$password = null;
		}
		$cache_settings['redis']['password'] = $password;
		$settings->set( $cache_settings );
		$settings->save();
	}

	/**
	 * Render the Redis settings.
	 */
	public static function render() {

		$settings = Cache_Settings::get_instance();
		$cache_settings = $settings->get();
		$enabled = isset( $cache_settings['redis']['enabled'] ) && $cache_settings['redis']['enabled'] !== null ? $cache_settings['redis']['enabled'] : false;
		$host = isset( $cache_settings['redis']['host'] ) && $cache_settings['redis']['host'] !== null ? $cache_settings['redis']['host'] : '';
		$port = isset( $cache_settings['redis']['port'] ) && $cache_settings['redis']['port'] !== null ? $cache_settings['redis']['port'] : '';
		$username = isset( $cache_settings['redis']['username'] ) && $cache_settings['redis']['username'] !== null ? $cache_settings['redis']['username'] : '';
		$password = isset( $cache_settings['redis']['password'] ) && $cache_settings['redis']['password'] !== null ? $cache_settings['redis']['password'] : '';

		echo '<div id="product-search-cache-tab" class="product-search-tab">';

		echo '<h3 class="section-heading">';
		echo esc_html( 'Redis' );
		echo ' ';
		printf(
			'<a class="section-navigation-up" href="%s" title="%s">%s</a>',
			esc_url( self::get_admin_section_url( self::SECTION_CACHE ) ),
			esc_html( 'Caches', 'woocommerce-product-search' ),
			'<span class="dashicons dashicons-arrow-up-alt"></span>'
		);
		echo '</h3>';

		echo '<p>';
		esc_html_e( 'Use the Redis in-memory data structure store for high-performance caching.', 'woocommerce-product-search' );
		echo '</p>';

		echo '<div class="cache-settings-field">';
		echo '<div class="cache-settings-field-label">';
		printf( '<label for="%s">', 'redis-enabled' );
		echo esc_html__( 'Enabled', 'woocommerce-product-search' );
		echo '</label>';
		echo '</div>';
		echo '<div class="cache-settings-field-input">';
		printf( '<label for="%s">', 'redis-enabled' );
		printf( '<input id="%s" name="%s" type="checkbox" %s />', 'redis-enabled', 'redis-enabled', $enabled ? ' checked="checked" ' : '' );
		echo ' ';
		echo esc_html__( 'Enable Redis', 'woocommerce-product-search' );
		echo '</label>';
		echo '</div>';
		echo '</div>';

		echo '<div class="cache-settings-field">';
		echo '<div class="cache-settings-field-label">';
		printf( '<label for="%s">', 'host' );
		echo esc_html( __( 'Host', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</div>';
		echo '<div class="cache-settings-field-input">';
		printf( '<input id="%s" name="%s" type="text" value="%s" placeholder="%s" />', 'host', 'host', esc_attr( $host ), Redis_Cache_Base::HOST_DEFAULT );
		echo ' ';
		printf( '<label for="%s">', 'host' );
		echo 'Host or socket.';
		echo ' ';
		printf( 'The default is %s.', '<code>' . esc_html( Redis_Cache_Base::HOST_DEFAULT ) . '</code>' );
		echo '</label>';
		echo '</div>';
		echo '</div>';

		echo '<div class="cache-settings-field">';
		echo '<div class="cache-settings-field-label">';
		printf( '<label for="%s">', 'port' );
		echo esc_html( __( 'Port', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</div>';
		echo '<div class="cache-settings-field-input">';
		printf( '<input id="%s" name="%s" type="number" value="%s" placeholder="%s"/>', 'port', 'port', esc_attr( $port ), Redis_Cache_Base::PORT_DEFAULT );
		echo ' ';
		printf( '<label for="%s">', 'port' );
		printf( 'The default is %s.', '<code>' . esc_html( Redis_Cache_Base::PORT_DEFAULT ) . '</code>' );
		echo '</label>';
		echo '</div>';
		echo '</div>';

		echo '<div class="cache-settings-field">';
		echo '<div class="cache-settings-field-label">';
		printf( '<label for="%s">', 'username' );
		echo esc_html( __( 'Username', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</div>';
		echo '<div class="cache-settings-field-input">';
		printf( '<input id="%s" name="%s" type="text" value="%s" placeholder="%s" autocomplete="off"/>', 'username', 'username', esc_attr( $username ), '' );
		echo '</div>';
		echo '</div>';

		echo '<div class="cache-settings-field">';
		echo '<div class="cache-settings-field-label">';
		printf( '<label for="%s">', 'password' );
		echo esc_html( __( 'Password', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</div>';
		echo '<div class="cache-settings-field-input">';
		printf( '<input id="%s" name="%s" type="password" value="%s" placeholder="%s" autocomplete="off" />', 'password', 'password', esc_attr( $password ), '' );
		echo '</div>';
		echo '</div>';

		echo '</div>';
	}

}
