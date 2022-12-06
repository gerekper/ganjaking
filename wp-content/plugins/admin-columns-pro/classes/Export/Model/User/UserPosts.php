<?php
declare( strict_types=1 );

namespace ACP\Export\Model\User;

use ACP;
use ACP\Export\Model;

/**
 * @property ACP\Column\User\UserPosts $column
 */
class UserPosts extends Model {

	public function __construct( ACP\Column\User\UserPosts $column ) {
		parent::__construct( $column );
	}

	public function get_value( $id ) {
		$ids = $this->column->get_raw_value( $id );

		return count( $ids );
	}

}