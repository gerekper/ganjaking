<?php
	
	namespace ElementPack\Modules\VisibilityControls\Conditions;
	
	use ElementPack\Base\Condition;
	use Elementor\Controls_Manager;
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	
	class Os extends Condition {
		
		/**
		 * Get the name of condition
		 * @return string as per our condition control name
		 * @since  5.3.0
		 */
		public function get_name() {
			return 'os';
		}
		
		/**
		 * Get the title of condition
		 * @return string as per condition control title
		 * @since  5.3.0
		 */
		public function get_title() {
			return esc_html__( 'Operating System', 'bdthemes-element-pack' );
		}

		/**
		 * Get the group of condition
		 * @return string as per our condition control name
		 * @since  6.11.3
		 */
		public function get_group() {
			return 'system';
		}
		
		/**
		 * Get the control value
		 * @return array as per condition control value
		 * @since  5.3.0
		 */
		public function get_control_value() {
			return [
				'type'        => Controls_Manager::SELECT,
				'default'     => array_keys( $this->get_os_options() )[0],
				'label_block' => true,
				'options'     => $this->get_os_options(),
			];
		}
		
		/**
		 * Get the operating system
		 * @return array of different types of operating system
		 * @since  5.3.0
		 */
		protected function get_os_options() {
			return [
				'iphone'   => 'iPhone',
				'android'  => 'Android',
				'windows'  => 'Windows',
				'open_bsd' => 'OpenBSD',
				'sun_os'   => 'SunOS',
				'linux'    => 'Linux',
				'mac_os'   => 'Mac OS',
			];
		}
		
		/**
		 * Check the condition
		 *
		 * @param string $relation Comparison operator for compare function
		 * @param mixed $val will check the control value as per condition needs
		 *
		 * @since 5.3.0
		 */
		public function check( $relation, $val ) {
			$oses = [
				'iphone'   => '(iPhone)',
				'android'  => '(Android)',
				'windows'  => 'Win16|(Windows 95)|(Win95)|(Windows_95)|(Windows 98)|(Win98)|(Windows NT 5.0)|(Windows 2000)|(Windows NT 5.1)|(Windows XP)|(Windows NT 5.2)|(Windows NT 6.0)|(Windows Vista)|(Windows NT 6.1)|(Windows 7)|(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)|Windows ME',
				'open_bsd' => 'OpenBSD',
				'sun_os'   => 'SunOS',
				'linux'    => '(Linux)|(X11)',
				'mac_os'   => '(Mac_PowerPC)|(Macintosh)',
			];
			
			return $this->compare( preg_match( '@' . $oses[ $val ] . '@', $_SERVER['HTTP_USER_AGENT'] ), true, $relation );
		}
	}
