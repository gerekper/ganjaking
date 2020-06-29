<?php

namespace ACP\Export\Model\Post;

use ACP\Export\Model;

/**
 * @since 4.1
 */
class ChildPages extends Model {

	public function get_value( $id ) {
		$titles = [];

		foreach ( $this->get_column()->get_raw_value( $id ) as $post_id ) {
			$titles[] = get_post_field( 'post_title', $post_id );
		}

		return implode( ',', $titles );
	}

}