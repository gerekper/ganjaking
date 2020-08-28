<?php

/**
 * SearchWP CLI.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP;

/**
 * Class CLI is responsible for adding WP CLI commands.
 *
 * @since 4.0
 */
class CLI {

	/**
	 * Constructor.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function __construct() {
		if ( ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			return;
		}

		\WP_CLI::add_command( 'searchwp index', [ $this, 'index' ] );
	}

	/**
	 * Retrieves unindexed site entries and introduces them to the Index.
	 *
	 * @since 4.0
	 * @return array
	 */
	private static function setup_site_entries() {
		global $wpdb;

		$entries = [];
		$index   = \SearchWP::$index;

		foreach ( Settings::get_engines( true ) as $engine ) {
			foreach ( $engine->get_sources() as $source ) {
				$site      = get_current_blog_id();
				$timestamp = current_time( 'mysql' );

				while ( ! empty ( $unindexed_entries_ids = $source->get_unhandled_ids( 100 ) ) ) {
					$these_entries = array_map( function( $entry_id ) use ( $source, $site ) {
						return [
							'source' => $source->get_name(),
							'id'     => $entry_id,
							'site'   => $site,
						];
					}, $unindexed_entries_ids );

					unset( $unindexed_entries_ids );

					// Introduce these Entries to the Index.
					$values       = [];
					$placeholders = [];

					foreach ( $these_entries as $this_entry ) {
						array_push(
							$values,
							$this_entry['id'],
							$this_entry['source'],
							$timestamp,
							$site
						);
						$placeholders[] = '( %s, %s, %s, %d )';
					}

					$wpdb->query( $wpdb->prepare( "
						INSERT INTO {$index->get_tables()['status']->table_name}
						( id, source, queued, site )
						VALUES " . implode( ', ', $placeholders ),
						$values
					) );

					$entries = array_merge( $entries, $these_entries );

					unset( $these_entries );
				}
			}
		}

		return $entries;
	}

	/**
	 * Trigger the Indexer, optionally rebuilding it.
	 *
	 * ## OPTIONS
	 *
	 * [--site=<all|ids>]
	 * : Whether to rebuild the index before indexing.
	 *
	 * [--rebuild]
	 * : Whether to rebuild the index before indexing.
	 *
	 * @since 4.0
	 */
	public static function index( $args = [], $assoc_args = [] ) {
		gc_enable();

		add_filter( 'searchwp\debug', '__return_false', 99999 );

		$arguments = wp_parse_args( $assoc_args, array(
			'rebuild' => false, // Whether to rebuild the index before indexing.
			'site'    => 'all', // Whether to rebuild the index before indexing.
		) );

		if ( is_numeric( $arguments['site'] ) ) {
			$arguments['site'] = [ absint( $arguments['site'] ) ];
		} else if ( false !== strpos( $arguments['site'], ',' ) ) {
			$arguments['site'] = array_filter( array_unique( array_map( function( $site ) {
				return is_numeric( $site ) ? absint( $site ) : false;
			}, explode( ',', $arguments['site'] ) ) ) );
		} else {
			$arguments['site'] = 'all';
		}

		$index   = \SearchWP::$index;
		$indexer = \SearchWP::$indexer;

		// Prevent the indexer from running because this supercedes it.
		$indexer->pause();
		$indexer->_destroy_queue();

		/**
		 * Rebuild the index?
		 */
		if ( ! empty( $arguments['rebuild'] ) && 'all' === $arguments['site'] ) {
			\WP_CLI::line( 'Dropping index' );
			$index->truncate_tables();
		} else if ( is_array( $arguments['site'] ) ) {
			foreach( $arguments['site'] as $site_id ) {
				\WP_CLI::line( 'Dropping index for site ' . $site_id );
				$index->drop_site( $site_id );
			}
		}

		$entries = [];

		if ( is_multisite() ) {
			foreach( get_sites( [ 'fields' => 'ids' ] ) as $site_id ) {
				if ( is_array( $arguments['site'] ) && ! in_array( $site_id, $arguments['site'] ) ) {
					\WP_CLI::line( 'Skipping site ' . $site_id );
					continue;
				}

				\WP_CLI::line( 'Enqueueing entries for site ' . $site_id );

				switch_to_blog( $site_id );
				$indexer->init();
				$entries = array_merge( $entries, self::setup_site_entries() );
				restore_current_blog();
				$indexer->init();
			}
		} else {
			\WP_CLI::line( 'Enqueueing entries...' );
			$entries = self::setup_site_entries();
		}

		if ( ! empty( $entries ) ) {
			$progress = \WP_CLI\Utils\make_progress_bar( 'Building index:', count( $entries ) );

			foreach ( $entries as $key => $entry ) {

				$switched_site = false;
				if ( is_multisite() && $entry['site'] != get_current_blog_id() ) {
					switch_to_blog( $entry['site'] );
					$indexer->init();
					$switched_site = true;
				}

				$source = $index->get_source_by_name( $entry['source'] );
				$entry  = apply_filters( 'searchwp\indexer\entry', new Entry( $source, $entry['id'] ) );

				if ( ! $entry instanceof Entry ) {
					$progress->tick();
					continue;
				}

				if ( false === $index->add( $entry ) ) {
					$index->mark_entry_as( $entry, 'omitted' );
				}

				if ( $switched_site ) {
					restore_current_blog();
					$indexer->init();
				}

				// Clean up variables and cache usage (because it doesn't apply here).
				unset( $entry );
				unset( $source );
				unset( $entries[ $key ] );
				gc_collect_cycles();

				$progress->tick();
			}

			$progress->finish();
		}

		$indexer->unpause();

		\WP_CLI::success( 'Index built!' );
	}
}
