<?php

namespace ACP\Editing\Service\Media;

use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View;

class AlternateText extends Service\Basic {

	public function __construct() {
		parent::__construct(
			new View\Text(),
			new Storage\Post\Meta( '_wp_attachment_image_alt' )
		);
	}

	public function get_value( $id ) {
		if ( ! wp_attachment_is_image( $id ) ) {
			return null;
		}

		return parent::get_value( $id );
	}

}