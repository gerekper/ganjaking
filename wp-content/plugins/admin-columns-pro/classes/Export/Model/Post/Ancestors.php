<?php

namespace ACP\Export\Model\Post;

use AC\Collection;
use ACP\Column;
use ACP\Export\Service;

class Ancestors implements Service {

	private $column;

	public function __construct( Column\Post\Ancestors $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$ancestors = $this->column->get_ancestor_ids( $id );
		$formatted_values = $this->column->get_formatted_value( new Collection( $ancestors ) );

		return strip_tags( $formatted_values->implode( ', ' ) );
	}

}