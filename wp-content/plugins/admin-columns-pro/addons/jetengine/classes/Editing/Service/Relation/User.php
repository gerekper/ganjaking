<?php

namespace ACA\JetEngine\Editing\Service\Relation;

use AC\Helper\Select\Options\Paginated;
use ACA\JetEngine\Editing;
use ACP\Helper\Select\User\PaginatedFactory;

class User extends Editing\Service\Relationship {

	public function get_value( $id ) {
		$value = [];
		$user_ids = parent::get_value( $id );

		foreach ( $user_ids as $user_id ) {
			$value[ $user_id ] = ac_helper()->user->get_display_name( $user_id );
		}

		return $value;
	}

	public function get_paginated_options( string $search, int $page, int $id = null ): Paginated {
		return ( new PaginatedFactory() )->create( [
			'paged'  => $page,
			'search' => $search,
		] );
	}

}