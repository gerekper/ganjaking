<?php

namespace ACP\Settings\Column;

use AC;

class CustomField extends AC\Settings\Column\CustomField {

	public function get_dependent_settings() {
		return [ new CustomFieldType( $this->column ) ];
	}

}