<?php
/**
 * class-section-css.php
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

use com\itthinx\woocommerce\search\engine\Settings;

/**
 * CSS Settings.
 */
class Section_CSS extends \WooCommerce_Product_Search_Admin_Base {

	/**
	 * Records changes made to the settings.
	 */
	public static function save() {
		$settings = Settings::get_instance();
		$settings->set( \WooCommerce_Product_Search::ENABLE_CSS, isset( $_POST[\WooCommerce_Product_Search::ENABLE_CSS] ) );
		$settings->set( \WooCommerce_Product_Search::ENABLE_INLINE_CSS, isset( $_POST[\WooCommerce_Product_Search::ENABLE_INLINE_CSS] ) );
		$settings->set( \WooCommerce_Product_Search::INLINE_CSS, isset( $_POST[\WooCommerce_Product_Search::INLINE_CSS] ) ? trim( strip_tags( $_POST[\WooCommerce_Product_Search::INLINE_CSS] ) ) : \WooCommerce_Product_Search::INLINE_CSS_DEFAULT );
		$settings->save();
	}

	/**
	 * Renders the section.
	 */
	public static function render() {

		$settings = Settings::get_instance();

		$enable_css        = $settings->get( \WooCommerce_Product_Search::ENABLE_CSS, \WooCommerce_Product_Search::ENABLE_CSS_DEFAULT );
		$enable_inline_css = $settings->get( \WooCommerce_Product_Search::ENABLE_INLINE_CSS, \WooCommerce_Product_Search::ENABLE_INLINE_CSS_DEFAULT );
		$inline_css        = $settings->get( \WooCommerce_Product_Search::INLINE_CSS, \WooCommerce_Product_Search::INLINE_CSS_DEFAULT );

		echo '<div id="product-search-css-tab" class="product-search-tab">';
		echo '<h3 class="section-heading">' . esc_html( __( 'CSS', 'woocommerce-product-search' ) ) . '</h3>';

		echo '<p>';
		esc_html_e( 'These settings are related to the Product Search Field and Product Filters.', 'woocommerce-product-search' );
		echo '</p>';

		echo '<h4>' . esc_html( __( 'Standard Stylesheet', 'woocommerce-product-search' ) ) . '</h4>';

		echo '<p>';
		echo '<label>';
		printf( '<input name="%s" type="checkbox" %s />', esc_attr( \WooCommerce_Product_Search::ENABLE_CSS ), $enable_css ? ' checked="checked" ' : '' );
		echo ' ';
		echo esc_html( __( 'Use the standard stylesheet', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';
		echo '<p class="description">';
		echo esc_html( __( 'If this option is enabled, the standard stylesheet is loaded when the Product Search Field or Product Filters are displayed.', 'woocommerce-product-search' ) );
		echo '</p>';

		echo '<h4>' . esc_html( __( 'Inline Styles', 'woocommerce-product-search' ) ) . '</h4>';

		echo '<p>';
		echo '<label>';
		printf( '<input name="%s" type="checkbox" %s />', esc_attr( \WooCommerce_Product_Search::ENABLE_INLINE_CSS ), $enable_inline_css ? ' checked="checked" ' : '' );
		echo ' ';
		echo esc_html( __( 'Use inline styles', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';
		echo '<p class="description">';
		echo esc_html( __( 'If this option is enabled, the inline styles are used when the Product Search Field or Product Filters are displayed.', 'woocommerce-product-search' ) );
		echo '</p>';

		echo '<p>';
		echo '<label>';
		echo esc_html( __( 'Inline styles', 'woocommerce-product-search' ) );
		echo '<br/>';
		printf( '<textarea style="font-family:monospace;width:50%%;height:25em;" name="%s">%s</textarea>', esc_attr( \WooCommerce_Product_Search::INLINE_CSS ), esc_textarea( stripslashes( $inline_css ) ) );
		echo '</label>';
		echo '</p>';

		echo '</div>';
	}

}
