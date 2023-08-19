<?php

namespace ACP\Settings\Column;

use AC\MetaType;

class UserCustomField extends CustomField {

	protected function get_post_type() {
		return false;
	}

	protected function get_meta_type() {
		return MetaType::USER;
	}

}