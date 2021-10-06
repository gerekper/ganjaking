<?php

namespace ACP\Editing\ApplyFilter;

use AC;

class PostStatus implements AC\ApplyFilter {

	/**
	 * @var AC\Column
	 */
	private $column;

	public function __construct( AC\Column $column ) {
		$this->column = $column;
	}

	public function apply_filters( $value ) {
		return apply_filters( 'acp/editing/post_statuses', $value, $this->column );
	}

}