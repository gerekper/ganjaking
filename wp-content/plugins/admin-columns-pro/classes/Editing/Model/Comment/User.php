<?php

namespace ACP\Editing\Model\Comment;

use AC;
use ACP\Editing\Model;
use ACP\Editing\PaginatedOptions;
use ACP\Helper\Select;

class User extends Model\Comment
	implements PaginatedOptions {

	public function get_view_settings() {
		return [
			'type'               => 'select2_dropdown',
			'ajax_populate'      => true,
			'store_single_value' => true,
		];
	}

	public function get_edit_value( $id ) {
		$user = get_userdata( $this->column->get_raw_value( $id ) );

		if ( ! $user ) {
			return false;
		}

		return [
			$user->ID => ac_helper()->user->get_display_name( $user ),
		];
	}

	public function get_paginated_options( $search, $page, $id = null ) {
		$entities = new Select\Entities\User( compact( 'search', 'page' ) );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Select\Group\UserRole(
				new Select\Formatter\UserName( $entities )
			)
		);
	}

	public function save( $id, $value ) {
		return $this->update_comment( $id, [ 'user_id' => $value ] );
	}

}