<?php
/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * ElasticPress 
 * https://wordpress.org/plugins/elasticpress/
 *
 * @package Extra Product Options/Compatibility
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_CP_elasticpress {

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

		add_filter( 'ep_skip_query_integration', array( $this, 'ep_skip_query_integration' ), 10, 2 );

	}

	public function ep_skip_query_integration( $ret, $query ) {

		$post_type = $query->get( 'post_type', FALSE );

		if ( $post_type == THEMECOMPLETE_EPO_GLOBAL_POST_TYPE ) {
			return TRUE;
		}

		return $ret;
	}


}
