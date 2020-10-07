<?php

namespace ACP\Column\NetworkSite;

use AC;
use ACP\Settings;

class Options extends Option {

	public function __construct() {
		$this->set_type( 'column-msite_options' );
		$this->set_label( __( 'Options', 'codepress-admin-columns' ) );
	}

	public function get_value( $id ) {
		$value = parent::get_value( $id );

		if ( ! $value ) {
			return $this->get_empty_char();
		}

		return $this->get_formatted_value( $value );
	}

	public function get_option_name() {
		return $this->get_setting( 'field' )->get_value();
	}

	public function register_settings() {
		$this->add_setting( new Settings\Column\NetworkSite\Options( $this ) );
		$this->add_setting( new AC\Settings\Column\BeforeAfter( $this ) );
	}

}