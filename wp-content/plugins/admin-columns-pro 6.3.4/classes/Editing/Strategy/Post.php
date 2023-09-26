<?php

namespace ACP\Editing\Strategy;

use ACP\Editing\RequestHandler;
use ACP\Editing\Strategy;
use WP_Post_Type;

class Post implements Strategy {

	protected $post_type;

	public function __construct( WP_Post_Type $post_type ) {
		$this->post_type = $post_type;
	}

	public function user_can_edit(): bool {
		return current_user_can( $this->post_type->cap->edit_posts );
	}

	public function user_can_edit_item( int $id ): bool {
		return $this->user_can_edit()
		       && current_user_can( $this->post_type->cap->edit_post, $id )
		       && ! wp_check_post_lock( $id );
	}

	public function get_query_request_handler(): RequestHandler {
		return new RequestHandler\Query\Post();
	}

}