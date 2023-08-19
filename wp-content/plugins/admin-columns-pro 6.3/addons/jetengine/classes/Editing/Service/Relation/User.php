<?php

namespace ACA\JetEngine\Editing\Service\Relation;

use ACA\JetEngine\Editing;
use ACP;

class User extends Editing\Service\Relationship {

	public function get_value( $id ) {
		$value = [];
		$user_ids = parent::get_value( $id );

		foreach ( $user_ids as $user_id ) {
			$value[ $user_id ] = ac_helper()->user->get_display_name( $user_id );
		}

		return $value;
	}

	public function get_paginated_options( $search, $page, $id = null ) {
		return new ACP\Helper\Select\Paginated\Users( $search, $page );
	}

}