<?php
/**
 * SearchWP's Indexer.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP;

use SearchWP\Entry;
use SearchWP\Source;
use SearchWP\Entries;
use SearchWP\Processes\BackgroundProcess;

/**
 * Class Indexer maintains Sources in the Index.
 *
 * @since 4.0
 */
class Indexer extends BackgroundProcess {

	/**
	 * A reference to the Index itself.
	 *
	 * @since 4.0
	 * @var   Index
	 */
	private $index;

	/**
	 * The name of this action.
	 *
	 * @since 4.0
	 * @var string
	 */
	protected $action = 'indexer';

	/**
	 * Indexer constructor.
	 *
	 * @since 4.0
	 */
	function __construct() {
		parent::__construct();

		$this->init();

		// When we switch sites, the Sources will have new database tables.
		// The provider does this as well, but this hook is left in place in case of external use.
		add_action( 'switch_blog', [ $this, 'init' ] );

		// When a site is deleted from the network, we need to drop it too.
		add_action( 'wp_uninitialize_site', [ $this, 'wp_delete_site' ] );

		$this->check_for_process_failure();
	}

	/**
	 * Initializer.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function init() {
		$this->index = \SearchWP::$index;
		do_action( 'searchwp\indexer\init' );
	}

	/**
	 * Handler for each batch of entries to index.
	 *
	 * @since 4.0
	 * @param mixed $batch
	 * @return false|mixed|bool[][]
	 */
	protected function task( $batch ) {
		do_action( 'searchwp\debug\log', 'Processing batch', 'indexer' );
		do_action( 'searchwp\indexer\batch' );

		$source = $batch['source'];
		$site   = $batch['site'];

		$switched_site = false;
		if ( $site != get_current_blog_id() ) {
			switch_to_blog( $site );
			$switched_site = true;
		}

		$index = \SearchWP::$index;

		// Iterate over the entry IDs for this batch and index them.
		foreach ( $batch['ids'] as $batch_id => $source_id ) {
			// IDs are valid if they have been marked as queued.
			// This check needs to happen on each iteration in case of index failure, in which case
			// the failing entry will be marked as omitted while this task is still running.
			$valid_ids = $index->validate_queued_ids( array_values( $batch['ids'] ), $source );

			// If this ID is invalid, drop it.
			if ( ! in_array( $source_id, $valid_ids, true ) ) {
				unset( $batch['ids'][ $batch_id ] );
				do_action( 'searchwp\debug\log', 'Invalid ID found in [' . $source . '] batch: ' . $source_id, 'indexer' );

				continue;
			}

			$entry_orig = new Entry( $source, $source_id );
			$entry = apply_filters( 'searchwp\indexer\entry', $entry_orig );

			if ( ! $entry instanceof Entry ) {
				do_action( 'searchwp\debug\log', 'Invalid Entry detected: omitting', 'indexer' );
				do_action( 'searchwp\debug\log', $entry, 'indexer' );
				$index->mark_entry_as( $entry_orig, 'omitted' );

				continue;
			}

			do_action( 'searchwp\debug\log', 'Indexing ' . $source . ':' . $source_id, 'indexer' );
			$result = $index->add( $entry );

			// The add method returns true if the entry was successfully indexed. It
			// returns false if there was an unknown error. If the indexer was not
			// able to index everything in one pass, the remaining content is returned.
			if ( true === $result ) {
				unset( $batch['ids'][ $batch_id ] );
			} elseif ( false === $result ) {
				// Flag this entry as problematic and skipped.
				$index->mark_entry_as( $entry, 'omitted' );
				unset( $batch['ids'][ $batch_id ] );
			} else {
				// There was too much data to index in one pass, this is the leftover.
				$batch['ids'][ $batch_id ] = $result;
			}
		}

		// Throttler.
		sleep(1);

		if ( $switched_site ) {
			restore_current_blog();
		}

		if ( empty( $batch['ids'] ) ) {
			// Everything was indexed successfully!
			return false;
		} else {
			// There is more work to do; store where we left off.
			return $batch;
		}
	}

	/**
	 * Triggers a forced cycling of the Indexer.
	 *
	 * @since 4.0
	 */
	public function trigger() {
		$this->clear_scheduled_event();
		$this->queue_unindexed_entries();
		$this->dispatch();
	}

