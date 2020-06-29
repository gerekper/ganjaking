<?php

namespace ACP\Editing\Model\Comment;

use ACP\Editing\Model;

/**
 * @property \AC\Column\Comment\Status $column
 */
class Status extends Model\Comment {

	public function get_view_settings() {
		return [
			'type'    => 'select',
			'options' => [
				'1'     => __( 'Approved' ),
				'0'     => __( 'Pending' ),
				'spam'  => __( 'Spam' ),
				'trash' => __( 'Trash' ),
			],
		];
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
			$this->set_error( $result );

			return false;
		}

		return true === $result;
	}

	/**
	 * @param int    $id
	 * @param string $value
	 *
	 * @return bool
	 */
	protected function save( $id, $value ) {
		switch ( $value ) {

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