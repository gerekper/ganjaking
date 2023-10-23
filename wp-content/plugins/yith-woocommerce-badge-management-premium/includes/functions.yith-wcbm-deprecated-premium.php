<?php
/**
 * Deprecated functions
 * Where functions come to die.
 *
 * @package YITH\BadgeManagementPremium\Functions
 */

defined( 'YITH_WCBM' ) || exit;

/** ------------------------------------------------------------------------------
 * Deprecated Filters
 */

$deprecated_filters = array(
	array(
		'deprecated' => 'yith_wcmb_is_wpml_parent_based_on_default_language',
		'since'      => '2.0.0',
		'use'        => 'yith_wcbm_is_wpml_parent_based_on_default_language',
		'params'     => 1,
	),
	array(
		'deprecated' => 'yith_wcmb_wpml_autosync_product_badge_translations',
		'since'      => '2.0.0',
		'use'        => 'yith_wcbm_wpml_autosync_product_badge_translations',
		'params'     => 1,
	),
	array(
		'deprecated' => 'yith_wcmb_badges_to_show_on_product',
		'since'      => '2.0.0',
		'use'        => 'yith_wcbm_badges_to_show_on_product',
		'params'     => 2,
	),
	array(
		'deprecated' => 'yith_wcmb_get_badges_premium',
		'since'      => '2.0.0',
		'use'        => 'yith_wcbm_get_badges_premium',
		'params'     => 2,
	),
);

yith_wcbm_handle_deprecated_filters( $deprecated_filters );


/** ------------------------------------------------------------------------------
 * Deprecated functions
 */

if ( ! function_exists( 'yith_wcmb_is_wpml_parent_based_on_default_language' ) ) {
	/**
	 * Check if is WPML parent based on default language
	 *
	 * @return bool
	 * @depreacted since 2.0.0
	 * @use        yith_wcbm_is_wpml_parent_based_on_default_language instead
	 */
	function yith_wcmb_is_wpml_parent_based_on_default_language() {
		return yith_wcbm_is_wpml_parent_based_on_default_language();
	}
}

if ( ! function_exists( 'yith_wcmb_wpml_autosync_product_badge_translations' ) ) {
	/**
	 * Autosync product badge translations
	 *
	 * @return bool
	 * @depreacted since 2.0.0
	 * @use        yith_wcbm_wpml_autosync_product_badge_translations instead
	 */
	function yith_wcmb_wpml_autosync_product_badge_translations() {
		return yith_wcbm_wpml_autosync_product_badge_translations();
	}
}
