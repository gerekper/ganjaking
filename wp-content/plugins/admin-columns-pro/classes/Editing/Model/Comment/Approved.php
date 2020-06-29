<?php

namespace ACP\Editing\Model\Comment;

use ACP\Editing\Model;

class Approved extends Model\Comment {

	public function get_view_settings() {
		return [
			'type'    => 'togglable',
			'options' => [
				0 => __( 'Unapprove' ),
				1 => __( 'Approve' ),
			],
		];
	}

	public function save( $id, $value ) {
		return $this->update_comment( $id, [ 'comment_approved' => $value ] );
	}

}