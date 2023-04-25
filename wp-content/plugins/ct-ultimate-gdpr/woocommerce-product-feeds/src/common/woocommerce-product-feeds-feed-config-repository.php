<?php

/**
 * Class WoocommerceProductFeedsFeedConfigRepository
 */
class WoocommerceProductFeedsFeedConfigRepository {

	/**
	 * @var array|null
	 */
	private $feed_configs = null;

	/**
	 * Retrieve all feed configs
	 *
	 * @return array
	 */
	public function all() {
		$this->ensure_loaded();
		$results = [];
		foreach ( $this->feed_configs as $feed_id => $feed_config ) {
			$config = new WoocommerceProductFeedsFeedConfig();
			$config->set_id( $feed_id );
			foreach ( $feed_config as $key => $value ) {
				$config->$key = $value;
			}
			$results[] = $config;
		}

		return $results;
	}

	/**
	 * Retrieve a stored config by ID.
	 *
	 * @param string $config_id
	 *
	 * @return WoocommerceProductFeedsFeedConfig|null
	 */
	public function get( $config_id ) {
		$this->ensure_loaded();

		if ( ! isset( $this->feed_configs[ $config_id ] ) ) {
			return null;
		}
		$config     = new WoocommerceProductFeedsFeedConfig();
		$config->id = $config_id;
		foreach ( $this->feed_configs[ $config_id ] as $key => $value ) {
			$config->$key = $value;
		}

		return $config;
	}

	/**
	 * Save a stored config.
	 *
	 * @param array $config
	 * @param string|null $config_id
	 */
	public function save( $config, $config_id = null ) {
		$this->ensure_loaded();
		if ( null === $config_id ) {
			$config_id = $this->generate_config_id();
		}
		$this->feed_configs[ $config_id ] = $config;
		update_option( 'woocommerce_gpf_feed_configs', $this->feed_configs );
	}

	/**
	 * Delete a stored config.
	 *
	 * @param $config_id
	 */
	public function delete( $config_id ) {
		$this->ensure_loaded();
		unset( $this->feed_configs[ $config_id ] );
		update_option( 'woocommerce_gpf_feed_configs', $this->feed_configs );
	}

	/**
	 * Get a list of active product feed feed types.
	 */
	public function get_active_feed_formats() {
		$this->ensure_loaded();

		return array_values( array_unique( wp_list_pluck( $this->feed_configs, 'type' ) ) );
	}

	/**
	 * Generate a new ID.
	 *
	 * @return false|string
	 */
	private function generate_config_id() {
		$this->ensure_loaded();
		do {
			$config_id = substr( wp_hash( microtime() ), 0, 16 );
		} while ( isset( $this->feed_configs[ $config_id ] ) );

		return $config_id;
	}

	/**
	 * Ensure that the configs have been loaded from the database.
	 */
	private function ensure_loaded() {
		if ( null === $this->feed_configs ) {
			$this->feed_configs = get_option( 'woocommerce_gpf_feed_configs', [] );
		}
	}
}
