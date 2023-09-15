<?php

/**
 * SearchWP's Index Controller.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Index;

use SearchWP\Utils;
use SearchWP\Entry;
use SearchWP\Tokens;
use SearchWP\BackgroundProcess;
use SearchWP\Indexer;

/**
 * Class Controller is responsible for facilitating interaction with the search index.
 *
 * @since 4.0
 */
class Controller extends BackgroundProcess {
	/**
	 * Database table map.
	 *
	 * @since 4.0
	 * @var   array
	 */
	private $tables;

	/**
	 * Query alias.
	 *
	 * @since 4.0
	 * @var string
	 */
	private $alias = 's';

	/**
	 * All available Sources.
	 *
	 * @since 4.0
	 * @var   \SearchWP\Source[]
	 */
	private $sources;

	/**
	 * Maximum number of tokens per chunk.
	 *
	 * @since 4.0
	 * @var int
	 */
	private static $tokens_max = 500;

	/**
	 * Whether delta changes are needed.
	 *
	 * @since 4.1
	 * @var boolean
	 */
	private $has_delta = false;

	/**
	 * Index constructor.
	 *
	 * @since 4.0
	 */
	function __construct() {
		$this->name       = 'index';
		$this->identifier = SEARCHWP_PREFIX . 'index_controller';

		parent::init();
		$this->set_tables();
		$this->set_sources();

		self::$tokens_max = absint( apply_filters( 'searchwp\index\tokens_max', self::$tokens_max ) );

		$this->enabled = ! \SearchWP\Settings::get( 'indexer_paused', 'boolean' ) && apply_filters( 'searchwp\index\process\enabled', true );

		add_action( 'shutdown', function() {
			if ( $this->has_delta ) {
				$this->dispatch();
			}
		} );
	}

	/**
	 * Ensure database tables are created and available.
	 *
	 * @since 4.0
	 * @return void
	 */
	private function set_tables() {
		$this->tables = [
			'index'  => new \SearchWP\Index\Tables\IndexTable(),
			'status' => new \SearchWP\Index\Tables\StatusTable(),
			'tokens' => new \SearchWP\Index\Tables\TokensTable(),
			'log'    => new \SearchWP\Index\Tables\LogTable(),
		];
	}

	/**
	 * Handler when process time limit has been exceeded.
	 *
	 * @since 4.1
	 */
	protected function time_limit_exceeded() {
		return false;
	}

	/**
	 * Executed when the process has failed.
	 *
	 * @since 4.1.8
	 * @return mixed
	 */
	protected function handle_process_failure() {
		do_action( 'searchwp\debug\log', 'Process failed!', 'index' );
	}

	/**
	 * Pauses the process.
	 *
	 * @since 4.1.16
	 * @return void
	 */
	public function pause() {
		if ( self::use_legacy_lock() ) {
			$this->locked = $this->get_lock();
		}

		$this->enabled = false;
	}

	/**
	 * Unpauses the process.
	 *
	 * @since 4.1.16
	 * @return void
	 */
	public function unpause() {
		if ( self::use_legacy_lock() ) {
			$this->unlock_process();
		}

		$this->enabled = true;
	}

