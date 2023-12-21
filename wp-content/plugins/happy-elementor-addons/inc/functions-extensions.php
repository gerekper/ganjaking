<?php
/**
 * Filters function defination
 *
 * @package Happy_Addons
 * @since 2.13.0
 * @author HappyMonster
 *
 */
defined( 'ABSPATH' ) || die();

if ( ! function_exists( 'ha_is_adminbar_menu_enabled' ) ) {
	/**
	 * Check if Adminbar is enabled
	 *
	 * @return bool
	 */
	function ha_is_adminbar_menu_enabled() {
		return apply_filters( 'happyaddons/extensions/adminbar_menu', true );
	}
}

if ( ! function_exists( 'ha_is_background_overlay_enabled' ) ) {
	/**
	 * Check if Background Overlay is enabled
	 *
	 * @return bool
	 */
	function ha_is_background_overlay_enabled() {
		return apply_filters( 'happyaddons/extensions/background_overlay', true );
	}
}

if ( ! function_exists( 'ha_is_css_transform_enabled' ) ) {
	/**
	 * Check if CSS Transform is enabled
	 *
	 * @return bool
	 */
	function ha_is_css_transform_enabled() {
		return apply_filters( 'happyaddons/extensions/css_transform', true );
	}
}

if ( ! function_exists( 'ha_is_floating_effects_enabled' ) ) {
	/**
	 * Check if Floating Effects is enabled
	 *
	 * @return bool
	 */
	function ha_is_floating_effects_enabled() {
		return apply_filters( 'happyaddons/extensions/floating_effects', true );
	}
}

if ( ! function_exists( 'ha_is_grid_layer_enabled' ) ) {
	/**
	 * Check if Grid Layer is enabled
	 *
	 * @return bool
	 */
	function ha_is_grid_layer_enabled() {
		return apply_filters( 'happyaddons/extensions/grid_layer', true );
	}
}

if ( ! function_exists( 'ha_is_wrapper_link_enabled' ) ) {
	/**
	 * Check if Wrapper Link is enabled
	 *
	 * @return bool
	 */
	function ha_is_wrapper_link_enabled() {
		return apply_filters( 'happyaddons/extensions/wrapper_link', true );
	}
}

if ( ! function_exists( 'ha_is_happy_clone_enabled' ) ) {
	/**
	 * Check if Happy Clone is enabled
	 *
	 * @return bool
	 */
	function ha_is_happy_clone_enabled() {
		return apply_filters( 'happyaddons/extensions/happy_clone', true );
	}
}

if ( ! function_exists( 'ha_is_on_demand_cache_enabled' ) ) {
	/**
	 * Check if On Demand Cache is enabled
	 *
	 * @return bool
	 */
	function ha_is_on_demand_cache_enabled() {
		return apply_filters( 'happyaddons/extensions/on_demand_cache', true );
	}
}

if ( ! function_exists( 'ha_is_equal_height_enabled' ) ) {
	/**
	 * Check if equal height is enabled
	 *
	 * @return bool
	 */
	function ha_is_equal_height_enabled() {
		return apply_filters( 'happyaddons/extensions/equal_height', true );
	}
}

if ( ! function_exists( 'ha_is_shape_divider_enabled' ) ) {
	/**
	 * Check if Happy Shape Divider is enabled
	 *
	 * @return bool
	 */
	function ha_is_shape_divider_enabled() {
		return apply_filters( 'happyaddons/extensions/shape_divider', true );
	}
}
