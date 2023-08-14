<?php
/**
 * SearchWP's Indexer.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP;

use SearchWP\Entry;
use SearchWP\BackgroundProcess;

class Indexer extends BackgroundProcess {
	/**
	 * The Index Controller.
	 *
	 * @since 4.1
	 * @var mixed
	 */
	private $index;

	/**
	 * Constructor.
	 *
	 * @since 4.1
	 */
	function __construct() {
		$this->init();

		// When we switch sites, the Sources will have new database tables.
		// The provider does this as well, but this hook is left in place in case of external use.
		add_action( 'switch_blog', [ $this, 'init' ] );

		// When a site is deleted from the network, we need to drop it too.
		add_action( 'wp_uninitialize_site', [ $this, 'wp_delete_site' ] );

		if ( Settings::get( 'indexer_paused' ) ) {
			$this->enabled = false;
		}
	}

	/**
	 * Initializer.
	 *
	 * @since 4.1
	 * @return void
	 */
	public function init() {
		$this->index      = \SearchWP::$index;
		$this->name       = 'indexer';
		$this->identifier = SEARCHWP_PREFIX . 'indexer';

		parent::init();

		do_action( 'searchwp\indexer\init', $this );
	}

	/**
	 * Handler if/when process time limit has been exceeded.
	 *
	 * @since 4.1
	 * @return bool|array
	 */
	protected function time_limit_exceeded() {
		// Retrieve the last Entry we tried to index.
		$latest_entry = get_site_option( $this->identifier . '_indexing' );

		// If nothing is there then there was no failure.
		if ( empty( $latest_entry ) || ! is_array( $latest_entry ) ) {
			return false;
		}

		// The failed entry was timestamped at index start. If that timestamp plus
		// the process time limit is less than now, it has not failed.
		if ( time() < absint( $latest_entry['timestamp'] ) + absint( apply_filters( 'searchwp\background_process\process_time_limit', 60 ) ) ) {
			return false;
		}

		// We have exceeded the time limit on the failed entry.
		do_action( 'searchwp\debug\log', 'Process time limit exceeded!', 'indexer' );

		return true;
	}

	/**
	 * Executed when the process has failed. Omits latest Entry we tried to index.
	 *
	 * @since 4.1.8
	 * @return mixed
	 */
	protected function handle_process_failure() {
		// The failed entry was defined in time_limit_exceeded().
		$failed_entry = get_site_option( $this->identifier . '_indexing' );

		if ( empty( $failed_entry ) || ! is_array( $failed_entry ) ) {
			do_action( 'searchwp\debug\log', 'Irrecoverable process failure.', 'indexer' );

			return;
		}

		// If the failed Entry was not part of this site, the indexing process failed over there, don't mess with it.
		if ( get_current_blog_id() != $failed_entry['site'] ) {
			do_action( 'searchwp\debug\log', 'Indexing failed on site [' . $failed_entry['site'] . '] exiting to let that job finish', 'indexer' );

			return;
		}

		do_action( 'searchwp\debug\log', $failed_entry['source'] . ':' . $failed_entry['id'] . ' failed to index', 'indexer' );
		delete_site_option( $this->identifier . '_indexing' );
		$this->index->mark_entry_as( new Entry( $failed_entry['source'], $failed_entry['id'], false, false ), 'omitted' );
	}

	/**
	 * Uninstallation routine triggered in uninstall.php.
	 *
	 * @since 4.1
	 * @return void
	 */
	public function _uninstall() {
		delete_site_option( $this->identifier . '_indexing' );

		parent::_uninstall();
	}

	/**
	 * Callback after a site is deleted from the network.
	 *
	 * @since 4.1
	 * @param WP_Site $site The site that was deleted.
	 * @return void
	 */
	public function wp_delete_site( $site ) {
		$this->index->drop_site( $site->blog_id );
	}

	/**
	 * Cycles the indexer. Indexes queued entries, finds entries to queue.
	 *
	 * @since 4.1
	 * @return int Number of Entries queued during this cycle.
	 */
	public function cycle() {
		$to_index = $this->index->get_queued();

		// First priority is to index what has been queued already.
		if ( ! empty( $to_index ) ) {
			do_action( 'searchwp\debug\log', 'Processing batch (' . count( $to_index ) . ')', 'indexer' );
			$this->index_entries( $to_index );
		}

		// Second priority (if there is nothing ot index) is to queue the next batch.
		$has_updates = $this->queue_unindexed_entries();

		// If there are entries to index, schedule that action.
		if ( $has_updates ) {
			do_action( 'searchwp\debug\log', 'Queued next batch (' . absint( $has_updates ) . ')', 'indexer' );
		} else {
			// If the cron health check is running, this is a good place to be but we don't need to know about it.
			if ( ! wp_doing_cron() ) {
				do_action( 'searchwp\indexer\complete' );
				do_action( 'searchwp\debug\log', 'Index built', 'indexer' );

				delete_site_option( $this->identifier . '_indexing' );
			}
		}

		return $has_updates;
	}

	/**
	 * Destroys the queue and clears the schedule.
	 *
	 * @since 4.1
	 * @return void
	 */
	public function _destroy_queue() {
		global $wpdb;

		$status = \SearchWP::$index->get_tables()['status']->table_name;
		$wpdb->query( $wpdb->prepare( "
			DELETE FROM {$status}
			WHERE indexed IS NULL
			AND omitted IS NULL
			AND queued IS NOT NULL
			AND site = %d",
			get_current_blog_id()
		) );

		if ( self::use_legacy_lock() ) {
			$this->unlock_process();
		}
	}

	/**
	 * Forcefully wakes up the indexer by destroying the queue and triggering itself.
	 *
	 * @since 4.1
	 * @return void
	 */
	public function _wake_up() {
		do_action( 'searchwp\debug\log', 'Waking up', 'indexer' );
		$this->_destroy_queue();
		sleep( 1 );
		$this->trigger();
	}

	/**
	 * Index queued Entries.
	 *
	 * @since 4.1
	 * @param \stdClass[] $to_index Entries to index as retrieved by \SearchWP\Index\Controller::get_queued().
	 * @return void
	 */
	private function index_entries( array $to_index ) {
		do_action( 'searchwp\indexer\batch' );

		$start_time = time();

		// Detect whether this is a repeated index attempt e.g. caused an Error
		// of some sort but went under the radar of the health check which
		// in turn could cause an infinite indexing loop on this Entry.
		$last_indexed = get_site_option( $this->identifier . '_indexing' );
		if (
			! empty( $last_indexed )
			&& isset( $to_index[0] )
			&& ( $to_index[0]->source === $last_indexed['source'] )
			&& ( $to_index[0]->id === $last_indexed['id'] )
		) {
			do_action( 'searchwp\debug\log', "Detected repeated index attempt: omitting {$to_index[0]->source}:{$to_index[0]->id}", 'indexer' );
			$redundant_entry = new Entry( $to_index[0]->source, $to_index[0]->id, false );
			$this->index->mark_entry_as( $redundant_entry, 'omitted' );
			unset( $to_index[0] );
		}

		foreach ( $to_index as $entry_to_index ) {
			$source     = $entry_to_index->source;
			$source_id  = $entry_to_index->id;

			do_action( 'searchwp\debug\log', 'Indexing ' . $source . ':' . $source_id, 'indexer' );

			update_site_option(
				$this->identifier . '_indexing', [
					'source'    => $source,
					'id'        => $source_id,
					'timestamp' => current_time( 'timestamp' ),
					'site'      => get_current_blog_id(),
				], 'no'
			);

			$entry_orig = new Entry( $source, $source_id );
			$entry      = apply_filters( 'searchwp\indexer\entry', $entry_orig );

			if ( ! $entry instanceof Entry ) {
				do_action( 'searchwp\debug\log', 'Invalid Entry detected: omitting', 'indexer' );
				do_action( 'searchwp\debug\log', $entry, 'indexer' );
				$this->index->mark_entry_as( $entry_orig, 'omitted' );

				continue;
			}

			$result = $this->index->add( $entry );

			// Did this Entry fail to index?
			if ( false === $result ) {
				do_action( 'searchwp\debug\log', '[Failed] Omitting ' . $source . ':' . $source_id, 'indexer' );
				$this->index->mark_entry_as( $entry, 'omitted' );
			} else {
				delete_site_option( $this->identifier . '_indexing' );
			}

			// Have we exceeded our time limit?
			if ( time() > $start_time + absint( apply_filters( 'searchwp\indexer\process_time_limit', 60 ) ) ) {
				do_action( 'searchwp\debug\log', 'Process time limit exceeded! Exiting batch.', 'indexer' );

				// Destroy the queue so as to not neglect any remaining entries during the next cycle.
				$this->_destroy_queue();

				break;
			}

			if ( $this->memory_exceeded() ) {
				do_action( 'searchwp\debug\log', 'Memory threshold exceeded! Exiting batch.', 'indexer' );

				// Destroy the queue so as to not neglect any remaining entries during the next cycle.
				$this->_destroy_queue();

				break;
			}
		}
	}

	/**
	 * Pauses the indexer.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function pause() {
		if ( self::use_legacy_lock() ) {
			$this->locked = $this->get_lock();
		}

		$this->enabled = false;
		Settings::update( 'indexer_paused', true );
	}

	/**
	 * Unpauses the indexer.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function unpause() {
		if ( self::use_legacy_lock() ) {
			$this->unlock_process();
		}

		$this->enabled = true;
		Settings::update( 'indexer_paused', false );
	}

	/**
	 * Adds unindexed Entries to the queue.
	 *
	 * @since 4.0
	 * @return int Number of Entries that were queued.
	 */
	private function queue_unindexed_entries() {
		do_action( 'searchwp\indexer\update' );

		$has_updates = 0;
		$batch_size  = Settings::get_single( 'reduced_indexer_aggressiveness', 'boolean' ) ? 1 : 25;
		$limit       = absint( apply_filters( 'searchwp\indexer\batch_size', $batch_size ) );

		// Iterate over Engines to determine what to index. This will automatically
		// apply Engine Rules as we iterate and queue anything that meets any criteria
		// for any saved Engine. Querying will also apply the same Engine Rules.
		foreach ( Settings::get_engines() as $engine ) {
			foreach ( $engine->get_sources() as $source ) {
				$limit = absint( apply_filters( 'searchwp\indexer\batch_size\\' . $source->get_name() , $limit ) );

				if ( $limit < 1 ) {
					$limit = 1;
				}

				$unindexed_entries = $source->get_unindexed_entries( $limit );

				if ( ! empty( $unindexed_entries->get_ids() ) ) {
					$this->index->introduce( $unindexed_entries );
					$has_updates = count( $unindexed_entries->get_ids() );

					break;
				}
			}

			if ( $has_updates ) {
				do_action( 'searchwp\indexer\has_updates' );
				break;
			}
		}

		return $has_updates;
	}

	/**
	 * Returns WP_Cron interval offset. We want to offset this process to the Index Controller.
	 *
	 * @since 4.1
	 * @return int Offset in seconds.
	 */
	protected function interval_offset() {
		// Run two minutes later.
		return 120;
	}
}
