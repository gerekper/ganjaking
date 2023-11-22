<?php
/**
 * class-section-weights.php
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
 * Weights section.
 */
class Section_Weights extends \WooCommerce_Product_Search_Admin_Base {

	/**
	 * Records changes made to the settings.
	 */
	public static function save() {
		$settings = Settings::get_instance();
		$settings->set( \WooCommerce_Product_Search::USE_WEIGHTS, isset( $_POST[\WooCommerce_Product_Search::USE_WEIGHTS] ) );
		$settings->set( \WooCommerce_Product_Search::WEIGHT_TITLE, isset( $_POST[\WooCommerce_Product_Search::WEIGHT_TITLE] ) && strlen( trim( $_POST[\WooCommerce_Product_Search::WEIGHT_TITLE] ) ) > 0 ? intval( $_POST[\WooCommerce_Product_Search::WEIGHT_TITLE] ) : \WooCommerce_Product_Search::WEIGHT_TITLE_DEFAULT );
		$settings->set( \WooCommerce_Product_Search::WEIGHT_EXCERPT, isset( $_POST[\WooCommerce_Product_Search::WEIGHT_EXCERPT] ) && strlen( trim( $_POST[\WooCommerce_Product_Search::WEIGHT_EXCERPT] ) ) > 0 ? intval( $_POST[\WooCommerce_Product_Search::WEIGHT_EXCERPT] ) : \WooCommerce_Product_Search::WEIGHT_EXCERPT_DEFAULT );
		$settings->set( \WooCommerce_Product_Search::WEIGHT_CONTENT, isset( $_POST[\WooCommerce_Product_Search::WEIGHT_CONTENT] ) && strlen( trim( $_POST[\WooCommerce_Product_Search::WEIGHT_CONTENT] ) ) > 0 ? intval( $_POST[\WooCommerce_Product_Search::WEIGHT_CONTENT] ) : \WooCommerce_Product_Search::WEIGHT_CONTENT_DEFAULT );
		$settings->set( \WooCommerce_Product_Search::WEIGHT_TAGS, isset( $_POST[\WooCommerce_Product_Search::WEIGHT_TAGS] ) && strlen( trim( $_POST[\WooCommerce_Product_Search::WEIGHT_TAGS] ) ) > 0 ? intval( $_POST[\WooCommerce_Product_Search::WEIGHT_TAGS] ) : \WooCommerce_Product_Search::WEIGHT_TAGS_DEFAULT );
		$settings->set( \WooCommerce_Product_Search::WEIGHT_CATEGORIES, isset( $_POST[\WooCommerce_Product_Search::WEIGHT_CATEGORIES] ) && strlen( trim( $_POST[\WooCommerce_Product_Search::WEIGHT_CATEGORIES] ) ) > 0 ? intval( $_POST[\WooCommerce_Product_Search::WEIGHT_CATEGORIES] ) : \WooCommerce_Product_Search::WEIGHT_CATEGORIES_DEFAULT );
		$settings->set( \WooCommerce_Product_Search::WEIGHT_ATTRIBUTES, isset( $_POST[\WooCommerce_Product_Search::WEIGHT_ATTRIBUTES] ) && strlen( trim( $_POST[\WooCommerce_Product_Search::WEIGHT_ATTRIBUTES] ) ) > 0 ? intval( $_POST[\WooCommerce_Product_Search::WEIGHT_ATTRIBUTES] ) : \WooCommerce_Product_Search::WEIGHT_ATTRIBUTES_DEFAULT );
		$settings->set( \WooCommerce_Product_Search::WEIGHT_SKU, isset( $_POST[\WooCommerce_Product_Search::WEIGHT_SKU] ) && strlen( trim( $_POST[\WooCommerce_Product_Search::WEIGHT_SKU] ) ) > 0 ? intval( $_POST[\WooCommerce_Product_Search::WEIGHT_SKU] ) : \WooCommerce_Product_Search::WEIGHT_SKU_DEFAULT );
		$settings->save();
	}

