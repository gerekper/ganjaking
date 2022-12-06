<?php

namespace ACP\Editing\Strategy;

use ACP\Editing\RequestHandler;
use ACP\Editing\Strategy;

class Post implements Strategy {

	/**
	 * @var string
	 */
	protected $post_type;

	/**
	 * @param string $post_type
	 */
	public function __construct( $post_type ) {
		$this->post_type = (string) $post_type;
	}

	public function user_can_edit() {
		return current_user_can( 'edit_posts' );
	}

	public function user_can_edit_item( $id ) {
		return $this->user_can_edit() && current_user_can( 'edit_post', $id ) && ! wp_check_post_lock( $id );
	}

	public function get_query_request_handler() {
		return new RequestHandler\Query\Post();
	}

}