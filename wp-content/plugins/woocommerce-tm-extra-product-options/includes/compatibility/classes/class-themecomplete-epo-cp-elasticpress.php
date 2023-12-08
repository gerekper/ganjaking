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
 * ElasticPress
 * https://wordpress.org/plugins/elasticpress/
 *
 * @package Extra Product Options/Compatibility
 * @version 6.4
 */
final class THEMECOMPLETE_EPO_CP_Elasticpress {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_Elasticpress|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return THEMECOMPLETE_EPO_CP_Elasticpress
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
		add_filter( 'ep_skip_query_integration', [ $this, 'ep_skip_query_integration' ], 10, 2 );
	}

	/**
	 * Skip query integration
	 *
	 * @param boolean  $ret if we want to skip query integration.
	 * @param WP_Query $query The query oibject.
	 * @return boolean
	 */
	public function ep_skip_query_integration( $ret, $query ) {
		$post_type = $query->get( 'post_type', false );

		if ( THEMECOMPLETE_EPO_GLOBAL_POST_TYPE === $post_type ) {
			return true;
		}

		return $ret;
	}
}
