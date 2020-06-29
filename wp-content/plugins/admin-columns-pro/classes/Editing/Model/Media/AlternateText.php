<?php

namespace ACP\Editing\Model\Media;

use AC\MetaType;
use ACP\Editing\Model;

class AlternateText extends Model {

	public function get_edit_value( $id ) {
		if ( ! wp_attachment_is_image( $id ) ) {
			return null;
		}

		return parent::get_edit_value( $id );
	}

	public function save( $id, $value ) {
		return (bool) update_metadata( MetaType::POST, $id, '_wp_attachment_image_alt', $value );
	}

}