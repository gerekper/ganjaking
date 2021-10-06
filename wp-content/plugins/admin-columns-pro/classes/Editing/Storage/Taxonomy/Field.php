<?php

namespace ACP\Editing\Storage\Taxonomy;

use ACP\Editing\Storage;
use RuntimeException;

class Field implements Storage {

	/**
	 * @var string
	 */
	private $field;

	/**
	 * @var string
	 */
	private $taxonomy;

	public function __construct( $taxonomy, $field ) {
		$this->taxonomy = $taxonomy;
		$this->field = (string) $field;
	}

	public function get( $id ) {
		return ac_helper()->taxonomy->get_term_field( $this->field, $id, $this->taxonomy );
	}

	public function update( $id, $value ) {
		$result = wp_update_term( $id, $this->taxonomy, [
			$this->field => $value,
		] );

		if ( is_wp_error( $result ) ) {
			throw new RuntimeException( $result->get_error_message() );
		}

		return is_int( $result ) && $result > 0;
	}

}