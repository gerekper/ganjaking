<?php
/**
 * class-section-thumbnails.php
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
 * Admin Settings.
 */
class Section_Thumbnails extends \WooCommerce_Product_Search_Admin_Base {

	/**
	 * Records changes made to the settings.
	 */
	public static function save() {

		$settings = Settings::get_instance();

		$thumbnail_width = isset( $_POST[\WooCommerce_Product_Search_Thumbnail::THUMBNAIL_WIDTH] ) ? intval( $_POST[\WooCommerce_Product_Search_Thumbnail::THUMBNAIL_WIDTH] ) : \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_WIDTH;
		if ( ( $thumbnail_width < 0 ) || $thumbnail_width > \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_MAX_DIM ) {
			$thumbnail_width = \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_WIDTH;
		}
		$settings->set( \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_WIDTH, $thumbnail_width );

		$thumbnail_height = isset( $_POST[\WooCommerce_Product_Search_Thumbnail::THUMBNAIL_HEIGHT] ) ? intval( $_POST[\WooCommerce_Product_Search_Thumbnail::THUMBNAIL_HEIGHT] ) : \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_HEIGHT;
		if ( ( $thumbnail_height < 0 ) || $thumbnail_height > \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_MAX_DIM ) {
			$thumbnail_height = \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_HEIGHT;
		}
		$settings->set( \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_HEIGHT, $thumbnail_height );

		$settings->set( \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_CROP, isset( $_POST[\WooCommerce_Product_Search_Thumbnail::THUMBNAIL_CROP] ) );
		$settings->set( \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_USE_PLACEHOLDER, isset( $_POST[\WooCommerce_Product_Search_Thumbnail::THUMBNAIL_USE_PLACEHOLDER] ) );

		$product_taxonomies = \WooCommerce_Product_Search_Thumbnail::get_product_taxonomies();
		foreach( $product_taxonomies as $product_taxonomy ) {
			if ( $taxonomy = get_taxonomy( $product_taxonomy ) ) {
				$thumbnail_width = isset( $_POST[$taxonomy->name . '-' . \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_WIDTH] ) ? intval( $_POST[$taxonomy->name . '-' . \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_WIDTH] ) : \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_WIDTH;
				if ( ( $thumbnail_width < 0 ) || $thumbnail_width > \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_MAX_DIM ) {
					$thumbnail_width = \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_WIDTH;
				}
				$settings->set( $taxonomy->name . '-' . \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_WIDTH, $thumbnail_width );

				$thumbnail_height = isset( $_POST[$taxonomy->name . '-' . \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_HEIGHT] ) ? intval( $_POST[$taxonomy->name . '-' . \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_HEIGHT] ) : \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_HEIGHT;
				if ( ( $thumbnail_height < 0 ) || $thumbnail_height > \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_MAX_DIM ) {
					$thumbnail_height = \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_HEIGHT;
				}
				$settings->set( $taxonomy->name . '-' . \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_HEIGHT, $thumbnail_height );

				$settings->set( $taxonomy->name . '-' . \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_CROP, isset( $_POST[$taxonomy->name . '-' . \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_CROP] ) );
				$settings->set( $taxonomy->name . '-' . \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_USE_PLACEHOLDER, isset( $_POST[$taxonomy->name . '-' . \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_USE_PLACEHOLDER] ) );
			}
		}

		$settings->save();
	}

