<?php

namespace ACP\RequestHandler\Ajax;

use AC;
use ACP\RequestAjaxHandler;

class Permalinks implements RequestAjaxHandler {

	public function handle() {
		$request = new AC\Request();
		$search = $request->get( 'search', '' );

		$posts = get_posts( [
			's'         => $search,
			'post_type' => [ 'any' ],
		] );

		$result = [];

		foreach ( $posts as $post ) {
			$result[] = [
				'id'        => $post->ID,
				'title'     => $post->post_title,
				'post_type' => $post->post_type,
				'permalink' => get_permalink( $post ),
			];
		}

		wp_send_json_success( $result );
	}

}