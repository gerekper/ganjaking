<?php
namespace Happy_Addons_Pro\Extension\Conditions;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Day
 * contain all element of day condition
 * @package Happy_Addons_Pro\Extension\Conditions
 */
class Country  extends Condition {

	/**
	 * Get Condition Key
	 *
	 * @return string|void
	 */
	public function get_key_name() {
		return 'country';
	}

	/**
	 * Get Condition Title
	 *
	 * @return string|void
	 */
	public function get_title() {
		return __( 'Country', 'happy-addons-pro' );
	}

	/**
	 * Get Repeater Control Field Value
	 *
	 * @param array $condition
	 * @return array|void
	 */
	public function get_repeater_control( array $condition ) {
		return [
			'label'       => $this->get_title(),
			'show_label'  => false,
			'type'        => Controls_Manager::SELECT2,
			'default'     => ['BD'],
			'label_block' => true,
			'multiple'    => true,
			'options'     => $this->get_countries(),
			'condition'   => $condition,
		];
	}

	/**
	 * Get Countries
	 *
	 * @return array
	 */
	public function get_countries() {

		$countries = [
			'AF' => __( 'Afghanistan', 'happy-addons-pro' ),
			'AL' => __( 'Albania', 'happy-addons-pro' ),
			'DZ' => __( 'Algeria', 'happy-addons-pro' ),
			'AS' => __( 'American Samoa', 'happy-addons-pro' ),
			'AD' => __( 'Andorra', 'happy-addons-pro' ),
			'AO' => __( 'Angola', 'happy-addons-pro' ),
			'AI' => __( 'Anguilla', 'happy-addons-pro' ),
			'AQ' => __( 'Antarctica', 'happy-addons-pro' ),
			'AG' => __( 'Antigua and Barbuda', 'happy-addons-pro' ),
			'AR' => __( 'Argentina', 'happy-addons-pro' ),
			'AM' => __( 'Armenia', 'happy-addons-pro' ),
			'AW' => __( 'Aruba', 'happy-addons-pro' ),
			'AU' => __( 'Australia', 'happy-addons-pro' ),
			'AT' => __( 'Austria', 'happy-addons-pro' ),
			'AZ' => __( 'Azerbaijan', 'happy-addons-pro' ),
			'BS' => __( 'Bahamas', 'happy-addons-pro' ),
			'BH' => __( 'Bahrain', 'happy-addons-pro' ),
			'BD' => __( 'Bangladesh', 'happy-addons-pro' ),
			'BB' => __( 'Barbados', 'happy-addons-pro' ),
			'BY' => __( 'Belarus', 'happy-addons-pro' ),
			'BE' => __( 'Belgium', 'happy-addons-pro' ),
			'BZ' => __( 'Belize', 'happy-addons-pro' ),
			'BJ' => __( 'Benin', 'happy-addons-pro' ),
			'BM' => __( 'Bermuda', 'happy-addons-pro' ),
			'BT' => __( 'Bhutan', 'happy-addons-pro' ),
			'BO' => __( 'Bolivia', 'happy-addons-pro' ),
			'BA' => __( 'Bosnia and Herzegovina', 'happy-addons-pro' ),
			'BW' => __( 'Botswana', 'happy-addons-pro' ),
			'BV' => __( 'Bouvet Island', 'happy-addons-pro' ),
			'BR' => __( 'Brazil', 'happy-addons-pro' ),
			'BQ' => __( 'British Antarctic Territory', 'happy-addons-pro' ),
			'IO' => __( 'British Indian Ocean Territory', 'happy-addons-pro' ),
			'VG' => __( 'British Virgin Islands', 'happy-addons-pro' ),
			'BN' => __( 'Brunei', 'happy-addons-pro' ),
			'BG' => __( 'Bulgaria', 'happy-addons-pro' ),
			'BF' => __( 'Burkina Faso', 'happy-addons-pro' ),
			'BI' => __( 'Burundi', 'happy-addons-pro' ),
			'KH' => __( 'Cambodia', 'happy-addons-pro' ),
			'CM' => __( 'Cameroon', 'happy-addons-pro' ),
			'CA' => __( 'Canada', 'happy-addons-pro' ),
			'CT' => __( 'Canton and Enderbury Islands', 'happy-addons-pro' ),
			'CV' => __( 'Cape Verde', 'happy-addons-pro' ),
			'KY' => __( 'Cayman Islands', 'happy-addons-pro' ),
			'CF' => __( 'Central African Republic', 'happy-addons-pro' ),
			'TD' => __( 'Chad', 'happy-addons-pro' ),
			'CL' => __( 'Chile', 'happy-addons-pro' ),
			'CN' => __( 'China', 'happy-addons-pro' ),
			'CX' => __( 'Christmas Island', 'happy-addons-pro' ),
			'CC' => __( 'Cocos [Keeling] Islands', 'happy-addons-pro' ),
			'CO' => __( 'Colombia', 'happy-addons-pro' ),
			'KM' => __( 'Comoros', 'happy-addons-pro' ),
			'CG' => __( 'Congo - Brazzaville', 'happy-addons-pro' ),
			'CD' => __( 'Congo - Kinshasa', 'happy-addons-pro' ),
			'CK' => __( 'Cook Islands', 'happy-addons-pro' ),
			'CR' => __( 'Costa Rica', 'happy-addons-pro' ),
			'HR' => __( 'Croatia', 'happy-addons-pro' ),
			'CU' => __( 'Cuba', 'happy-addons-pro' ),
			'CY' => __( 'Cyprus', 'happy-addons-pro' ),
			'CZ' => __( 'Czech Republic', 'happy-addons-pro' ),
			'CI' => __( 'Côte d’Ivoire', 'happy-addons-pro' ),
			'DK' => __( 'Denmark', 'happy-addons-pro' ),
			'DJ' => __( 'Djibouti', 'happy-addons-pro' ),
			'DM' => __( 'Dominica', 'happy-addons-pro' ),
			'DO' => __( 'Dominican Republic', 'happy-addons-pro' ),
			'NQ' => __( 'Dronning Maud Land', 'happy-addons-pro' ),
			'DD' => __( 'East Germany', 'happy-addons-pro' ),
			'EC' => __( 'Ecuador', 'happy-addons-pro' ),
			'EG' => __( 'Egypt', 'happy-addons-pro' ),
			'SV' => __( 'El Salvador', 'happy-addons-pro' ),
			'GQ' => __( 'Equatorial Guinea', 'happy-addons-pro' ),
			'ER' => __( 'Eritrea', 'happy-addons-pro' ),
			'EE' => __( 'Estonia', 'happy-addons-pro' ),
			'ET' => __( 'Ethiopia', 'happy-addons-pro' ),
			'FK' => __( 'Falkland Islands', 'happy-addons-pro' ),
			'FO' => __( 'Faroe Islands', 'happy-addons-pro' ),
			'FJ' => __( 'Fiji', 'happy-addons-pro' ),
			'FI' => __( 'Finland', 'happy-addons-pro' ),
			'FR' => __( 'France', 'happy-addons-pro' ),
			'GF' => __( 'French Guiana', 'happy-addons-pro' ),
			'PF' => __( 'French Polynesia', 'happy-addons-pro' ),
			'TF' => __( 'French Southern Territories', 'happy-addons-pro' ),
			'FQ' => __( 'French Southern and Antarctic Territories', 'happy-addons-pro' ),
			'GA' => __( 'Gabon', 'happy-addons-pro' ),
			'GM' => __( 'Gambia', 'happy-addons-pro' ),
			'GE' => __( 'Georgia', 'happy-addons-pro' ),
			'DE' => __( 'Germany', 'happy-addons-pro' ),
			'GH' => __( 'Ghana', 'happy-addons-pro' ),
			'GI' => __( 'Gibraltar', 'happy-addons-pro' ),
			'GR' => __( 'Greece', 'happy-addons-pro' ),
			'GL' => __( 'Greenland', 'happy-addons-pro' ),
			'GD' => __( 'Grenada', 'happy-addons-pro' ),
			'GP' => __( 'Guadeloupe', 'happy-addons-pro' ),
			'GU' => __( 'Guam', 'happy-addons-pro' ),
			'GT' => __( 'Guatemala', 'happy-addons-pro' ),
			'GG' => __( 'Guernsey', 'happy-addons-pro' ),
			'GN' => __( 'Guinea', 'happy-addons-pro' ),
			'GW' => __( 'Guinea-Bissau', 'happy-addons-pro' ),
			'GY' => __( 'Guyana', 'happy-addons-pro' ),
			'HT' => __( 'Haiti', 'happy-addons-pro' ),
			'HM' => __( 'Heard Island and McDonald Islands', 'happy-addons-pro' ),
			'HN' => __( 'Honduras', 'happy-addons-pro' ),
			'HK' => __( 'Hong Kong SAR China', 'happy-addons-pro' ),
			'HU' => __( 'Hungary', 'happy-addons-pro' ),
			'IS' => __( 'Iceland', 'happy-addons-pro' ),
			'IN' => __( 'India', 'happy-addons-pro' ),
			'ID' => __( 'Indonesia', 'happy-addons-pro' ),
			'IR' => __( 'Iran', 'happy-addons-pro' ),
			'IQ' => __( 'Iraq', 'happy-addons-pro' ),
			'IE' => __( 'Ireland', 'happy-addons-pro' ),
			'IM' => __( 'Isle of Man', 'happy-addons-pro' ),
			'IL' => __( 'Israel', 'happy-addons-pro' ),
			'IT' => __( 'Italy', 'happy-addons-pro' ),
			'JM' => __( 'Jamaica', 'happy-addons-pro' ),
			'JP' => __( 'Japan', 'happy-addons-pro' ),
			'JE' => __( 'Jersey', 'happy-addons-pro' ),
			'JT' => __( 'Johnston Island', 'happy-addons-pro' ),
			'JO' => __( 'Jordan', 'happy-addons-pro' ),
			'KZ' => __( 'Kazakhstan', 'happy-addons-pro' ),
			'KE' => __( 'Kenya', 'happy-addons-pro' ),
			'KI' => __( 'Kiribati', 'happy-addons-pro' ),
			'KW' => __( 'Kuwait', 'happy-addons-pro' ),
			'KG' => __( 'Kyrgyzstan', 'happy-addons-pro' ),
			'LA' => __( 'Laos', 'happy-addons-pro' ),
			'LV' => __( 'Latvia', 'happy-addons-pro' ),
			'LB' => __( 'Lebanon', 'happy-addons-pro' ),
			'LS' => __( 'Lesotho', 'happy-addons-pro' ),
			'LR' => __( 'Liberia', 'happy-addons-pro' ),
			'LY' => __( 'Libya', 'happy-addons-pro' ),
			'LI' => __( 'Liechtenstein', 'happy-addons-pro' ),
			'LT' => __( 'Lithuania', 'happy-addons-pro' ),
			'LU' => __( 'Luxembourg', 'happy-addons-pro' ),
			'MO' => __( 'Macau SAR China', 'happy-addons-pro' ),
			'MK' => __( 'Macedonia', 'happy-addons-pro' ),
			'MG' => __( 'Madagascar', 'happy-addons-pro' ),
			'MW' => __( 'Malawi', 'happy-addons-pro' ),
			'MY' => __( 'Malaysia', 'happy-addons-pro' ),
			'MV' => __( 'Maldives', 'happy-addons-pro' ),
			'ML' => __( 'Mali', 'happy-addons-pro' ),
			'MT' => __( 'Malta', 'happy-addons-pro' ),
			'MH' => __( 'Marshall Islands', 'happy-addons-pro' ),
			'MQ' => __( 'Martinique', 'happy-addons-pro' ),
			'MR' => __( 'Mauritania', 'happy-addons-pro' ),
			'MU' => __( 'Mauritius', 'happy-addons-pro' ),
			'YT' => __( 'Mayotte', 'happy-addons-pro' ),
			'FX' => __( 'Metropolitan France', 'happy-addons-pro' ),
			'MX' => __( 'Mexico', 'happy-addons-pro' ),
			'FM' => __( 'Micronesia', 'happy-addons-pro' ),
			'MI' => __( 'Midway Islands', 'happy-addons-pro' ),
			'MD' => __( 'Moldova', 'happy-addons-pro' ),
			'MC' => __( 'Monaco', 'happy-addons-pro' ),
			'MN' => __( 'Mongolia', 'happy-addons-pro' ),
			'ME' => __( 'Montenegro', 'happy-addons-pro' ),
			'MS' => __( 'Montserrat', 'happy-addons-pro' ),
			'MA' => __( 'Morocco', 'happy-addons-pro' ),
			'MZ' => __( 'Mozambique', 'happy-addons-pro' ),
			'MM' => __( 'Myanmar [Burma]', 'happy-addons-pro' ),
			'NA' => __( 'Namibia', 'happy-addons-pro' ),
			'NR' => __( 'Nauru', 'happy-addons-pro' ),
			'NP' => __( 'Nepal', 'happy-addons-pro' ),
			'NL' => __( 'Netherlands', 'happy-addons-pro' ),
			'AN' => __( 'Netherlands Antilles', 'happy-addons-pro' ),
			'NT' => __( 'Neutral Zone', 'happy-addons-pro' ),
			'NC' => __( 'New Caledonia', 'happy-addons-pro' ),
			'NZ' => __( 'New Zealand', 'happy-addons-pro' ),
			'NI' => __( 'Nicaragua', 'happy-addons-pro' ),
			'NE' => __( 'Niger', 'happy-addons-pro' ),
			'NG' => __( 'Nigeria', 'happy-addons-pro' ),
			'NU' => __( 'Niue', 'happy-addons-pro' ),
			'NF' => __( 'Norfolk Island', 'happy-addons-pro' ),
			'KP' => __( 'North Korea', 'happy-addons-pro' ),
			'VD' => __( 'North Vietnam', 'happy-addons-pro' ),
			'MP' => __( 'Northern Mariana Islands', 'happy-addons-pro' ),
			'NO' => __( 'Norway', 'happy-addons-pro' ),
			'OM' => __( 'Oman', 'happy-addons-pro' ),
			'PC' => __( 'Pacific Islands Trust Territory', 'happy-addons-pro' ),
			'PK' => __( 'Pakistan', 'happy-addons-pro' ),
			'PW' => __( 'Palau', 'happy-addons-pro' ),
			'PS' => __( 'Palestinian Territories', 'happy-addons-pro' ),
			'PA' => __( 'Panama', 'happy-addons-pro' ),
			'PZ' => __( 'Panama Canal Zone', 'happy-addons-pro' ),
			'PG' => __( 'Papua New Guinea', 'happy-addons-pro' ),
			'PY' => __( 'Paraguay', 'happy-addons-pro' ),
			'YD' => __( 'People\'s Democratic Republic of Yemen', 'happy-addons-pro' ),
			'PE' => __( 'Peru', 'happy-addons-pro' ),
			'PH' => __( 'Philippines', 'happy-addons-pro' ),
			'PN' => __( 'Pitcairn Islands', 'happy-addons-pro' ),
			'PL' => __( 'Poland', 'happy-addons-pro' ),
			'PT' => __( 'Portugal', 'happy-addons-pro' ),
			'PR' => __( 'Puerto Rico', 'happy-addons-pro' ),
			'QA' => __( 'Qatar', 'happy-addons-pro' ),
			'RO' => __( 'Romania', 'happy-addons-pro' ),
			'RU' => __( 'Russia', 'happy-addons-pro' ),
			'RW' => __( 'Rwanda', 'happy-addons-pro' ),
			'BL' => __( 'Saint Barthélemy', 'happy-addons-pro' ),
			'SH' => __( 'Saint Helena', 'happy-addons-pro' ),
			'KN' => __( 'Saint Kitts and Nevis', 'happy-addons-pro' ),
			'LC' => __( 'Saint Lucia', 'happy-addons-pro' ),
			'MF' => __( 'Saint Martin', 'happy-addons-pro' ),
			'PM' => __( 'Saint Pierre and Miquelon', 'happy-addons-pro' ),
			'VC' => __( 'Saint Vincent and the Grenadines', 'happy-addons-pro' ),
			'WS' => __( 'Samoa', 'happy-addons-pro' ),
			'SM' => __( 'San Marino', 'happy-addons-pro' ),
			'SA' => __( 'Saudi Arabia', 'happy-addons-pro' ),
			'SN' => __( 'Senegal', 'happy-addons-pro' ),
			'RS' => __( 'Serbia', 'happy-addons-pro' ),
			'CS' => __( 'Serbia and Montenegro', 'happy-addons-pro' ),
			'SC' => __( 'Seychelles', 'happy-addons-pro' ),
			'SL' => __( 'Sierra Leone', 'happy-addons-pro' ),
			'SG' => __( 'Singapore', 'happy-addons-pro' ),
			'SK' => __( 'Slovakia', 'happy-addons-pro' ),
			'SI' => __( 'Slovenia', 'happy-addons-pro' ),
			'SB' => __( 'Solomon Islands', 'happy-addons-pro' ),
			'SO' => __( 'Somalia', 'happy-addons-pro' ),
			'ZA' => __( 'South Africa', 'happy-addons-pro' ),
			'GS' => __( 'South Georgia and the South Sandwich Islands', 'happy-addons-pro' ),
			'KR' => __( 'South Korea', 'happy-addons-pro' ),
			'ES' => __( 'Spain', 'happy-addons-pro' ),
			'LK' => __( 'Sri Lanka', 'happy-addons-pro' ),
			'SD' => __( 'Sudan', 'happy-addons-pro' ),
			'SR' => __( 'Suriname', 'happy-addons-pro' ),
			'SJ' => __( 'Svalbard and Jan Mayen', 'happy-addons-pro' ),
			'SZ' => __( 'Swaziland', 'happy-addons-pro' ),
			'SE' => __( 'Sweden', 'happy-addons-pro' ),
			'CH' => __( 'Switzerland', 'happy-addons-pro' ),
			'SY' => __( 'Syria', 'happy-addons-pro' ),
			'ST' => __( 'São Tomé and Príncipe', 'happy-addons-pro' ),
			'TW' => __( 'Taiwan', 'happy-addons-pro' ),
			'TJ' => __( 'Tajikistan', 'happy-addons-pro' ),
			'TZ' => __( 'Tanzania', 'happy-addons-pro' ),
			'TH' => __( 'Thailand', 'happy-addons-pro' ),
			'TL' => __( 'Timor-Leste', 'happy-addons-pro' ),
			'TG' => __( 'Togo', 'happy-addons-pro' ),
			'TK' => __( 'Tokelau', 'happy-addons-pro' ),
			'TO' => __( 'Tonga', 'happy-addons-pro' ),
			'TT' => __( 'Trinidad and Tobago', 'happy-addons-pro' ),
			'TN' => __( 'Tunisia', 'happy-addons-pro' ),
			'TR' => __( 'Turkey', 'happy-addons-pro' ),
			'TM' => __( 'Turkmenistan', 'happy-addons-pro' ),
			'TC' => __( 'Turks and Caicos Islands', 'happy-addons-pro' ),
			'TV' => __( 'Tuvalu', 'happy-addons-pro' ),
			'UM' => __( 'U.S. Minor Outlying Islands', 'happy-addons-pro' ),
			'PU' => __( 'U.S. Miscellaneous Pacific Islands', 'happy-addons-pro' ),
			'VI' => __( 'U.S. Virgin Islands', 'happy-addons-pro' ),
			'UG' => __( 'Uganda', 'happy-addons-pro' ),
			'UA' => __( 'Ukraine', 'happy-addons-pro' ),
			'SU' => __( 'Union of Soviet Socialist Republics', 'happy-addons-pro' ),
			'AE' => __( 'United Arab Emirates', 'happy-addons-pro' ),
			'GB' => __( 'United Kingdom', 'happy-addons-pro' ),
			'US' => __( 'United States', 'happy-addons-pro' ),
			'ZZ' => __( 'Unknown or Invalid Region', 'happy-addons-pro' ),
			'UY' => __( 'Uruguay', 'happy-addons-pro' ),
			'UZ' => __( 'Uzbekistan', 'happy-addons-pro' ),
			'VU' => __( 'Vanuatu', 'happy-addons-pro' ),
			'VA' => __( 'Vatican City', 'happy-addons-pro' ),
			'VE' => __( 'Venezuela', 'happy-addons-pro' ),
			'VN' => __( 'Vietnam', 'happy-addons-pro' ),
			'WK' => __( 'Wake Island', 'happy-addons-pro' ),
			'WF' => __( 'Wallis and Futuna', 'happy-addons-pro' ),
			'EH' => __( 'Western Sahara', 'happy-addons-pro' ),
			'YE' => __( 'Yemen', 'happy-addons-pro' ),
			'ZM' => __( 'Zambia', 'happy-addons-pro' ),
			'ZW' => __( 'Zimbabwe', 'happy-addons-pro' ),
			'AX' => __( 'Åland Islands', 'happy-addons-pro' ),
		];

		return $countries;
	}