	/**
	 * Complete the process.
	 *
	 * @since 4.0
	 */
	protected function complete() {
		parent::complete();

		// Determine if there is more to index.
		$more_to_do = $this->queue_unindexed_entries();

		if ( $more_to_do ) {
			do_action( 'searchwp\debug\log', 'Queueing next batch', 'indexer' );
			$this->dispatch();

			return;
		}

		sleep( 2 );

		// If there are queued entries but no batches to process those entries, something
		// has gone wrong during indexing and we should create a new batch out of the queued
		// items otherwise they'll be stuck in limbo and never processed.
		$entries_in_limbo = \SearchWP::$index->get_queued();

		if ( ! empty( $entries_in_limbo ) ) {
			do_action( 'searchwp\debug\log', 'Additional entries found, reintroducing ' . count( $entries_in_limbo ), 'indexer' );
			$this->_destroy_queue();
			$this->queue_unindexed_entries();
			$this->dispatch();

			return;
		}

		do_action( 'searchwp\indexer\complete' );
		do_action( 'searchwp\debug\log', 'Index built', 'indexer' );
	}

	/**
	 * !!!!! USE WITH CAUTION !!!!!
	 * Wakes up the indexer by force deleting the queue and triggering an update.
	 *
	 * @since 4.0
	 */
	public function _wake_up() {
		$this->_destroy_queue();
		$this->trigger();
	}

	/**
	 * Executes loopback call to determine if it works.
	 *
	 * @since 4.0
	 * @return string
	 */
	public function _method() {
		if ( apply_filters( 'searchwp\indexer\alternate', false ) ) {
			return 'alternate';
		}

		$args             = $this->get_post_args();
		$args['blocking'] = true;
		$args['timeout']  = 0.5;
		$args['body']     = 'SearchWP Indexer Communication Test';

		try	{
			$cache_key = md5( serialize( $args ) . esc_url_raw( $this->get_query_url() ) );
		} catch ( \Exception $e ) {
			// Something went wrong with the args so just skip the cache.
			$cache_key = false;
		}

		$response = ! empty( $cache_key ) ? wp_cache_get( $cache_key, '' ) : '';

		if ( empty( $response ) ) {
			$response = wp_remote_post( esc_url_raw( $this->get_query_url() ), $args );

			if ( ! empty( $cache_key ) ) {
				wp_cache_set( $cache_key, $response, '', 1 );
			}
		}

		if (
			is_wp_error( $response )
			&& isset( $response->errors['http_request_failed'] )
			&& isset( $response->errors['http_request_failed'][0] )
			&& false !== strpos( strtolower( $response->errors['http_request_failed'][0] ), 'could not resolve' )
		) {
			return 'alternate';
		} else if (
			! is_wp_error( $response)
			&& isset( $response['response']['code'] )
			&& 401 === (int) $response['response']['code']
		) {
			return 'basicauth';
		}

		return 'default';
	}

	/**
	 * !!!!! USE WITH CAUTION !!!!!
	 * Destroys the background process by force deleting the queue and force unlocking.
	 *
	 * @since 4.0
	 */
	public function _destroy_queue() {
		global $wpdb;

		parent::_destroy_queue();

		// Remove queued entries.
		$status = \SearchWP::$index->get_tables()['status']->table_name;
		$wpdb->query( $wpdb->prepare( "
			DELETE FROM {$status}
			WHERE indexed IS NULL
			AND omitted IS NULL
			AND queued IS NOT NULL
			AND site = %d",
			get_current_blog_id()
		) );
	}

