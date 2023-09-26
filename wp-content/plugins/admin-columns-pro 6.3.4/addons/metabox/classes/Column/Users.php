<?php

namespace ACA\MetaBox\Column;

use AC;
use ACA\MetaBox\Editing;
use ACP;

class Users extends User {

	public function format_single_value( $value, $id = null ) {
		if ( ! $value ) {
			return $this->get_empty_char();
		}

		$formatted_value = $this->get_formatted_value( new AC\Collection( $value ), $id );
		$setting_limit = $this->get_setting( 'number_of_items' );

		return ac_helper()->html->more( $formatted_value->all(), $setting_limit ? $setting_limit->get_value() : false );
	}

	protected function register_settings() {
		parent::register_settings();

		$this->add_setting( new AC\Settings\Column\NumberOfItems( $this ) );
	}

	public function editing() {
		if ( $this->is_clonable() ) {
			return false;
		}

		return new ACP\Editing\Service\Users(
			( new ACP\Editing\View\AjaxSelect() )->set_multiple( true )->set_clear_button( true ),
			( new Editing\StorageFactory() )->create( $this, false ),
			new ACP\Editing\PaginatedOptions\Users( $this->get_field_setting( 'query_args' ) )
		);
	}

	public function is_multiple() {
		return true;
	}

}