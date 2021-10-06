<?php

namespace ACP\Editing\Storage;

use AC\MetaType;
use ACP\Editing\Storage;

class Meta implements Storage {

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

	public function get( $id ) {
		return get_metadata( $this->meta_type->get(), $id, $this->meta_key, true );
	}

	public function update( $id, $value ) {
		return false !== update_metadata( $this->meta_type->get(), $id, $this->meta_key, $value );
	}

}