	/**
	 * Find and queue Entries to be indexed.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function queue_unindexed_entries() {
		do_action( 'searchwp\indexer\update' );

		if ( is_multisite() ) {
			foreach( get_sites( [ 'fields' => 'ids' ] ) as $site_id ) {
				$site_has_updates = false;

				switch_to_blog( $site_id );

				$greedy = apply_filters( 'searchwp\indexer\greedy', false, [
					'site_id' => $site_id,
				] );

				$index_this_site = $greedy || (
					is_plugin_active_for_network( 'searchwp/index.php' )
					|| is_plugin_active( 'searchwp/index.php' )
				);

				if ( $index_this_site ) {
					$site_has_updates = $this->_queue_unindexed_site_entries();
				}

				restore_current_blog();

				if ( $site_has_updates ) {
					break;
				}
			}
		} else {
			$site_has_updates = $this->_queue_unindexed_site_entries();
		}

		if ( $site_has_updates ) {
			do_action( 'searchwp\indexer\has_updates' );
		}

		return $site_has_updates;
	}

	/**
	 * Check for unhandled Source Entries for this site and enqueue them.
	 *
	 * @since 4.0
	 * @return bool Whether there are updates.
	 */
	public function _queue_unindexed_site_entries() {
		$has_updates = 0;
		$batch_size  = Settings::get_single( 'reduced_indexer_aggressiveness', 'boolean' ) ? 10 : 50;
		$limit       = absint( apply_filters( 'searchwp\indexer\batch_size', $batch_size ) );

		// Iterate over Engines to determine what to index. This will automatically
		// apply Engine Rules as we iterate, and index anything that meets any criteria
		// for any saved engine. Querying will also apply the same Engine Rules.
		foreach ( Settings::get_engines() as $engine ) {
			foreach ( $engine->get_sources() as $source ) {
				$limit = absint( apply_filters( 'searchwp\indexer\batch_size\\' . $source->get_name() , $batch_size ) );

				if ( $limit < 1 ) {
					$limit = 1;
				}

				$unindexed_entries = $source->get_unindexed_entries( $limit );

				if ( ! empty( $unindexed_entries->get_ids() ) ) {
					$ids = $this->queue_unindexed_entries_for_source( $unindexed_entries, $source );
					$has_updates = count( $ids );

					break;
				}
			}

			if ( $has_updates ) {
				break;
			}
		}

		return $has_updates;
	}

	/**
	 * When unindexed Entries are found they need to be flagged as queued and added to the queue itself.
	 *
	 * @since 4.0
	 * @param Entries $entries The Entries to queue.
	 * @param Source  $source  The Source of the Entries
	 * @return void
	 */
	private function queue_unindexed_entries_for_source( Entries $entries, Source $source ) {
		// This is a two-part process. The first part introduces these Entries to the Index.
		$entries = $this->index->introduce( $entries );
		$ids = $entries->get_ids();

		if ( empty( $ids ) ) {
			return $ids;
		}

		// The second part adds these Entries to background process queue.
		$this->push_to_queue( [
			'ids'    => $ids,
			'source' => $source->get_name(),
			'site'   => get_current_blog_id(),
		] );

		// Save this process batch.
		$this->save();

		return $ids;
	}

	/**
	 * Callback after a site is deleted from the network.
	 *
	 * @since 4.0
	 * @param WP_Site $site The site that was deleted.
	 * @return void
	 */
	public function wp_delete_site( $site ) {
		$this->index->drop_site( $site->blog_id );
	}

	/**
	 * Pauses the indexer.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function pause() {
		$this->lock_process();
		Settings::update( 'indexer_paused', true );
	}

	/**
	 * Unpauses the indexer.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function unpause() {
		$this->unlock_process();
		Settings::update( 'indexer_paused', false );
	}

	/**
	 * Determine whether the indexer got 'stuck' when trying to do its job.
	 *
	 * @since 4.0
	 * @return void
	 */
	private function check_for_process_failure() {
		if ( ! $this->is_process_asleep() ) {
			return;
		}

		// It's most likely that the entry with the lowest queued date is the cause for failure.
		// We need to check for and retrieve that entry and mark it as omitted.
		$batch = $this->get_batch();

		if ( empty( $batch ) || ! isset( $batch->data[0] ) || empty( $batch->data[0] ) ) {
			return;
		}

		$site   = $batch->data[0]['site'];
		$source = $batch->data[0]['source'];
		$ids    = $batch->data[0]['ids'];

		$entry_being_indexed = \SearchWP::$index->get_assumed_entry_being_indexed();

		if (
			empty( $entry_being_indexed )
			|| ! in_array( $entry_being_indexed->id, $ids )
			|| $source != $entry_being_indexed->source
			|| $site != $entry_being_indexed->site
		) {
			return;
		}

		do_action( 'searchwp\debug\log', 'Failure detected ' . $source . ':' . $entry_being_indexed->id, 'indexer' );

		\SearchWP::$index->mark_entry_as( new Entry( $source, $entry_being_indexed->id, false ), 'omitted' );

		$this->_wake_up();
	}
}
