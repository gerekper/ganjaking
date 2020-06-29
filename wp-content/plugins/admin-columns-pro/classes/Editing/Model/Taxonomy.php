<?php

namespace ACP\Editing\Model;

use ACP\Editing\Model;

abstract class Taxonomy extends Model {

	/**
	 * @param $id
	 * @param $args
	 *
	 * @return bool
	 */
	protected function update_term( $id, $args ) {
		$result = wp_update_term( $id, $this->get_column()->get_taxonomy(), $args );

		if ( is_wp_error( $result ) ) {
			$this->set_error( $result );

			return false;
		}

		return true;
	}

}