<?php

namespace ACP\Export\Model\Post;

use AC\Column;
use ACP\Export\Service;

class ImageFileSizes implements Service {

	private $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		return ac_helper()->file->get_readable_filesize( array_sum( $this->column->get_raw_value( $id ) ) );
	}

}