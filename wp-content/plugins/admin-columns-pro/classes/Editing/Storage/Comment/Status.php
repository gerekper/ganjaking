<?php

namespace ACP\Editing\Storage\Comment;

use ACP\Editing\Storage;
use RuntimeException;

class Status implements Storage {

	public function get( int $id ) {
		return get_comment( $id )->comment_approved;
	}

	private function set_comment_status( $id, $status ) {
		$result = wp_set_comment_status( $id, $status );

		if ( is_wp_error( $result ) ) {
			throw new RuntimeException( $result->get_error_message() );
		}

		return $result;
	}

	public function update( int $id, $data ): bool {
		switch ( $data ) {
			case 'trash' :
				return wp_trash_comment( $id );
			case 'spam' :
				return wp_spam_comment( $id );
			case '1' :
				return $this->set_comment_status( $id, 'approve' );
			case '0' :
				return $this->set_comment_status( $id, 'hold' );
			default:
				return false;
		}
	}

}