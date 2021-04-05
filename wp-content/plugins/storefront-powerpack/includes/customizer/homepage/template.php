<?php
/**
 * Storefront Powerpack template functions.
 *
 * @package Storefront_Powerpack
 */

if ( ! function_exists( 'sp_homepage_product_section_view_more_button' ) ) {
	/**
	 * Display a 'more' button after a product section on the homepage that shows a link to the user specified url
	 *
	 * @param  string $setting The Customizer setting ID to query for the URL.
	 * @param  string $label   The label to display on the button.
	 * @return void
	 */
	function sp_homepage_product_section_view_more_button( $setting, $label ) {
		$url = get_theme_mod( $setting );

		if ( '' !== $url ) {
			echo '<p class="clearfix"><a href="' . esc_url( $url ) . '" class="button alignright">' . esc_attr__( $label, 'storefront-powerpack' ) . '</a></p>';
		}
	}
}

if ( ! function_exists( 'sp_homepage_product_categories_view_more' ) ) {
	/**
	 * Product categories view more button
	 *
	 * @return void
	 */
	function sp_homepage_product_categories_view_more() {
		sp_homepage_product_section_view_more_button( 'sp_homepage_category_more_url', __( 'View more product categories', 'storefront-powerpack' ) );
	}
}

if ( ! function_exists( 'sp_homepage_recent_products_view_more' ) ) {
	/**
	 * Recent products view more button
	 *
	 * @return void
	 */
	function sp_homepage_recent_products_view_more() {
		sp_homepage_product_section_view_more_button( 'sp_homepage_recent_products_more_url', __( 'View more new products', 'storefront-powerpack' ) );
	}
}

if ( ! function_exists( 'sp_homepage_featured_products_view_more' ) ) {
	/**
	 * Featured products view more button
	 *
	 * @return void
	 */
	function sp_homepage_featured_products_view_more() {
		sp_homepage_product_section_view_more_button( 'sp_homepage_featured_products_more_url', __( 'View more featured products', 'storefront-powerpack' ) );
	}
}

if ( ! function_exists( 'sp_homepage_top_rated_products_view_more' ) ) {
	/**
	 * Top rated products view more button
	 *
	 * @return void
	 */
	function sp_homepage_top_rated_products_view_more() {
		sp_homepage_product_section_view_more_button( 'sp_homepage_top_rated_products_more_url', __( 'View more popular products', 'storefront-powerpack' ) );
	}
}

if ( ! function_exists( 'sp_homepage_on_sale_products_view_more' ) ) {
	/**
	 * On sale products view more button
	 *
	 * @return void
	 */
	function sp_homepage_on_sale_products_view_more() {
		sp_homepage_product_section_view_more_button( 'sp_homepage_on_sale_products_more_url', __( 'View more products on sale', 'storefront-powerpack' ) );
	}
}

if ( ! function_exists( 'sp_homepage_best_selling_products_view_more' ) ) {
	/**
	 * On sale products view more button
	 *
	 * @return void
	 */
	function sp_homepage_best_selling_products_view_more() {
		sp_homepage_product_section_view_more_button( 'sp_homepage_best_sellers_products_more_url', __( 'View more best selling products', 'storefront-powerpack' ) );
	}
}

if ( ! function_exists( 'sp_homepage_product_section_description' ) ) {
	/**
	 * Display a description for the product section on the homepage if one has been specified in the Customizer
	 *
	 * @param  string $setting The Customizer setting ID to check.
	 * @return void
	 */
	function sp_homepage_product_section_description( $setting ) {
		$description = get_theme_mod( $setting );

		if ( '' !== $description ) {
			echo '<div class="sp-section-description">' . wp_kses_post( wpautop( $description ) ) . '</div>';
		}
	}
}

if ( ! function_exists( 'sp_homepage_product_categories_description' ) ) {
	/**
	 * Homepage Product Categories description
	 *
	 * @return void
	 */
	function sp_homepage_product_categories_description() {
		sp_homepage_product_section_description( 'sp_homepage_category_description' );
	}
}

if ( ! function_exists( 'sp_homepage_recent_products_description' ) ) {
	/**
	 * Homepage Recent Products description.
	 *
	 * @return void
	 */
	function sp_homepage_recent_products_description() {
		sp_homepage_product_section_description( 'sp_homepage_recent_products_description' );
	}
}

if ( ! function_exists( 'sp_homepage_featured_products_description' ) ) {
	/**
	 * Homepage Featured Products description.
	 *
	 * @return void
	 */
	function sp_homepage_featured_products_description() {
		sp_homepage_product_section_description( 'sp_homepage_featured_products_description' );
	}
}

if ( ! function_exists( 'sp_homepage_popular_products_description' ) ) {
	/**
	 * Homepage Popular Products description.
	 *
	 * @return void
	 */
	function sp_homepage_popular_products_description() {
		sp_homepage_product_section_description( 'sp_homepage_top_rated_products_description' );
	}
}

if ( ! function_exists( 'sp_homepage_on_sale_products_description' ) ) {
	/**
	 * Homepage On Sale Products description.
	 *
	 * @return void
	 */
	function sp_homepage_on_sale_products_description() {
		sp_homepage_product_section_description( 'sp_homepage_on_sale_products_description' );
	}
}

if ( ! function_exists( 'sp_homepage_best_selling_products_description' ) ) {
	/**
	 * Homepage Best Selling Products description.
	 *
	 * @return void
	 */
	function sp_homepage_best_selling_products_description() {
		sp_homepage_product_section_description( 'sp_homepage_best_sellers_products_description' );
	}
}