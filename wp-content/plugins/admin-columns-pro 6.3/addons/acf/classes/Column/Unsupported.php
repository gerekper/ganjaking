<?php

namespace ACA\ACF\Column;

use ACA\ACF;
use ACA\ACF\Column;
use ACP\Export\Model\StrippedValue;

class Unsupported extends Column {

	public function get_value( $id ) {
		$value = get_field( $this->get_meta_key(), $id );

		return is_scalar( $value ) ? (string) $value : __( 'Unsupported value format', 'codepress-admin-columns' );
	}

	public function export() {
		return new StrippedValue( $this );
	}

	protected function register_settings() {
		parent::register_settings();

		$this->add_setting( new ACF\Settings\Column\Unsupported( $this ) );
	}

}