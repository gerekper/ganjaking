<?php

namespace ACP\Export\Model\Media;

use ACP\Export\Model;

class Title extends Model {

	public function get_value( $id ) {
		return wp_get_attachment_url( $id );
	}

}