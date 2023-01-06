<?php

if ( ! function_exists( 'wcml_wpml_get_admin_notices' ) ) {
	/**
	 * We could not reuse the original function name `wpml_get_admin_notices`
	 * because we have a double definition when WPML is being activated,
	 * which causes a fatal error.
	 *
	 * @see sitepress-multilingual-cms/inc/wpml-private-actions.php
	 *
	 * @return WPML_Notices
	 */
	function wcml_wpml_get_admin_notices() {
		global $wpml_admin_notices;

		if ( ! $wpml_admin_notices ) {
			$wpml_admin_notices = new WPML_Notices( new WPML_Notice_Render() );
			$wpml_admin_notices->init_hooks();
		}

		return $wpml_admin_notices;
	}
}

if ( ! function_exists( 'wpml_is_ajax' ) ) {
	/**
	 * @see sitepress-multilingual-cms/inc/functions.php
	 *
	 * @return bool
	 */
	function wpml_is_ajax() {
		if ( defined( 'DOING_AJAX' ) ) {
			return true;
		}

		return ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && wpml_mb_strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) ? true : false;
	}
}