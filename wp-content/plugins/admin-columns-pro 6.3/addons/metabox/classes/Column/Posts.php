<?php

namespace ACA\MetaBox\Column;

use AC;
use AC\Settings;
use ACA\MetaBox\Editing;
use ACP;

class Posts extends Post {

	public function format_single_value( $value, $id = null ) {
		$formatted_value = $this->get_formatted_value( new AC\Collection( $value ), $id );
		$setting_limit = $this->get_setting( 'number_of_items' );

		return ac_helper()->html->more( $formatted_value->all(), $setting_limit ? $setting_limit->get_value() : false );
	}

	protected function register_settings() {
		parent::register_settings();

		$this->add_setting( new Settings\Column\NumberOfItems( $this ) );
	}

	public function editing() {
		return $this->is_clonable()
			? false
			: new ACP\Editing\Service\Posts(
				( new ACP\Editing\View\AjaxSelect() )->set_clear_button( true )->set_multiple( true ),
				( new Editing\StorageFactory() )->create( $this, false ),
				new ACP\Editing\PaginatedOptions\Posts( (array) $this->get_field_setting( 'post_type' ), $this->get_field_setting( 'query_args' ) )
			);
	}

}