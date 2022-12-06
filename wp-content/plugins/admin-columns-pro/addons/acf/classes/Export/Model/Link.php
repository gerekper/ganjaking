<?php

namespace ACA\ACF\Export\Model;

use ACA;
use ACP;

class Link extends ACP\Export\Model {

	public function get_value( $id ) {
		$link = $this->column->get_raw_value( $id );

		if ( empty( $link ) ) {
			return '';
		}

		return $link['url'];
	}

}