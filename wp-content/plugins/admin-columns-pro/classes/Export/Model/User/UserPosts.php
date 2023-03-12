<?php
declare( strict_types=1 );

namespace ACP\Export\Model\User;

use ACP;

class UserPosts implements ACP\Export\Service {

	private $column;

	public function __construct( ACP\Column\User\UserPosts $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$ids = $this->column->get_raw_value( $id );

		return (string) count( $ids );
	}

}