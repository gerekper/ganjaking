<?php
/**
 * Update functions
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagement\Functions
 */

if ( ! function_exists( 'yith_wcbm_update_200_badges_meta' ) ) {
	/**
	 * Update Badges meta
	 *
	 * @return bool If true will repeat the process, otherwise it will stop
	 * @since 2.0.0
	 */
	function yith_wcbm_update_200_badges_meta() {
		$args      = array(
			'posts_per_page' => 10,
			'fields'         => 'ids',
			'post_type'      => YITH_WCBM_Post_Types::$badge,
			'post_status'    => 'any',
			'meta_key'       => '_badge_meta', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		);
		$badge_ids = get_posts( $args );
		if ( ! $badge_ids ) {
			return false;
		}
		foreach ( $badge_ids as $badge_id ) {
			yith_wcbm_update_badge_meta( $badge_id );
		}

		return true;
	}
}

if ( ! function_exists( 'yith_wcbm_update_200_products_badge_meta' ) ) {
	/**
	 * Update Products Badge meta
	 *
	 * @return bool
	 */
	function yith_wcbm_update_200_products_badge_meta() {
		$args        = array(
			'posts_per_page' => 10,
			'fields'         => 'ids',
			'post_type'      => 'product',
			'post_status'    => 'any',
			'meta_key'       => '_yith_wcbm_product_meta', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		);
		$product_ids = get_posts( $args );
		if ( ! $product_ids ) {
			return false;
		}

		foreach ( $product_ids as $product_id ) {
			yith_wcbm_update_product_badge_meta( $product_id );
		}

		return false;
	}
}

if ( ! function_exists( 'yith_wcbm_update_200_badges_settings' ) ) {
	/**
	 * Update Badges Settings function
	 *
	 * @since 2.0.0
	 */
	function yith_wcbm_update_200_badges_settings() {
		$force_positioning        = get_option( 'yith-wcbm-force-badge-positioning', 'no' );
		$enable_force_positioning = wc_bool_to_string( 'no' !== $force_positioning );
		$force_positioning        = in_array( $force_positioning, array( 'single-product', 'single-product-image', 'shop', 'everywhere' ), true ) ? $force_positioning : 'single-product';

		update_option( 'yith-wcbm-enable-force-badge-positioning', $enable_force_positioning );
		update_option( 'yith-wcbm-force-badge-positioning', $force_positioning );

		$hide_on_sale_badge     = wc_string_to_bool( get_option( 'yith-wcbm-hide-on-sale-default', 'no' ) );
		$override_on_sale_badge = wc_string_to_bool( get_option( 'yith-wcbm-product-badge-overrides-default-on-sale', 'no' ) );
		update_option( 'yith-wcbm-hide-on-sale-default', wc_bool_to_string( $hide_on_sale_badge || $override_on_sale_badge ) );
		update_option( 'yith-wcbm-when-hide-on-sale', $hide_on_sale_badge || ! $override_on_sale_badge ? 'all-products' : 'products-with-badge' );

		update_option( 'yith-wcbm-mobile-breakpoint', absint( str_replace( 'px', '', get_option( 'yith-wcbm-mobile-breakpoint', '768px' ) ) ) );
	}
}
