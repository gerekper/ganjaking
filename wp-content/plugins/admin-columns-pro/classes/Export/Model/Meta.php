<?php

namespace ACP\Export\Model;

use AC;
use ACP\Export\Model;

/**
 * @since 5.7
 */
class Meta extends Model {

	/**
	 * @var string
	 */
	protected $meta_key;

	/**
	 * @var AC\MetaType
	 */
	protected $meta_type;

	public function __construct( AC\Column $column, AC\MetaType $meta_type, $meta_key ) {
		parent::__construct( $column );

		$this->meta_key = $meta_key;
		$this->meta_type = $meta_type;
	}

	public function get_value( $id ) {
		$value = get_metadata( $this->meta_type->get(), $id, $this->meta_key, true );

		return is_scalar( $value )
			? (string) $value
			: '';
	}

}