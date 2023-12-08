<?php
/**
 * Compatibility class
 *
 * @package Extra Product Options/Compatibility
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * The SEO Framework
 * https://wordpress.org/plugins/autodescription/
 *
 * @package Extra Product Options/Compatibility
 * @version 6.4
 */
final class THEMECOMPLETE_EPO_CP_TheSeoFramework {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_TheSeoFramework|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return THEMECOMPLETE_EPO_CP_TheSeoFramework
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'wc_epo_add_compatibility', [ $this, 'add_compatibility' ] );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @return void
	 * @since 1.0
	 */
	public function add_compatibility() {
		add_filter( 'the_seo_framework_do_adjust_archive_query', [ $this, 'the_seo_framework_do_adjust_archive_query' ], 10, 2 );
	}

	/**
	 * Alters archive query functionality
	 *
	 * @param boolean  $ret Whether to adjust the query.
	 * @param WP_Query $query The query object.
	 * @return boolean
	 */
	public function the_seo_framework_do_adjust_archive_query( $ret, $query ) {

		$post_type = $query->get( 'post_type', false );

		if ( THEMECOMPLETE_EPO_GLOBAL_POST_TYPE === $post_type ) {
			return false;
		}

		return $ret;
	}
}
