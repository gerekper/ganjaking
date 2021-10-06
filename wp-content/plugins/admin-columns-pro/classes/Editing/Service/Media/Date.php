<?php

namespace ACP\Editing\Service\Media;

use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Date extends Service\DateTime {

	public function __construct() {
		parent::__construct(
			new View\DateTime(),
			new Storage\Post\Date()
		);
	}

	public function get_value( $id ) {
		if ( $this->is_unsupported_post_status( $id ) ) {
			return null;
		}

		return parent::get_value( $id );
	}

	private function is_unsupported_post_status( $id ) {
		return 'draft' === get_post( $id )->post_status;
	}

}