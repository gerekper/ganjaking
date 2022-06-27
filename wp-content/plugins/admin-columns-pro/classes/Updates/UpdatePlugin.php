<?php

namespace ACP\Updates;

use AC\Registrable;
use AC\Storage\KeyValuePair;
use stdClass;

/**
 * Hooks into the WordPress update process for plugins
 */
class UpdatePlugin implements Registrable {

	/**
	 * @var string
	 */
	private $base_name;

	/**
	 * @var string
	 */
	private $version;

	/**
	 * @var KeyValuePair
	 */
	private $storage;

	public function __construct( $base_name, $version, KeyValuePair $storage ) {
		$this->base_name = $base_name;
		$this->version = $version;
		$this->storage = $storage;
	}

	public function register() {
		add_action( 'pre_set_site_transient_update_plugins', [ $this, 'check_update' ] );
	}

	public function check_update( $transient ) {
		$data = $this->storage->get();

		if ( empty( $data ) || ! is_array( $data ) ) {
			return $transient;
		}

		$dir_name = dirname( $this->base_name );

		if ( ! isset( $data[ $dir_name ] ) ) {
			return $transient;
		}

		$plugin_data = (object) $data[ $dir_name ];

		if ( null === $transient ) {
			$transient = new stdClass();
		}

		if ( version_compare( $this->version, $plugin_data->new_version, '<' ) ) {
			$transient->response[ $this->base_name ] = $plugin_data;
		}

		return $transient;
	}

}