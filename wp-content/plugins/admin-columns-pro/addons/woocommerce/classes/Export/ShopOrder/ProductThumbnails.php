<?php

namespace ACA\WC\Export\ShopOrder;

use ACP;

/**
 * WooCommerce order shipping address (default column) exportability model
 * @since 2.2.1
 */
class ProductThumbnails extends ACP\Export\Model {

	public function get_value( $id ) {
		$thumbnails_ids = $this->column->get_raw_value( $id );
		$values = [];

		foreach ( $thumbnails_ids as $thumbnail_id ) {
			$values[] = wp_get_attachment_image_url( $thumbnail_id, 'full' );
		}

		return implode( ', ', $values );
	}

}