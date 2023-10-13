<?php

namespace WCML\Utilities;

use wpdb;

class DB {

	/**
	 * Changes array of items into string of items, separated by comma and sql-escaped
	 *
	 * @see https://coderwall.com/p/zepnaw
	 * @see wpml_prepare_in
	 *
	 * @global wpdb $wpdb
	 *
	 * @param mixed|array $items  item(s) to be joined into string
	 * @param string      $format %s or %d
	 *
	 * @return string Items separated by comma and sql-escaped
	 */
	public static function prepareIn( $items, $format = '%s') {
		/** @var wpdb $wpdb */
		global $wpdb;

		$items    = (array) $items;
		$how_many = count( $items );
		if ( $how_many > 0 ) {
			$placeholders    = array_fill( 0, $how_many, $format );
			$prepared_format = implode( ',', $placeholders );
			$prepared_in     = $wpdb->prepare( $prepared_format, $items );
		} else {
			$prepared_in = '';
		}

		return $prepared_in;
	}
}
