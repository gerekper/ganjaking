<?php

namespace ACP\Editing\Model\Post;

use AC;
use ACP\Editing\Model;
use ACP\Editing\PaginatedOptions;
use ACP\Helper\Select;

class Author extends Model\Post implements PaginatedOptions {

	public function get_edit_value( $id ) {
		if ( current_user_can( 'author' ) ) {
			return null;
		}

		$user = get_userdata( ac_helper()->post->get_raw_field( 'post_author', $id ) );

		if ( ! $user ) {
			return false;
		}

		return [
			$user->ID => $user->display_name,
		];
	}

	public function get_view_settings() {
		return [
			'type'               => 'select2_dropdown',
			'ajax_populate'      => true,
			'store_single_value' => true,
		];
	}

	public function get_paginated_options( $search, $paged, $id = null ) {
		$entities = new Select\Entities\User( compact( 'search', 'paged' ) );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Select\Group\UserRole(
				new Select\Formatter\UserName( $entities )
			)
		);
	}

	public function save( $id, $value ) {
		return $this->update_post( $id, [ 'post_author' => $value ] );
	}

}