	/**
	 * Renders the section.
	 */
	public static function render() {

		$settings = Settings::get_instance();

		$use_weights       = $settings->get( \WooCommerce_Product_Search::USE_WEIGHTS, \WooCommerce_Product_Search::USE_WEIGHTS_DEFAULT );
		$weight_title      = $settings->get( \WooCommerce_Product_Search::WEIGHT_TITLE, \WooCommerce_Product_Search::WEIGHT_TITLE_DEFAULT );
		$weight_excerpt    = $settings->get( \WooCommerce_Product_Search::WEIGHT_EXCERPT, \WooCommerce_Product_Search::WEIGHT_EXCERPT_DEFAULT );
		$weight_content    = $settings->get( \WooCommerce_Product_Search::WEIGHT_CONTENT, \WooCommerce_Product_Search::WEIGHT_CONTENT_DEFAULT );
		$weight_tags       = $settings->get( \WooCommerce_Product_Search::WEIGHT_TAGS, \WooCommerce_Product_Search::WEIGHT_TAGS_DEFAULT );
		$weight_categories = $settings->get( \WooCommerce_Product_Search::WEIGHT_CATEGORIES, \WooCommerce_Product_Search::WEIGHT_CATEGORIES_DEFAULT );
		$weight_attributes = $settings->get( \WooCommerce_Product_Search::WEIGHT_ATTRIBUTES, \WooCommerce_Product_Search::WEIGHT_ATTRIBUTES_DEFAULT );
		$weight_sku        = $settings->get( \WooCommerce_Product_Search::WEIGHT_SKU, \WooCommerce_Product_Search::WEIGHT_SKU_DEFAULT );

		echo '<div id="product-search-weights-tab" class="product-search-tab">';
		echo '<h3 class="section-heading">' . esc_html( __( 'Search Weights', 'woocommerce-product-search' ) ) . '</h3>';

		echo '<p>';
		echo '<label>';
		printf( '<input name="%s" type="checkbox" %s />', \WooCommerce_Product_Search::USE_WEIGHTS, $use_weights ? ' checked="checked" ' : '' );
		echo ' ';
		echo esc_html( __( 'Use weights', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';
		echo '<p class="description">';
		echo esc_html( __( 'If enabled, the relevance in product search results is enhanced by taking weights into account.', 'woocommerce-product-search' ) );
		echo '</p>';

		echo '<h4>' . esc_html( __( 'Relevance', 'woocommerce-product-search' ) ) . '</h4>';

		echo '<p class="description">';
		echo esc_html( __( 'The following weights determine the relevance of matches in the product title, excerpt, content, tags, categories, attributes and SKU.', 'woocommerce-product-search' ) );
		echo ' ';
		echo esc_html( __( 'By default, a higher title and SKU weight will promote search results that have matches in the title and SKU.', 'woocommerce-product-search' ) );
		echo ' ';
		echo esc_html( __( 'The weight of products and product categories can be modified individually, the computed sum of weights determines the relevance of a product in search results.', 'woocommerce-product-search' ) );
		echo '</p>';

		echo '<table>';

		echo '<tr>';
		echo '<td>';
		printf( '<label for="%s">', esc_attr( \WooCommerce_Product_Search::WEIGHT_TITLE ) );
		echo esc_html( __( 'Title', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</td>';
		echo '<td>';
		printf(
			'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d"/>',
			esc_attr( \WooCommerce_Product_Search::WEIGHT_TITLE ),
			esc_attr( $weight_title ),
			esc_attr( \WooCommerce_Product_Search::WEIGHT_TITLE_DEFAULT )
		);
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td>';
		printf( '<label for="%s">', esc_attr( \WooCommerce_Product_Search::WEIGHT_EXCERPT ) );
		echo esc_html( __( 'Excerpt', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</td>';
		echo '<td>';
		printf(
			'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d"/>',
			esc_attr( \WooCommerce_Product_Search::WEIGHT_EXCERPT ),
			esc_attr( $weight_excerpt ),
			esc_attr( \WooCommerce_Product_Search::WEIGHT_EXCERPT_DEFAULT )
		);
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td>';
		printf( '<label for="%s">', esc_attr( \WooCommerce_Product_Search::WEIGHT_CONTENT ) );
		echo esc_html( __( 'Content', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</td>';
		echo '<td>';
		printf(
			'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d"/>',
			esc_attr( \WooCommerce_Product_Search::WEIGHT_CONTENT ),
			esc_attr( $weight_content ),
			esc_attr( \WooCommerce_Product_Search::WEIGHT_CONTENT_DEFAULT )
		);
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td>';
		printf( '<label for="%s">', esc_attr( \WooCommerce_Product_Search::WEIGHT_TAGS ) );
		echo esc_html( __( 'Tags', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</td>';
		echo '<td>';
		printf(
			'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d"/>',
			esc_attr( \WooCommerce_Product_Search::WEIGHT_TAGS ),
			esc_attr( $weight_tags ),
			esc_attr( \WooCommerce_Product_Search::WEIGHT_TAGS_DEFAULT )
		);
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td>';
		printf( '<label for="%s">', esc_attr( \WooCommerce_Product_Search::WEIGHT_CATEGORIES ) );
		echo esc_html( __( 'Categories', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</td>';
		echo '<td>';
		printf(
			'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d"/>',
			esc_attr( \WooCommerce_Product_Search::WEIGHT_CATEGORIES ),
			esc_attr( $weight_categories ),
			esc_attr( \WooCommerce_Product_Search::WEIGHT_CATEGORIES_DEFAULT )
		);
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td>';
		printf( '<label for="%s">', esc_attr( \WooCommerce_Product_Search::WEIGHT_ATTRIBUTES ) );
		echo esc_html( __( 'Attributes', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</td>';
		echo '<td>';
		printf(
			'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d"/>',
			esc_attr( \WooCommerce_Product_Search::WEIGHT_ATTRIBUTES ),
			esc_attr( $weight_attributes ),
			esc_attr( \WooCommerce_Product_Search::WEIGHT_ATTRIBUTES_DEFAULT )
		);
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td>';
		printf( '<label for="%s">', esc_attr( \WooCommerce_Product_Search::WEIGHT_SKU ) );
		echo esc_html( __( 'SKU', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</td>';
		echo '<td>';
		printf(
			'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d"/>',
			esc_attr( \WooCommerce_Product_Search::WEIGHT_SKU ),
			esc_attr( $weight_sku ),
			esc_attr( \WooCommerce_Product_Search::WEIGHT_SKU_DEFAULT )
		);
		echo '</td>';
		echo '</tr>';

		echo '</table>';

		echo '</div>';
	}

}
