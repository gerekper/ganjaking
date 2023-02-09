<?php

namespace WooCommerce\Warranty;

defined( 'ABSPATH' ) || exit;

/**
 * Trait Warranty_Util
 *
 * A collection of helpful methods that can be used in
 * multiple classes to help keep things DRY
 *
 * @package WooCommerce\Warranty
 */
trait Warranty_Util {

	/**
	 * Builds a warranty array based on the passed loop args,
	 * loop warranty type and loop index.
	 *
	 * @param array      $loop_args the loop arguments.
	 * @param array      $loop_warranty_type the loop warranty_type.
	 * @param int|string $index the loop index.
	 *
	 * @return array
	 */
	public static function build_warranty_array_inside_loop( array $loop_args, array $loop_warranty_type, $index ) {
		$warranty_type = isset( $loop_warranty_type[ $index ] ) ? $loop_warranty_type[ $index ] : '';

		$args = array(
			'included_warranty_length'                  => '',
			'limited_warranty_length_value'             => '',
			'limited_warranty_length_duration'          => '',
			'addon_warranty_amount'                     => array(),
			'addon_warranty_length_value'               => array(),
			'addon_warranty_length_duration'            => array(),
			'addon_no_warranty'                         => 'no',
			'variable_included_warranty_length'         => array(),
			'variable_limited_warranty_length_value'    => array(),
			'variable_limited_warranty_length_duration' => array(),
		);

		foreach ( $args as $key => $value ) {
			// key name mismatch fix.
			$loop_key = ( false !== strpos( $key, 'addon_' ) ) ? str_replace( 'addon_', 'variable_addon_', $key ) : $key;

			if ( empty( $loop_args[ $loop_key ][ $index ] ) ) {
				continue;
			}

			$args[ $key ] = $loop_args[ $loop_key ][ $index ];
		}

		return self::build_warranty_array( $args, $warranty_type );
	}

	/**
	 * Builds a warranty array based on passed args
	 * and warranty type.
	 *
	 * @param array  $args Warranty data.
	 * @param string $warranty_type Warranty type.
	 *
	 * @return array
	 */
	public static function build_warranty_array( array $args, $warranty_type ) {
		$warranty = array();
		switch ( $warranty_type ) {
			case 'no_warranty':
				$warranty['type'] = $warranty_type;
				break;
			case 'included_warranty':
				$warranty['type'] = $warranty_type;
				// Parent Product.
				$warranty['length']   = ! empty( $args['included_warranty_length'] ) ? $args['included_warranty_length'] : '';
				$warranty['value']    = ! empty( $args['limited_warranty_length_value'] ) ? absint( $args['limited_warranty_length_value'] ) : '';
				$warranty['duration'] = ! empty( $args['limited_warranty_length_duration'] ) ? $args['limited_warranty_length_duration'] : '';
				// Variation.
				$warranty['length']   = empty( $warranty['length'] ) && ! empty( $args['variable_included_warranty_length'] ) ? $args['variable_included_warranty_length'] : $warranty['length'];
				$warranty['value']    = empty( $warranty['value'] ) && ! empty( $args['variable_limited_warranty_length_value'] ) ? absint( $args['variable_limited_warranty_length_value'] ) : $warranty['value'];
				$warranty['duration'] = empty( $warranty['duration'] ) && ! empty( $args['variable_limited_warranty_length_duration'] ) ? $args['variable_limited_warranty_length_duration'] : $warranty['duration'];
				break;
			case 'addon_warranty':
				$warranty['type']               = $warranty_type;
				$warranty['addons']             = self::build_warranty_addons_array( $args );
				$warranty['no_warranty_option'] = ! empty( $args['addon_no_warranty'] ) ? $args['addon_no_warranty'] : 'no';
				break;
		}

		return $warranty;
	}

	/**
	 * Loops through the passed data, validates it, sanitizes it
	 * and returns the final warranty addons array. This is only
	 * used for the addon_warranty warranty type.
	 *
	 * @param array $args Addon Warranty data.
	 *
	 * @return array
	 */
	public static function build_warranty_addons_array( array $args ) {
		$addons    = array();
		$amounts   = ! empty( $args['addon_warranty_amount'] ) ? $args['addon_warranty_amount'] : array();
		$values    = ! empty( $args['addon_warranty_length_value'] ) ? $args['addon_warranty_length_value'] : array();
		$durations = ! empty( $args['addon_warranty_length_duration'] ) ? $args['addon_warranty_length_duration'] : array();
		$items     = count( $amounts );

		for ( $x = 0; $x < $items; $x ++ ) {
			if ( empty( $amounts[ $x ] ) || empty( $values[ $x ] ) || empty( $durations[ $x ] ) ) {
				continue;
			}

			$addons[] = array(
				'amount'   => sanitize_text_field( $amounts[ $x ] ),
				'value'    => sanitize_text_field( $values[ $x ] ),
				'duration' => sanitize_text_field( $durations[ $x ] ),
			);
		}

		return $addons;
	}

	public static function post_get_field( $key, $default ) {
		$data = warranty_request_post_data();
		return ! empty( $data[ $key ] ) ? $data[ $key ] : $default;
	}

	public static function post_get_string( $key, $default = '' ) {
		$data = warranty_request_post_data();
		return ! empty( $data[ $key ] ) && is_string( $data[ $key ] ) ? $data[ $key ] : $default;
	}

	public static function post_field_equals( $key, $value ) {
		$data = warranty_request_post_data();
		return isset( $data[ $key ] ) && $data[ $key ] === $value ? true : false;
	}

	public static function post_is_empty( $key ) {
		$data = warranty_request_post_data();
		return empty( $data[ $key ] ) ? true : false;
	}

}
