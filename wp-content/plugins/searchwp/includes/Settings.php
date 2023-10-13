<?php

/**
 * SearchWP Settings.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP;

/**
 * Class Settings is responsible for handling project settings.
 *
 * @since 4.0
 */
class Settings {

	/**
	 * Capability requirement for managing Settings.
	 *
	 * @since 4.0
	 * @since 4.2.6 Visibility changed from public to private.
	 *
	 * @var string
	 */
	private static $capability = 'manage_options';

	/**
	 * Cache key.
	 *
	 * @since 4.0
	 *
	 * @var string
	 */
	public static $engines_cache_key = SEARCHWP_PREFIX . 'engines_settings';

	/**
	 * Comprehensive list of (unprefixed) Settings keys.
	 *
	 * @since 4.0
	 * @var string[]
	 */
	private static $keys = [
		'engines',
		'stopwords',
		'synonyms',
		'forms',
		'results_page',
		'migrated',
		'upgraded_from',
		'ignored_queries',
		'dismissed_notices',
		'trim_stats_logs_after',
		'document_content_reset',
		'document_content_reset_dismissed',
	];

	/**
	 * Comprehensive list of (unprefixed) Settings keys that should be autoloaded.
	 *
	 * @since 4.0
	 * @var string[]
	 */
	private static $autoload_keys = [
		'debug',
		'version',
		'index_outdated',
		'partial_matches',
		'highlighting',
		'parse_shortcodes',
		'do_suggestions',
		'quoted_search_support',
		'tokenize_pattern_matches',
		'remove_min_word_length',
		'reduced_indexer_aggressiveness',
		'hide_announcements',
		'nuke_on_delete',
		'indexer_paused',
		'license',
		'new_activation',
	];

	/**
	 * Getter for capability tag.
	 *
	 * @since 4.0.12
	 *
	 * @return string
	 */
	public static function get_capability() {

		return (string) apply_filters( 'searchwp\settings\capability', self::$capability );
	}

	/**
	 * Getter for all settings.
	 *
	 * @since 4.0
	 * @return array Settings.
	 */
	public static function get( string $setting = '', $type = null ) {
		if ( ! empty( $setting ) ) {
			return self::get_single( $setting, $type );
		}

		$index   = \SearchWP::$index;
		$engines = self::get_engines();

		return [
			'sources' => $index->get_sources(),
			'engines' => $engines,
			'weights' => Utils::get_weight_definitions(),
			'misc'    => [
				'colors'             => self::get_colors(),
				'prefix'             => SEARCHWP_PREFIX,
				'hasInitialSave'     => ! empty( $engines ),
				'docContentResetAsk' => empty( self::get_single( 'document_content_reset', 'boolean' ) )
										&& empty( self::get_single( 'document_content_reset_dismissed', 'boolean' ) ),
			],
		];
	}

	/**
	 * Getter for settings keys.
	 *
	 * @since 4.0
	 * @return string[]
	 */
	public static function get_keys() {
		return array_merge( self::$keys, self::$autoload_keys );
	}

	/**
	 * Getter for autoloaded settings keys.
	 *
	 * @since 4.0
	 * @return string[]
	 */
	public static function get_autoload_keys() {
		return self::$autoload_keys;
	}

	/**
	 * Retrieves a single Setting value.
	 *
	 * @since 4.0
	 * @param string $setting The setting key.
	 * @return mixed
	 */
	public static function get_single( string $setting, $type = null ) {
		if ( ! in_array( $setting, self::get_keys() ) ) {
			return null;
		}

		$cache = wp_cache_get( SEARCHWP_PREFIX . 'settings_' . $setting, '' );

		if ( ! empty( $cache ) && is_array( $cache ) && array_key_exists( $setting, $cache ) ) {
			return self::normalize_value( $cache[ $setting ], $type );
		}

		$setting_value = get_option( SEARCHWP_PREFIX . $setting );

		// Because some values will be FALSE we're going to cache an array so as to flag the cache.
		wp_cache_set( SEARCHWP_PREFIX . 'settings_' . $setting, [ $setting => $setting_value ], '', 1 );

		return self::normalize_value( $setting_value, $type );
	}

	/**
	 * Noramlizes a value based on the passed type.
	 *
	 * @since 4.1
	 * @param mixed $setting_value The incoming value.
	 * @param mixed|null $type The type.
	 * @return mixed The normalized value.
	 */
	public static function normalize_value( $setting_value, $type = null ) {
		if ( 'boolean' === $type || 'bool' === $type ) {
			$setting_value = '1' == $setting_value ? true : false;
		}

		if ( 'array' === $type ) {
			$setting_value = is_array( $setting_value ) ? $setting_value : [];
		}

		if ( 'int' === $type || 'integer' === $type ) {
			$setting_value = intval( $setting_value );
		}

		return $setting_value;
	}

	/**
	 * Setter for setting.
	 *
	 * @since 4.0
	 * @param string $setting The setting key.
	 * @param mixed  $value   The setting value.
	 * @return mixed
	 */
	public static function update( string $setting = '', $value = null ) {
		if ( ! in_array( $setting, self::get_keys() ) ) {
			return null;
		}

		$autoload = in_array( $setting, self::$autoload_keys ) ? 'yes' : 'no';

		wp_cache_delete( SEARCHWP_PREFIX . 'settings_' . $setting );

		update_option( SEARCHWP_PREFIX . $setting, $value, $autoload );

		// By default WP_Cache will return `false` if the key doesn't exist, but sometimes
		// our intended value is `false` so we're going to wrap this in an array.
		wp_cache_set( SEARCHWP_PREFIX . 'settings_' . $setting, [ $setting => $value ], '', 1 );

		do_action( "searchwp\settings\update\\" . $setting, $value );

		return $value;
	}

