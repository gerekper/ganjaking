<?php

namespace WPML\TM\TranslationDashboard;

use WPML\API\Sanitize;
use WPML\Element\API\Languages;
use WPML\FP\Obj;

class FiltersStorage {
	/**
	 * @return array
	 */
	public static function get() {
		$result = [];

		if ( isset( $_COOKIE['wp-translation_dashboard_filter'] ) ) {
			parse_str( Sanitize::stringProp( 'wp-translation_dashboard_filter', $_COOKIE ), $result );
		}

		return $result;
	}

	/**
	 * @return string
	 */
	public static function getFromLanguage() {
		return Obj::propOr( Languages::getCurrentCode(), 'from_lang', self::get() );
	}
}
