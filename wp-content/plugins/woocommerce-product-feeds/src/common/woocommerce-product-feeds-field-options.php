<?php

/**
 * Class WoocommerceProductFeedsFieldOptions
 *
 * Returns the valid dropdown options for various fields to allow re-use in different contexts.
 */
class WoocommerceProductFeedsFieldOptions {

	public static function adult_options() {
		return [
			'yes' => _x( 'Yes', 'Option for "adult" field', 'woocommerce_gpf' ),
			'no'  => _x( 'No', 'Option for "adult" field', 'woocommerce_gpf' ),
		];
	}

	public static function pause_options() {
		return [
			'ads' => _x(
				'[Ads] Pause for all ad locations (including Shopping Ads, Display Ads, and local inventory ads)',
				'Option for "pause" field',
				'woocommerce_gpf'
			),
			'all' => _x(
				'[All] Pause for all Shopping locations (including Shopping Ads, Display Ads, local inventory ads, Buy on Google, and free listings).',
				'Option for "pause" field',
				'woocommerce_gpf'
			),
		];
	}

	public static function age_group_options() {
		return [
			'newborn' => _x( 'Newborn', 'Option for "age group" field', 'woocommerce_gpf' ),
			'infant'  => _x( 'Infant', 'Option for "age group" field', 'woocommerce_gpf' ),
			'toddler' => _x( 'Toddler', 'Option for "age group" field', 'woocommerce_gpf' ),
			'kids'    => _x( 'Kids', 'Option for "age group" field', 'woocommerce_gpf' ),
			'adult'   => _x( 'Adult', 'Option for "age group" field', 'woocommerce_gpf' ),
		];
	}

	public static function availability_options() {
		return [
			'in stock'     => _x( 'In stock', 'Option for "availability" field', 'woocommerce_gpf' ),
			'preorder'     => _x( 'Pre-order', 'Option for "availability" field', 'woocommerce_gpf' ),
			'backorder'    => _x( 'Backorder', 'Option for "availability" field', 'woocommerce_gpf' ),
			'out of stock' => _x( 'Out of stock', 'Option for "availability" field', 'woocommerce_gpf' ),
		];
	}

	public static function condition_options() {
		return [
			'new'         => _x( 'New', 'Option for "condition" field', 'woocommerce_gpf' ),
			'refurbished' => _x( 'Refurbished', 'Option for "condition" field', 'woocommerce_gpf' ),
			'used'        => _x( 'Used', 'Option for "condition" field', 'woocommerce_gpf' ),
		];
	}

	public static function energy_efficiency_class_options() {
		return [
			'A+++' => _x( 'A+++', 'Option for "energy efficiency class" field', 'woocommerce_gpf' ),
			'A++'  => _x( 'A++', 'Option for "energy efficiency class" field', 'woocommerce_gpf' ),
			'A+'   => _x( 'A+', 'Option for "energy efficiency class" field', 'woocommerce_gpf' ),
			'A'    => _x( 'A', 'Option for "energy efficiency class" field', 'woocommerce_gpf' ),
			'B'    => _x( 'B', 'Option for "energy efficiency class" field', 'woocommerce_gpf' ),
			'C'    => _x( 'C', 'Option for "energy efficiency class" field', 'woocommerce_gpf' ),
			'D'    => _x( 'D', 'Option for "energy efficiency class" field', 'woocommerce_gpf' ),
			'E'    => _x( 'E', 'Option for "energy efficiency class" field', 'woocommerce_gpf' ),
			'F'    => _x( 'F', 'Option for "energy efficiency class" field', 'woocommerce_gpf' ),
			'G'    => _x( 'G', 'Option for "energy efficiency class" field', 'woocommerce_gpf' ),
		];
	}

	public static function gender_options() {
		return [
			'male'   => _x( 'Male', 'Option for "gender" field', 'woocommerce_gpf' ),
			'female' => _x( 'Female', 'Option for "gender" field', 'woocommerce_gpf' ),
			'unisex' => _x( 'Unisex', 'Option for "gender" field', 'woocommerce_gpf' ),
		];
	}

