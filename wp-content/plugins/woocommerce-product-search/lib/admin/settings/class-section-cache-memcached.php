<?php
/**
 * class-section-cache-memcached.php
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
use com\itthinx\woocommerce\search\engine\Memcached_Cache;
use com\itthinx\woocommerce\search\engine\Settings;

/**
 * Cache Settings.
 */
class Section_Cache_Memcached extends \WooCommerce_Product_Search_Admin_Base {

	/**
	 * Records changes made to the settings.
	 */
	public static function save() {
		$settings = Cache_Settings::get_instance();
		$cache_settings = $settings->get();

		$enabled = !empty( $_POST['memcached-enabled'] );
		$cache_settings['memcached']['enabled'] = $enabled;

		$host = !empty( $_POST['host'] ) ? $_POST['host'] : '';
		$host = trim( stripslashes( $host ) );
		if ( strlen( $host ) === 0 ) {
			$host = null;
		} else {
			$host = implode( ',', array_map( 'trim', explode( ',', $host ) ) );
		}
		$cache_settings['memcached']['host'] = $host;

		$port = !empty( $_POST['port'] ) ? $_POST['port'] : '';
		$port = trim( stripslashes( $port ) );
		if ( strlen( $port ) === 0 ) {
			$port = null;
		} else {
			$ports = array();
			$_ports = array_map( 'intval', array_map( 'trim', explode( ',', $port ) ) );
			foreach ( $_ports as $_port ) {
				if ( $_port >= 0 ) {
					$ports[] = $_port;
				}
			}
			if ( count( $ports ) > 0 ) {
				$port = implode( ',', $ports );
			} else {
				$port = null;
			}
		}
		$cache_settings['memcached']['port'] = $port;

		$username = !empty( $_POST['username'] ) ? $_POST['username'] : '';
		$username = trim( stripslashes( $username ) );
		if ( strlen( $username ) === 0 ) {
			$username = null;
		}
		$cache_settings['memcached']['username'] = $username;

		$password = !empty( $_POST['password'] ) ? $_POST['password'] : '';
		$password = trim( stripslashes( $password ) );
		if ( strlen( $password ) === 0 ) {
			$password = null;
		}
		$cache_settings['memcached']['password'] = $password;

		$settings->set( $cache_settings );
		$settings->save();
	}

	/**
	 * Render the Memcached settings.
	 */
	public static function render() {

		$settings = Cache_Settings::get_instance();
		$cache_settings = $settings->get();
		$enabled = isset( $cache_settings['memcached']['enabled'] ) && $cache_settings['memcached']['enabled'] !== null ? $cache_settings['memcached']['enabled'] : false;
		$host = isset( $cache_settings['memcached']['host'] ) && $cache_settings['memcached']['host'] !== null ? $cache_settings['memcached']['host'] : '';
		$port = isset( $cache_settings['memcached']['port'] ) && $cache_settings['memcached']['port'] !== null ? $cache_settings['memcached']['port'] : '';
		$username = isset( $cache_settings['memcached']['username'] ) && $cache_settings['memcached']['username'] !== null ? $cache_settings['memcached']['username'] : '';
		$password = isset( $cache_settings['memcached']['password'] ) && $cache_settings['memcached']['password'] !== null ? $cache_settings['memcached']['password'] : '';

		echo '<div id="product-search-cache-tab" class="product-search-tab">';

		echo '<h3 class="section-heading">';
		echo esc_html( 'Memcached' );
		echo ' ';
		printf(
			'<a class="section-navigation-up" href="%s" title="%s">%s</a>',
			esc_url( self::get_admin_section_url( self::SECTION_CACHE ) ),
			esc_html( 'Caches', 'woocommerce-product-search' ),
			'<span class="dashicons dashicons-arrow-up-alt"></span>'
		);
		echo '</h3>';

		echo '<p>';
		esc_html_e( 'Use the Memcached high-performance, distributed memory object caching system.', 'woocommerce-product-search' );
		echo '</p>';

		echo '<div class="cache-settings-field">';
		echo '<div class="cache-settings-field-label">';
		printf( '<label for="%s">', 'memcached-enabled' );
		echo esc_html__( 'Enabled', 'woocommerce-product-search' );
		echo '</label>';
		echo '</div>';
		echo '<div class="cache-settings-field-input">';
		printf( '<label for="%s">', 'memcached-enabled' );
		printf( '<input id="%s" name="%s" type="checkbox" %s />', 'memcached-enabled', 'memcached-enabled', $enabled ? ' checked="checked" ' : '' );
		echo ' ';
		echo esc_html__( 'Enable Memcached', 'woocommerce-product-search' );
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
		printf( '<input id="%s" name="%s" type="text" value="%s" placeholder="%s" />', 'host', 'host', esc_attr( $host ), Memcached_Cache::HOST_DEFAULT );
		echo ' ';
		printf( '<label for="%s">', 'host' );
		echo esc_html__( 'Host or socket.', 'woocommerce-product-search' );
		echo ' ';
		printf( 'The default is %s.', '<code>' . esc_html( Memcached_Cache::HOST_DEFAULT ) . '</code>' );
		echo ' ';
		echo esc_html__( 'One or more entries separated by comma.', 'woocommerce-product-search' );
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
		printf( '<input id="%s" name="%s" type="text" value="%s" placeholder="%s"/>', 'port', 'port', esc_attr( $port ), Memcached_Cache::PORT_DEFAULT );
		echo ' ';
		printf( '<label for="%s">', 'port' );
		printf( 'The default is %s.', '<code>' . esc_html( Memcached_Cache::PORT_DEFAULT ) . '</code>' );
		echo ' ';
		echo esc_html__( 'One or more entries separated by comma.', 'woocommerce-product-search' );
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
