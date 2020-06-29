<?php

namespace ACP\Editing\Settings;

use AC\Settings\Option;

class CustomField extends Option {

	public function __construct() {
		parent::__construct( 'custom_field_editable' );
	}

	/**
	 * @return bool
	 */
	public function is_enabled() {
		return '1' === $this->get();
	}

}