	public static function google_funded_promotion_eligibility_options() {
		return [
			'all'  => _x( 'All', 'Option for "Google funded promotion eligibility" fiield', 'woocommerce_gpf' ),
			'none' => _x( 'None', 'Option for "Google funded promotion eligibility" fiield', 'woocommerce_gpf' ),
		];
	}

	public static function is_bundle_options() {
		return [
			'on' => _x( 'Yes', 'Option for "is bundle" field', 'woocommerce_gpf' ),
		];
	}

	public static function pickup_method_options() {
		return [
			'buy'           => _x( 'Buy', 'Option for "pickup method" field', 'woocommerce_gpf' ),
			'reserve'       => _x( 'Reserve', 'Option for "pickup method" field', 'woocommerce_gpf' ),
			'ship to store' => _x( 'Ship to store', 'Option for "pickup method" field', 'woocommerce_gpf' ),
			'not supported' => _x( 'Not supported', 'Option for "pickup method" field', 'woocommerce_gpf' ),
		];
	}

	public static function pickup_sla_options() {
		return [
			'same day'   => _x( 'Same day', 'Option for "pickup SLA" field', 'woocommerce_gpf' ),
			'next day'   => _x( 'Next day', 'Option for "pickup SLA" field', 'woocommerce_gpf' ),
			'1-day'      => _x( '1-day', 'Option for "pickup SLA" field', 'woocommerce_gpf' ),
			'2-day'      => _x( '2-day', 'Option for "pickup SLA" field', 'woocommerce_gpf' ),
			'3-day'      => _x( '3-day', 'Option for "pickup SLA" field', 'woocommerce_gpf' ),
			'4-day'      => _x( '4-day', 'Option for "pickup SLA" field', 'woocommerce_gpf' ),
			'5-day'      => _x( '5-day', 'Option for "pickup SLA" field', 'woocommerce_gpf' ),
			'6-day'      => _x( '6-day', 'Option for "pickup SLA" field', 'woocommerce_gpf' ),
			'7-day'      => _x( '7-day', 'Option for "pickup SLA" field', 'woocommerce_gpf' ),
			'multi-week' => _x( 'Multi-week', 'Option for "pickup SLA" field', 'woocommerce_gpf' ),
		];
	}

	public static function size_system_options() {
		return [
			'US'  => _x( 'US', 'Option for "size system" field', 'woocommerce_gpf' ),
			'UK'  => _x( 'UK', 'Option for "size system" field', 'woocommerce_gpf' ),
			'EU'  => _x( 'EU', 'Option for "size system" field', 'woocommerce_gpf' ),
			'AU'  => _x( 'AU', 'Option for "size system" field', 'woocommerce_gpf' ),
			'BR'  => _x( 'BR', 'Option for "size system" field', 'woocommerce_gpf' ),
			'CN'  => _x( 'CN', 'Option for "size system" field', 'woocommerce_gpf' ),
			'FR'  => _x( 'FR', 'Option for "size system" field', 'woocommerce_gpf' ),
			'DE'  => _x( 'DE', 'Option for "size system" field', 'woocommerce_gpf' ),
			'IT'  => _x( 'IT', 'Option for "size system" field', 'woocommerce_gpf' ),
			'JP'  => _x( 'JP', 'Option for "size system" field', 'woocommerce_gpf' ),
			'MEX' => _x( 'MEX', 'Option for "size system" field', 'woocommerce_gpf' ),
		];
	}

	public static function size_type_options() {
		return [
			'regular'      => _x( 'Regular', 'Option for "size type" field', 'woocommerce_gpf' ),
			'petite'       => _x( 'Petite', 'Option for "size type" field', 'woocommerce_gpf' ),
			'plus'         => _x( 'Plus', 'Option for "size type" field', 'woocommerce_gpf' ),
			'big and tall' => _x( 'Big and tall', 'Option for "size type" field', 'woocommerce_gpf' ),
			'maternity'    => _x( 'Maternity', 'Option for "size type" field', 'woocommerce_gpf' ),
		];
	}

