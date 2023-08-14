<?php

/**
 * SearchWP WooCommerceAdminSearch.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Integrations;

/**
 * Class WooCommerceAdminSearch is responsible for supporting Admin WooCommerce searches.
 *
 * @since 4.1.4
 */
class WooCommerceAdminSearch {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	function __construct() {
		add_filter( 'searchwp\native\force', [ $this, 'maybe_force_admin_search' ] );
		add_filter( 'searchwp\native\short_circuit', function( $short_circuit, $query ) {
			if ( ! self::environment_pre_check() ) {
				return $short_circuit;
			}

			return ! self::is_woocommerce_admin_search( $short_circuit, $query );
		}, 8, 2 );
	}

	/**
	 * Return whether we are in the admin and WooCommerce is active.
	 *
	 * @since 4.1.16
	 * @return bool
	 */
	public static function environment_pre_check() {
		return is_admin()
			&& (
				function_exists( 'is_plugin_active' )
				&& is_plugin_active( 'woocommerce/woocommerce.php' )
			);
	}

	/**
	 * Whether the current request is a WooCommerce Admin search.
	 *
	 * @since 4.1.4
	 * @return boolean
	 */
	public static function is_woocommerce_admin_search( $short_circuit, $query ) {
		if ( ! self::environment_pre_check() ) {
			return $short_circuit;
		}

		if ( ! is_archive() || ! isset( $_GET['s'] ) || empty( stripslashes( $_GET['s'] ) ) ) {
			return $short_circuit;
		}

		if ( ! isset( $_GET['post_type'] ) || 'product' !== $_GET['post_type'] ) {
			return $short_circuit;
		}

		return apply_filters( 'searchwp\integration\woocommerce_admin_search\force', true );
	}

	/**
	 * Force an admin search when applicable.
	 *
	 * @since 4.1.4
	 * @param mixed $args
	 * @return bool
	 */
	public function maybe_force_admin_search( $args ) {
		if ( ! self::is_woocommerce_admin_search( false, null ) ) {
			return false;
		}

		// If this is an admin search and there is an admin engine with Products, force it to happen.
		// We have to do this because $query->is_search() is false at runtime.
		$admin_engine = \SearchWP\Settings::get_admin_engine();

		if ( empty( $admin_engine ) ) {
			return $args;
		}

		$engine_model = new \SearchWP\Engine( $admin_engine );

		if ( ! array_key_exists( 'post' . SEARCHWP_SEPARATOR . 'product', $engine_model->get_sources() ) ) {
			return $args;
		}

		add_filter( 'searchwp\native\args', array( $this, 'set_admin_search_args' ) );

		return true;
	}

	/**
	 * Sets the arguments for the search.
	 *
	 * @since 4.1.4
	 * @param array $args The query arguments.
	 * @return array The query arguments.
	 */
	public function set_admin_search_args( $args ) {
		remove_filter( 'searchwp\native\args', array( $this, 'set_admin_search_args' ) );

		if ( array_key_exists( 'product_search', $args ) && $args['product_search'] ) {
			$args['post__in'] = [];
		}

		return $args;
	}
}
