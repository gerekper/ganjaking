<?php

namespace ACP\Editing\Model\CustomField;

use AC;
use ACP\Editing\Model;
use ACP\Editing\PaginatedOptions;
use ACP\Helper\Select;
use ACP\Helper\Select\Formatter;
use ACP\Helper\Select\Group;

class User extends Model\CustomField
	implements PaginatedOptions {

	public function get_edit_value( $object_id ) {
		$ids = $this->column->get_raw_value( $object_id );

		if ( empty( $ids ) ) {
			return false;
		}

		$value = [];

		foreach ( (array) $ids as $id ) {
			$value[ $id ] = ac_helper()->user->get_display_name( $id );
		}

		return $value;
	}

	public function get_view_settings() {
		return [
			'type'          => 'select2_dropdown',
			'ajax_populate' => true,
		];
	}

	public function get_paginated_options( $s, $paged, $id = null ) {
		$entities = new Select\Entities\User( [
			'search' => $s,
			'paged'  => $paged,
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Group\UserRole(
				new Formatter\UserName( $entities )
			)
		);
	}

}