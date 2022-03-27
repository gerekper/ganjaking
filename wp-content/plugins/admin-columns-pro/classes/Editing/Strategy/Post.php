<?php

namespace ACP\Editing\Strategy;

use ACP\Editing\Strategy;
use WP_Post;

class Post implements Strategy {

	/**
	 * @var string
	 */
	protected $post_type;

	/**
	 * @param string $post_type
	 */
	public function __construct( $post_type ) {
		$this->post_type = $post_type;
	}

	/**
	 * @param WP_Post|int $post
	 *
	 * @return bool
	 */
	public function user_has_write_permission( $post ) {
		if ( ! $post instanceof WP_Post ) {
			$post = get_post( $post );

			if ( ! $post instanceof WP_Post ) {
				return false;
			}
		}

		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return false;
		}

		if ( wp_check_post_lock( $post->ID ) ) {
			return false;
		}

		return true;
	}

}