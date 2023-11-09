<?php

namespace Smush\Core\Modules\Bulk;

class Smush_Background_Task implements \Serializable {
	const TASK_TYPE_SMUSH = 'SMUSH';
	const TASK_TYPE_RESMUSH = 'RESMUSH';
	const TASK_TYPE_ERROR = 'ERROR';

	private $type;

	private $image_id;

	public function __construct( $type, $image_id ) {
		$this->type     = $type;
		$this->image_id = $image_id;
	}

	public function is_valid() {
		return $this->is_type_valid( $this->type )
		       && $this->is_image_id_valid( $this->image_id );
	}

	private function is_type_valid( $type ) {
		$valid_types = array( self::TASK_TYPE_SMUSH, self::TASK_TYPE_RESMUSH );

		return in_array( $type, $valid_types );
	}

	private function is_image_id_valid( $image_id ) {
		return intval( $image_id ) > 0;
	}

	/**
	 * @return mixed
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * @param mixed $type
	 */
	public function set_type( $type ) {
		$this->type = $type;
	}

	/**
	 * @return mixed
	 */
	public function get_image_id() {
		return $this->image_id;
	}

	/**
	 * @param mixed $image_id
	 */
	public function set_image_id( $image_id ) {
		$this->image_id = $image_id;
	}

	private static function get( $array, $key ) {
		return empty( $array[ $key ] ) ? null : $array[ $key ];
	}

	public function serialize() {
		return json_encode( $this->__serialize() );
	}

	public function unserialize( $data ) {
		$this->__unserialize( json_decode( $data, true ) );
	}

	public function __unserialize( $data ) {
		$type = self::get( $data, 'type' );
		$type = $this->is_type_valid( $type ) ? $type : '';
		$this->set_type( $type );

		$image_id = self::get( $data, 'image_id' );
		$image_id = $this->is_image_id_valid( $image_id ) ? $image_id : 0;
		$this->set_image_id( $image_id );
	}

	public function __serialize() {
		return array(
			'type'     => $this->type,
			'image_id' => $this->image_id,
		);
	}

	public function __toString() {
		return json_encode( $this->__serialize() );
	}
}