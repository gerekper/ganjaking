<?php

namespace ACP\Editing\Service;

use ACP\Editing\Service;
use ACP\Editing\Storage;

abstract class BasicStorage implements Service {

	protected $storage;

	public function __construct( Storage $storage ) {
		$this->storage = $storage;
	}

	public function update( int $id, $data ): void {
		$this->storage->update( $id, $data );
	}

	public function get_value( int $id ) {
		return $this->storage->get( $id );
	}

}