<?php

namespace ACA\BbPress\Editing\Service\Topic;

use AC\Helper\Select\Options\Paginated;
use ACP\Editing;
use ACP\Editing\View;
use ACP\Helper\Select;
use ACP\Helper\Select\Post\PaginatedFactory;

class Forum implements Editing\PaginatedOptions, Editing\Service {

	public function get_value( int $id ) {
		$forum_id = get_post_meta( $id, '_bbp_forum_id', true );
		$post = get_post( $forum_id );

		if ( ! $post ) {
			return false;
		}

		return [
			$post->ID => $post->post_title,
		];
	}

	public function update( int $id, $data ): void {
		update_post_meta( $id, '_bbp_forum_id', $data );
	}

	public function get_view( string $context ): ?View {
		return ( new View\AjaxSelect() )->set_multiple( false )->set_clear_button( true );
	}

	public function get_paginated_options( string $search, int $page, int $id = null ): Paginated {
		return ( new PaginatedFactory() )->create( [
			's'         => $search,
			'paged'     => $page,
			'post_type' => 'forum',
		] );
	}

}