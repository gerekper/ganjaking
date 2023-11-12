<?php
/**
 * UAEL Display Conditions feature.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\DisplayConditions\Conditions;

use Elementor\Controls_Manager;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Operating_System
 * contain all element of operating system condition
 *
 * @package UltimateElementor\Modules\DisplayConditions\Conditions
 */
class Operating_System extends Condition {

	/**
	 * Get Condition Key
	 *
	 * @since 1.32.0
	 * @return string|void
	 */
	public function get_key_name() {
		return 'operating_system';
	}

	/**
	 * Get Condition Title
	 *
	 * @since 1.32.0
	 * @return string|void
	 */
	public function get_title() {
		return __( 'Operating System', 'uael' );
	}

	/**
	 * Get Repeater Control Field Value
	 *
	 * @param array $condition return key's.
	 *
	 * @since 1.32.0
	 * @return array|void
	 */
	public function get_repeater_control( array $condition ) {
		return array(
			'label'       => $this->get_title(),
			'show_label'  => false,
			'type'        => Controls_Manager::SELECT2,
			'default'     => 'mac_os',
			'label_block' => true,
			'options'     => array(
				'windows'    => __( 'Windows', 'uael' ),
				'mac_os'     => __( 'Mac OS', 'uael' ),
				'linux'      => __( 'Linux', 'uael' ),
				'iphone'     => __( 'iPhone', 'uael' ),
				'android'    => __( 'Android', 'uael' ),
				'blackberry' => __( 'BlackBerry', 'uael' ),
				'open_bsd'   => __( 'OpenBSD', 'uael' ),
				'sun_os'     => __( 'SunOS', 'uael' ),
				'qnx'        => __( 'QNX', 'uael' ),
				'beos'       => __( 'BeOS', 'uael' ),
				'os2'        => __( 'OS/2', 'uael' ),
			),
			'multiple'    => true,
			'condition'   => $condition,
		);
	}

	/**
	 * Compare Condition value
	 *
	 * @param String $settings return settings.
	 * @param String $operator return relationship operator.
	 * @param String $value value.
	 * @return bool|void
	 * @since 1.32.0
	 */
	public function compare_value( $settings, $operator, $value ) {
		$match = '';
		$os    = array(
			'windows'    => '(Win16)|(Windows 95)|(Win95)|(Windows_95)|(Windows 98)|(Win98)|(Windows NT 5.0)|(Windows 2000)|(Windows NT 5.1)|(Windows XP)|(Windows NT 5.2)|(Windows NT 6.0)|(Windows Vista)|(Windows NT 6.1)|(Windows 7)|(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)|(Windows ME)',
			'mac_os'     => '(Mac_PowerPC)|(Macintosh)|(mac os x)',
			'linux'      => '(Linux)|(X11)',
			'iphone'     => 'iPhone',
			'android'    => '(Android)',
			'blackberry' => 'BlackBerry',
			'open_bsd'   => 'OpenBSD',
			'sun_os'     => 'SunOS',
			'qnx'        => 'QNX',
			'beos'       => 'BeOS',
			'os2'        => 'OS/2',
		);

		$is_invalid = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $key => $name ) {
				$match = isset( $_SERVER['HTTP_USER_AGENT'] ) ? preg_match( '/' . $os[ $name ] . '/i', sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) ) : ''; // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints___SERVER__HTTP_USER_AGENT__

				if ( $match ) {
					if ( 'not' !== $operator ) {
						return UAEL_Helper::display_conditions_compare( true, true, $operator );
					} else {
						$is_invalid = true;
					}
				}
			}
		}

		if ( 'not' === $operator ) {
			return UAEL_Helper::display_conditions_compare( $is_invalid, true, $operator );
		}

		return false;
	}
}
