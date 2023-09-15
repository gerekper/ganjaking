<?php

/**
 * SearchWP EnginesView.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Admin\Views;

use SearchWP\Utils;
use SearchWP\Entry;
use SearchWP\Engine;
use SearchWP\Settings;
use SearchWP\Admin\NavTab;

/**
 * Class EnginesView is responsible for providing the UI to manage Engines config.
 *
 * @since 4.0
 */
class EnginesView {

	private static $slug = 'engines';

	/**
	 * EnginesView constructor.
	 *
	 * @since 4.0
	 */
	function __construct() {

		if ( Utils::is_swp_admin_page( 'algorithm' ) ) {
			new NavTab( [
				'page'       => 'algorithm',
				'tab'        => self::$slug,
				'label'      => __( 'Engines', 'searchwp' ),
				'is_default' => true,
			] );
		}

		if ( Utils::is_swp_admin_page( 'algorithm', 'default' ) ) {
			add_action( 'searchwp\settings\view',  [ $this, 'render' ] );
			add_action( 'searchwp\settings\after', [ $this, 'assets' ] );
		}

		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'engines_view',      [ __CLASS__, 'update_config' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'engines_configs',   [ __CLASS__, 'update_engines' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'rebuild_index',     [ __CLASS__, 'rebuild_index' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'migrate_stats',     [ __CLASS__, 'migrate_stats' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'reintroduce_entry', [ __CLASS__, 'reintroduce_entry' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'memory_limits',     [ __CLASS__, 'memory_limits' ] );

		// Implement alternate indexer callbacks.
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'indexer_method',  [ __CLASS__, '_indexer_method' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'trigger_indexer', [ __CLASS__, '_trigger_indexer' ] );
	}

	/**
	 * AJAX callback retrieve memory limits.
	 *
	 * @since 4.1
	 * @return void
	 */
	public static function memory_limits() {

		Utils::check_ajax_permissions();

		$wp  = wp_convert_hr_to_bytes( WP_MEMORY_LIMIT );
		$php = Utils::get_memory_limit();

		// At least 64MB RAM is recommended if available.
		$recommended = 67108864; // 64MB.
		$sufficient  = true;     // Assume true in case recommended is not available.

		if ( $php < $recommended ) {
			$sufficient = false;
		}

		$sufficient = (bool) apply_filters( 'searchwp\memory_sufficient', $sufficient );

		wp_send_json_success( [
			'wp'          => size_format( $wp ),
			'php'         => size_format( $php ),
			'recommended' => size_format( $recommended ),
			'sufficient'  => $sufficient,
		] );
	}

	/**
	 * AJAX callback to migrate Statistics from 3.x.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function migrate_stats() {
		global $wpdb;

		Utils::check_ajax_permissions();

		$statistics_table = new \SearchWP\Index\Tables\LogTable();
		$legacy_table     = $wpdb->prefix . 'swp_log';

		$wpdb->query( $wpdb->prepare( "
			INSERT INTO {$statistics_table->table_name}
			SELECT
				id AS logid,
				query AS query,
				tstamp AS tstamp,
				hits AS hits,
				engine AS engine,
				%d AS site
			FROM {$legacy_table}
			WHERE CHAR_LENGTH(query) < 80", // There is a schema change with an 80 char limit in the new table.
		get_current_blog_id() ) );

		wp_send_json_success();
	}

	/**
	 * AJAX callback to reintroduce an entry to the index.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function reintroduce_entry() {

		Utils::check_ajax_permissions();

		$source = isset( $_REQUEST['source'] ) ? stripslashes( $_REQUEST['source'] ) : '';
		$id     = isset( $_REQUEST['id'] )     ? stripslashes( $_REQUEST['id'] )     : '';

		$entry = new Entry( $source, $id, false, false );
		\SearchWP::$index->drop( $entry->get_source(), $id, 'ALL' );
		\SearchWP::$indexer->trigger();

		wp_send_json_success( \SearchWP::$index->get_stats() );
	}

	/**
	 * AJAX callback to reset the index.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function rebuild_index() {

		Utils::check_ajax_permissions();

		// Reset the Index.
		$index = \SearchWP::$index;
		$index->reset();

		// When the index is rebuilt, it is no longer outdated.
		Settings::update( 'index_outdated', false );
	}

	/**
	 * AJAX callback to save the engine settings.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function update_engines( $configs ) {
		$index = \SearchWP::$index;

		$doing_import = ! empty( $configs );
		if ( ! $doing_import ) {
			Utils::check_ajax_permissions();
			$configs = isset( $_REQUEST['configs'] ) ? json_decode( stripslashes( $_REQUEST['configs'] ), true ) : false;
		}

		$original = Settings::_get_engines_settings();

		// If this is the initial save we are going to reset the Index just to be safe.
		if ( ! $doing_import && empty( $original ) ) {
			$index->drop_site( get_current_blog_id() );
		}

		// Validate the configs by loading proper Engine models.
		$engines = call_user_func_array( 'array_merge', array_map( function( $name, $config ) use ( $doing_import ) {
			// Build an Engine model.
			$engine = new Engine( $name, ! $doing_import ? Utils::normalize_engine_config( $config ) : $config );

			// Extract a (validated) config from the model.
			$config = Utils::normalize_engine_config( json_decode( json_encode( $engine ), true ) );

			unset( $config['name'] );

			return [ $name => $config ];
		}, array_keys( $configs ), array_values( $configs ) ) );

		$outdated = self::apply_new_engines_config( $original, $engines );

		if ( ! $doing_import ) {
			\SearchWP::$indexer->trigger();

			wp_send_json_success( [
				'outdated' => $outdated,
				'engines'  => Settings::get_engines( true ),
				'index'    => $index->get_stats(), // We have already defined $index above.
			] );
		}
	}

	/**
	 * Persists updated Engines configs. Returns whether the Index is outdated as a result of the update.
	 *
	 * @since 4.0
	 * @param array $old_config The starting engines confings.
	 * @param array $new_config The new engines configs.
	 * @return boolean Whether the Index is outdated.
	 */
	public static function apply_new_engines_config( $old_config, $new_config ) {
		// Persist the Engines configs and invalidate the cache.
		Settings::update_engines_config( $new_config );

		// Conditions that can be ignored as the Indexer will automatically resolve:
		//    - Source added

		$index_outdated = self::is_index_outdated( $old_config, $new_config );

		// Flag the status of the Index.
		Settings::update( 'index_outdated', (bool) $index_outdated );

		// Optimizations we can make inline IF an index rebuild IS NOT nececessary. If it is we're just wasting time now.
		if ( ! $index_outdated ) {
			self::remove_invalid_index_content( $old_config, $new_config );
		}

		// FUTURE: Determine if Rule change(s) only invalidate Entries; we can drop them.

		return $index_outdated;
	}

	/**
	 * Determines whether the Index is outdated as a reult of an Engines config update.
	 * Conditions that require an Index rebuild (because all Entries will need to be re-evaluated):
	 *    - Source Attribute added
	 *    - Source Attribute Option added
	 *
	 * @since 4.0
	 * @param array $old_config The old Engines config.
	 * @param array $new_config The new Engines config.
	 * @return bool Whether the Index is out of date.
	 */
	private static function is_index_outdated( array $old_config, array $new_config ) {
		$already_outdated = Settings::get( 'index_outdated' );

		if ( $already_outdated ) {
			return true;
		}

		// Limit our comparison to only Sources that were already present in the configs.
		$existing_sources = array_intersect(
			self::get_sources_from_config( $old_config ),
			self::get_sources_from_config( $new_config )
		);

		$all_new_source_attributes = array_diff(
			self::flattened_sources_attributes_from_config( $new_config ),
			self::flattened_sources_attributes_from_config( $old_config )
		);

		if ( empty( $existing_sources ) || empty( $all_new_source_attributes ) ) {
			return false;
		}

		foreach( $all_new_source_attributes as $maybe_new_source_attribute ) {
			foreach ( $existing_sources as $existing_source ) {
				// If this Source Attribute is part of an existing Source, it counts.
				if ( 0 === strpos( $maybe_new_source_attribute, $existing_source . SEARCHWP_SEPARATOR ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Removes invalid data from the index after an Engines configs update.
	 *    - Source removed, drop all Source entries in status and index tables for this site.
	 *    - Source Attribute removed, drop all Source Attribute entries in index table for this site.
	 *    - Source Attribute Option removed, drop all Source Attribute Option entries in index table for this site.
	 *
	 * @since 4.0
	 * @param array $old_config The old config.
	 * @param array $new_config The new config.
	 * @return void
	 */
	public static function remove_invalid_index_content( array $old_config, array $new_config ) {
		$removed_sources = array_diff(
			self::get_sources_from_config( $old_config ),
			self::get_sources_from_config( $new_config )
		);

		$removed_source_attributes = self::get_removed_attributes_from_config_update(
			$old_config,
			$new_config,
			$removed_sources
		);

		if ( empty( $removed_sources ) && empty( $removed_source_attributes ) ) {
			return;
		}

		$index = \SearchWP::$index;

		if ( ! empty( $removed_sources ) ) {
			call_user_func_array( [ $index, 'drop_source' ], $removed_sources );
		}

		if ( ! empty( $removed_source_attributes ) ) {
			foreach ( $removed_source_attributes as $source => $attributes ) {
				if ( in_array( $source, $removed_sources ) ) {
					// This was already removed.
					continue;
				}

				$index->drop_source_attributes( $source, $attributes );
			}
		}
	}

	/**
	 * Determines which Source Attributes have been removed (skipping Sources that were already removed).
	 *
	 * @since 4.0
	 * @param array $old_config
	 * @param array $new_config
	 * @param array $sources_to_skip
	 * @return array
	 */
	public static function get_removed_attributes_from_config_update( array $old_config, array $new_config, array $sources_to_skip ) {
		$old_config_attributes = self::get_sources_attributes_from_config( $old_config );
		$new_config_attributes = self::get_sources_attributes_from_config( $new_config );

		$removed_attributes = [];

		foreach ( $old_config_attributes as $source => $attributes ) {
			if ( in_array( $source, $sources_to_skip ) ) {
				// This Source was already dropped; we can bail.
				continue;
			}

			$removed = array_diff( $attributes, $new_config_attributes[ $source ] );

			if ( ! empty( $removed ) ) {
				$removed_attributes[ $source ] = $removed;
			}
		}

		return $removed_attributes;
	}

	/**
	 * Flattens Sources/Attributes from a config into something easily compared.
	 *
	 * @since 4.0
	 * @param array $config
	 * @return array
	 */
	public static function flattened_sources_attributes_from_config( array $config ) {
		$config = self::get_sources_attributes_from_config( $config );

		if ( empty( $config ) ) {
			return [];
		}

		return call_user_func_array( 'array_merge', array_map( function( $source, $attributes ) {
			return array_map( function( $attribute ) use ( $source ) {
				return $source . SEARCHWP_SEPARATOR . $attribute;
			}, $attributes );
		}, array_keys( $config ), array_values( $config ) ) );
	}

	/**
	 * Extracts Sources in an Engine config
	 *
	 * @since 4.0
	 * @param array $config The Engines config to work with.
	 * @return array Attributes in use keyed by their Source.
	 */
	public static function get_sources_from_config( $config ) {
		if ( empty( $config ) ) {
			return [];
		}

		return array_unique( call_user_func_array( 'array_merge',
			array_values( array_map( function( $engine ) {
				return array_keys( $engine['sources'] );
			}, array_values( $config ) ) ) )
		);
	}

	/**
	 * Extracts Attributes for all Sources in an Engine config
	 *
	 * @since 4.0
	 * @param array $config The Engines config to work with.
	 * @return array Attributes in use keyed by their Source.
	 */
	public static function get_sources_attributes_from_config( array $config ) {
		if ( empty( $config ) ) {
			return [];
		}

		// Build a nested array of Engine > Sources > Attributes.
		$sources_attributes = [];
		$engines_sources_attributes = array_map( function( $engine ) {
			// This can happen if an Engine has no Sources but it saved anyway.
			if ( empty( $engine['sources'] ) ) {
				return [];
			}

			return call_user_func_array( 'array_merge', array_map( function( $source, $source_config ) {
				$attributes = [];
				foreach ( $source_config['attributes'] as $attribute => $settings ) {
					if ( is_array( $settings ) ) {
						foreach ( $settings as $option => $setting ) {
							$attributes[] = $attribute . SEARCHWP_SEPARATOR . $option;
						}
					} else {
						$attributes[] = $attribute;
					}
				}

				return [ $source => $attributes ];
			}, array_keys( $engine['sources'] ), array_values( $engine['sources'] ) ) );
		}, $config );

		// Generate an array keyed by Sources with values of all Attribute names for all Engines.
		foreach ( $engines_sources_attributes as $engine => $sources ) {
			foreach ( $sources as $source => $attributes ) {
				if ( ! array_key_exists( $source, $sources_attributes ) ) {
					$sources_attributes[ $source ] = [];
				}

				$sources_attributes[ $source ] = array_unique( array_merge( $sources_attributes[ $source ], $attributes ) );
			}
		}

		return $sources_attributes;
	}

	/**
	 * Outputs the assets needed for the Engine configuration UI.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function assets() {
		$index  = \SearchWP::$index;
		$handle = SEARCHWP_PREFIX . self::$slug;
		$debug  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true || isset( $_GET['script_debug'] ) ? '' : '.min';

		wp_enqueue_script( $handle,
			SEARCHWP_PLUGIN_URL . "assets/javascript/dist/engines{$debug}.js",
			[ 'jquery' ], SEARCHWP_VERSION, true );

		wp_enqueue_style(
            $handle,
			SEARCHWP_PLUGIN_URL . "assets/javascript/dist/engines{$debug}.css",
			[
				Utils::$slug . 'collapse-layout',
				Utils::$slug . 'input',
				Utils::$slug . 'modal',
				Utils::$slug . 'style',
			],
            SEARCHWP_VERSION
        );

		$settings = Settings::get();

		if ( empty( $settings['misc']['hasInitialSave'] ) ) {
			$default_engine_settings = json_decode( json_encode( new Engine( 'default' ) ), true );
			$default_engine_settings['settings']['stemming'] = true;

			$settings['engines'] = [
				'default' => $default_engine_settings,
			];
		}

		// If there are somehow no Engines...
		if ( ! array_key_exists( 'engines', $settings ) ) {
			$settings['engines'] = [];
		}

		// If there is somehow no Default Engine...
		if ( ! array_key_exists( 'default', $settings['engines'] ) ) {
			$settings['engines']['default'] = json_decode( json_encode( new Engine( 'default' ) ), true );
		}

		// Flag for 3.x migration.
		$migrated = $this->did_migration_just_happen( $settings );
		if ( $migrated ) {
			\SearchWP\Settings::update( 'migrated', true );

			// With the migration complete, we kick off an index build.
			\SearchWP::$indexer->trigger();
		}

		Utils::localize_script( $handle, array_merge( [
			'view'     => self::get_config(),
			'index'    => $index->get_stats(),
			'welcome'  => empty( $settings['misc']['hasInitialSave'] )
							&& isset( $_GET['welcome'] ),
			'migrated' => $migrated,
			'cron'     => \SearchWP\Utils::is_cron_operational(),
			// Use the source prefix to exclude all sources from that family. (e.g. 'taxonomy.' excludes all Taxonomy sources).
			// Use the whole source name to exclude a specific source only (e.g. 'post.page' excludes Pages only).
			'newEngineExcludedSources' => ['taxonomy.']
		], $settings ) );
	}

	/**
	 * Whether a migration from SearchWP 3.x happened on this page load.
	 *
	 * @since 4.0
	 * @param array $settings SearchWP's settings.
	 * @return bool Whether the migration happened right now.
	 */
	private function did_migration_just_happen( $settings ) {
		return ! empty( $settings['misc']['hasInitialSave'] )
			// Showing welcome modal and migration hasn't happened.
			&& isset( $_GET['welcome'] )
			&& ! \SearchWP\Settings::get( 'migrated', 'boolean' )
			// 3.x settings.
			&& get_option( 'searchwp_settings' );
	}

	/**
	 * Callback for the render of this view.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function render() {
		// This node structure is as such to inherit WP-admin CSS.
		?>
        <div class="swp-content-container">
            <div id="searchwp-engines"></div>
        </div>
		<?php
	}

	/**
	 * AJAX callback to save the view settings (e.g. which meta boxes are collapsed)
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function update_config() {

		Utils::check_ajax_permissions();

		$config   = isset( $_REQUEST['config'] ) ? $_REQUEST['config'] : false;
		$existing = self::get_config();

		// Validate groups.
		$config = array_filter( (array) $config, function( $group ) use ( $existing ) {
			return array_key_exists( $group, $existing );
		}, ARRAY_FILTER_USE_KEY );

		// Validate group values.
		if ( empty( $config['collapsed'] ) ) {
			$config['collapsed'] = [];
		}

		$config['collapsed'] = array_map( 'sanitize_text_field', $config['collapsed'] );

		update_user_meta(
			get_current_user_id(),
			SEARCHWP_PREFIX . 'settings_view_config',
			$config
		);

		wp_send_json_success();
	}

	/**
	 * Getter for this config.
	 *
	 * @since 4.0
	 * @return mixed
	 */
	public static function get_config() {
		$settings = Settings::get();
		$config   = get_user_meta( get_current_user_id(), SEARCHWP_PREFIX . 'settings_view_config', true );

		if ( ! is_array( $config ) ) {
			$config = [];
		}

		// Verify storage for meta box collapsed status.
		if ( ! isset( $config['collapsed'] ) || ! is_array( $config['collapsed'] ) ) {
			$config['collapsed'] = [];
		}

		// Make sure that collapsed Sources are still added to the engine.
		$engine_index = 0;
		foreach ( $settings['engines'] as $engine => $engine_settings ) {
			$valid_collapsed_sources = array_map( function( $source ) use ( $engine_index ) {
				return $engine_index . SEARCHWP_SEPARATOR . $source;
			}, array_keys( $engine_settings->get_sources() ) );

			$settings_collapsed_sources = array_filter( $config['collapsed'], function( $source ) use ( $engine_index ) {
				return 0 === strpos( $source, $engine_index . SEARCHWP_SEPARATOR );
			} );

			// Check for invalid Sources.
			$invalid_collapsed_sources = array_diff( $settings_collapsed_sources, $valid_collapsed_sources );
			if ( ! empty( $invalid_collapsed_sources ) ) {
				$config['collapsed'] = array_values( array_diff( $config['collapsed'], $invalid_collapsed_sources ) );
			}

			// If the engine is somehow invalid, it'll get taken care of if/when a new engine takes its place.
			$engine_index++;
		}

		return $config;
	}

	/**
	 * INTERNAL. AJAX callback to return the indexer method.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function _indexer_method() {

		Utils::check_ajax_permissions();

		$indexer = \SearchWP::$indexer;

		wp_send_json_success( $indexer->_method() );
	}

	/**
	 * INTERNAL. AJAX callback to trigger the indexer.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function _trigger_indexer() {

		Utils::check_ajax_permissions();

		\SearchWP::$indexer->trigger();

		wp_send_json_success();
	}
}
