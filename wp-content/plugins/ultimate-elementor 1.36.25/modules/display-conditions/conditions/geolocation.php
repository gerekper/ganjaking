<?php
/**
 * UAEL Display Conditions feature.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\DisplayConditions\Conditions;

use Elementor\Controls_Manager;
use UltimateElementor\Classes\UAEL_Helper;
use UltimateElementor\Classes\UAEL_Geolocation;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Geolocation
 *
 * @package UltimateElementor\Modules\DisplayConditions\Conditions
 */
class Geolocation extends Condition {
	/**
	 * Get Condition Key
	 *
	 * @since 1.35.1
	 * @return string|void
	 */
	public function get_key_name() {
		return 'geolocation';
	}

	/**
	 * Get Condition Title
	 *
	 * @since 1.35.1
	 * @return string|void
	 */
	public function get_title() {
		return __( 'Geolocation', 'uael' );
	}

	/**
	 * All countries and their codes for geolocating.
	 *
	 * @return array all countries and it's code.
	 * @since 1.35.1
	 */
	public function uae_geo_all_countries() {
		return array(
			'AF' => __( 'Afghanistan', 'uael' ),
			'AX' => __( 'Aland Islands', 'uael' ),
			'AL' => __( 'Albania', 'uael' ),
			'DZ' => __( 'Algeria', 'uael' ),
			'AS' => __( 'American Samoa', 'uael' ),
			'AD' => __( 'Andorra', 'uael' ),
			'AO' => __( 'Angola', 'uael' ),
			'AI' => __( 'Anguilla', 'uael' ),
			'AQ' => __( 'Antarctica', 'uael' ),
			'AG' => __( 'Antigua and Barbuda', 'uael' ),
			'AR' => __( 'Argentina', 'uael' ),
			'AM' => __( 'Armenia', 'uael' ),
			'AW' => __( 'Aruba', 'uael' ),
			'AU' => __( 'Australia', 'uael' ),
			'AT' => __( 'Austria', 'uael' ),
			'AZ' => __( 'Azerbaijan', 'uael' ),
			'BS' => __( 'Bahamas', 'uael' ),
			'BH' => __( 'Bahrain', 'uael' ),
			'BD' => __( 'Bangladesh', 'uael' ),
			'BB' => __( 'Barbados', 'uael' ),
			'BY' => __( 'Belarus', 'uael' ),
			'BE' => __( 'Belgium', 'uael' ),
			'PW' => __( 'Belau', 'uael' ),
			'BZ' => __( 'Belize', 'uael' ),
			'BJ' => __( 'Benin', 'uael' ),
			'BM' => __( 'Bermuda', 'uael' ),
			'BT' => __( 'Bhutan', 'uael' ),
			'BO' => __( 'Bolivia', 'uael' ),
			'BQ' => __( 'Bonaire, Saint Eustatius and Saba', 'uael' ),
			'BA' => __( 'Bosnia and Herzegovina', 'uael' ),
			'BW' => __( 'Botswana', 'uael' ),
			'BV' => __( 'Bouvet Island', 'uael' ),
			'BR' => __( 'Brazil', 'uael' ),
			'IO' => __( 'British Indian Ocean Territory', 'uael' ),
			'VG' => __( 'British Virgin Islands', 'uael' ),
			'BN' => __( 'Brunei', 'uael' ),
			'BG' => __( 'Bulgaria', 'uael' ),
			'BF' => __( 'Burkina Faso', 'uael' ),
			'BI' => __( 'Burundi', 'uael' ),
			'KH' => __( 'Cambodia', 'uael' ),
			'CM' => __( 'Cameroon', 'uael' ),
			'CA' => __( 'Canada', 'uael' ),
			'CV' => __( 'Cape Verde', 'uael' ),
			'KY' => __( 'Cayman Islands', 'uael' ),
			'CF' => __( 'Central African Republic', 'uael' ),
			'TD' => __( 'Chad', 'uael' ),
			'CL' => __( 'Chile', 'uael' ),
			'CN' => __( 'China', 'uael' ),
			'CX' => __( 'Christmas Island', 'uael' ),
			'CC' => __( 'Cocos (Keeling) Islands', 'uael' ),
			'CO' => __( 'Colombia', 'uael' ),
			'KM' => __( 'Comoros', 'uael' ),
			'CG' => __( 'Congo (Brazzaville)', 'uael' ),
			'CD' => __( 'Congo (Kinshasa)', 'uael' ),
			'CK' => __( 'Cook Islands', 'uael' ),
			'CR' => __( 'Costa Rica', 'uael' ),
			'HR' => __( 'Croatia', 'uael' ),
			'CU' => __( 'Cuba', 'uael' ),
			'CW' => __( 'Cura&ccedil;ao', 'uael' ),
			'CY' => __( 'Cyprus', 'uael' ),
			'CZ' => __( 'Czech Republic', 'uael' ),
			'DK' => __( 'Denmark', 'uael' ),
			'DJ' => __( 'Djibouti', 'uael' ),
			'DM' => __( 'Dominica', 'uael' ),
			'DO' => __( 'Dominican Republic', 'uael' ),
			'EC' => __( 'Ecuador', 'uael' ),
			'EG' => __( 'Egypt', 'uael' ),
			'SV' => __( 'El Salvador', 'uael' ),
			'GQ' => __( 'Equatorial Guinea', 'uael' ),
			'ER' => __( 'Eritrea', 'uael' ),
			'EE' => __( 'Estonia', 'uael' ),
			'ET' => __( 'Ethiopia', 'uael' ),
			'FK' => __( 'Falkland Islands', 'uael' ),
			'FO' => __( 'Faroe Islands', 'uael' ),
			'FJ' => __( 'Fiji', 'uael' ),
			'FI' => __( 'Finland', 'uael' ),
			'FR' => __( 'France', 'uael' ),
			'GF' => __( 'French Guiana', 'uael' ),
			'PF' => __( 'French Polynesia', 'uael' ),
			'TF' => __( 'French Southern Territories', 'uael' ),
			'GA' => __( 'Gabon', 'uael' ),
			'GM' => __( 'Gambia', 'uael' ),
			'GE' => __( 'Georgia', 'uael' ),
			'DE' => __( 'Germany', 'uael' ),
			'GH' => __( 'Ghana', 'uael' ),
			'GI' => __( 'Gibraltar', 'uael' ),
			'GR' => __( 'Greece', 'uael' ),
			'GL' => __( 'Greenland', 'uael' ),
			'GD' => __( 'Grenada', 'uael' ),
			'GP' => __( 'Guadeloupe', 'uael' ),
			'GU' => __( 'Guam', 'uael' ),
			'GT' => __( 'Guatemala', 'uael' ),
			'GG' => __( 'Guernsey', 'uael' ),
			'GN' => __( 'Guinea', 'uael' ),
			'GW' => __( 'Guinea-Bissau', 'uael' ),
			'GY' => __( 'Guyana', 'uael' ),
			'HT' => __( 'Haiti', 'uael' ),
			'HM' => __( 'Heard Island and McDonald Islands', 'uael' ),
			'HN' => __( 'Honduras', 'uael' ),
			'HK' => __( 'Hong Kong', 'uael' ),
			'HU' => __( 'Hungary', 'uael' ),
			'IS' => __( 'Iceland', 'uael' ),
			'IN' => __( 'India', 'uael' ),
			'ID' => __( 'Indonesia', 'uael' ),
			'IR' => __( 'Iran', 'uael' ),
			'IQ' => __( 'Iraq', 'uael' ),
			'IE' => __( 'Ireland', 'uael' ),
			'IM' => __( 'Isle of Man', 'uael' ),
			'IL' => __( 'Israel', 'uael' ),
			'IT' => __( 'Italy', 'uael' ),
			'CI' => __( 'Ivory Coast', 'uael' ),
			'JM' => __( 'Jamaica', 'uael' ),
			'JP' => __( 'Japan', 'uael' ),
			'JE' => __( 'Jersey', 'uael' ),
			'JO' => __( 'Jordan', 'uael' ),
			'KZ' => __( 'Kazakhstan', 'uael' ),
			'KE' => __( 'Kenya', 'uael' ),
			'KI' => __( 'Kiribati', 'uael' ),
			'KW' => __( 'Kuwait', 'uael' ),
			'KG' => __( 'Kyrgyzstan', 'uael' ),
			'LA' => __( 'Laos', 'uael' ),
			'LV' => __( 'Latvia', 'uael' ),
			'LB' => __( 'Lebanon', 'uael' ),
			'LS' => __( 'Lesotho', 'uael' ),
			'LR' => __( 'Liberia', 'uael' ),
			'LY' => __( 'Libya', 'uael' ),
			'LI' => __( 'Liechtenstein', 'uael' ),
			'LT' => __( 'Lithuania', 'uael' ),
			'LU' => __( 'Luxembourg', 'uael' ),
			'MO' => __( 'Macao S.A.R., China', 'uael' ),
			'MK' => __( 'Macedonia', 'uael' ),
			'MG' => __( 'Madagascar', 'uael' ),
			'MW' => __( 'Malawi', 'uael' ),
			'MY' => __( 'Malaysia', 'uael' ),
			'MV' => __( 'Maldives', 'uael' ),
			'ML' => __( 'Mali', 'uael' ),
			'MT' => __( 'Malta', 'uael' ),
			'MH' => __( 'Marshall Islands', 'uael' ),
			'MQ' => __( 'Martinique', 'uael' ),
			'MR' => __( 'Mauritania', 'uael' ),
			'MU' => __( 'Mauritius', 'uael' ),
			'YT' => __( 'Mayotte', 'uael' ),
			'MX' => __( 'Mexico', 'uael' ),
			'FM' => __( 'Micronesia', 'uael' ),
			'MD' => __( 'Moldova', 'uael' ),
			'MC' => __( 'Monaco', 'uael' ),
			'MN' => __( 'Mongolia', 'uael' ),
			'ME' => __( 'Montenegro', 'uael' ),
			'MS' => __( 'Montserrat', 'uael' ),
			'MA' => __( 'Morocco', 'uael' ),
			'MZ' => __( 'Mozambique', 'uael' ),
			'MM' => __( 'Myanmar', 'uael' ),
			'NA' => __( 'Namibia', 'uael' ),
			'NR' => __( 'Nauru', 'uael' ),
			'NP' => __( 'Nepal', 'uael' ),
			'NL' => __( 'Netherlands', 'uael' ),
			'NC' => __( 'New Caledonia', 'uael' ),
			'NZ' => __( 'New Zealand', 'uael' ),
			'NI' => __( 'Nicaragua', 'uael' ),
			'NE' => __( 'Niger', 'uael' ),
			'NG' => __( 'Nigeria', 'uael' ),
			'NU' => __( 'Niue', 'uael' ),
			'NF' => __( 'Norfolk Island', 'uael' ),
			'MP' => __( 'Northern Mariana Islands', 'uael' ),
			'KP' => __( 'North Korea', 'uael' ),
			'NO' => __( 'Norway', 'uael' ),
			'OM' => __( 'Oman', 'uael' ),
			'PK' => __( 'Pakistan', 'uael' ),
			'PS' => __( 'Palestinian Territory', 'uael' ),
			'PA' => __( 'Panama', 'uael' ),
			'PG' => __( 'Papua New Guinea', 'uael' ),
			'PY' => __( 'Paraguay', 'uael' ),
			'PE' => __( 'Peru', 'uael' ),
			'PH' => __( 'Philippines', 'uael' ),
			'PN' => __( 'Pitcairn', 'uael' ),
			'PL' => __( 'Poland', 'uael' ),
			'PT' => __( 'Portugal', 'uael' ),
			'PR' => __( 'Puerto Rico', 'uael' ),
			'QA' => __( 'Qatar', 'uael' ),
			'RE' => __( 'Reunion', 'uael' ),
			'RO' => __( 'Romania', 'uael' ),
			'RU' => __( 'Russia', 'uael' ),
			'RW' => __( 'Rwanda', 'uael' ),
			'BL' => __( 'Saint Barth&eacute;lemy', 'uael' ),
			'SH' => __( 'Saint Helena', 'uael' ),
			'KN' => __( 'Saint Kitts and Nevis', 'uael' ),
			'LC' => __( 'Saint Lucia', 'uael' ),
			'MF' => __( 'Saint Martin (French part)', 'uael' ),
			'SX' => __( 'Saint Martin (Dutch part)', 'uael' ),
			'PM' => __( 'Saint Pierre and Miquelon', 'uael' ),
			'VC' => __( 'Saint Vincent and the Grenadines', 'uael' ),
			'SM' => __( 'San Marino', 'uael' ),
			'ST' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'uael' ),
			'SA' => __( 'Saudi Arabia', 'uael' ),
			'SN' => __( 'Senegal', 'uael' ),
			'RS' => __( 'Serbia', 'uael' ),
			'SC' => __( 'Seychelles', 'uael' ),
			'SL' => __( 'Sierra Leone', 'uael' ),
			'SG' => __( 'Singapore', 'uael' ),
			'SK' => __( 'Slovakia', 'uael' ),
			'SI' => __( 'Slovenia', 'uael' ),
			'SB' => __( 'Solomon Islands', 'uael' ),
			'SO' => __( 'Somalia', 'uael' ),
			'ZA' => __( 'South Africa', 'uael' ),
			'GS' => __( 'South Georgia/Sandwich Islands', 'uael' ),
			'KR' => __( 'South Korea', 'uael' ),
			'SS' => __( 'South Sudan', 'uael' ),
			'ES' => __( 'Spain', 'uael' ),
			'LK' => __( 'Sri Lanka', 'uael' ),
			'SD' => __( 'Sudan', 'uael' ),
			'SR' => __( 'Suriname', 'uael' ),
			'SJ' => __( 'Svalbard and Jan Mayen', 'uael' ),
			'SZ' => __( 'Swaziland', 'uael' ),
			'SE' => __( 'Sweden', 'uael' ),
			'CH' => __( 'Switzerland', 'uael' ),
			'SY' => __( 'Syria', 'uael' ),
			'TW' => __( 'Taiwan', 'uael' ),
			'TJ' => __( 'Tajikistan', 'uael' ),
			'TZ' => __( 'Tanzania', 'uael' ),
			'TH' => __( 'Thailand', 'uael' ),
			'TL' => __( 'Timor-Leste', 'uael' ),
			'TG' => __( 'Togo', 'uael' ),
			'TK' => __( 'Tokelau', 'uael' ),
			'TO' => __( 'Tonga', 'uael' ),
			'TT' => __( 'Trinidad and Tobago', 'uael' ),
			'TN' => __( 'Tunisia', 'uael' ),
			'TR' => __( 'Turkey', 'uael' ),
			'TM' => __( 'Turkmenistan', 'uael' ),
			'TC' => __( 'Turks and Caicos Islands', 'uael' ),
			'TV' => __( 'Tuvalu', 'uael' ),
			'UG' => __( 'Uganda', 'uael' ),
			'UA' => __( 'Ukraine', 'uael' ),
			'AE' => __( 'United Arab Emirates', 'uael' ),
			'GB' => __( 'United Kingdom (UK)', 'uael' ),
			'US' => __( 'United States (US)', 'uael' ),
			'UM' => __( 'United States (US) Minor Outlying Islands', 'uael' ),
			'VI' => __( 'United States (US) Virgin Islands', 'uael' ),
			'UY' => __( 'Uruguay', 'uael' ),
			'UZ' => __( 'Uzbekistan', 'uael' ),
			'VU' => __( 'Vanuatu', 'uael' ),
			'VA' => __( 'Vatican', 'uael' ),
			'VE' => __( 'Venezuela', 'uael' ),
			'VN' => __( 'Vietnam', 'uael' ),
			'WF' => __( 'Wallis and Futuna', 'uael' ),
			'EH' => __( 'Western Sahara', 'uael' ),
			'WS' => __( 'Samoa', 'uael' ),
			'YE' => __( 'Yemen', 'uael' ),
			'ZM' => __( 'Zambia', 'uael' ),
			'ZW' => __( 'Zimbabwe', 'uael' ),
		);
	}

	/**
	 * Checks if maxmind license key is integrated.
	 *
	 * @return bool Returns true if the maxmind license key is available else false.
	 * @since 1.35.1
	 */
	public function is_maxmind_integration() {
		$maxmind_license_key = UAEL_Helper::get_integrations_options();
		if ( empty( $maxmind_license_key['uael_maxmind_geolocation_license_key'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get Repeater Control Field Value
	 *
	 * @since 1.35.1
	 * @param array $condition return key's.
	 * @return array|void
	 */
	public function get_repeater_control( array $condition ) {

		$widget_list = UAEL_Helper::get_widget_list();
		$admin_link  = $widget_list['DisplayConditions']['setting_url'];
		$admin_link  = esc_url( $admin_link );

		return array(
			'label'       => $this->get_title(),
			'show_label'  => false,
			'type'        => Controls_Manager::SELECT,
			'default'     => '',
			/* translators: 1: <div class="elementor-panel-alert elementor-panel-alert-warning"> 2: setting link 3: </div> */
			'description' => ( ! $this->is_maxmind_integration() ) ? sprintf( __( '%1$s MaxMind license key required for this display condition. Please configure the license key <a href="%2$s" target="_blank" rel="noopener">here.</a>%3$s', 'uael' ), '<div class="elementor-panel-alert elementor-panel-alert-warning ">', $admin_link, '</div>' ) : '',
			'label_block' => true,
			'options'     => $this->uae_geo_all_countries(),
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
	 * @since 1.35.1
	 */
	public function compare_value( $settings, $operator, $value ) {

		$geolocation = new UAEL_Geolocation();
		$location    = $geolocation->geolocate_ip();
		$country     = $location['country'];

		return UAEL_Helper::display_conditions_compare( $country, $value, $operator );
	}
}
