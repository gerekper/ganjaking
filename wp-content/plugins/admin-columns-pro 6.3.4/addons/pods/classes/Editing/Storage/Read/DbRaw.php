<?php

namespace ACA\Pods\Editing\Storage\Read;

use ACA\Pods\Editing\Storage\ReadStorage;
use ACA\Pods\Value;

class DbRaw implements ReadStorage {

	/**
	 * @var string
	 */
	private $meta_key;

	/**
	 * @var string
	 */
	private $meta_type;

	public function __construct( $meta_key, $meta_type ) {
		$this->meta_key = $meta_key;
		$this->meta_type = $meta_type;
	}

	public function get( int $id ) {
		return ( new Value\DbRaw( $this->meta_key, $this->meta_type ) )->get_value( $id );
	}

}