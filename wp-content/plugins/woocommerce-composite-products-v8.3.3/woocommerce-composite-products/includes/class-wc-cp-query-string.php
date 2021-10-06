<?php
/**
 * WC_CP_Query_String class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    6.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Compresses and expands query strings.
 *
 * @class    WC_CP_Query_String
 * @version  6.3.0
 */
class WC_CP_Query_String {

	/**
	 * Prefix for compressed component IDs.
	 *
	 * @var string
	 */
	protected static $compressed_component_id_prefix = 'c';

	/*
	 * Initilize.
	 */
	public static function init() {

		// Read compressed query string and populate $_GET and $_REQUEST variables.
		add_filter( 'wp_loaded', array( __CLASS__, 'maybe_expand_query_string' ), 0 );
	}

	/**
	 * Reads compressed query strings and populates $_GET and $_REQUEST variables.
	 *
	 * @return void
	 */
	public static function maybe_expand_query_string() {

		// The 'wccpl' variable must exist for parsing to be possible.
		if ( ! empty( $_GET[ 'wccpl' ] ) ) {

			$expanded_args = self::expand( $_GET );

			foreach ( $expanded_args as $arg_name => $arg_value ) {

				// Only populate globals if a form is not being posted.
				if ( empty( $_POST[ 'add-to-cart' ] ) ) {
					$_REQUEST[ $arg_name ] = $arg_value;
					$_GET[ $arg_name ]     = $arg_value;
				}
			}
		}
	}

	/**
	 * Compresses query string data.
	 *
	 * @since 6.3.0
	 *
	 * @param  array  $args
	 * @return array
	 */
	public static function compress( $args ) {

		$ref_names_map = array(
			'wccp_component_selection' => 'wccps',
			'wccp_component_quantity'  => 'wccpq',
			'wccp_variation_id'        => 'wccpv'
		);

		$compressed_args           = array();
		$compressed_component_id   = 0;
		$compressed_components_map = array();

		if ( ! empty( $args ) ) {
			foreach ( $args as $arg_name => $arg_data ) {

				$compressed_arg_name = false;

				if ( in_array( $arg_name, array_keys( $ref_names_map ) ) ) {
					$compressed_arg_name = $ref_names_map[ $arg_name ];
				} elseif ( 0 === strpos( $arg_name, 'wccp_' ) ) {
					$compressed_arg_name = $arg_name;
				}

				if ( $compressed_arg_name ) {

					foreach ( $arg_data as $component_id => $arg_value ) {

						if ( ! isset( $compressed_components_map[ $component_id ] ) ) {
							$compressed_components_map[ $component_id ] = $compressed_component_id;
							$compressed_component_id++;
						}

						// e.g. 'c10'
						$compressed_arg_name_suffix = self::$compressed_component_id_prefix . $compressed_components_map[ $component_id ];

						// e.g. 'wccps_c10'
						$compressed_args[ $compressed_arg_name . '_' . $compressed_arg_name_suffix ] = $arg_value;
					}

				} else {

					$compressed_args[ $arg_name ] = $arg_data;
				}
			}
		}

		if ( ! empty( $compressed_components_map ) ) {

			// Map compressed to expanded component IDs.
			foreach ( $compressed_components_map as $component_id => $compressed_component_id ) {
				$compressed_args[ 'wccpm' . $compressed_component_id ] = $component_id;
			}

			// Save map length.
			$compressed_args[ 'wccpl' ] = count( $compressed_components_map );
		}

		return $compressed_args;
	}

	/**
	 * Expands compressed query string data.
	 *
	 * @param  array  $compressed_args
	 * @param  bool   $remove_compressed
	 * @return array
	 */
	public static function expand( &$compressed_args, $remove_compressed = true ) {

		$expanded_args = array();
		$ref_names_map = array(
			'wccps' => 'wccp_component_selection',
			'wccpq' => 'wccp_component_quantity',
			'wccpv' => 'wccp_variation_id'
		);

		$map_length = $compressed_args[ 'wccpl' ];

		// First, decompress parameters with a known name.
		foreach ( $ref_names_map as $compressed_arg_ref_name => $expanded_arg_ref_name ) {
			for ( $i = 0; $i < $map_length; $i++ ) {

				$component_id = isset( $compressed_args[ 'wccpm' . $i ] ) ? $compressed_args[ 'wccpm' . $i ] : false;

				if ( ! $component_id ) {
					continue;
				}

				$compressed_arg_name = $compressed_arg_ref_name . '_' . self::$compressed_component_id_prefix . $i;
				$arg_value           = isset( $compressed_args[ $compressed_arg_name ] ) ? $compressed_args[ $compressed_arg_name ] : null;

				if ( null !== $arg_value ) {

					$expanded_args[ $expanded_arg_ref_name ][ $component_id ] = $arg_value;

					if ( $remove_compressed ) {
						unset( $compressed_args[ $compressed_arg_name ] );
					}
				}
			}
		}

		// Then, decompress taxonomy parameters. These will always start with 'wccp_' and end with a compressed component ID, like '_c10'.
		foreach ( $compressed_args as $compressed_arg_name => $arg_value ) {

			// Note that all query string parameters starting with 'wccp_' are preserved.
			if ( 0 === strpos( $compressed_arg_name, 'wccp_' ) ) {

				for ( $i = 0; $i < $map_length; $i++ ) {

					$component_id = isset( $compressed_args[ 'wccpm' . $i ] ) ? $compressed_args[ 'wccpm' . $i ] : false;

					if ( ! $component_id ) {
						continue;
					}

					$compressed_arg_name_suffix        = '_' . self::$compressed_component_id_prefix . $i;
					$compressed_arg_name_suffix_length = strlen( $compressed_arg_name_suffix );

					if ( substr( $compressed_arg_name, - $compressed_arg_name_suffix_length ) === $compressed_arg_name_suffix ) {

						// e.g. if the compressed name is 'wccp_color_c10', just keep the 'wccp_color' part.
						$expanded_arg_name                                    = substr( $compressed_arg_name, 0, strlen( $compressed_arg_name ) - $compressed_arg_name_suffix_length );
						$expanded_args[ $expanded_arg_name ][ $component_id ] = $arg_value;

						if ( $remove_compressed ) {
							unset( $compressed_args[ $compressed_arg_name ] );
						}
					}
				}

			} elseif ( $remove_compressed && 0 === strpos( $compressed_arg_name, 'wccp' ) ) {
				unset( $compressed_args[ $compressed_arg_name ] );
			}
		}

		return $expanded_args;
	}
}

WC_CP_Query_String::init();