	/**
	 * List of countries
	 *
	 * Used for ships_from_country and shopping_ads_excluded_country
	 *
	 * @return array
	 */
	public static function country_options() {
		return [
			'AF' => __( 'Afghanistan', 'woocommerce_gpf' ),
			'AL' => __( 'Albania', 'woocommerce_gpf' ),
			'DZ' => __( 'Algeria', 'woocommerce_gpf' ),
			'AS' => __( 'American Samoa', 'woocommerce_gpf' ),
			'AD' => __( 'Andorra', 'woocommerce_gpf' ),
			'AO' => __( 'Angola', 'woocommerce_gpf' ),
			'AI' => __( 'Anguilla', 'woocommerce_gpf' ),
			'AQ' => __( 'Antarctica', 'woocommerce_gpf' ),
			'AG' => __( 'Antigua and Barbuda', 'woocommerce_gpf' ),
			'AR' => __( 'Argentina', 'woocommerce_gpf' ),
			'AM' => __( 'Armenia', 'woocommerce_gpf' ),
			'AW' => __( 'Aruba', 'woocommerce_gpf' ),
			'AU' => __( 'Australia', 'woocommerce_gpf' ),
			'AT' => __( 'Austria', 'woocommerce_gpf' ),
			'AZ' => __( 'Azerbaijan', 'woocommerce_gpf' ),
			'BS' => __( 'Bahamas', 'woocommerce_gpf' ),
			'BH' => __( 'Bahrain', 'woocommerce_gpf' ),
			'BD' => __( 'Bangladesh', 'woocommerce_gpf' ),
			'BB' => __( 'Barbados', 'woocommerce_gpf' ),
			'BY' => __( 'Belarus', 'woocommerce_gpf' ),
			'BE' => __( 'Belgium', 'woocommerce_gpf' ),
			'BZ' => __( 'Belize', 'woocommerce_gpf' ),
			'BJ' => __( 'Benin', 'woocommerce_gpf' ),
			'BM' => __( 'Bermuda', 'woocommerce_gpf' ),
			'BT' => __( 'Bhutan', 'woocommerce_gpf' ),
			'BO' => __( 'Bolivia', 'woocommerce_gpf' ),
			'BQ' => __( 'Bonaire, Sint Eustatius and Saba', 'woocommerce_gpf' ),
			'BA' => __( 'Bosnia and Herzegovina', 'woocommerce_gpf' ),
			'BW' => __( 'Botswana', 'woocommerce_gpf' ),
			'BV' => __( 'Bouvet Island', 'woocommerce_gpf' ),
			'BR' => __( 'Brazil', 'woocommerce_gpf' ),
			'IO' => __( 'British Indian Ocean Territory', 'woocommerce_gpf' ),
			'BN' => __( 'Brunei Darussalam', 'woocommerce_gpf' ),
			'BG' => __( 'Bulgaria', 'woocommerce_gpf' ),
			'BF' => __( 'Burkina Faso', 'woocommerce_gpf' ),
			'BI' => __( 'Burundi', 'woocommerce_gpf' ),
			'KH' => __( 'Cambodia', 'woocommerce_gpf' ),
			'CM' => __( 'Cameroon', 'woocommerce_gpf' ),
			'CA' => __( 'Canada', 'woocommerce_gpf' ),
			'CV' => __( 'Cape Verde', 'woocommerce_gpf' ),
			'KY' => __( 'Cayman Islands', 'woocommerce_gpf' ),
			'CF' => __( 'Central African Republic', 'woocommerce_gpf' ),
			'TD' => __( 'Chad', 'woocommerce_gpf' ),
			'CL' => __( 'Chile', 'woocommerce_gpf' ),
			'CN' => __( 'China', 'woocommerce_gpf' ),
			'CX' => __( 'Christmas Island', 'woocommerce_gpf' ),
			'CC' => __( 'Cocos (Keeling) Islands', 'woocommerce_gpf' ),
			'CO' => __( 'Colombia', 'woocommerce_gpf' ),
			'KM' => __( 'Comoros', 'woocommerce_gpf' ),
			'CG' => __( 'Congo', 'woocommerce_gpf' ),
			'CD' => __( 'Congo, Democratic Republic', 'woocommerce_gpf' ),
			'CK' => __( 'Cook Islands', 'woocommerce_gpf' ),
			'CR' => __( 'Costa Rica', 'woocommerce_gpf' ),
			'CI' => __( "Cote d'Ivoire", 'woocommerce_gpf' ),
			'HR' => __( 'Croatia', 'woocommerce_gpf' ),
			'CU' => __( 'Cuba', 'woocommerce_gpf' ),
			'CW' => __( 'Curaçao', 'woocommerce_gpf' ),
			'CY' => __( 'Cyprus', 'woocommerce_gpf' ),
			'CZ' => __( 'Czechia', 'woocommerce_gpf' ),
			'DK' => __( 'Denmark', 'woocommerce_gpf' ),
			'DJ' => __( 'Djibouti', 'woocommerce_gpf' ),
			'DM' => __( 'Dominica', 'woocommerce_gpf' ),
			'do' => __( 'Dominican Republic', 'woocommerce_gpf' ),
			'TL' => __( 'East Timor', 'woocommerce_gpf' ),
			'EC' => __( 'Ecuador', 'woocommerce_gpf' ),
			'EG' => __( 'Egypt', 'woocommerce_gpf' ),
			'SV' => __( 'El Salvador', 'woocommerce_gpf' ),
			'GQ' => __( 'Equatorial Guinea', 'woocommerce_gpf' ),
			'ER' => __( 'Eritrea', 'woocommerce_gpf' ),
			'EE' => __( 'Estonia', 'woocommerce_gpf' ),
			'ET' => __( 'Ethiopia', 'woocommerce_gpf' ),
			'FK' => __( 'Falkland Islands (Malvinas)', 'woocommerce_gpf' ),
			'FO' => __( 'Faroe Islands', 'woocommerce_gpf' ),
			'FJ' => __( 'Fiji', 'woocommerce_gpf' ),
			'FI' => __( 'Finland', 'woocommerce_gpf' ),
			'FR' => __( 'France', 'woocommerce_gpf' ),
			'GF' => __( 'French Guiana', 'woocommerce_gpf' ),
			'PF' => __( 'French Polynesia', 'woocommerce_gpf' ),
			'TF' => __( 'French Southern Territories', 'woocommerce_gpf' ),
			'GA' => __( 'Gabon', 'woocommerce_gpf' ),
			'GM' => __( 'Gambia', 'woocommerce_gpf' ),
			'GE' => __( 'Georgia', 'woocommerce_gpf' ),
			'DE' => __( 'Germany', 'woocommerce_gpf' ),
			'GH' => __( 'Ghana', 'woocommerce_gpf' ),
			'GI' => __( 'Gibraltar', 'woocommerce_gpf' ),
			'GR' => __( 'Greece', 'woocommerce_gpf' ),
			'GL' => __( 'Greenland', 'woocommerce_gpf' ),
			'GD' => __( 'Grenada', 'woocommerce_gpf' ),
			'GP' => __( 'Guadeloupe', 'woocommerce_gpf' ),
			'GU' => __( 'Guam', 'woocommerce_gpf' ),
			'GT' => __( 'Guatemala', 'woocommerce_gpf' ),
			'GG' => __( 'Guernsey', 'woocommerce_gpf' ),
			'GN' => __( 'Guinea', 'woocommerce_gpf' ),
			'GW' => __( 'Guinea - Bissau', 'woocommerce_gpf' ),
			'GY' => __( 'Guyana', 'woocommerce_gpf' ),
			'HT' => __( 'Haiti', 'woocommerce_gpf' ),
			'HM' => __( 'Heard and McDonald Islands', 'woocommerce_gpf' ),
			'HN' => __( 'Honduras', 'woocommerce_gpf' ),
			'HK' => __( 'Hong Kong', 'woocommerce_gpf' ),
			'HU' => __( 'Hungary', 'woocommerce_gpf' ),
			'IS' => __( 'Iceland', 'woocommerce_gpf' ),
			'IN' => __( 'India', 'woocommerce_gpf' ),
			'ID' => __( 'Indonesia', 'woocommerce_gpf' ),
			'IR' => __( 'Iran', 'woocommerce_gpf' ),
			'IQ' => __( 'Iraq', 'woocommerce_gpf' ),
			'IE' => __( 'Ireland', 'woocommerce_gpf' ),
			'IL' => __( 'Israel', 'woocommerce_gpf' ),
			'IT' => __( 'Italy', 'woocommerce_gpf' ),
			'JM' => __( 'Jamaica', 'woocommerce_gpf' ),
			'JP' => __( 'Japan', 'woocommerce_gpf' ),
			'JE' => __( 'Jersey', 'woocommerce_gpf' ),
			'JO' => __( 'Jordan', 'woocommerce_gpf' ),
			'KZ' => __( 'Kazakhstan', 'woocommerce_gpf' ),
			'KE' => __( 'Kenya', 'woocommerce_gpf' ),
			'KI' => __( 'Kiribati', 'woocommerce_gpf' ),
			'KW' => __( 'Kuwait', 'woocommerce_gpf' ),
			'KG' => __( 'Kyrgyzstan', 'woocommerce_gpf' ),
			'LA' => __( "Lao People's Democratic Republic", 'woocommerce_gpf' ),
			'LV' => __( 'Latvia', 'woocommerce_gpf' ),
			'LB' => __( 'Lebanon', 'woocommerce_gpf' ),
			'LS' => __( 'Lesotho', 'woocommerce_gpf' ),
			'LR' => __( 'Liberia', 'woocommerce_gpf' ),
			'LY' => __( 'Libya', 'woocommerce_gpf' ),
			'LI' => __( 'Liechtenstein', 'woocommerce_gpf' ),
			'LT' => __( 'Lithuania', 'woocommerce_gpf' ),
			'LU' => __( 'Luxembourg', 'woocommerce_gpf' ),
			'MO' => __( 'Macau', 'woocommerce_gpf' ),
			'MK' => __( 'Macedonia', 'woocommerce_gpf' ),
			'MG' => __( 'Madagascar', 'woocommerce_gpf' ),
			'MW' => __( 'Malawi', 'woocommerce_gpf' ),
			'MY' => __( 'Malaysia', 'woocommerce_gpf' ),
			'MV' => __( 'Maldives', 'woocommerce_gpf' ),
			'ML' => __( 'Mali', 'woocommerce_gpf' ),
			'MT' => __( 'Malta', 'woocommerce_gpf' ),
			'MH' => __( 'Marshall Islands', 'woocommerce_gpf' ),
			'MQ' => __( 'Martinique', 'woocommerce_gpf' ),
			'MR' => __( 'Mauritania', 'woocommerce_gpf' ),
			'MU' => __( 'Mauritius', 'woocommerce_gpf' ),
			'YT' => __( 'Mayotte', 'woocommerce_gpf' ),
			'MX' => __( 'Mexico', 'woocommerce_gpf' ),
			'FM' => __( 'Micronesia', 'woocommerce_gpf' ),
			'MD' => __( 'Moldova', 'woocommerce_gpf' ),
			'MC' => __( 'Monaco', 'woocommerce_gpf' ),
			'MN' => __( 'Mongolia', 'woocommerce_gpf' ),
			'ME' => __( 'Montenegro', 'woocommerce_gpf' ),
			'MS' => __( 'Montserrat', 'woocommerce_gpf' ),
			'MA' => __( 'Morocco', 'woocommerce_gpf' ),
			'MZ' => __( 'Mozambique', 'woocommerce_gpf' ),
			'MM' => __( 'Myanmar', 'woocommerce_gpf' ),
			'NA' => __( 'Namibia', 'woocommerce_gpf' ),
			'NR' => __( 'Nauru', 'woocommerce_gpf' ),
			'NP' => __( 'Nepal', 'woocommerce_gpf' ),
			'NL' => __( 'Netherlands', 'woocommerce_gpf' ),
			'AN' => __( 'Netherlands Antilles', 'woocommerce_gpf' ),
			'NC' => __( 'new Caledonia', 'woocommerce_gpf' ),
			'NZ' => __( 'new Zealand', 'woocommerce_gpf' ),
			'NI' => __( 'Nicaragua', 'woocommerce_gpf' ),
			'NE' => __( 'Niger', 'woocommerce_gpf' ),
			'NG' => __( 'Nigeria', 'woocommerce_gpf' ),
			'NU' => __( 'Niue', 'woocommerce_gpf' ),
			'NF' => __( 'Norfolk Island', 'woocommerce_gpf' ),
			'KP' => __( 'North Korea', 'woocommerce_gpf' ),
			'MP' => __( 'Northern Mariana Islands', 'woocommerce_gpf' ),
			'NO' => __( 'Norway', 'woocommerce_gpf' ),
			'OM' => __( 'Oman', 'woocommerce_gpf' ),
			'PK' => __( 'Pakistan', 'woocommerce_gpf' ),
			'PW' => __( 'Palau', 'woocommerce_gpf' ),
			'PS' => __( 'Palestinian Territory', 'woocommerce_gpf' ),
			'PA' => __( 'Panama', 'woocommerce_gpf' ),
			'PG' => __( 'Papua new Guinea', 'woocommerce_gpf' ),
			'PY' => __( 'Paraguay', 'woocommerce_gpf' ),
			'PE' => __( 'Peru', 'woocommerce_gpf' ),
			'PH' => __( 'Philippines', 'woocommerce_gpf' ),
			'PN' => __( 'Pitcairn', 'woocommerce_gpf' ),
			'PL' => __( 'Poland', 'woocommerce_gpf' ),
			'PT' => __( 'Portugal', 'woocommerce_gpf' ),
			'PR' => __( 'Puerto Rico', 'woocommerce_gpf' ),
			'QA' => __( 'Qatar', 'woocommerce_gpf' ),
			'XK' => __( 'Republic of Kosovo', 'woocommerce_gpf' ),
			'RE' => __( 'Reunion', 'woocommerce_gpf' ),
			'RO' => __( 'Romania', 'woocommerce_gpf' ),
			'RU' => __( 'Russian Federation', 'woocommerce_gpf' ),
			'RW' => __( 'Rwanda', 'woocommerce_gpf' ),
			'KN' => __( 'Saint Kitts and Nevis', 'woocommerce_gpf' ),
			'LC' => __( 'Saint Lucia', 'woocommerce_gpf' ),
			'VC' => __( 'Saint Vincent and the Grenadines', 'woocommerce_gpf' ),
			'WS' => __( 'Samoa', 'woocommerce_gpf' ),
			'SM' => __( 'San Marino', 'woocommerce_gpf' ),
			'ST' => __( 'Sao Tome and Principe', 'woocommerce_gpf' ),
			'SA' => __( 'Saudi Arabia', 'woocommerce_gpf' ),
			'SN' => __( 'Senegal', 'woocommerce_gpf' ),
			'RS' => __( 'Serbia', 'woocommerce_gpf' ),
			'SC' => __( 'Seychelles', 'woocommerce_gpf' ),
			'SL' => __( 'Sierra Leone', 'woocommerce_gpf' ),
			'SG' => __( 'Singapore', 'woocommerce_gpf' ),
			'SX' => __( 'Sint Maarten (Dutch part)', 'woocommerce_gpf' ),
			'SK' => __( 'Slovakia', 'woocommerce_gpf' ),
			'SI' => __( 'Slovenia', 'woocommerce_gpf' ),
			'SB' => __( 'Solomon Islands', 'woocommerce_gpf' ),
			'SO' => __( 'Somalia', 'woocommerce_gpf' ),
			'ZA' => __( 'South Africa', 'woocommerce_gpf' ),
			'GS' => __( 'South Georgia and The South Sandwich Islands', 'woocommerce_gpf' ),
			'KR' => __( 'South Korea', 'woocommerce_gpf' ),
			'ES' => __( 'Spain', 'woocommerce_gpf' ),
			'LK' => __( 'Sri Lanka', 'woocommerce_gpf' ),
			'SH' => __( 'St . Helena', 'woocommerce_gpf' ),
			'PM' => __( 'St . Pierre and Miquelon', 'woocommerce_gpf' ),
			'SD' => __( 'Sudan', 'woocommerce_gpf' ),
			'SR' => __( 'Suriname', 'woocommerce_gpf' ),
			'SJ' => __( 'Svalbard and Jan Mayen Islands', 'woocommerce_gpf' ),
			'SZ' => __( 'Swaziland', 'woocommerce_gpf' ),
			'SE' => __( 'Sweden', 'woocommerce_gpf' ),
			'CH' => __( 'Switzerland', 'woocommerce_gpf' ),
			'SY' => __( 'Syria', 'woocommerce_gpf' ),
			'TW' => __( 'Taiwan', 'woocommerce_gpf' ),
			'TJ' => __( 'Tajikistan', 'woocommerce_gpf' ),
			'TZ' => __( 'Tanzania', 'woocommerce_gpf' ),
			'TH' => __( 'Thailand', 'woocommerce_gpf' ),
			'TG' => __( 'Togo', 'woocommerce_gpf' ),
			'TK' => __( 'Tokelau', 'woocommerce_gpf' ),
			'TO' => __( 'Tonga', 'woocommerce_gpf' ),
			'TT' => __( 'Trinidad and Tobago', 'woocommerce_gpf' ),
			'TN' => __( 'Tunisia', 'woocommerce_gpf' ),
			'TR' => __( 'Turkey', 'woocommerce_gpf' ),
			'TM' => __( 'Turkmenistan', 'woocommerce_gpf' ),
			'TC' => __( 'Turks and Caicos Islands', 'woocommerce_gpf' ),
			'TV' => __( 'Tuvalu', 'woocommerce_gpf' ),
			'UG' => __( 'Uganda', 'woocommerce_gpf' ),
			'UA' => __( 'Ukraine', 'woocommerce_gpf' ),
			'AE' => __( 'United Arab Emirates', 'woocommerce_gpf' ),
			'GB' => __( 'United Kingdom', 'woocommerce_gpf' ),
			'US' => __( 'United States', 'woocommerce_gpf' ),
			'UM' => __( 'United States Minor Outlying Islands', 'woocommerce_gpf' ),
			'UY' => __( 'Uruguay', 'woocommerce_gpf' ),
			'UZ' => __( 'Uzbekistan', 'woocommerce_gpf' ),
			'VU' => __( 'Vanuatu', 'woocommerce_gpf' ),
			'VA' => __( 'Vatican', 'woocommerce_gpf' ),
			'VE' => __( 'Venezuela', 'woocommerce_gpf' ),
			'VN' => __( 'Viet Nam', 'woocommerce_gpf' ),
			'VG' => __( 'Virgin Islands (British)', 'woocommerce_gpf' ),
			'VI' => __( 'Virgin Islands (US)', 'woocommerce_gpf' ),
			'WF' => __( 'Wallis and Futuna Islands', 'woocommerce_gpf' ),
			'EH' => __( 'Western Sahara', 'woocommerce_gpf' ),
			'YE' => __( 'Yemen', 'woocommerce_gpf' ),
			'ZM' => __( 'Zambia', 'woocommerce_gpf' ),
			'ZW' => __( 'Zimbabwe', 'woocommerce_gpf' ),
		];
	}
}
