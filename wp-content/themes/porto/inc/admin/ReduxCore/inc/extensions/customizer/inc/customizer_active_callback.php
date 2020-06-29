<?php
/**
 * Active callback used with the "required" argument in fields
 */

class Redux_Customizer_Active_Callback {

	public static function evaluate( $object ) {

		if ( ! isset( $object->setting->id ) ) {
			return true;
		}

		$show = true;
		global $reduxPortoSettings;
		$reduxFramework = $reduxPortoSettings->ReduxFramework;

		$field    = false;
		$field_id = str_replace( array( 'porto_settings[', ']' ), '', $object->setting->id );
		foreach ( $reduxFramework->sections as $section ) {
			if ( isset( $section['fields'] ) ) {
				foreach ( $section['fields'] as $f ) {
					if ( isset( $f['id'] ) && $field_id == $f['id'] ) {
						$field = $f;
						break 2;
					}
				}
			}
		}
		if ( ! $field ) {
			return true;
		}

		if ( isset( $field['required'] ) ) {

			$show = self::evaluate_requirement( $object, $field['required'] );
			if ( ! $show ) {
				return false;
			}
		}

		return true;
	}

	private static function evaluate_requirement( $object, $requirement ) {

		// Look for comparison array.
		if ( is_array( $requirement ) && count( $requirement ) === 3 ) {

			// $current_setting = $object->manager->get_setting( 'porto_settings['. $requirement[0] . ']' );
			global $porto_settings;

			// if ( method_exists( $current_setting, 'value' ) ) {
				return self::compare( $requirement[2], $porto_settings[ $requirement[0] ], $requirement[1] );
			// }
		}

		return true;
	}

	public static function compare( $value1, $value2, $operator ) {
		switch ( $operator ) {
			case '===':
				$show = ( $value1 === $value2 ) ? true : false;
				break;
			case '==':
			case '=':
			case 'equals':
			case 'equal':
				if ( is_array( $value1 ) ) {
					$show = in_array( $value2, $value1 );
				} else {
					$show = ( $value1 == $value2 ) ? true : false;
				}
				break;
			case '!==':
				$show = ( $value1 !== $value2 ) ? true : false;
				break;
			case '!=':
			case 'not equal':
				$show = ( $value1 != $value2 ) ? true : false;
				break;
			case '>=':
			case 'greater or equal':
			case 'equal or greater':
				$show = ( $value2 >= $value1 ) ? true : false;
				break;
			case '<=':
			case 'smaller or equal':
			case 'equal or smaller':
				$show = ( $value2 <= $value1 ) ? true : false;
				break;
			case '>':
			case 'greater':
				$show = ( $value2 > $value1 ) ? true : false;
				break;
			case '<':
			case 'smaller':
				$show = ( $value2 < $value1 ) ? true : false;
				break;
			case 'contains':
			case 'in':
				if ( is_array( $value1 ) && ! is_array( $value2 ) ) {
					$array  = $value1;
					$string = $value2;
				} elseif ( is_array( $value2 ) && ! is_array( $value1 ) ) {
					$array  = $value2;
					$string = $value1;
				}
				if ( isset( $array ) && isset( $string ) ) {
					if ( ! in_array( $string, $array, true ) ) {
						$show = false;
					}
				} else {
					if ( false === strrpos( $value1, $value2 ) && false === strpos( $value2, $value1 ) ) {
						$show = false;
					}
				}
				break;
			default:
				$show = ( $value1 == $value2 ) ? true : false;

		} // End switch().

		if ( isset( $show ) ) {
			return $show;
		}

		return true;
	}
}
