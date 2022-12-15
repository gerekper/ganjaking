<?php

namespace WPMailSMTP\Pro\AdditionalConnections;

use WPMailSMTP\Options;
use WPMailSMTP\WP;

/**
 * Class ConnectionOptions.
 *
 * @since 3.7.0
 */
class ConnectionOptions extends Options {

	/**
	 * That's where additional connections options are saved in wp_options table.
	 *
	 * @since 3.7.0
	 *
	 * @var string
	 */
	const META_KEY = 'wp_mail_smtp_additional_connections';

	/**
	 * The connection ID.
	 *
	 * @since 3.7.0
	 *
	 * @var string
	 */
	private $connection_id;

	/**
	 * All connections options.
	 *
	 * @since 3.7.0
	 *
	 * @var array
	 */
	private $all_options = [];

	/**
	 * All instances of ConnectionOptions class that should be notified about options update.
	 *
	 * @since 3.7.0
	 *
	 * @var ConnectionOptions[]
	 */
	protected static $update_observers;

	/**
	 * Constructor.
	 *
	 * @since 3.7.0
	 *
	 * @param string $connection_id The connection ID.
	 */
	public function __construct( $connection_id ) {

		$this->connection_id = $connection_id;

		parent::__construct();
	}

	/**
	 * Whether current class is a main options.
	 *
	 * @since 3.7.0
	 *
	 * @var bool
	 */
	protected function is_main_options() {

		return false;
	}

	/**
	 * Retrieve connection options.
	 *
	 * @since 3.7.0
	 */
	protected function populate_options() {

		if ( WP::use_global_plugin_settings() ) {
			$this->all_options = get_blog_option( get_main_site_id(), static::META_KEY, [] );
		} else {
			$this->all_options = get_option( static::META_KEY, [] );
		}

		// Populate connection options.
		$this->options = isset( $this->all_options[ $this->connection_id ] ) ? $this->all_options[ $this->connection_id ] : [];
	}

	/**
	 * Set connection options.
	 *
	 * @since 3.7.0
	 *
	 * @param array $options            Connection options to save.
	 * @param bool  $once               Whether to update existing options or to add these options only once.
	 * @param bool  $overwrite_existing Whether to overwrite existing settings or merge these passed options with existing ones.
	 */
	public function set( $options, $once = false, $overwrite_existing = true ) {

		// Merge existing settings with new values.
		if ( ! $overwrite_existing ) {
			$options = self::array_merge_recursive( $this->get_all_raw(), $options );
		}

		// Filter out non-connection options.
		$connection_options = array_merge( [ 'connection', 'mail', 'smtp' ], self::$mailers );
		$options            = array_intersect_key( $options, array_flip( $connection_options ) );

		// Sanitize options.
		if ( isset( $options['connection']['name'] ) ) {
			$options['connection']['name'] = sanitize_text_field( $options['connection']['name'] );
		}

		$options = $this->process_generic_options( $options );
		$options = $this->process_mailer_specific_options( $options );

		$all_options = array_merge( $this->all_options, [ $this->connection_id => $options ] );

		$this->save_options( $all_options, $once );
	}

	/**
	 * Whether constants redefinition is enabled or not.
	 *
	 * @since 3.7.0
	 *
	 * @return bool
	 */
	public function is_const_enabled() {

		// For now, additional connections do not support constants.
		return false;
	}
}
