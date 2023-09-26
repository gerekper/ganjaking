<?php

namespace ACP\Export\Model\Post;

use ACP\Column;
use ACP\Export\Service;

class Shortcodes implements Service {

	private $column;

	public function __construct( Column\Post\Shortcodes $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$raw_value = $this->column->get_raw_value( (int) $id );

		return $raw_value
			? implode( ', ', array_keys( $raw_value ) )
			: '';
	}

}