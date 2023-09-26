<?php

namespace ACA\Types\Editing\Storage;

use AC\MetaType;
use ACP;

class Repeater implements ACP\Editing\Storage {

	/**
	 * @var string
	 */
	private $meta_key;

	/**
	 * @var MetaType
	 */
	private $meta_type;

	public function __construct( $meta_key, MetaType $meta_type ) {
		$this->meta_key = $meta_key;
		$this->meta_type = $meta_type;
	}

	public function get( int $id ) {
		return get_metadata( $this->meta_type->get(), $id, $this->meta_key, false );
	}

	public function update( int $id, $data ): bool {
		delete_metadata( $this->meta_type->get(), $id, $this->meta_key, null );

		$results = [];

		foreach ( $data as $_val ) {
			$results[] = add_metadata( $this->meta_type->get(), $id, $this->meta_key, $_val );
		}

		return ! in_array( false, $results, true );
	}

}