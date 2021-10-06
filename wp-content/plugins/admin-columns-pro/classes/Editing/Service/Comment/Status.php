<?php

namespace ACP\Editing\Service\Comment;

use AC\Request;
use ACP\Editing\Service;
use ACP\Editing\View;
use RuntimeException;

class Status implements Service {

	public function get_view( $context ) {
		return new View\Select( [
			'1'     => __( 'Approved' ),
			'0'     => __( 'Pending' ),
			'spam'  => __( 'Spam' ),
			'trash' => __( 'Trash' ),
		] );
	}

	public function get_value( $id ) {
		return get_comment( $id )->comment_approved;
	}

	/**
	 * @param int    $id
	 * @param string $status
	 *
	 * @return bool
	 */
	private function set_comment_status( $id, $status ) {
		$result = wp_set_comment_status( $id, $status );

		if ( is_wp_error( $result ) ) {
			throw new RuntimeException( $result->get_error_message() );
		}

		return $result;
	}

	public function update( Request $request ) {
		switch ( $request->get( 'value', '' ) ) {
			case 'trash' :
				return wp_trash_comment( $request->get( 'id' ) );
			case 'spam' :
				return wp_spam_comment( $request->get( 'id' ) );
			case '1' :
				return $this->set_comment_status( $request->get( 'id' ), 'approve' );
			case '0' :
				return $this->set_comment_status( $request->get( 'id' ), 'hold' );
			default:
				return false;
		}
	}

}