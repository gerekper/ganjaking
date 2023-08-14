<?php

/**
 * SearchWP BeaverBuilder.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Integrations;

/**
 * Class BeaverBuilder is responsible for customizing SearchWP's Native implementation to work with BeaverBuilder queries.
 *
 * @since 4.1.5
 */
class BeaverBuilder extends PageBuilder {

	/**
	 * Name used for canonical reference to Integration.
	 *
	 * @since 4.1.5
	 * @var   string
	 */
	protected $name = 'beaver-builder';

	/**
	 * Constructor.
	 *
	 * @since 4.1.8
	 * @return void
	 */
	public function __construct() {
		if ( ! has_action( 'parse_query', [ $this, 'maybe_integrate_themer' ] ) ) {
			add_action( 'parse_query', [ $this, 'maybe_integrate_themer' ] );
		}

		add_filter( 'fl_builder_loop_query_args', [ $this, 'maybe_hijack_search_module' ], 20 );
	}

	/**
	 * Allow SearchWP to take over any Beaver Builder WP_Query doing a search.
	 *
	 * @since 4.1.14
	 * @param mixed $query_args Incoming Beaver Builder WP_Query args.
	 * @return mixed Outgoing WP_Query args.
	 */
	public function maybe_hijack_search_module( $query_args ) {
		if ( ! apply_filters(
			'searchwp\integration\beaver-builder-search-module',
			is_plugin_active( 'bb-plugin/fl-builder.php' ) )
		) {
			return;
		}

		// If there's no search, bail out.
		if ( ! self::is_applicable( $query_args ) ) {
			return $query_args;
		}

		// Force SearchWP to run on this query.
		add_filter( 'searchwp\native\force', function( $force, $args ) {
			return self::is_applicable( $args['query']->query_vars );
		}, 20, 2 );

		add_filter( 'searchwp\native\strict', function( $strict, $query ) {
			return ! self::is_applicable( $query->query_vars );
		}, 20, 2 );

		add_filter( 'searchwp\native\args', function( $args, $query ) {
			$args['engine'] = apply_filters( 'searchwp\integration\pagebuilder\engine', 'default', [
				'context' => $this->name . SEARCHWP_SEPARATOR . 'module',
				'query'   => $query,
			] );

			return $args;
		}, 20, 2 );

		return $query_args;
	}

	/**
	 * Returns whether the query args indicate this is a Beaver Builder search query.
	 *
	 * @since 4.1.14
	 * @param array $query_args Incoming query args.
	 * @return bool Whether applicable.
	 */
	public static function is_applicable( $query_args ) {
		return isset( $query_args['fl_builder_loop'] )
			&& ! empty( $query_args['fl_builder_loop'] )
			&& isset( $query_args['s'] )
			&& ! empty( trim( $query_args['s'] ) );
	}

	/**
	 * Integrate when Beaver Builder Themer is used for a Search Archive.
	 *
	 * @since 4.1.8
	 */
	public function maybe_integrate_themer() {
		if ( ! apply_filters( 'searchwp\integration\beaver-builder-themer', false ) ) {
			return;
		}

		if ( ! is_search() ) {
			return;
		}

		if ( ! class_exists( '\\FLThemeBuilderLayoutData' ) ) {
			return;
		}

		// Make sure Beaver Builder Themer is being used for this archive.
		$layouts = \FLThemeBuilderLayoutData::get_current_page_layouts( 'archive' );

		if ( 0 == count( $layouts ) ) {
			return;
		}

		$types     = wp_list_pluck( $layouts, 'type' );
		$locations = call_user_func_array( 'array_merge',
			array_values( wp_list_pluck( $layouts, 'locations' ) ) );

		if ( ! in_array( 'archive', $types, true ) || ! in_array( 'general:search', $locations, true ) ) {
			return;
		}

		$this->modify_native_behavior();

		// Prevent redundancy.
		remove_action( 'parse_query', [ $this, 'maybe_integrate' ] );
	}
}