	/**
	 * Renders the section.
	 */
	public static function render() {

		$settings = Settings::get_instance();

		$thumbnail_width   = $settings->get( \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_WIDTH, \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_WIDTH );
		$thumbnail_height  = $settings->get( \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_HEIGHT, \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_HEIGHT );
		$thumbnail_crop    = $settings->get( \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_CROP, \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_CROP );
		$thumbnail_use_placeholder = $settings->get( \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_USE_PLACEHOLDER, \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_USE_PLACEHOLDER_DEFAULT );

		echo '<div id="product-search-thumbnails-tab" class="product-search-tab">';

		echo '<h2 class="section-heading">';
		echo esc_html( __( 'Thumbnails', 'woocommerce-product-search' ) );
		echo '</h2>';

		echo '<h3>';
		echo esc_html( __( 'Product Thumbnails', 'woocommerce-product-search' ) );
		echo '</h3>';

		echo '<p>';
		esc_html_e( 'The size defined here applies to the product thumbnails shown in the results of the Product Search Field.', 'woocommerce-product-search' );
		echo '</p>';

		echo '<p class="description">';
		echo esc_html( __( 'Width and height in pixels used for thumbnails displayed in product search results.', 'woocommerce-product-search' ) );
		echo '</p>';

		echo '<p>';

		echo '<label>';
		echo esc_html( __( 'Width', 'woocommerce-product-search' ) );
		echo ' ';
		printf( '<input name="%s" style="width:5em;text-align:right;" type="text" value="%d" />', esc_attr( \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_WIDTH ), esc_attr( $thumbnail_width ) );
		echo ' ';
		echo esc_html( __( 'px', 'woocommerce-product-search' ) );
		echo '</label>';

		echo '&emsp;&emsp;';

		echo '<label>';
		echo esc_html( __( 'Height', 'woocommerce-product-search' ) );
		echo ' ';
		printf( '<input name="%s" style="width:5em;text-align:right;" type="text" value="%d" />', esc_attr( \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_HEIGHT ), esc_attr( $thumbnail_height ) );
		echo ' ';
		echo esc_html( __( 'px', 'woocommerce-product-search' ) );
		echo '</label>';

		echo '&emsp;&emsp;';

		printf( '<label title="%s">', esc_attr__( 'If enabled, the thumbnail images are cropped to match the dimensions exactly. Otherwise the thumbnails will be adjusted in size while matching the aspect ratio of the original image.', 'woocommerce-product-search' ) );
		printf( '<input name="%s" type="checkbox" %s />', esc_attr( \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_CROP ), $thumbnail_crop ? ' checked="checked" ' : '' );
		echo ' ';
		echo esc_html( __( 'Crop thumbnails', 'woocommerce-product-search' ) );
		echo '</label>';

		echo '&emsp;&emsp;';

		printf( '<label title="%s">', esc_attr__( 'If enabled, products without a featured product image will show a default placeholder thumbnail image.', 'woocommerce-product-search' ) );
		printf( '<input name="%s" type="checkbox" %s />', esc_attr( \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_USE_PLACEHOLDER ), $thumbnail_use_placeholder ? ' checked="checked" ' : '' );
		echo ' ';
		echo esc_html( __( 'Placeholder thumbnails', 'woocommerce-product-search' ) );
		echo '</label>';

		echo '</p>';

		echo '<h3>';
		echo esc_html( __( 'Filter Thumbnails', 'woocommerce-product-search' ) );
		echo '</h3>';

		$product_taxonomies = \WooCommerce_Product_Search_Thumbnail::get_product_taxonomies();

		echo '<p>';
		echo esc_html__( 'The sizes defined in this section determine the appearance of thumbnails used for product category, product tag and product attribute filters.', 'woocommerce-product-search' );
		echo '</p>';

		foreach( $product_taxonomies as $product_taxonomy ) {

			if ( !( $taxonomy = get_taxonomy( $product_taxonomy ) ) ) {
				continue;
			}

			$thumbnail_width  = $settings->get( $taxonomy->name . '-' . \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_WIDTH, \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_WIDTH );
			$thumbnail_height = $settings->get( $taxonomy->name . '-' . \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_HEIGHT, \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_HEIGHT );
			$thumbnail_crop   = $settings->get( $taxonomy->name . '-' . \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_CROP, \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_DEFAULT_CROP );
			$thumbnail_use_placeholder = $settings->get( $taxonomy->name . '-' . \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_USE_PLACEHOLDER, \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_USE_PLACEHOLDER_DEFAULT );

			echo '<h4>';
			echo esc_html__( $taxonomy->label, 'woocommerce-product-search' );
			echo '</h4>';

			echo '<p>';

			echo '<label>';
			echo esc_html( __( 'Width', 'woocommerce-product-search' ) );
			echo ' ';
			printf( '<input name="%s" style="width:5em;text-align:right;" type="text" value="%d" />', esc_attr( $taxonomy->name . '-' . \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_WIDTH ), esc_attr( $thumbnail_width ) );
			echo ' ';
			echo esc_html( __( 'px', 'woocommerce-product-search' ) );
			echo '</label>';

			echo '&emsp;&emsp;';

			echo '<label>';
			echo esc_html( __( 'Height', 'woocommerce-product-search' ) );
			echo ' ';
			printf( '<input name="%s" style="width:5em;text-align:right;" type="text" value="%d" />', esc_attr( $taxonomy->name . '-' . \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_HEIGHT ), esc_attr( $thumbnail_height ) );
			echo ' ';
			echo esc_html( __( 'px', 'woocommerce-product-search' ) );
			echo '</label>';

			echo '&emsp;&emsp;';

			printf( '<label title="%s">', esc_attr__( 'If enabled, the thumbnail images are cropped to match the dimensions exactly. Otherwise the thumbnails will be adjusted in size while matching the aspect ratio of the original image.', 'woocommerce-product-search' ) );
			printf( '<input name="%s" type="checkbox" %s />', esc_attr( $taxonomy->name . '-' . \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_CROP ), $thumbnail_crop ? ' checked="checked" ' : '' );
			echo ' ';
			echo esc_html( __( 'Crop thumbnails', 'woocommerce-product-search' ) );
			echo '</label>';

			echo '&emsp;&emsp;';

			printf( '<label title="%s">', esc_attr__( 'If enabled, terms without a search filter image will show a default placeholder thumbnail image.', 'woocommerce-product-search' ) );
			printf( '<input name="%s" type="checkbox" %s />', esc_attr( $taxonomy->name . '-' . \WooCommerce_Product_Search_Thumbnail::THUMBNAIL_USE_PLACEHOLDER ), $thumbnail_use_placeholder ? ' checked="checked" ' : '' );
			echo ' ';
			echo esc_html( __( 'Placeholder thumbnails', 'woocommerce-product-search' ) );
			echo '</label>';

			echo '</p>';
		}
		echo '</div>';
	}

}