	/**
	 * Cycles the indexer. Indexes queued entries, finds entries to queue.
	 *
	 * @since 4.1
	 * @return int Number of Entries queued during this cycle.
	 */
	public function cycle() {
		global $wpdb;

		if ( ! apply_filters( 'searchwp\index\cycle\forced', $this->enabled, $this ) ) {
			return;
		}

		$table   = $wpdb->options;
		$column  = 'option_name';
		$orderby = 'option_id';
		$value   = 'option_value';

		if ( is_multisite() ) {
			$table   = $wpdb->sitemeta;
			$column  = 'meta_key';
			$orderby = 'meta_id';
			$value   = 'meta_value';
		}

		$deltas = $wpdb->get_results( $wpdb->prepare( "
			SELECT *
			FROM {$table}
			WHERE {$column} LIKE %s
			ORDER BY {$orderby} ASC
			LIMIT 11
		", $wpdb->esc_like( SEARCHWP_PREFIX . 'index_drop_' . get_current_blog_id() . '_' ) . '%' ) );

		$more = false;

		// We pulled 11 results but are only going to process 10. If there are 11, there's more to do.
		if ( count( $deltas ) > 10 ) {
			$more   = true;
			$deltas = array_slice( $deltas, 0, 10 );
		}

		// Drop the delta updates from the index.
		if ( ! empty( $deltas ) ) {
			foreach ( $deltas as $delta ) {
				$entry_data = maybe_unserialize( $delta->{$value} );
				$source_obj = $this->get_source_by_name( $entry_data['source'] );

				if ( $source_obj instanceof \SearchWP\Source ) {
					$this->drop( $source_obj, $entry_data['id'], true );
				}

				delete_site_option( $delta->{$column} );
			}
		}

		return $more;
	}

	/**
	 * Callback when background process is complete.
	 *
	 * @since 4.1
	 * @return void
	 */
	protected function complete() {
		$indexer = new Indexer();
		$indexer->trigger();
	}

	/**
	 * Destroys the queue.
	 *
	 * @since 4.1
	 * @return void
	 */
	public function _destroy_queue() {
		global $wpdb;

		$wpdb->query( $wpdb->prepare( "
			DELETE FROM {$wpdb->options}
			WHERE option_name LIKE %s
		", $wpdb->esc_like( SEARCHWP_PREFIX . 'index_drop_' . get_current_blog_id() . '_' ) . '%' ) );

		if ( self::use_legacy_lock() ) {
			$this->unlock_process();
		}
	}

	/**
	 * Uninstallation routine triggered in uninstall.php.
	 *
	 * @since 4.1
	 * @return void
	 */
	public function _uninstall() {
		foreach ( $this->tables as $table ) {
			$table->uninstall();
		}

		$this->_destroy_queue();

		parent::_uninstall();
	}

	/**
	 * Resets the index by TRUNCATING the index, status, and tokens tables.
	 *
	 * @since 4.0
	 * @param boolean $all_sites Whether to drop all sites (default: current site only).
	 * @return void
	 */
	public function reset( $all_sites = false ) {
		$indexer = \SearchWP::$indexer;
		$indexer->_destroy_queue();

		// Check integrity. Use case: site was migrated without our tables, but the version record still exists.
		foreach ( $this->tables as $table ) {
			if ( ! $table->exists() ) {
				$table->create();
			}
		}

		if ( $all_sites || ! is_multisite() ) {
			$this->truncate_tables();
		} else {
			$this->drop_site( get_current_blog_id() );
		}

		// Clear this queue and unlock the process.
		$this->_destroy_queue();

		do_action( 'searchwp\index\rebuild' );

		// Trigger the Indexer to rebuild the Index.
		// If alternate indexer is in use, it is triggering in $indexer::_trigger_indexer().
		if ( 'default' === $indexer->_method() ) {
			$indexer->trigger();
		}
	}

	/**
	 * Truncates the index database tables.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function truncate_tables() {
		foreach ( $this->tables as $table => $model ) {
			// We do not want to reset the stats.
			if ( 'log' === $table ) {
				continue;
			}

			do_action( 'searchwp\debug\log', "Truncating {$table} table", 'index' );

			$model->truncate();
		}
	}

	/**
	 * Getter for query alias.
	 *
	 * @since 4.0
	 * @return string The alias.
	 */
	public function get_alias() {
		return $this->alias;
	}

	/**
	 * Getter for database tables.
	 *
	 * @since 4.0
	 * @return array The database tables.
	 */
	public function get_tables() {
		return $this->tables;
	}

	/**
	 * Sets Sources.
	 *
	 * @since 4.0
	 * @return void
	 */
	private function set_sources() {
		$this->sources = \SearchWP::get_sources();
	}

	/**
	 * Returns Sources that have Attributes with default values.
	 *
	 * @since 4.0
	 * @return \SearchWP\Source[]
	 */
	public function get_default_sources( $apply_defaults = false ) {
		$defaults = array_filter( $this->sources, function( $source ) {
			$attributes_with_defaults = array_filter( $source->get_attributes(), function( $attribute ) {
				return ! empty( $attribute->get_default() ) && empty( $attribute->options_ajax_tag );
			} );

			return ! empty( $attributes_with_defaults );
		} );

		if ( ! $apply_defaults ) {
			return $defaults;
		}

		// Apply the defaults.
		foreach ( $defaults as $source => $source_config ) {
			foreach ( $source_config->get_attributes() as $attribute ) {

				$attribute_default = $attribute->get_default();

				if (
					! empty( $attribute_default )
					&& empty( $attribute->options_ajax_tag ) // Skip if options are provided via AJAX.
				) {
					$options = $attribute->get_options();

					if ( ! empty( $options ) && is_array( $options ) ) {
						$settings = call_user_func_array( 'array_merge', array_map( function( $option ) use ( $attribute_default ) {
							return [ $option->get_value() => $attribute_default ];
						}, $options ) );
					} else {
						$settings = $attribute_default;
					}

					$attribute->set_settings( $settings );
				}
			}
		}

		return $defaults;
	}

	/**
	 * INTERNAL. Add class hooks, add Source hooks.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function _add_hooks() {
		foreach ( $this->sources as $source ) {
			// Allow developers to control whether these hooks are added.
			if ( apply_filters( 'searchwp\index\source\add_hooks', true, [
				'source' => $source,
				'active' => Utils::any_engine_has_source( $source ),
			] ) ) {
				$source->add_hooks( [ 'active' => Utils::any_engine_has_source( $source ), ] );
			}
		}

		do_action( 'searchwp\index\init', $this );

		if ( ! has_action( SEARCHWP_PREFIX . 'index_stats', [ $this, '_index_stats' ] ) ) {
			add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'index_stats', [ $this, '_index_stats' ] );
		}
	}

	/**
	 * Callback for WP AJAX request to get stats.
	 *
	 * @since 4.0
	 */
	public function _index_stats() {

		Utils::check_ajax_permissions();

		wp_send_json_success( $this->get_stats() );
	}

	/**
	 * Getter for Sources.
	 *
	 * @since 4.0
	 * @return array
	 */
	public function get_sources() {
		return $this->sources;
	}

	/**
	 * Retrieve Source object from its name.
	 *
	 * @since 4.0
	 * @param string $name The name of the Source.
	 * @return \SearchWP\Source
	 */
	public function get_source_by_name( string $name ) {
		if ( ! array_key_exists( $name, $this->sources ) ) {
			return new \WP_Error( 'source_name', __( 'Invalid SearchWP Source name', 'searchwp' ), $name );
		}

		return $this->sources[ $name ];
	}

	/**
	 * Getter for the status of an Entry.
	 *
	 * @since 4.0
	 * @param Entry $entry The Entry.
	 * @return null|object
	 */
	public function get_entry_status( Entry $entry ) {
		return $this->get_source_id_status( $entry->get_source()->get_name(), $entry->get_id() );
	}

	/**
	 * Getter for the status of a Source ID.
	 *
	 * @since 4.0
	 * @param string     $source The name of the Source.
	 * @param string|int $id     The ID of the entry.
	 * @return null|object
	 */
	public function get_source_id_status( string $source, $id ) {
		global $wpdb;

		$result = $wpdb->get_row( $wpdb->prepare("
			SELECT *
			FROM {$this->tables['status']->table_name}
			WHERE id = %s
				AND source = %s
				AND site = %d
			LIMIT 1",
			(string) $id, $source, get_current_blog_id()
		) );

		return $result;
	}

	/**
	 * Prevent duplicate IDs from being indexed by verifying their status as queued.
	 *
	 * @since 4.0
	 * @param array  $ids         The IDs to validate.
	 * @param string $source_name The Source name of the IDs.
	 * @return array              IDs that have been marked as queued.
	 */
	public function validate_queued_ids( array $ids, string $source_name ) {
		global $wpdb;

		if ( empty( $ids ) ) {
			return $ids;
		}

		// TODO: Consider batching this? It would be to work around issues like
		//       the WP Engine MySQL Governor, and only apply if the batch limit
		//       was increased to be something very large. Punted for now.

		$queued_ids = $wpdb->get_col( $wpdb->prepare( "
			SELECT DISTINCT id
			FROM {$this->tables['status']->table_name}
			WHERE
				indexed IS NULL
				AND omitted IS NULL
				AND queued IS NOT NULL
				AND source = %s
				AND site = %d
				AND id IN (" . implode( ', ', array_fill( 0, count( $ids ), '%s' ) ) . ')',
			array_merge( [ $source_name ], [ get_current_blog_id() ], $ids )
		) );

		return array_unique( array_intersect( $ids, $queued_ids ) );
	}

	/**
	 * Prevent duplicate entries from making it into the background processing queue.
	 *
	 * @since 4.0
	 * @param \SearchWP\Entries $entries The Entries to validate.
	 * @return \SearchWP\Entries
	 */
	public function validate_queueable_entries( \SearchWP\Entries $entries ) {
		global $wpdb;

		$values       = [];
		$placeholders = [];
		$site         = get_current_blog_id();

		foreach ( $entries->get() as $entry ) {
			array_push(
				$values,
				$entry->get_id(),
				$entry->get_source()->get_name(),
				$site
			);
			$placeholders[] = '( id = %s AND source = %s AND site = %d )';
		}

		$already_queued = $wpdb->get_col( $wpdb->prepare( "
			SELECT id FROM {$this->tables['status']->table_name}
			WHERE " . implode( ' OR ', $placeholders ),
			$values
		) );

		$original_ids = $entries->get_ids();
		$invalid_ids  = array_intersect( $original_ids, $already_queued );

		if ( ! empty( $invalid_ids ) ) {
			foreach ( $entries->get() as $entry ) {
				if ( in_array( $entry->get_id(), $invalid_ids, true ) ) {
					$entries->remove( $entry );
				}
			}
		}

		return $entries;
	}

	/**
	 * Marks submitted Entries as queued. This also introduces status records
	 * for entries, so we can assume a status record exists for every Entry
	 * because the only way into the index is through the queue.
	 *
	 * @since 4.0
	 * @param \SearchWP\Entries $entries The applicable Entries.
	 * @return boolean|int
	 */
	public function introduce( \SearchWP\Entries $entries ) {
		global $wpdb;

		// This may be redundant but it will ensure no duplicates.
		$entries = $this->validate_queueable_entries( $entries );

		if ( empty( $entries->get() ) ) {
			return $entries;
		}

		// Add status records for each Entry.
		$values       = [];
		$placeholders = [];
		$timestamp    = current_time( 'mysql' );
		$site         = get_current_blog_id();

		foreach ( $entries->get() as $entry ) {
			array_push(
				$values,
				$entry->get_id(),
				$entry->get_source()->get_name(),
				$timestamp,
				$site
			);
			$placeholders[] = '( %s, %s, %s, %d )';
		}

		$wpdb->query( $wpdb->prepare( "
			INSERT INTO {$this->tables['status']->table_name}
			( id, source, queued, site )
			VALUES " . implode( ', ', $placeholders ),
			$values
		) );

		return $entries;
	}

	/**
	 * Marks an Entry.
	 *
	 * @since 4.0
	 * @param Entry  $entry  The applicable Entry.
	 * @param string $status The status to set.
	 * @return void
	 */
	public function mark_entry_as( Entry $entry, string $status ) {
		global $wpdb;

		// Make sure the status is valid as per our column names.
		$stati = [ 'queued', 'indexed', 'omitted' ];

		if ( ! in_array( $status, $stati, true ) ) {
			unset( $entry );
			return false;
		}

		$source = $entry->get_source();

		if ( method_exists( $source, 'get_name' ) ) {
			do_action( 'searchwp\debug\log', "Marking [" . get_current_blog_id() . "] {$source->get_name()} {$entry->get_id()} as {$status}", 'index' );
		} else {
			// Malformed Source.
			do_action( 'searchwp\debug\log', "Malformed Source [" . get_current_blog_id() . "] when trying to mark {$entry->get_id()} as {$status}:", 'index' );
			do_action( 'searchwp\debug\log', print_r( $source, true ), 'index' );
		}

		// We only want the applicable timestamp. We'll build our values array
		// by first nullifying all stati, and then appending our current status
		// so we know what order the placeholders should be in as well.
		$values = array_fill_keys( $stati, null );
		unset( $values[ $status ] );
		$values[ $status ] = current_time( 'mysql' );

		// Update the proper table column with a timestamp for this status.
		$result = $wpdb->update(
			$this->tables['status']->table_name,
			$values,
			[
				'id'     => $entry->get_id(),
				'source' => $entry->get_source()->get_name(),
				'site'   => get_current_blog_id(),
			],
			[ null, null, '%s', ], // Timestamps.
			[ '%s', '%s', '%d', ]  // WHERE clauses.
		);

		do_action( 'searchwp\index\update_entry', [
			'index'  => $this,
			'status' => $status,
			'id'     => $entry->get_id(),
			'source' => $entry->get_source()->get_name(),
			'site'   => get_current_blog_id(),
		] );

		unset( $entry );

		return $result;
	}

	/**
	 * Normalizes Entry data from its storage structure to something we can use in the Index.
	 *
	 * @since 4.0
	 * @param Entry $entry Entry to normalize.
	 * @return array Noramlized data.
	 */
	public function normalize_entry_data( Entry $entry ) {
		$entry_data = [];

		foreach ( $entry->get_data() as $attribute => $data ) {
			// If it's just a single option Attribute, we can bail out early.
			if ( $data instanceof Tokens && ! empty( $data->raw ) ) {
				$entry_data[ $attribute ] = $data;
				continue;
			}

			// If it's an Attribute with no chosen options, we can also bail.
			if ( ! is_array( $data ) || empty( $data ) ) {
				continue;
			}

			// Apply Attribute namespace to these Options.
			foreach( $data as $option => $option_data ) {
				if ( $option_data instanceof Tokens && ! empty( $option_data->raw ) ) {
					$entry_data[ $attribute . SEARCHWP_SEPARATOR . $option ] = $option_data;
				}
			}
		}

		return $entry_data;
	}

	/**
	 * Adds an Entry to the Index.
	 *
	 * @since 4.0
	 * @param Entry $entry The Entry to index.
	 * @return boolean
	 */
	public function add( Entry $entry ) {
		do_action( 'searchwp\index\add', $entry );

		$entry_data = $this->normalize_entry_data( $entry );
		$attributes = [];
		$result     = true;

		// Iterate over Entry Attributes and add all data to the Index.
		foreach ( $entry_data as $attribute => $data ) {
			// There might not be any data for this Attribute.
			if ( ! $data instanceof Tokens ) {
				continue;
			}

			$this_attribute = [
				'attribute' => $attribute,
				'result'    => $this->index_tokenized_entry_attribute_data( $entry, $attribute, $data ),
			];

			$attributes[] = $this_attribute;

			// If indexing this Attribute failed, mark it.
			if ( false === $this_attribute['result'] ) {
				$result = false;
			}
		}

		if ( apply_filters( 'searchwp\debug\log\detailed', false ) ) {
			do_action(
				'searchwp\debug\log',
				"Indexing source: {$entry->get_source()->get_name()} [{$entry->get_id()}] " . implode( ', ', $attributes ),
				'index'
			);
		}

		if ( $result ) {
			$this->mark_entry_as( $entry, 'indexed' );
		} else {
			$this->mark_entry_as( $entry, 'omitted' );

			do_action(
				'searchwp\debug\log',
				"Failed indexing source: {$entry->get_source()->get_name()} [{$entry->get_id()}] " . implode( ', ', $attributes ),
				'index'
			);
		}

		unset( $entry_data );
		unset( $attributes );

		return $result;
	}

	/**
	 * Drops a Source from the Index.
	 *
	 * @since 4.0
	 * @param string $source The namne of the Source to drop.
	 */
	public function drop_source( string $source ) {
		global $wpdb;

		$site = get_current_blog_id();

		$wpdb->query( $wpdb->prepare( "
			DELETE FROM {$this->tables['index']->table_name}
			WHERE source = %s AND site = %d",
			$source,
			$site
		) );

		$wpdb->query( $wpdb->prepare( "
			DELETE FROM {$this->tables['status']->table_name}
			WHERE source = %s AND site = %d",
			$source,
			$site
		) );

		do_action( 'searchwp\debug\log', "Dropping all {$source} entries [{$site}]", 'index' );
	}

	/**
	 * Drops a Source from the Index.
	 *
	 * @since 4.0
	 * @param string $source The name of the Source to drop.
	 * @param string[] $attributes The names of the Attributes to drop.
	 */
	public function drop_source_attributes( string $source, array $attributes ) {
		global $wpdb;

		$wpdb->query( $wpdb->prepare( "
			DELETE FROM {$this->tables['index']->table_name}
			WHERE source = %s
				AND attribute IN (" . implode( ',', array_fill( 0, count( $attributes ), '%s' ) ) . ")
				AND site = %d",
			array_merge( [ $source ], $attributes, [ get_current_blog_id() ] )
		) );

		do_action( 'searchwp\debug\log', "Dropping all {$source} " . implode(', ', $attributes ) . " entries [" . get_current_blog_id() . "]", 'index' );
	}

	/**
	 * Drops an Entry from the Index.
	 *
	 * @since 4.0
	 * @param \SearchWP\Source $source The Source to drop.
	 * @param string|int       $id     The id of the Entry to drop.
	 * @param bool             $force  Whether to drop this entry right now (instead of in the background)
	 * @return boolean
	 */
	public function drop( \SearchWP\Source $source, $id, $force = false ) {
		global $wpdb;

		$entry       = new Entry( $source, $id, false );
		$entry_id    = $entry->get_id();
		$source_name = $entry->get_source()->get_name();
		$site        = get_current_blog_id();

		$cache_key = SEARCHWP_PREFIX . 'index_drop_' . $site . '_' . $source_name . '_' . $entry_id;
		$cache     = wp_cache_get( $cache_key, '' );

		if ( ! empty( $cache ) && ! $force ) {
			return true;
		}

		wp_cache_set( $cache_key, 'drop', '', 1 );

		$indexer = \SearchWP::$indexer;
		$indexer_method = $indexer instanceof \SearchWP\Indexer ? $indexer->_method() : '';

		if ( 'alternate' === $indexer_method ) {
			$force = 'ALL' === $force ? 'ALL' : true;
		}

		// If HTTP Basic Authentication is play, WP Cron isn't going to cooperate.
		$basic_auth = apply_filters( 'searchwp\indexer\http_basic_auth_credentials', [] );
		if ( ! empty( $basic_auth ) ) {
			$force = true;
		}

		if ( $force || apply_filters( 'searchwp\index\aggressive_delta', false ) ) {
			// Drop the Entry from the index.
			$index_drop = $wpdb->query( $wpdb->prepare( "
				DELETE FROM {$this->tables['index']->table_name}
				WHERE id = %s AND source = %s AND site = %d",
				$entry_id,
				$source_name,
				$site
			) );

			// This clause depends on whether we're intentionally reintroducing.
			$omitted = ' AND omitted IS NULL';
			if ( $force && 'ALL' === $force ) {
				$omitted = '';
			}

			// Drop the status. This ALSO reintroduces this Entry to be indexed.
			$status_drop = $wpdb->query( $wpdb->prepare( "
				DELETE FROM {$this->tables['status']->table_name}
				WHERE id = %s AND source = %s AND site = %d" . $omitted,
				$entry_id,
				$source_name,
				$site
			) );

			do_action( 'searchwp\debug\log', "Dropping {$source_name} {$entry_id}", 'index' );

			if ( 'alternate' === $indexer_method ) {
				$indexer->trigger();
			}

			do_action( 'searchwp\index\drop', [ 'entry' => $entry, 'controller' => $this, ] );

			return $index_drop && $status_drop;
		} else {
			// Add to the drop queue.
			do_action( 'searchwp\debug\log', "Marking {$source_name} {$entry_id} to be dropped", 'index' );

			update_site_option( $cache_key, [ 'source' => $source_name, 'id' => $entry_id, ], 'no' );

			if ( $this->enabled ) {
				$this->has_delta = true;
			} else {
				do_action( 'searchwp\debug\log', 'Skipping delta dispatch', 'index' );
			}
		}
	}

	/**
	 * Removes an entire site from the index.
	 *
	 * @param int $site
	 * @return boolean Whether the operation was successful.
	 */
	public function drop_site( int $site ) {
		global $wpdb;

		// NOTE: It's too resource intensive to extract tokens unique to the site.
		// So for now the tokens are left in place. We should not rely on accurate
		// tokens when performing logic elsewhere.

		// Step 1: Drop all site content from the index.
		$index_drop = $wpdb->query( $wpdb->prepare( "
			DELETE FROM {$this->tables['index']->table_name}
			WHERE site = %d",
			$site
		) );

		// Step 2: Drop all site status entries.
		$status_drop = $wpdb->query( $wpdb->prepare( "
			DELETE FROM {$this->tables['status']->table_name}
			WHERE site = %d",
			$site
		) );

		do_action( 'searchwp\debug\log', "Dropping site {$site}", 'index' );

		return $index_drop && $status_drop;
	}

	/**
	 * Drops an array of Token IDs from the index.
	 *
	 * @since 4.0
	 * @param array $token_ids The Token IDs to drop.
	 * @return void
	 */
	private function drop_tokens( array $token_ids ) {
		global $wpdb;

		foreach ( array_chunk( $token_ids, self::$tokens_max ) as $batch ) {
			$wpdb->query( $wpdb->prepare( "
				DELETE FROM {$this->tables['tokens']->table_name}
				WHERE id IN ( " . implode( ', ',
					array_fill( 0, count( $batch ), '%d' )
				) . ' )',
				$batch
			) );
		}
	}

	/**
	 * Adds tokenized Entry Attribute data to the index.
	 *
	 * @since 4.0
	 * @param Entry  $entry     The Entry being indexed.
	 * @param string $attribute The attribute being indexed.
	 * @param Tokens $tokens    The tokenized data being indexed.
	 * @return boolean
	 */
	private function index_tokenized_entry_attribute_data( Entry $entry, string $attribute, Tokens $tokens ) {
		global $wpdb;

		if ( empty( $tokens->get() ) ) {
			return true;
		}

		// First we need to make sure these Tokens are indexed.
		$this->add_tokens( $tokens );

		// Now we can retrieve the IDs. We're going to create a map of token_id => token.
		$token_id_map = [];
		foreach ( $this->get_token_ids( $tokens ) as $tokens_db_record ) {
			$token_id_map[ absint( $tokens_db_record['id'] )] = $tokens_db_record['token'];
		}

		// Calculate occurrence count.
		$occurrences = array_count_values( array_filter(
			array_map( function( $token ) use ( $token_id_map ) {
				return array_search( $token, $token_id_map, true );
			}, $tokens->get() ),
			function( $value ) {
				return is_numeric( $value ) || is_string( $value );
			}
		) );

		// Insert into the index.
		$values       = [];
		$placeholders = [];
		$site_id      = get_current_blog_id();
		$entry_id     = $entry->get_id();
		$source       = $entry->get_source()->get_name();

		// To avoid large queries we are going to batch this process.
		$batch_index = 0;
		foreach ( array_keys( $token_id_map ) as $token_id ) {

			if ( count( $placeholders ) > 100 ) {
				$batch_index++;
			}

			if ( ! array_key_exists( $batch_index, $values ) ) {
				$values[ $batch_index ] = [];
			}

			if ( ! array_key_exists( $batch_index, $placeholders ) ) {
				$placeholders[ $batch_index ] = [];
			}

			array_push(
				$values[ $batch_index ],
				$token_id,
				$occurrences[ $token_id ],
				$entry_id,
				$attribute,
				$source,
				$site_id
			);
			$placeholders[ $batch_index ][] = '( %d, %d, %s, %s, %s, %d )';
		}

		unset( $token_id_map );
		unset( $occurrences );

		$result = true;

		for ( $i = 0; $i <= $batch_index; $i++ ) {
			$batch_result = $wpdb->query( $wpdb->prepare( "
				INSERT INTO {$this->tables['index']->table_name}
				( token, occurrences, id, attribute, source, site )
				VALUES " . implode( ', ', $placeholders[ $i ] ),
				$values[ $i ]
			) );

			if ( ! $batch_result ) {
				$result = $batch_result;
			}
		}
		return $result;
	}

	/**
	 * Retrieve tokens from the index table in id => token pairs.
	 *
	 * @since 4.0
	 * @param Tokens $tokens The tokens to retrieve.
	 * @return array
	 */
	private function get_token_ids( Tokens $tokens ) {
		return call_user_func_array( 'array_merge', array_map( function( $batch ) {
			global $wpdb;

			return $wpdb->get_results( $wpdb->prepare( "
				SELECT id AS id, token AS token FROM {$this->tables['tokens']->table_name}
				WHERE token IN ( " . implode( ', ',
					array_fill( 0, count( $batch ), '%s' )
				) . ' )',
				$batch
			), ARRAY_A );
		}, array_chunk( $tokens->get(), self::$tokens_max ) ) );
	}

	/**
	 * Retrieve tokens from the index for a given Entry.
	 *
	 * @since 4.0
	 * @param Entry $entry The Entry.
	 * @return array
	 */
	private function get_unique_token_ids_for_entry( Entry $entry ) {
		global $wpdb;

		$entry_id    = $entry->get_id();
		$source_name = $entry->get_source()->get_name();
		$site        = get_current_blog_id();

		$unique_ids = $wpdb->get_col( $wpdb->prepare( "
			SELECT DISTINCT token
			FROM {$this->tables['index']->table_name}
			WHERE ( id = %s AND source = %s AND site = %d )
				AND token NOT IN (
					SELECT DISTINCT token
					FROM {$this->tables['index']->table_name}
					WHERE id != %s AND site = %d
				)
			",
			$entry_id, $source_name, $site, $entry_id, $site
		) );

		return $unique_ids;
	}

	/**
	 * Reduce Tokens to unique Tokens that are not in the database.
	 *
	 * @since 4.0
	 * @param Tokens $tokens The tokens to reduce.
	 * @return array
	 */
	private function reduce_to_unique_tokens( Tokens $tokens ) {
		// Tokens are UNIQUE so we need to remove any that exist.
		$existing_tokens = call_user_func_array( 'array_merge', array_map( function( $batch ) {
			global $wpdb;

			return $wpdb->get_col( $wpdb->prepare( "
				SELECT token FROM {$this->tables['tokens']->table_name}
				WHERE token IN ( " . implode( ', ',
					array_fill( 0, count( $batch ), '%s' )
				) . ' )',
				$batch
			) );
		}, array_chunk( $tokens->get(), self::$tokens_max ) ) );

		// Remove any existing tokens from this incoming set of tokens.
		return array_unique( array_filter( $tokens->get(), function( $token ) use ( $existing_tokens ) {
			return ! in_array( $token, $existing_tokens, true );
		} ) );
	}

	/**
	 * Adds Tokens (not necessarily unique) to the index.
	 *
	 * @since 4.0
	 * @param Tokens $tokens The tokens to add.
	 * @return void|boolean
	 */
	private function add_tokens( Tokens $tokens ) {
		global $wpdb;

		$tokens = $this->reduce_to_unique_tokens( $tokens );

		if ( empty( $tokens ) ) {
			// No new tokens to add!
			return;
		}

		$results = [];
		$stemmer = new \SearchWP\Stemmer();

		// To prevent gigantic queries, we are going to split this array of tokens
		// into batches with a maximum size, so as to utilize separate queries.
		$batches = array_chunk( $tokens, self::$tokens_max );

		foreach ( $batches as $batch ) {
			$values       = [];
			$placeholders = [];

			foreach ( $batch as $token ) {
				array_push( $values, $token, $stemmer->stem( $token ) );
				$placeholders[] = '( %s, %s )';
			}

			$results[] = $wpdb->query( $wpdb->prepare( "
				INSERT INTO {$this->tables['tokens']->table_name}
				( token, stem )
				VALUES " . implode( ', ', $placeholders ),
				$values
			) );
		}

		return $results;
	}

	/**
	 * Determines whether the submitted tokens are in the index.
	 *
	 * @since 4.0
	 * @param array $tokens  Strings to check.
	 * @param array $sources Applicable Sources.
	 * @param array $sites   Applicable sites.
	 * @return array Tokens that are in the index keyed by their ID.
	 */
	public function has_tokens( array $tokens, array $sources, $sites = [] ) {
		global $wpdb;

		if ( empty( $tokens ) || empty( $sources ) ) {
			return [];
		}

		// Validate Sources.
		$sources = array_filter( array_map( function( $source ) {
			if ( $source instanceof \SearchWP\Source ) {
				$source = $source->get_name();
			}

			if ( ! array_key_exists( $source, $this->sources ) ) {
				return false;
			}

			return $this->sources[ $source ]->get_name();
		}, $sources ) );

		$sources_clause = "i.source IN ( " . implode( ', ', array_fill( 0, count( $sources ), '%s' ) ) . ' )';

		// Validate Sites.
		if ( empty( $sites ) ) {
			$sites = [ get_current_blog_id() ];
		}

		if ( 'all' !== $sites ) {
			$sites_clause = "i.site IN ( " . implode( ', ', array_fill( 0, count( $sites ), '%d' ) ) . ' )';
			$values = array_merge( array_map( 'absint', $sites ), $sources, $tokens );
		} else {
			$sites_clause = '1=1';
			$values = array_merge( $sources, $tokens );
		}

		return array_map( function( $token ) {
			return $token->token;
		}, $wpdb->get_results( $wpdb->prepare(
			"SELECT DISTINCT t.id, t.token
			FROM {$this->get_tables()['tokens']->table_name} t
			LEFT JOIN {$this->get_tables()['index']->table_name} i ON i.token = t.id
			WHERE {$sites_clause}
				AND {$sources_clause}
				AND t.token IN ( " . implode( ', ', array_fill( 0, count( $tokens ), '%s' ) ) . ' )',
			$values
		), OBJECT_K ) );
	}

	/**
	 * Returns timestamp of the last recorded Index activity.
	 *
	 * @since 4.1.14
	 * @return string MySQL-formatted timestamp of last indexed Entry.
	 */
	public function get_last_activity_timestamp() {
		global $wpdb;

		return $wpdb->get_var( "
			SELECT indexed
			FROM {$this->get_tables()['status']->table_name}
			WHERE site = " . absint( get_current_blog_id() ) . "
			ORDER BY indexed DESC
			LIMIT 1
		" );
	}

	/**
	 * Returns human readable representation of the last recorded Index activity.
	 *
	 * @since 4.0
	 * @return string
	 */
	public function get_last_activity() {
		$last_activity = $this->get_last_activity_timestamp();

		if ( ! empty( $last_activity ) ) {
			$last_activity = sprintf(
				// Translators: placeholder is a human readable time reference e.g. "ten hours"
				__( '%s ago', 'searchwp' ),
				human_time_diff( date( 'U', strtotime( $last_activity ) ), current_time( 'timestamp' ) )
			);
		} elseif ( is_multisite() ) {
			// The indexer may be busy on another site in the network.
			$last_activity = $this->locked ? __( 'Waiting in network queue', 'searchwp' ) : '--' ;
		}

		return $last_activity ? $last_activity : '--';
	}

	/**
	 * Returns the number of indexed Source Entries.
	 *
	 * @since 4.0
	 * @return int The count.
	 */
	public function get_count_indexed() {
		global $wpdb;

		return (int) $wpdb->get_var( "
			SELECT COUNT(DISTINCT source, id) AS indexed
			FROM {$this->get_tables()['status']->table_name}
			WHERE indexed IS NOT NULL
			AND site = " . absint( get_current_blog_id() ) );
	}

	/**
	 * Returns the entry currently being indexed by assuming that the entry with the lowest
	 * queued timestamp is the entry currently being indexed.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function get_assumed_entry_being_indexed() {
		global $wpdb;

		return $wpdb->get_row( "
			SELECT id, source, site
			FROM {$this->tables['status']->table_name}
			WHERE queued IS NOT NULL
			ORDER BY queued ASC
			LIMIT 1" );
	}

	/**
	 * Returns the total number of Source Entries.
	 *
	 * @since 4.0
	 * @return int The count.
	 */
	public function get_count_entries() {
		$engines = \SearchWP\Settings::get_engines();

		if ( empty( $engines ) ) {
			return 0;
		}

		// We need to iterate over our Engines and find the maximum number of Entries
		// per Source and use that to gauge the overall index size. This is due to
		// our inability to collapse all Source Rules across engines.
		$source_entry_ceilings = [];

		foreach ( $engines as $engine ) {
			foreach ( $engine->get_sources() as $source ) {
				$source_name = $source->get_name();

				if ( ! array_key_exists( $source_name, $source_entry_ceilings ) ) {
					$source_entry_ceilings[ $source_name ] = 0;
				}

				$engine_source_entry_count = $source->get_entry_db_records( true );

				if ( $engine_source_entry_count > $source_entry_ceilings[ $source_name ] ) {
					$source_entry_ceilings[ $source_name ] = $engine_source_entry_count;
				}
			}
		}

		return array_sum( $source_entry_ceilings );
	}

	/**
	 * Retrieves the current Index stats.
	 *
	 * @since 4.0
	 * @return (string|int)[]
	 */
	public function get_stats() {

		$indexed = $this->get_count_indexed();
		$omitted = $this->get_omitted();
		$total   = $this->get_count_entries() - count( $omitted );

		if ( $indexed > $total ) {
			// An Engine configuration change (or something similar) caused the index to have too much data.
			// A notice will be displayed on the settings screen indicating as such, but as far as these
			// stats are concerned, we can assume index coverage.
			$indexed = $total;
		}

		return [
			'lastActivity'  => $this->get_last_activity(),
			'indexed'       => $indexed,
			'total'         => $total,
			'omitted'       => $omitted,
			'outdated'      => (bool) \SearchWP\Settings::get( 'index_outdated' ),
			'indexerPaused' => ! $this->enabled,
		];
	}

	/**
	 * Retrieve entries that are omitted from the index.
	 *
	 * @since 4.0
	 * @return array
	 */
	public function get_omitted() {
		global $wpdb;

		$omitted = $wpdb->get_results( $wpdb->prepare( "
			SELECT id, source, site, omitted
			FROM {$this->tables['status']->table_name} i
			WHERE omitted IS NOT NULL AND site = %d",
			get_current_blog_id()
		) );

		// Append applicable data to each record e.g. permalink, title, etc.
		return array_map( function( $record ) {
			if ( ! array_key_exists( $record->source, $this->sources ) ) {
				return $record;
			}

			$class = get_class( $this->sources[ $record->source ] );

			if ( ! class_exists( $class ) ) {
				return $record;
			}

			$source = explode( SEARCHWP_SEPARATOR, $record->source );
			$source = ! isset( $source[1] ) ? new $class : new $class( $source[1] );

			$record->permalink = $source::get_permalink( $record->id );
			$record->edit_link = $source::get_edit_link( $record->id );

			return $record;
		}, $omitted );
	}

	/**
	 * Retrieve entries that are queued to be indexed.
	 *
	 * @since 4.0
	 * @return array
	 */
	public function get_queued( $site_id = false ) {
		global $wpdb;

		// If a site ID was omitted (or an invalid ID passed) assume the current site.
		if ( empty( $site_id ) || ! is_numeric( $site_id ) ) {
			$site_id = get_current_blog_id();
		}

		return $wpdb->get_results( $wpdb->prepare( "
			SELECT id, source, site, queued
			FROM {$this->tables['status']->table_name}
			WHERE queued IS NOT NULL AND site = %d",
			absint( $site_id )
		) );
	}

	/**
	 * Retrieve all Token IDs for a stem.
	 *
	 * @since 4.0.4
	 * @param string $stem The stem.
	 * @return array
	 */
	public function get_tokens_for_stem( string $stem = '' ) {
		global $wpdb;

		return $wpdb->get_col( $wpdb->prepare( "
			SELECT id
			FROM {$this->tables['tokens']->table_name}
			WHERE stem = %s",
			$stem
		) );
	}

	/**
	 * Retrieve token groups based on a common stem for each token.
	 *
	 * @since 4.0.4
	 * @param int[] $tokens_ids The token IDs to use.
	 * @return array
	 */
	public function group_tokens_by_stem_from_tokens( array $tokens_ids ) {
		global $wpdb;

		// Retrieve all unique stems from the submitted token IDs.
		$unique_stems = $wpdb->get_col( $wpdb->prepare( "
			SELECT DISTINCT stem
			FROM {$this->tables['tokens']->table_name}
			WHERE id IN (" . implode( ', ', array_fill( 0, count( $tokens_ids ), '%s' ) ) . ')',
			$tokens_ids
		) );

		// Using the unique stems we can then find all applicable tokens for each.
		return array_map( function( $stem ) use ( $wpdb ) {
			return $wpdb->get_col( $wpdb->prepare( "
				SELECT id
				FROM {$this->tables['tokens']->table_name}
				WHERE stem = %s",
				$stem
			) );
		}, $unique_stems );
	}

	/**
	 * Retrieves indexed Tokens for the submitted Entry.
	 *
	 * @since 4.0
	 * @param Entry $entry The Entry to examine.
	 * @return string[]
	 */
	public function get_tokens_for_entry( Entry $entry ) {
		global $wpdb;

		return $wpdb->get_col( $wpdb->prepare( "
			SELECT t.token
			FROM {$this->tables['index']->table_name} i
			LEFT JOIN {$this->tables['tokens']->table_name} t ON t.id = i.token
			WHERE i.source = %s
				AND i.id = %s
				AND i.site = %d",
			$entry->get_source()->get_name(),
			$entry->get_id(),
			get_current_blog_id()
		) );
	}
}
