<?php

namespace ACA\Pods\Editing\Storage;

use ACA\Pods\Editing\Storage\Read\PodsRaw;
use ACP;

class Field implements ACP\Editing\Storage {

	/**
	 * @var string
	 */
	protected $pod;

	/**
	 * @var string
	 */
	protected $field_name;

	/**
	 * @var ReadStorage
	 */
	protected $read_storage;

	public function __construct( $pod, $field_name, ReadStorage $read ) {
		$this->pod = $pod;
		$this->field_name = $field_name;
		$this->read_storage = $read ?: new PodsRaw( $pod, $field_name );
	}

	public function get( int $id ) {
		return $this->read_storage->get( $id );
	}

	public function update( int $id, $data ): bool {
		$pod = pods( $this->pod, $id, true );

		return false !== $pod->save( [ $this->field_name => $data ] );
	}

}