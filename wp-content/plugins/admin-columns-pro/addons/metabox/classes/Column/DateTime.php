<?php

namespace ACA\MetaBox\Column;

use ACA\MetaBox\Editing\StorageFactory;
use ACP\Editing\Service;
use ACP\Editing\View;

class DateTime extends Date {

	public function get_saved_format() {
		$save_format = $this->get_field_setting( 'save_format' );

		if ( ! $save_format ) {
			$save_format = $this->is_timestamp() ? 'U' : 'Y-m-d H:i:s';
		}

		return $save_format;
	}

	public function editing() {
		if ( $this->is_clonable() ) {
			return false;
		}

		return new Service\DateTime(
			( new View\DateTime() )->set_clear_button( true ),
			( new StorageFactory() )->create( $this ),
			$this->get_saved_format()
		);
	}

}