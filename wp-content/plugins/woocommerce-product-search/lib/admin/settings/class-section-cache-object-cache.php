<?php
/**
 * class-section-cache-object-cache.php
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
use com\itthinx\woocommerce\search\engine\Settings;

/**
 * Cache Settings.
 */
class Section_Cache_Object_Cache extends \WooCommerce_Product_Search_Admin_Base {

	/**
	 * Records changes made to the settings.
	 */
	public static function save() {
		$settings = Cache_Settings::get_instance();
		$cache_settings = $settings->get();
		$enabled = !empty( $_POST['object-cache-enabled'] );
		$cache_settings['object_cache']['enabled'] = $enabled;
		$settings->set( $cache_settings );
		$settings->save();
	}

	/**
	 * Render the Object Cache settings.
	 */
	public static function render() {

		$settings = Cache_Settings::get_instance();
		$cache_settings = $settings->get();
		$enabled = isset( $cache_settings['object_cache']['enabled'] ) ? $cache_settings['object_cache']['enabled'] : false;

		echo '<div id="product-search-cache-tab" class="product-search-tab">';
		echo '<h3 class="section-heading">';
		echo esc_html( 'Object Cache' );
		echo ' ';
		printf(
			'<a class="section-navigation-up" href="%s" title="%s">%s</a>',
			esc_url( self::get_admin_section_url( self::SECTION_CACHE ) ),
			esc_html( 'Caches', 'woocommerce-product-search' ),
			'<span class="dashicons dashicons-arrow-up-alt"></span>'
		);
		echo '</h3>';

		echo '<p>';
		esc_html_e( 'Use the WordPress Object Cache.', 'woocommerce-product-search' );
		echo ' ';
		esc_html_e( 'The Object Cache stores data in memory, or uses other mechanisms provided by cache plugins.', 'woocommerce-product-search' );
		echo '</p>';

		echo '<div class="cache-settings-field">';
		echo '<div class="cache-settings-field-label">';
		printf( '<label for="%s">', 'object-cache-enabled' );
		echo esc_html__( 'Enabled', 'woocommerce-product-search' );
		echo '</label>';
		echo '</div>';
		echo '<div class="cache-settings-field-input">';
		printf( '<label for="%s">', 'object-cache-enabled' );
		printf( '<input id="%s" name="%s" type="checkbox" %s />', 'object-cache-enabled', 'object-cache-enabled', $enabled ? ' checked="checked" ' : '' );
		echo ' ';
		echo esc_html__( 'Enable the Object Cache', 'woocommerce-product-search' );
		echo '</label>';
		echo '</div>';
		echo '</div>';

		echo '</div>';
	}

}
