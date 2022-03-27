<?php

namespace ACP\Export\Model\Post;

use ACP\Export\Model;

/**
 * Shows Internal / External links in post content
 * @since 4.1
 */
class LinkCount extends Model {

	public function get_value( $id ) {
		$links = $this->column->get_raw_value( $id );

		if ( ! $links ) {
			return false;
		}

		return sprintf( '%s / %s', count( $links[0] ), count( $links[1] ) );
	}

}