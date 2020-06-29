<?php

namespace ACP\ThirdParty\bbPress\Editing;

use AC;
use ACP\Editing;
use ACP\Helper\Select;

class TopicForum extends Editing\Model implements Editing\PaginatedOptions {

	public function get_view_settings() {
		return [
			'type'          => 'select2_dropdown',
			'ajax_populate' => true,
			'multiple'      => false,
			'clear_button'  => true,
		];
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

	public function get_edit_value( $id ) {
		$forum_id = get_post_meta( $id, '_bbp_forum_id', true );
		$post = get_post( $forum_id );

		if ( ! $post ) {
			return false;
		}

		return [
			$post->ID => $post->post_title,
		];
	}

	public function save( $id, $value ) {
		return update_post_meta( $id, '_bbp_forum_id', $value );
	}

}