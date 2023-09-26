<?php

namespace ACA\WC\Export\ShopOrder;

use AC\Column;
use ACP;

class ProductThumbnails implements ACP\Export\Service {

	private $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$values = [];

		$thumbnails_ids = $this->column->get_raw_value( $id );

		foreach ( $thumbnails_ids as $thumbnail_id ) {
			$values[] = wp_get_attachment_image_url( (int) $thumbnail_id, 'full' );
		}

		return implode( ', ', $values );
	}

}