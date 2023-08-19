<?php

namespace ACP\Export\Model\Media;

use ACP\Export\Service;

class Title implements Service {

	public function get_value( $id ) {
		return (string) wp_get_attachment_url( $id );
	}

}