<?php

namespace ACP\Search\Comparison\Meta;

use ACP\Search\Comparison;

class DateFactory {

	const FORMAT_UNIX_TIMESTAMP = 'U';
	const FORMAT_DATETIME = 'Y-m-d H:i:s';
	const FORMAT_DATE = 'Y-m-d';

	/**
	 * @param string $date_format
	 * @param string $meta_key
	 * @param string $meta_type
	 *
	 * @return Comparison
	 */
	public static function create( $date_format, $meta_key, $meta_type ) {
		switch ( $date_format ) {
			case self::FORMAT_UNIX_TIMESTAMP :
				return new DateTime\Timestamp( $meta_key, $meta_type );
			case self::FORMAT_DATETIME :
			case self::FORMAT_DATE :
				return new DateTime\ISO( $meta_key, $meta_type );
			default:
				return new Text( $meta_key, $meta_type );
		}
	}

}