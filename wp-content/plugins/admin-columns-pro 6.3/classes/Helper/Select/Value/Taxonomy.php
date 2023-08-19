<?php

namespace ACP\Helper\Select\Value;

use AC;
use LogicException;
use WP_Term;

final class Taxonomy
	implements AC\Helper\Select\Value {

	const ID = 'term_id';
	const SLUG = 'slug';

	/**
	 * @var string
	 */
	private $property;

	/**
	 * @param null|string $property
	 */
	public function __construct( $property = null ) {
		if ( null == $property ) {
			$property = self::ID;
		}

		$this->property = $property;

		$this->validate();
	}

	private function validate() {
		$properties = [ self::ID, self::SLUG ];

		if ( ! in_array( $this->property, $properties ) ) {
			throw new LogicException( 'Invalid property found.' );
		}
	}

	/**
	 * @param WP_Term $term
	 *
	 * @return string
	 */
	public function get_value( $term ) {
		$property = $this->property;

		return $term->$property;
	}

}