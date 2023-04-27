<?php

namespace ACP\Export\Model\Post;

use ACP\Column;
use ACP\Export\Service;

class LastModifiedAuthor implements Service {

	private $column;

	public function __construct( Column\Post\LastModifiedAuthor $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$value = $this->column->get_value( $id );

		if ( $value === $this->column->get_empty_char() ) {
			$value = '';
		}

		return $value;
	}

}