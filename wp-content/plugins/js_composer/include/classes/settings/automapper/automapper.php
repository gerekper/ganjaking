<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Helpers
if ( ! function_exists( 'vc_atm_build_categories_array' ) ) {
	/**
	 * @param $string
	 *
	 * @return array
	 */
	function vc_atm_build_categories_array( $string ) {
		return explode( ',', preg_replace( '/\,\s+/', ',', trim( $string ) ) );
	}
}
if ( ! function_exists( 'vc_atm_build_params_array' ) ) {
	/**
	 * @param $array
	 *
	 * @return array
	 */
	function vc_atm_build_params_array( $array ) {
		$params = array();
		if ( is_array( $array ) ) {
			foreach ( $array as $param ) {
				if ( 'dropdown' === $param['type'] ) {
					$param['value'] = explode( ',', preg_replace( '/\,\s+/', ',', trim( $param['value'] ) ) );
				}
				$param['save_always'] = true;
				$params[] = $param;
			}
		}

		return $params;
	}
}
