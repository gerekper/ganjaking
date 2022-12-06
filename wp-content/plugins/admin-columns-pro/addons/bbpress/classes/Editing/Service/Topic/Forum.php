<?php

namespace ACA\BbPress\Editing\Service\Topic;

use AC;
use ACP\Editing;
use ACP\Editing\View;
use ACP\Helper\Select;

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

	public function get_paginated_options( $s, $paged, $id = null ) {
		$entities = new Select\Entities\Post( [
			's'         => $s,
			'paged'     => $paged,
			'post_type' => 'forum',
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Select\Formatter\PostTitle( $entities )
		);
	}

}