<?php
/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * The SEO Framework 
 * https://wordpress.org/plugins/autodescription/
 *
 * @package Extra Product Options/Compatibility
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_CP_theseoframework {

	/**
	 * The single instance of the class
	 *
	 * @since 1.0
	 */
	protected static $_instance = NULL;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		add_action( 'wc_epo_add_compatibility', array( $this, 'add_compatibility' ) );

	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @since 1.0
	 */
	public function add_compatibility() {
		add_filter( 'the_seo_framework_do_adjust_archive_query', array( $this, 'the_seo_framework_do_adjust_archive_query' ), 10, 2 );
	}

	public function the_seo_framework_do_adjust_archive_query( $ret, $query ) {

		$post_type = $query->get( 'post_type', FALSE );

		if ( $post_type == THEMECOMPLETE_EPO_GLOBAL_POST_TYPE ) {
			return FALSE;
		}

		return $ret;
	}

}
