<?php

namespace ACA\Pods\Editing\Storage\Read;

use ACA\Pods\Editing\Storage\ReadStorage;

class PodsRaw implements ReadStorage {

	/**
	 * @var string
	 */
	private $pod;

	/**
	 * @var string
	 */
	private $field_name;

	public function __construct( $pod, $field_name ) {
		$this->pod = $pod;
		$this->field_name = $field_name;
	}

	public function get( int $id ) {
		return pods_field_raw( $this->pod, $id, $this->field_name, true );
	}

}