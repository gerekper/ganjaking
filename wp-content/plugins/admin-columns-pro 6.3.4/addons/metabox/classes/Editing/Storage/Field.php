<?php

namespace ACA\MetaBox\Editing\Storage;

use AC\MetaType;
use ACP;
use RWMB_Field;

class Field implements ACP\Editing\Storage {

	/**
	 * @var string
	 */
	protected $meta_key;

	/**
	 * @var MetaType
	 */
	protected $meta_type;

	/**
	 * @var array
	 */
	protected $field_settings;

	/**
	 * @var bool
	 */
	protected $single;

	public function __construct( $meta_key, MetaType $meta_type, array $field_settings, $single = true ) {
		$this->meta_key = (string) $meta_key;
		$this->meta_type = $meta_type;
		$this->field_settings = $field_settings;
		$this->single = (bool) $single;
	}

	public function get( int $id ) {
		return get_metadata( $this->meta_type->get(), $id, $this->meta_key, $this->single );
	}

	public function update( int $id, $data ): bool {
		RWMB_Field::save( $data, $this->get( $id ), $id, $this->field_settings );

		return true;
	}

}