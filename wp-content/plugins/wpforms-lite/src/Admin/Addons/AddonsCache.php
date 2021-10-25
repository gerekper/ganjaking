<?php

namespace WPForms\Admin\Addons;

/**
 * Addons cache handler.
 *
 * @since 1.6.6
 */
class AddonsCache {

	/**
	 * Settings.
	 *
	 * @since 1.6.6
	 *
	 * @var array
	 */
	private $settings;

	/**
	 * Constructor.
	 *
	 * @since 1.6.6
	 */
	public function __construct() {

		$this->setup();
		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 * @since 1.6.6
	 */
	protected function hooks() {

		// Schedule recurring updates.
		add_action( 'admin_init', [ $this, 'schedule_update_cache' ] );
		add_action( 'wpforms_admin_addons_cache_update', [ $this, 'update_cache' ] );
		add_action( 'admin_init', [ $this, 'cache_dir_complete' ] );
	}

	/**
	 * Set up settings and other things.
	 *
	 * @since 1.6.6
	 */
	private function setup() {

		$upload_dir  = wpforms_upload_dir();
		$upload_path = ! empty( $upload_dir['path'] ) ? trailingslashit( wp_normalize_path( $upload_dir['path'] ) ) : trailingslashit( WP_CONTENT_DIR ) . 'uploads/wpforms/';

		$this->settings = [

			// Remote source URL.
			'remote_source' => 'https://cdn.wpforms.com/wp-content/addons.json',

			// Docs cache file (full path).
			'cache_file'    => $upload_path . 'cache/addons.json',

			// Docs cache time to live in seconds.
			'cache_ttl'     => (int) apply_filters( 'wpforms_admin_addons_cache_ttl', WEEK_IN_SECONDS ),
		];
	}

	/**
	 * Get cached addons data.
	 *
	 * @since 1.6.6
	 *
	 * @return array Addons data.
	 */
	public function get_cached() {

		$cache_modified_time = 0;
		$current_time        = time();

		if ( file_exists( $this->settings['cache_file'] ) ) {
			clearstatcache( true, $this->settings['cache_file'] );
			$cache_modified_time = (int) filemtime( $this->settings['cache_file'] );
			$addons              = json_decode( file_get_contents( $this->settings['cache_file'] ), true );
		}

		if (
			! empty( $addons ) &&
			$cache_modified_time + $this->settings['cache_ttl'] > $current_time
		) {
			return $addons;
		}

		// This code should execute when the method was called for the first time,
		// Next update_cache() should be executed as scheduled.
		// Also, we will try to update the cache only if the latest unsuccessful try has been 10 (or more) minutes ago.
		if ( $cache_modified_time + 600 < $current_time ) {
			return $this->update_cache();
		}

		return [];
	}

	/**
	 * Update addons cache with actual data retrieved from the remote source.
	 *
	 * @since 1.6.6
	 *
	 * @return array Updated addons data. Empty array on error.
	 */
	public function update_cache() {

		$request = wp_remote_get( $this->settings['remote_source'] );

		if ( is_wp_error( $request ) ) {
			return [];
		}

		$json = wp_remote_retrieve_body( $request );

		if ( empty( $json ) ) {
			return [];
		}

		$addons = $this->prepare_addons_cache_data( json_decode( $json, true ) );
		$dir    = dirname( $this->settings['cache_file'] );

		// Just return the data if can't create the cache directory.
		if ( ! wp_mkdir_p( $dir ) ) {
			return $addons;
		}

		file_put_contents( // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
			$this->settings['cache_file'],
			wp_json_encode( $addons )
		);

		return $addons;
	}

	/**
	 * Schedule updates.
	 *
	 * @since 1.6.6
	 */
	public function schedule_update_cache() {

		$tasks = wpforms()->get( 'tasks' );

		if ( empty( $tasks->is_scheduled( 'wpforms_admin_addons_cache_update' ) ) ) {
			$tasks->create( 'wpforms_admin_addons_cache_update' )
				  ->recurring( time() + $this->settings['cache_ttl'], $this->settings['cache_ttl'] )
				  ->params()
				  ->register();
		}
	}

	/**
	 * Complete the cache directory.
	 *
	 * @since 1.6.6
	 */
	public function cache_dir_complete() {

		wpforms_create_upload_dir_htaccess_file();
		wpforms_create_index_html_file( dirname( $this->settings['cache_file'] ) );
	}

	/**
	 * Prepare addons data to store in a local cache -
	 * generate addons icon image file name for further use.
	 *
	 * @since 1.6.6
	 *
	 * @param array $addons Raw addons data.
	 *
	 * @return array Addons data without URLs.
	 */
	private function prepare_addons_cache_data( $addons ) {

		if ( empty( $addons ) || ! is_array( $addons ) ) {
			return [];
		}

		$addons_cache = [];

		foreach ( $addons as $addon ) {

			// Addon icon.
			$addon['icon'] = str_replace( 'wpforms-', 'addon-icon-', $addon['slug'] ) . '.png';

			// Use slug as a key for further usage.
			$addons_cache[ $addon['slug'] ] = $addon;
		}

		return $addons_cache;
	}
}
