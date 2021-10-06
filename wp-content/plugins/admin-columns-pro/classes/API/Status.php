<?php

namespace ACP\API;

use AC\Storage\KeyValuePair;
use AC\Storage\OptionFactory;
use ACP\Type;

class Status {

	const OPTION_KEY = 'acp_api_status';

	/**
	 * @var KeyValuePair
	 */
	private $storage;

	public function __construct( Type\SiteId $site_id, $network_active ) {
		$this->storage = ( new OptionFactory() )->create( self::OPTION_KEY . $site_id->get_hash(), (bool) $network_active );
	}

	public function save( Type\ApiStatus $status ) {
		$this->storage->save( $status->get_value() );
	}

	public function delete() {
		$this->storage->delete();
	}

	/**
	 * @return Type\ApiStatus|null
	 */
	public function get() {
		$status = $this->storage->get();

		return Type\ApiStatus::is_valid( $status )
			? new Type\ApiStatus( $status )
			: null;
	}

}