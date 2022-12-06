<?php

namespace ACA\MetaBox\Column;

use AC;
use ACA\MetaBox\Column;
use ACA\MetaBox\Editing;
use ACA\MetaBox\Search;
use ACP;

class SingleImage extends Column implements ACP\Search\Searchable, ACP\Editing\Editable {

	public function format_single_value( $value, $id = null ) {
		if ( empty( $value ) ) {
			return $this->get_empty_char();
		}

		if ( ! isset( $value['ID'] ) ) {
			return $this->get_empty_char();
		}

		return $this->get_formatted_value( $value['ID'] );
	}

	public function is_multiple() {
		return false;
	}

	protected function register_settings() {
		parent::register_settings();
		$this->add_setting( new AC\Settings\Column\Image( $this ) );
	}

	public function search() {
		return ( new Search\Factory\Meta )->create( $this );
	}

	public function editing() {
		return ( new Editing\ServiceFactory\File )->create( $this );
	}

}