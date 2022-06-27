<?php

namespace ACP\Editing\Service\Media\MetaData;

use ACP\Editing;
use ACP\Editing\Service;

class Audio extends Service\Media\MetaData {

	public function __construct( $sub_key ) {
		parent::__construct( new Editing\View\Text(), $sub_key );
	}

	public function get_value( $id ) {
		if ( ! wp_attachment_is( 'audio', $id ) ) {
			return null;
		}

		return parent::get_value( $id );
	}

}