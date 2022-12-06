<?php

namespace ACA\EC\ImportListscreens;

final class ImportedSetting {

	const IMPORTED_KEY = 'aca_ec_layouts_imported';

	public function is_imported() {
		return (bool) get_option( self::IMPORTED_KEY );
	}

	public function mark_as_imported() {
		return update_option( self::IMPORTED_KEY, true );
	}

	public function delete() {
		delete_option( self::IMPORTED_KEY );
	}

}