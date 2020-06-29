<?php

namespace ACP\Export\Model\Post;

use ACP\Export\Model;

/**
 * Shortcodes column exportability model
 * @since 4.1
 */
class Shortcodes extends Model {

	public function get_value( $id ) {
		$raw_value = $this->get_column()->get_raw_value( $id );

		return $raw_value ? implode( ', ', array_keys( $raw_value ) ) : '';
	}

}