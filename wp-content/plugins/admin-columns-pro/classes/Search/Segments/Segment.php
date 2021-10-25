<?php

namespace ACP\Search\Segments;

final class Segment {

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var array
	 */
	private $data;

	/**
	 * @param string $name
	 * @param array  $data
	 */
	public function __construct( $name, array $data ) {
		$this->name = $name;
		$this->data = $data;
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @param string $key
	 *
	 * @return array
	 */
	public function get_value( $key ) {
		return $this->data[ $key ];
	}

	/**
	 * @return array
	 */
	public function get_data() {
		return $this->data;
	}

}