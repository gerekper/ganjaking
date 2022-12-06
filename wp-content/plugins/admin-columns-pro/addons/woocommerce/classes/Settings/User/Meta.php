<?php

namespace ACA\WC\Settings\User;

use AC;

class Meta extends AC\Settings\Column\CustomField {

	protected function get_post_type() {
		return false;
	}

	protected function get_meta_type() {
		return 'user';
	}

}