	/**
	 * Get Repeater Control Field Value
	 */
	public function get_client_ip() {
		$ipaddress = '';
		if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED'] ) ) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		} elseif ( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		} elseif ( isset( $_SERVER['HTTP_FORWARDED'] ) ) {
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		}

		if ( '::1' == $ipaddress ) {
			$ipaddress = '';
		}
		return $ipaddress;
	}

	/**
	 * Get Repeater Control Field Value
	 */
	public function get_user_location() {
		$country = isset( $_COOKIE['HappyIpDt'] ) && ! empty( $_COOKIE['HappyIpDt'] ) ? $_COOKIE['HappyIpDt'] : '';
		if ( empty( $country ) ) {
			$ip      = $this->get_client_ip();
			$url     = $ip ? "https://ipinfo.io/{$ip}/json" : 'https://ipinfo.io/json';
			$ipdat   = @json_decode( file_get_contents( $url ) );
			$country = isset( $ipdat->country ) ? $ipdat->country : '';

			wp_add_inline_script(
				'happy-addons-pro',
				'!function($){"use strict";
					if (typeof haDisplayCondition != "undefined" && haDisplayCondition.status == "true") {
						var now = new Date();
						var time = now.getTime();
						var expireTime = time + 1000*60;//one minute plus from now
						now.setTime(expireTime);
						// Set user time in cookie
						var ha_secure = document.location.protocol === "https:" ? "secure" : "";
						var ipdata = "' . $country . '";
						document.cookie = "HappyIpDt=" +  ipdata + ";expires="+now.toUTCString()+";SameSite=Strict;" + ha_secure;
					}
				}(jQuery);'
			);
		}
		return ( isset( $country ) ? $country : '' );
	}

	/**
	 * Compare Condition value
	 *
	 * @param $settings
	 * @param $operator
	 * @param $value
	 * @return bool|void
	 */
	public function compare_value( $settings, $operator, $value ) {
		$country = $this->get_user_location();
		switch ( $operator ) {
			case 'is':
				return in_array( $country, $value );
			case 'not':
				return ! in_array( $country, $value );
			default:
				return in_array( $country, $value );
		}
	}

}
