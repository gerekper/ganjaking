<?php

namespace ACP\Export\Model\Post;

use AC\Column;
use ACP\Export\Service;

class Attachment implements Service {

	private $column;

	public function __construct( Column\Post\Attachment $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$urls = [];

		foreach ( $this->column->get_attachment_ids( (int) $id ) as $media_id ) {
			$urls[] = wp_get_attachment_url( (int) $media_id );
		}

		return implode( ', ', $urls );
	}

}