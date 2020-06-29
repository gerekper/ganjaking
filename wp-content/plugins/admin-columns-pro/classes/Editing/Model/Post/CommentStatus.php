<?php

namespace ACP\Editing\Model\Post;

use ACP\Editing\Model;

class CommentStatus extends Model\Post {

	public function get_view_settings() {
		return [
			'type'    => 'togglable',
			'options' => [
				'closed' => __( 'Closed', 'codepress-admin-columns' ),
				'open'   => __( 'Open', 'codepress-admin-columns' ),
			],
		];
	}

	public function save( $id, $value ) {
		return $this->update_post( $id, [ 'comment_status' => $value ] );
	}

}