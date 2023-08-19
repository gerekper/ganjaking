<?php

namespace ACP\Editing\View;

use ACP\Editing\View;

class Video extends View {

	use AttachmentTypeTrait,
		MultipleTrait;

	public function __construct() {
		parent::__construct( 'media' );

		$this->set_attachment_type( 'video' );
	}

}