<?php

namespace ACP\Editing\Model\Comment;

use ACP\Editing\Model;

class Type extends Model\Comment {

	public function save( $id, $value ) {
		return $this->update_comment( $id, [ 'comment_type' => $value ] );
	}

}