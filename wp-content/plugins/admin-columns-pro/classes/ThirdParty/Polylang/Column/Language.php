<?php

namespace ACP\ThirdParty\Polylang\Column;

use AC;
use ACP\ThirdParty\Polylang\ColumnGroup;

class Language extends AC\Column {

	const TYPE = 'polylang_flag_placeholder';

	public function __construct() {
		$this->set_type( self::TYPE )
		     ->set_label( __( 'Polylang Language', 'codepress-admin-columns' ) )
		     ->set_group( ColumnGroup::NAME );
	}

	public function get_value( $id ) {
		return $this->get_empty_char();
	}

	protected function register_settings() {
		parent::register_settings();

		$message = new AC\Settings\Column\Message( $this );
		$message->set_label( __( 'Instructions', 'codepress-admin-columns' ) );
		$message->set_message( __( 'This placeholder columns adds the Polylang language flags columns to the list page.', 'codepress-admin-column' ) );

		$this->add_setting( $message );
	}

}