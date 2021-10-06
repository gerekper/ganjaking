<?php

namespace ACP\Server;

use AC\Storage\KeyValuePair;
use AC\Storage\OptionFactory;
use ACP\Type;

class Platform {

	const OPTION_PLATFORM_PREFIX = 'acp_platform_';

	/**
	 * @var KeyValuePair
	 */
	private $storage;

	public function __construct( Type\SiteId $site_id, $network_active ) {
		$this->storage = ( new OptionFactory() )->create( self::OPTION_PLATFORM_PREFIX . $site_id->get_hash(), (bool) $network_active );
	}

	public function is_local() {
		return $this->is_server_local() || $this->is_remote_local();
	}

	public function is_remote_local() {
		return Type\Platform::LOCAL === $this->storage->get();
	}

	public function is_server_local() {
		return isset( $_SERVER['REMOTE_ADDR'] ) && in_array( $_SERVER['REMOTE_ADDR'], [ '127.0.0.1', '::1' ], true );
	}

	public function remote_exists() {
		return null !== $this->find();
	}

	/**
	 * @return Type\Platform|null
	 */
	private function find() {
		$platform = $this->storage->get();

		return Type\Platform::is_valid( $platform )
			? new Type\Platform( $platform )
			: null;
	}

	public function delete() {
		$this->storage->delete();
	}

	public function save( Type\Platform $platform ) {
		$this->storage->save( $platform->get_value() );
	}

}