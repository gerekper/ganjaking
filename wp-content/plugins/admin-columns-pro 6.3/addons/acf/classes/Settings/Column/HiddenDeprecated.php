<?php

namespace ACA\ACF\Settings\Column;

use AC;
use AC\View;

class HiddenDeprecated extends AC\Settings\Column {

	private $value;

	public function __construct( AC\Column $column, $setting_name ) {
		$this->name = $setting_name;
		parent::__construct( $column );

	}

	protected function define_options() {
		return [ $this->name ];
	}

	public function create_view() {
		return new View( [
			'class'   => '-hidden',
			'label'   => $this->name,
			'setting' => $this->create_element( 'text' ),
		] );
	}

	public function get_value( $option = null ) {
		return $this->value;
	}

	public function set_values( array $values ) {
		if ( array_key_exists( $this->name, $values ) ) {
			$this->value = $values[ $this->name ];
		}
	}

}