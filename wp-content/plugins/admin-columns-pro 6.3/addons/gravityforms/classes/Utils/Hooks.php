<?php

namespace ACA\GravityForms\Utils;

class Hooks {

	public static function get_load_form_entries() {
		global $page_hook;

		return strpos( $page_hook, '_page_gf_entries' ) !== false
			? 'load-' . $page_hook
			: 'load-forms_page_gf_entries';
	}

}