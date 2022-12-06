<?php

namespace ACP\Editing\Storage\Post;

use ACP\Editing\Storage;

class Sticky implements Storage {

	/**
	 * @var array
	 */
	private $stickies;

	private function is_sticky( int $id ): bool {
		if ( null === $this->stickies ) {
			$this->stickies = get_option( 'sticky_posts' );
		}

		return in_array( $id, $this->stickies, true );
	}

	public function get( $id ) {
		return $this->is_sticky( $id )
			? 'yes'
			: 'no';
	}

	public function update( int $id, $data ): bool {
		if ( 'yes' === $data ) {
			stick_post( $id );
		} else {
			unstick_post( $id );
		}

		wp_update_post( [ 'ID' => $id ] );

		return true;
	}

}