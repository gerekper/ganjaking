<?php

namespace ACP\Editing\Model;

use ACP\Editing\Model;

abstract class Comment extends Model {

	/**
	 * @param int   $id
	 * @param array $args
	 *
	 * @return bool
	 */
	protected function update_comment( $id, array $args = [] ) {
		$args['comment_ID'] = $id;

		return wp_update_comment( $args ) > 0;
	}

}