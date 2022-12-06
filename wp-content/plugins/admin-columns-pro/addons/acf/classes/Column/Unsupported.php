<?php

namespace ACA\ACF\Column;

use ACA\ACF;
use ACA\ACF\Column;
use ACP\Export\Model\StrippedValue;

class Unsupported extends Column {

	public function get_value( $id ) {
		return (string) get_field( $this->get_meta_key(), $id );
	}

	public function export() {
		return new StrippedValue( $this );
	}

	protected function register_settings() {
		parent::register_settings();

		$this->add_setting( new ACF\Settings\Column\Unsupported( $this ) );
	}

}