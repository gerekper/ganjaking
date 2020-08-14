<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YLC_User' ) ) {

	class YLC_User {

		/**
		 * @var string user agent
		 */
		private $agent;

		/**
		 * @var  array user info
		 */
		private $info = array();

		/**
		 * @var string user id
		 */
		public $ID = null;

		/**
		 * @var string displayed name
		 */
		public $display_name = '';

		/**
		 * @var string user email
		 */
		public $user_email = '';

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 *
		 * @param $user_id      string
		 * @param $display_name string
		 * @param $user_email   string
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct( $user_id, $display_name, $user_email ) {

			$this->ID           = $user_id;
			$this->display_name = $display_name;
			$this->user_email   = $user_email;

			$this->agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : null;
			$this->get_browser();
			$this->get_OS();
			$this->get_ip_address();
			$this->get_current_page_url();

		}

		/**
		 * Get browser info
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		private function get_browser() {

			$this->info['browser'] = 'N/A';
			$this->info['version'] = 'N/A';

			$browser = array(
				'Navigator'         => '/Navigator(.*)/i',
				'Firefox'           => '/Firefox(.*)/i',
				'Internet Explorer' => '/MSIE(.*)/i',
				'Chrome'            => '/chrome(.*)/i',
				'MAXTHON'           => '/MAXTHON(.*)/i',
				'Opera'             => '/Opera(.*)/i',
			);

			foreach ( $browser as $key => $value ) {

				if ( preg_match( $value, $this->agent ) ) {
					$this->info['browser'] = $key;
					$this->info['version'] = $this->get_version( $key, $value, $this->agent );
					break;

				}

			}

		}

		/**
		 * Get OS info
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		private function get_OS() {

			$this->info['os'] = 'N/A';

			$OS = array(

				'/windows nt 10.0/i'    => 'Windows 10',
				'/windows nt 6.3/i'     => 'Windows 8.1',
				'/windows nt 6.2/i'     => 'Windows 8',
				'/windows nt 6.1/i'     => 'Windows 7',
				'/windows nt 6.0/i'     => 'Windows Vista',
				'/windows nt 5.2/i'     => 'Windows Server 2003/XP x64',
				'/windows nt 5.1/i'     => 'Windows XP',
				'/windows xp/i'         => 'Windows XP',
				'/windows nt 5.0/i'     => 'Windows 2000',
				'/windows me/i'         => 'Windows ME',
				'/win98/i'              => 'Windows 98',
				'/win95/i'              => 'Windows 95',
				'/win16/i'              => 'Windows 3.11',
				'/macintosh|mac os x/i' => 'Mac OS X',
				'/mac_powerpc/i'        => 'Mac OS 9',
				'/linux/i'              => 'Linux',
				'/ubuntu/i'             => 'Ubuntu',
				'/iphone/i'             => 'iPhone',
				'/ipod/i'               => 'iPod',
				'/ipad/i'               => 'iPad',
				'/android/i'            => 'Android',
				'/blackberry/i'         => 'BlackBerry',
				'/webos/i'              => 'Mobile'

			);

			foreach ( $OS as $regex => $v ) {

				if ( preg_match( $regex, $this->agent ) && $this->info['os'] == 'N/A' ) {
					$this->info['os'] = $v;
				}

			}

		}

		/**
		 * Get version
		 *
		 * @since   1.0.0
		 *
		 * @param $browser string
		 * @param $search  string
		 * @param $agent   string
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		private function get_version( $browser, $search, $agent ) {

			$version = '';
			$browser = strtolower( $browser );
			preg_match_all( $search, $agent, $match );

			switch ( $browser ) {
				case "firefox":
					$version = str_replace( "/", "", $match[1][0] );
					break;

				case "internet explorer":
					$version = substr( $match[1][0], 0, 4 );
					break;

				case "opera":
					$version = str_replace( "/", "", substr( $match[1][0], 0, 5 ) );
					break;

				case "navigator":
					$version = substr( $match[1][0], 1, 7 );
					break;

				case "maxthon":
					$version = str_replace( ")", "", $match[1][0] );
					break;

				case "chrome":
					$version = substr( $match[1][0], 1, 10 );
			}

			return $version;
		}

		/**
		 * Get user IP address
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto ruggiero
		 */
		private function get_ip_address() {

			if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
				$ip_addr = $_SERVER['HTTP_CLIENT_IP'];
			} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
				$ip_addr = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip_addr = $_SERVER['REMOTE_ADDR'];
			}

			if ( $ip_addr === false ) {
				$this->info['ip'] = '0.0.0.0';
			}

			if ( strpos( $ip_addr, ',' ) !== false ) {
				$x       = explode( ',', $ip_addr );
				$ip_addr = trim( end( $x ) );
			}

			if ( ! $this->validate_ip( $ip_addr ) ) {
				$this->info['ip'] = '0.0.0.0';
			} else {
				$this->info['ip'] = $ip_addr;
			}

		}

		/**
		 * Validate IP Address
		 *
		 * @since   1.0.0
		 *
		 * @param   $ip    string
		 * @param   $which string
		 *
		 * @return  boolean
		 * @author  Alberto Ruggiero
		 */
		private function validate_ip( $ip, $which = '' ) {

			$which = strtolower( $which );

			// First check if filter_var is available
			if ( is_callable( 'filter_var' ) ) {
				switch ( $which ) {
					case 'ipv4':
						$flag = FILTER_FLAG_IPV4;
						break;

					case 'ipv6':
						$flag = FILTER_FLAG_IPV6;
						break;

					default:
						$flag = '';
						break;
				}

				return ( bool ) filter_var( $ip, FILTER_VALIDATE_IP, $flag );
			}
			if ( $which !== 'ipv6' && $which !== 'ipv4' ) {
				if ( strpos( $ip, ':' ) !== false ) {
					$which = 'ipv6';
				} elseif ( strpos( $ip, '.' ) !== false ) {
					$which = 'ipv4';
				} else {
					return false;
				}
			}

			return call_user_func( array( $this, 'validate_' . $which ), $ip );

		}

		/**
		 * Validate IPv4 Address
		 *
		 * @since   1.0.0
		 *
		 * @param   $ip string
		 *
		 * @return  boolean
		 * @author  Alberto Ruggiero
		 */
		private function validate_ipv4( $ip ) {

			$ip_segments = explode( '.', $ip );

			// Always 4 segments needed
			if ( count( $ip_segments ) !== 4 ) {
				return false;
			}
			// IP can not start with 0
			if ( $ip_segments[0][0] == '0' ) {
				return false;
			}

			// Check each segment
			foreach ( $ip_segments as $segment ) {
				// IP segments must be digits and can not be longer than 3 digits or greater then 255
				if ( $segment == '' || preg_match( "/[^0-9]/", $segment ) || $segment > 255 || strlen( $segment ) > 3 ) {
					return false;
				}
			}

			return true;
		}

		/**
		 * Validate IPv6 Address
		 *
		 * @since   1.0.0
		 *
		 * @param   $ip string
		 *
		 * @return  boolean
		 * @author  Alberto Ruggiero
		 */
		private function validate_ipv6( $ip ) {

			// 8 groups, separated by : 0-ffff per group one set of consecutive 0 groups can be collapsed to ::
			$groups    = 8;
			$collapsed = false;
			$chunks    = array_filter( preg_split( '/(:{1,2})/', $ip, null, PREG_SPLIT_DELIM_CAPTURE ) );

			// Rule out easy nonsense
			if ( current( $chunks ) == ':' || end( $chunks ) == ':' ) {
				return false;
			}

			// PHP supports IPv4-mapped IPv6 addresses, so we'll expect those as well
			if ( strpos( end( $chunks ), '.' ) !== false ) {
				$ipv4 = array_pop( $chunks );
				if ( ! ylc_validate_ipv4( $ipv4 ) ) {
					return false;
				}
				$groups --;
			}

			while ( $seg = array_pop( $chunks ) ) {
				if ( $seg[0] == ':' ) {
					if ( -- $groups == 0 ) {
						return false; // too many groups
					}
					if ( strlen( $seg ) > 2 ) {
						return false; // long separator
					}
					if ( $seg == '::' ) {
						if ( $collapsed ) {
							return false; // multiple collapsed
						}
						$collapsed = true;
					}
				} elseif ( preg_match( "/[^0-9a-f]/i", $seg ) || strlen( $seg ) > 4 ) {
					return false; // invalid segment
				}
			}

			return $collapsed || $groups == 1;
		}

		/**
		 * Get the user current page URL
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function get_current_page_url() {

			$page_URL = 'http';

			if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) {
				$page_URL .= "s";
			}

			$page_URL .= '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

			$this->info['current_page'] = $page_URL;

		}

		/**
		 * Show user info
		 *
		 * @since   1.0.0
		 *
		 * @param $info string
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_info( $info ) {

			$info = strtolower( $info );

			switch ( $info ) {
				case 'browser':
				case 'os':
				case 'version':
				case 'ip':
				case 'current_page':
					$data = $this->info[ $info ];
					break;

				default:
					$data = 'N/A';
					break;

			}

			return $data;

		}

	}

}