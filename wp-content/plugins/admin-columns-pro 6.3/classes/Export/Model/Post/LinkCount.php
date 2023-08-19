<?php

namespace ACP\Export\Model\Post;

use ACP\Column;
use ACP\Export\Service;

class LinkCount implements Service {

	private $column;

	public function __construct( Column\Post\LinkCount $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$links = $this->column->get_raw_value( $id );

		if ( ! $links ) {
			return false;
		}

		return sprintf( '%s / %s', count( $links[0] ), count( $links[1] ) );
	}

}