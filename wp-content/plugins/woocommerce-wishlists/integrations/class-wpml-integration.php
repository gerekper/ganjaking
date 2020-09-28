<?php
/**
 * WooCommerce WPML Compatibility
 */
if ( !defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( !class_exists( 'WPML_Compatibility' ) ) :

	class WPML_Compatibility {

		function __construct() {
			add_action( 'init', array( $this, 'init' ), 9 );
		}

		function init() {
			add_filter( 'wc_wishlists_get_page_id', array( $this, 'translate_page_id' ), 10, 1 );
		}

		function translate_page_id( $id ) {
			$id = icl_object_id( $id, 'page', true, ICL_LANGUAGE_CODE );

			return $id;
		}

	}

endif; // Class exists check

if ( class_exists( 'SitePress' ) ) {
	$wpml_compatibility = new WPML_Compatibility();
}