<?php

class FUE_Transients {
	protected static $_wp_using_ext_object_cache_previous;

	/**
	 * Contract to use before working with transients to ensure they are stored
	 * in DB.
	 *
	 * We need to turn off the object cache temporarily while we deal with
	 * transients, as a workaround to a W3 Total Cache object caching bug.
	 */
	protected static function contract_before() {
		global $_wp_using_ext_object_cache;

		set_time_limit(0);

		self::$_wp_using_ext_object_cache_previous = $_wp_using_ext_object_cache;
		$_wp_using_ext_object_cache = false;
	}

	/**
	 * Contract to use after working with transients to return the previous
	 * value of object cache.
	 */
	protected static function contract_after() {
		global $_wp_using_ext_object_cache;

		$_wp_using_ext_object_cache = self::$_wp_using_ext_object_cache_previous;
	}

	/**
	 * Split large arrays into smaller pieces before storing them as wp transients
	 *
	 * @param string $name
	 * @param array  $data
	 * @param int    $expiration The time the transient is valid (in seconds)
	 * @param int    $length The maximum number of elements per piece
	 */
	public static function set_transient( $name, $data, $expiration = 0, $length = 1000 ) {
		self::contract_before();

		$suffix = 1;

		if ( !is_array($data) ) {
			// store the data in a single transient row
			set_transient( $name, $data );
			self::contract_after();
			return;
		}

		self::delete_transient( $name );

		if ( count( $data ) <= $length ) {
			set_transient( $name .'__1', $data );
		} else {
			do {
				$offset = ( $suffix * $length ) - $length;
				$piece = array_slice( $data, $offset, $length, true );

				if ( empty( $piece ) ) {
					break;
				}

				set_transient( $name .'__'. $suffix, $piece, $expiration );
				$suffix++;
			} while ( true );
		}

		self::contract_after();
	}

	/**
	 * Get the split transients and join them into one big array
	 *
	 * @param string $name
	 * @return mixed
	 */
	public static function get_transient( $name ) {
		self::contract_before();

		// backward compatibility: if the transient name
		// without a suffix exists, simply return the data
		if ( ($data = get_transient( $name )) !== false ) {
			self::contract_after();
			return $data;
		}

		$data   = array();
		$suffix = 1;

		do {
			$piece = get_transient( $name .'__'. $suffix );
			$suffix++;

			if ( $piece === false ) {
				break;
			}

			$data = array_merge( $data, $piece );
		} while ( true );

		self::contract_after();

		if ( empty( $data ) ) {
			return false;
		}

		return $data;
	}

	public static function delete_transient( $name ) {
		global $wpdb;
		self::contract_before();

		$suffix = 1;

		do {
			$key = '_transient_'. $name .'__'. $suffix;
			if ( $wpdb->get_var($wpdb->prepare(
					"SELECT COUNT(*)
					FROM {$wpdb->options}
					WHERE option_name = %s",
					$key
				)) == 0
			) {
				break;
			}

			delete_transient( $name .'__'. $suffix );
			$suffix++;
		} while ( true );

		self::contract_after();
	}

}
