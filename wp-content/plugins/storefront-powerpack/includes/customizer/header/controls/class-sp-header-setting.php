<?php
/**
 * The Header setting
 *
 * This class incorporates code from the Kirki Customizer Framework.
 *
 * The Kirki Customizer Framework, Copyright Aristeides Stathopoulos (@aristath),
 * is licensed under the terms of the GNU GPL, Version 2 (or later).
 *
 * @link https://github.com/reduxframework/kirki/
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Header_Setting' ) ) {
	class SP_Header_Setting extends WP_Customize_Setting {

		public function __construct( $manager, $id, $args = array() ) {
			parent::__construct( $manager, $id, $args );

			// Will convert the setting from JSON to array. Must be triggered very soon
			add_filter( "customize_sanitize_{$this->id}", array( $this, 'sanitize_header_setting' ), 10, 1 );
		}

		public function value() {
			$value = parent::value();

			if ( ! is_array( $value ) ) {
				$value = array();
			}

			return $value;
		}

		/**
		 * Convert the JSON encoded setting coming from Customizer to an Array
		 *
		 * @param $value URL Encoded JSON Value
		 *
		 * @return array
		 */
		public function sanitize_header_setting( $value ) {
			if ( ! is_array( $value ) ) {
				$value = json_decode( urldecode( $value ), true );
			}

			$sanitized = ( empty( $value ) || ! is_array( $value ) ) ? array() : $value;

			// Make sure that every row is an array, not an object
			foreach ( $sanitized as $key => $_value ) {
				if ( empty( $_value ) ) {
					unset( $sanitized[ $key ] );
				} else {
					$sanitized[ $key ] = (array) $_value;
				}
			}

			return $sanitized;
		}
	}
}