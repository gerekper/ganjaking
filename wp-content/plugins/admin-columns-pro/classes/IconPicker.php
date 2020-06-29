<?php

namespace ACP;

use AC\Column;
use AC\Registrable;
use ACP\Settings\Column\Label;

class IconPicker implements Registrable {

	public function register() {
		add_action( 'ac/column/settings', [ $this, 'register_column_settings' ] );
	}

	/**
	 * Replace the default label setting with an pro version that includes an iconpicker
	 *
	 * @param Column $column
	 *
	 * @return void;
	 */
	public function register_column_settings( Column $column ) {
		// We overwrite the default label setting with a pro version
		$column->add_setting( new Label( $column ) );
	}

}