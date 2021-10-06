<?php

namespace ACP\Editing\Service;

use AC\Request;
use ACP\Editing;
use ACP\Editing\Storage;

abstract class BasicStorage implements Editing\Service {

	/**
	 * @var Storage
	 */
	private $storage;

	public function __construct( Storage $storage ) {
		$this->storage = $storage;
	}

	public function get_value( $id ) {
		return $this->storage->get( $id );
	}

	public function update( Request $request ) {
		return $this->storage->update( (int) $request->get( 'id' ), $request->get( 'value' ) );
	}

}