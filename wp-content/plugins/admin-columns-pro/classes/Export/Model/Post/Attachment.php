<?php

namespace ACP\Export\Model\Post;

use ACP\Export\Model;

/**
 * @since 4.1
 */
class Attachment extends Model {

	public function get_value( $id ) {
		$urls = [];

		foreach ( $this->get_column()->get_raw_value( $id ) as $media_id ) {
			$urls[] = wp_get_attachment_url( $media_id );
		}

		return implode( ',', $urls );
	}

}