<?php

namespace ACA\MetaBox\Editing\Service\Relation;

use ACA;
use ACP;

class User extends ACA\MetaBox\Editing\Service\Relation {

	public function get_value( $id ) {
		return array_map( 'get_the_title', parent::get_value( $id ) );
	}

	public function get_paginated_options( $s, $paged, $id = null ) {
		return new ACP\Helper\Select\Paginated\Users( $s, $paged );
	}

}