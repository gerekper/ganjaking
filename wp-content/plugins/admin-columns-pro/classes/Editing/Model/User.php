<?php

namespace ACP\Editing\Model;

use ACP\Editing\Model;
use WP_Error;

abstract class User extends Model {

	/**
	 * @param $id
	 * @param $args
	 *
	 * @return bool
	 */
	public function update_user( $id, $args ) {
		$args['ID'] = $id;

		$result = wp_update_user( $args );

		return ! $result instanceof WP_Error;
	}

}