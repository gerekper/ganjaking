<?php

namespace ACP\QuickAdd\Model\Create;

use ACP\QuickAdd\Model\Create;
use LogicException;
use RuntimeException;
use WP_User;

class Post implements Create {

	/**
	 * @var string
	 */
	protected $post_type;

	public function __construct( $post_type ) {
		if ( ! post_type_exists( $post_type ) ) {
			throw new LogicException( 'Post Type does not exists.' );
		}

		$this->post_type = $post_type;
	}

	public function create() {
		$args = [
			'post_type' => $this->post_type,
		];

		if ( post_type_supports( $this->post_type, 'title' ) ) {
			$args['post_title'] = __( '(no title)' );
		}

		add_filter( 'wp_insert_post_empty_content', '__return_false' );

		$id = wp_insert_post( $args, true );

		remove_filter( 'wp_insert_post_empty_content', '__return_false' );

		if ( is_wp_error( $id ) ) {
			throw new RuntimeException( $id->get_error_message() );
		}

		return (int) $id;
	}

	public function has_permission( WP_User $user ) {
		return user_can( $user, get_post_type_object( $this->post_type )->cap->create_posts );
	}

}