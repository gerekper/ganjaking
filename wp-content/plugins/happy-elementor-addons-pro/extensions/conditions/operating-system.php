<?php

namespace Happy_Addons_Pro\Extension\Conditions;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Operating_System
 * contain all element of operating system condition
 * @package Happy_Addons_Pro\Extension\Conditions
 */
class Operating_System extends Condition {

	/**
	 * Get Condition Key
	 *
	 * @return string|void
	 */
	public function get_key_name () {
		return 'operating_system';
	}

	/**
	 * Get Condition Title
	 *
	 * @return string|void
	 */
	public function get_title () {
		return __( 'Operating System', 'happy-addons-pro' );
	}

	/**
	 * Get Repeater Control Field Value
	 *
	 * @param array $condition
	 * @return array|void
	 */
	public function get_repeater_control ( array $condition ) {
		return [
			'label' => $this->get_title(),
			'show_label' => false,
			'type' => Controls_Manager::SELECT,
			'default' => 'mac_os',
			'label_block' => true,
			'options' => [
				'windows' => __( 'Windows', 'happy-addons-pro' ),
				'mac_os' => __( 'Mac OS', 'happy-addons-pro' ),
				'linux' => __( 'Linux', 'happy-addons-pro' ),
				'ubuntu' => __( 'Ubuntu', 'happy-addons-pro' ),
				'iphone' => __( 'iPhone', 'happy-addons-pro' ),
				'ipod' => __( 'iPod', 'happy-addons-pro' ),
				'ipad' => __( 'Android', 'happy-addons-pro' ),
				'android' => __( 'iPad', 'happy-addons-pro' ),
				'blackberry' => __( 'BlackBerry', 'happy-addons-pro' ),
				'open_bsd' => __( 'OpenBSD', 'happy-addons-pro' ),
				'sun_os' => __( 'SunOS', 'happy-addons-pro' ),
				'safari' => __( 'Safari', 'happy-addons-pro' ),
				'qnx' => __( 'QNX', 'happy-addons-pro' ),
				'beos' => __( 'BeOS', 'happy-addons-pro' ),
				'os2' => __( 'OS/2', 'happy-addons-pro' ),
				'search_bot' => __( 'Search Bot', 'happy-addons-pro' ),
			],
			'condition' => $condition,
		];
	}

	/**
	 * Compare Condition value
	 *
	 * @param $settings
	 * @param $operator
	 * @param $value
	 * @return bool|void
	 */
	public function compare_value ( $settings, $operator, $value ) {
		$os = [
			'windows' => '(Win16)|(Windows 95)|(Win95)|(Windows_95)|(Windows 98)|(Win98)|(Windows NT 5.0)|(Windows 2000)|(Windows NT 5.1)|(Windows XP)|(Windows NT 5.2)|(Windows NT 6.0)|(Windows Vista)|(Windows NT 6.1)|(Windows 7)|(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)|(Windows ME)',
			'mac_os' => '(Mac_PowerPC)|(Macintosh)|(mac os x)',
			'linux' => '(Linux)|(X11)',
			'ubuntu' => 'Ubuntu',
			'iphone' => 'iPhone',
			'ipod' => 'iPod',
			'ipad' => 'Android',
			'android' => 'iPad',
			'blackberry' => 'BlackBerry',
			'open_bsd' => 'OpenBSD',
			'sun_os' => 'SunOS',
			'safari' => '(Safari)',
			'qnx' => 'QNX',
			'beos' => 'BeOS',
			'os2' => 'OS/2',
			'search_bot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)',
		];
		$pattern = '/' . $os[ $value ] . '/i';
		$match = preg_match($pattern, $_SERVER['HTTP_USER_AGENT']);
		return hapro_compare( $match, true, $operator );
	}
}