	/**
	 * Setup for colors given the current admin color scheme.
	 *
	 * @since 4.0
	 * @return array Hex codes for colors to use in the WP Admin.
	 */
	public static function get_colors() {
		global $_wp_admin_css_colors;

		$scheme_id = get_user_option( 'admin_color' );

		// It's possible to completely remove all color schemes, so we have to be a bit verbose here.
		$colors = isset( $_wp_admin_css_colors['fresh'] ) ? $_wp_admin_css_colors['fresh'] : (object) [
			'name'        => 'Default',
			'url'         => '',
			'colors'      => [ '#222', '#333', '#0073aa', '#00a0d2', ],
			'icon_colors' => [ 'base' => '#a0a5aa', 'focus' => '#00a0d2', 'current' => '#fff', ],
		];
		$colors = isset( $_wp_admin_css_colors[ $scheme_id ] ) ? $_wp_admin_css_colors[ $scheme_id ] : $colors;

		$current = isset( $colors->colors[2] ) ? $colors->colors[2] : null;

		return [
			'text'      => '#444',
			'heading'   => '#23282d',
			'border'    => '#ccd0d4',
			'input'     => [ 'color' => '#32373c', 'border' => '#7e8993', ],
			'link'      => [ 'base' => '#0073aa', 'hover' => '#0096dd', ],
			'admin'     => $colors,
			'highlight' => isset( $colors->colors[0] ) ? $colors->colors[0] : null,
			'base'      => isset( $colors->colors[1] ) ? $colors->colors[1] : null,
			'current'   => $current,
			'hover'     => isset( $colors->colors[3] ) ? $colors->colors[3] : null,
		];
	}

	/**
	 * Getter for engines as name => label pairs.
	 *
	 * @since 4.0
	 * @return array Engine labels.
	 */
	public static function get_engines_as_name_label() {
		return array_map( function( $engine ) {
			return $engine->get_label();
		}, self::get_engines() );
	}

	/**
	 * Deletes setting.
	 *
	 * @since 4.0
	 * @param string $setting The setting key.
	 * @return mixed
	 */
	public static function delete( string $setting = '' ) {
		if ( ! in_array( $setting, self::get_keys() ) ) {
			return null;
		}

		delete_option( SEARCHWP_PREFIX . $setting );
	}

	/**
	 * Getter for engines as Engine objects.
	 *
	 * @since 4.0
	 * @return array Engine models.
	 */
	public static function get_engines( $skip_cache = false ) {

		// Statically cache engines to improve runtime performance.
		static $_engines = null;
		static $_engines_settings = null;

		$engines_settings = self::_get_engines_settings( $skip_cache );

		// Make sure we return statically cached $engines only if $engines_settings didn't change in the runtime.
		if ( $_engines !== null && $_engines_settings === $engines_settings ) {
			return $_engines;
		}

		$engines = [];

		foreach ( $engines_settings as $engine => $engine_settings ) {
			$engine_model = new Engine( $engine, $engine_settings );

			if ( ! empty( $engine_model->get_name() ) ) {
				$engines[ $engine ] = $engine_model;
			}
		}

		// Save the static cache.
		$_engines = $engines;
		$_engines_settings = $engines_settings;

		return $engines;
	}

	/**
	 * Getter for defined Admin Engine.
	 *
	 * @since 4.0
	 * @return string|false Name of Engine (or false).
	 */
	public static function get_admin_engine() {
		$admin_engine = array_filter( self::_get_engines_settings(), function( $engine ) {
			return ! empty( $engine['settings']['adminengine'] );
		} );

		if ( empty( $admin_engine ) ) {
			$admin_engine = false;
		} else {
			reset( $admin_engine );
			$admin_engine = key( $admin_engine );
		}

		return $admin_engine;
	}

	/**
	 * Getter for single Engine settings.
	 *
	 * @since 4.0
	 *
	 * @param string|null $name Engine name.
	 *
	 * @return mixed|false
	 */
	public static function get_engine_settings( ?string $name ) {

		if ( empty( $name ) ) {
			return false;
		}

		$engines = self::_get_engines_settings();

		return array_key_exists( $name, $engines ) ? $engines[ $name ] : false;
	}

	/**
	 * Getter for saved Engines settings stored in the database.
	 *
	 * @since 4.0
	 *
	 * @param bool $skip_cache Skip caching.
	 *
	 * @return array Raw Engine settings.
	 */
	public static function _get_engines_settings( $skip_cache = false ) {

		if ( ! $skip_cache ) {
			$cache = wp_cache_get( self::$engines_cache_key, '' );
		}

		if ( empty( $cache ) || $skip_cache ) {
			$engines_settings = get_option( SEARCHWP_PREFIX . 'engines' );
		} else {
			$engines_settings = $cache;
		}

		if ( ! $skip_cache ) {
			wp_cache_set( self::$engines_cache_key, $engines_settings, '', 1 );
		}

		return is_array( $engines_settings ) ? $engines_settings : [];
	}

	/**
	 * Setter for Engines configs.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function update_engines_config( $config ) {
		update_option( SEARCHWP_PREFIX . 'engines', $config, 'no' );
		wp_cache_delete( self::$engines_cache_key );
	}
}
