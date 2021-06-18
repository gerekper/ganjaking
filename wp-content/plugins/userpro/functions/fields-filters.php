<?php
                 /**
		  * Google reCaptcha Error Validation Start
		  * By Yogesh
		  * On 25 Aug 2015
		  */
			add_filter('userpro_login_validation', 'userpro_recaptcha_check', 10, 2);
			add_filter('userpro_register_validation', 'userpro_recaptcha_check', 10, 2);
			add_filter('userpro_form_validation', 'userpro_recaptcha_check', 10, 2);
			function userpro_recaptcha_check($errors, $form) {
				
				if (array_key_exists('g',$form)){
					if(!$form['g'])
					$errors['recaptcha'] = __('Please confirm the Captcha.','userpro');
				}

				return $errors;
			}
		 
		/**
		 * Google reCaptcha Error Validation End
		 * By Yogesh
		 * On 25 Aug 2015
		 */
		
	/* deny a blocked user from submitting form */
	add_filter('userpro_login_validation', 'userpro_check_blocked_user', 100, 2);
	add_filter('userpro_register_validation', 'userpro_check_blocked_user', 100, 2);
	add_filter('userpro_form_validation', 'userpro_check_blocked_user', 100, 2);
	function userpro_check_blocked_user($errors, $form) {
			global $userpro;
			$blocked_msg = __('This account has been blocked','userpro');
			if ( isset($form['username_or_email'])) {
				$user_id = get_user_id_by_login_email($form['username_or_email']);
				if(	get_user_meta( $user_id, 'userpro_account_status' , true)){
					$errors['username_or_email'] = $blocked_msg;
				}	
			}
			
			else if ( isset($form['user_login'])) {
				$user_id = get_user_id_by_login_email($form['user_login']);
				if(	get_user_meta( $user_id, 'userpro_account_status' , true) ){
					$errors['user_login'] = $blocked_msg;
				}
			}
			
			if ( isset($form['user_email']) ) {
				$user_id = get_user_id_by_login_email($form['user_email']);
				if(	get_user_meta( $user_id, 'userpro_account_status' , true) ){
					$errors['user_email'] = $blocked_msg;
				}
			}
			return $errors;
		}
		
		/* Get user_id by login name or login email */
		
	function get_user_id_by_login_email($param)
	{
		global $wpdb;
		$user_id = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_login = %s OR user_email = %s",$param,$param ));
		return $user_id;
	}
		

	/* validation on form processing */
	add_filter('userpro_login_validation', 'userpro_fields_validation', 10, 2);
	add_filter('userpro_register_validation', 'userpro_fields_validation', 10, 2);
	add_filter('userpro_form_validation', 'userpro_fields_validation', 10, 2);
	function userpro_fields_validation($errors, $form) {
		global $userpro;

        $allowed_roles = userpro_get_option('allowed_roles');

		$phonefields = userpro_get_option('phonefields');
		if (!$phonefields){
			$phonefields = array('');
		} else {
			$phonefields = explode(',', $phonefields);
		}
		
		if (userpro_get_option('max_field_length_active')) {
			$max_field_length_include = userpro_get_option('max_field_length_include');
			if ( $max_field_length_include != '') {
				$max_field_length_include = explode(',', $max_field_length_include);
			} else {
				$max_field_length_include = array('');
			}
		}
                
		if (userpro_get_option('min_field_length_active')) {
			$min_field_length_include = userpro_get_option('min_field_length_include');
			if ( $min_field_length_include != '') {
				$min_field_length_include = explode(',', $min_field_length_include);
			} else {
				$min_field_length_include = array('');
			}
		}
		// validate form
		foreach($form as $k => $v) {

		    // User Role.
            if($k === 'role' && !in_array($v, $allowed_roles) && !current_user_can( 'manage_options' )){
                $errors[$k] = __('Sorry, something went wrong.','userpro');
            }

			if ( $userpro->field_exists($k) && in_array($k, $phonefields) && $v != '' && !preg_match( userpro_get_option('phonefields_regex') , $v ) ) {
				$errors[$k] = __('Please enter a correct phone number','userpro');
			}

			if ( isset($max_field_length_include)) {
				if ( $userpro->field_exists($k) && $userpro->field_type($k) == 'text' && $v != '' && strlen($v) > userpro_get_option('max_field_length') && $userpro->field_label($k) && in_array($k, $max_field_length_include ) ) {
					$errors[$k] = sprintf(__('%s must not exceed %s characters','userpro'), $userpro->field_label($k), userpro_get_option('max_field_length'));
				}
			}
                        
                        if ( isset($min_field_length_include)) {
				if ( $userpro->field_exists($k) && $userpro->field_type($k) == 'text' && $v != '' && strlen($v) < userpro_get_option('min_field_length') && $userpro->field_label($k) && in_array($k, $min_field_length_include ) ) {
					$errors[$k] = sprintf(__('Minimum %s characters are required for %s','userpro'), userpro_get_option('min_field_length'),$userpro->field_label($k));
				}
			}
		}
		
		return $errors;
	}
		/**
		  * Security Question Answer Form Check Start
		  * By Rahul
		  * On 21 NOV 2014
		  */
		add_filter('userpro_login_validation', 'userpro_security_check', 10, 2);
		add_filter('userpro_register_validation', 'userpro_security_check', 10, 2);
		add_filter('userpro_form_validation', 'userpro_security_check', 10, 2);
		function userpro_security_check($errors, $form) {
				global $userpro;
			
				$array = get_option("userpro_fields_groups");
				if(isset($array[$form['template']]['default']['securityqa'])) {
					$array = $array[$form['template']]['default']['securityqa'];
					$questionAnswers = explode("\n", $array['security_qa']);
					if (isset($form['securityqa']) && isset($form['securitykey'])) {
						$questionAnswer = $questionAnswers[$form['securitykey']];
						$answer = explode(':', $questionAnswer);
						if (strcasecmp($form['securityqa'] , trim(preg_replace('/\s\s+/', ' ', $answer[1])))) {
							
							$errors['securityqa'] = __('Incorrect Answer. please try again.','userpro');
						}
					}
				}
				return $errors;
		}
		/**
		 * Security Question Answer Form Check End
		 * By Rahul
		 * On 21 NOV 2014
		 */
	/* Antispam check on forms */
	add_filter('userpro_login_validation', 'userpro_antispam_check', 10, 2);
	add_filter('userpro_register_validation', 'userpro_antispam_check', 10, 2);
	add_filter('userpro_form_validation', 'userpro_antispam_check', 10, 2);
	function userpro_antispam_check($errors, $form) {
		
		if (isset($form['antispam'])) {
			if ( $form['antispam'] != $form['answer'] ){ 
				$errors['antispam'] = __('Incorrect answer. please try again.','userpro');
			}
		}
		
		return $errors;
	}

	/* Display a dropdown filtered list */
	function userpro_filter_to_array($filter) {
		switch($filter) {
		
			/* user roles allowed */
			case 'roles':
				$array = array('');
				break;
			
			/* list of countries */
			case 'country':
				$array = array(
					''   => '',
					'AF' => 'Afghanistan',
					'AX' => 'Åland Islands',
					'AL' => 'Albania',
					'DZ' => 'Algeria',
					'AS' => 'American Samoa',
					'AD' => 'Andorra',
					'AO' => 'Angola',
					'AI' => 'Anguilla',
					'AQ' => 'Antarctica',
					'AG' => 'Antigua and Barbuda',
					'AR' => 'Argentina',
					'AM' => 'Armenia',
					'AW' => 'Aruba',
					'AU' => 'Australia',
					'AT' => 'Austria',
					'AZ' => 'Azerbaijan',
					'BS' => 'Bahamas',
					'BH' => 'Bahrain',
					'BD' => 'Bangladesh',
					'BB' => 'Barbados',
					'BY' => 'Belarus',
					'BE' => 'Belgium',
					'BZ' => 'Belize',
					'BJ' => 'Benin',
					'BM' => 'Bermuda',
					'BT' => 'Bhutan',
					'BO' => 'Bolivia, Plurinational State of',
					'BQ' => 'Bonaire, Sint Eustatius and Saba',
					'BA' => 'Bosnia and Herzegovina',
					'BW' => 'Botswana',
					'BV' => 'Bouvet Island',
					'BR' => 'Brazil',
					'IO' => 'British Indian Ocean Territory',
					'BN' => 'Brunei Darussalam',
					'BG' => 'Bulgaria',
					'BF' => 'Burkina Faso',
					'BI' => 'Burundi',
					'KH' => 'Cambodia',
					'CM' => 'Cameroon',
					'CA' => 'Canada',
					'CV' => 'Cape Verde',
					'KY' => 'Cayman Islands',
					'CF' => 'Central African Republic',
					'TD' => 'Chad',
					'CL' => 'Chile',
					'CN' => 'China',
					'CX' => 'Christmas Island',
					'CC' => 'Cocos (Keeling) Islands',
					'CO' => 'Colombia',
					'KM' => 'Comoros',
					'CG' => 'Congo',
					'CD' => 'Congo, the Democratic Republic of the',
					'CK' => 'Cook Islands',
					'CR' => 'Costa Rica',
					'CI' => 'Côte d\'Ivoire',
					'HR' => 'Croatia',
					'CU' => 'Cuba',
					'CW' => 'Curaçao',
					'CY' => 'Cyprus',
					'CZ' => 'Czech Republic',
					'DK' => 'Denmark',
					'DJ' => 'Djibouti',
					'DM' => 'Dominica',
					'DO' => 'Dominican Republic',
					'EC' => 'Ecuador',
					'EG' => 'Egypt',
					'SV' => 'El Salvador',
					'GQ' => 'Equatorial Guinea',
					'ER' => 'Eritrea',
					'EE' => 'Estonia',
					'ET' => 'Ethiopia',
					'FK' => 'Falkland Islands (Malvinas)',
					'FO' => 'Faroe Islands',
					'FJ' => 'Fiji',
					'FI' => 'Finland',
					'FR' => 'France',
					'GF' => 'French Guiana',
					'PF' => 'French Polynesia',
					'TF' => 'French Southern Territories',
					'GA' => 'Gabon',
					'GM' => 'Gambia',
					'GE' => 'Georgia',
					'DE' => 'Germany',
					'GH' => 'Ghana',
					'GI' => 'Gibraltar',
					'GR' => 'Greece',
					'GL' => 'Greenland',
					'GD' => 'Grenada',
					'GP' => 'Guadeloupe',
					'GU' => 'Guam',
					'GT' => 'Guatemala',
					'GG' => 'Guernsey',
					'GN' => 'Guinea',
					'GW' => 'Guinea-Bissau',
					'GY' => 'Guyana',
					'HT' => 'Haiti',
					'HM' => 'Heard Island and McDonald Islands',
					'VA' => 'Holy See (Vatican City State)',
					'HN' => 'Honduras',
					'HK' => 'Hong Kong',
					'HU' => 'Hungary',
					'IS' => 'Iceland',
					'IN' => 'India',
					'ID' => 'Indonesia',
					'IR' => 'Iran, Islamic Republic of',
					'IQ' => 'Iraq',
					'IE' => 'Ireland',
					'IM' => 'Isle of Man',
					'IL' => 'Israel',
					'IT' => 'Italy',
					'JM' => 'Jamaica',
					'JP' => 'Japan',
					'JE' => 'Jersey',
					'JO' => 'Jordan',
					'KZ' => 'Kazakhstan',
					'KE' => 'Kenya',
					'KI' => 'Kiribati',
					'KP' => 'Korea, Democratic People\'s Republic of',
					'KR' => 'Korea, Republic of',
					'KW' => 'Kuwait',
					'KG' => 'Kyrgyzstan',
					'LA' => 'Lao People\'s Democratic Republic',
					'LV' => 'Latvia',
					'LB' => 'Lebanon',
					'LS' => 'Lesotho',
					'LR' => 'Liberia',
					'LY' => 'Libya',
					'LI' => 'Liechtenstein',
					'LT' => 'Lithuania',
					'LU' => 'Luxembourg',
					'MO' => 'Macao',
					'MK' => 'Macedonia, The Former Yugoslav Republic of',
					'MG' => 'Madagascar',
					'MW' => 'Malawi',
					'MY' => 'Malaysia',
					'MV' => 'Maldives',
					'ML' => 'Mali',
					'MT' => 'Malta',
					'MH' => 'Marshall Islands',
					'MQ' => 'Martinique',
					'MR' => 'Mauritania',
					'MU' => 'Mauritius',
					'YT' => 'Mayotte',
					'MX' => 'Mexico',
					'FM' => 'Micronesia, Federated States of',
					'MD' => 'Moldova, Republic of',
					'MC' => 'Monaco',
					'MN' => 'Mongolia',
					'ME' => 'Montenegro',
					'MS' => 'Montserrat',
					'MA' => 'Morocco',
					'MZ' => 'Mozambique',
					'MM' => 'Myanmar',
					'NA' => 'Namibia',
					'NR' => 'Nauru',
					'NP' => 'Nepal',
					'NL' => 'Netherlands',
					'NC' => 'New Caledonia',
					'NZ' => 'New Zealand',
					'NI' => 'Nicaragua',
					'NE' => 'Niger',
					'NG' => 'Nigeria',
					'NU' => 'Niue',
					'NF' => 'Norfolk Island',
					'MP' => 'Northern Mariana Islands',
					'NO' => 'Norway',
					'OM' => 'Oman',
					'PK' => 'Pakistan',
					'PW' => 'Palau',
					'PS' => 'Palestinian Territory',
					'PA' => 'Panama',
					'PG' => 'Papua New Guinea',
					'PY' => 'Paraguay',
					'PE' => 'Peru',
					'PH' => 'Philippines',
					'PN' => 'Pitcairn',
					'PL' => 'Poland',
					'PT' => 'Portugal',
					'PR' => 'Puerto Rico',
					'QA' => 'Qatar',
					'RE' => 'Réunion',
					'RO' => 'Romania',
					'RU' => 'Russian Federation',
					'RW' => 'Rwanda',
					'BL' => 'Saint Barthélemy',
					'SH' => 'Saint Helena, Ascension and Tristan da Cunha',
					'KN' => 'Saint Kitts and Nevis',
					'LC' => 'Saint Lucia',
					'MF' => 'Saint Martin (French part)',
					'PM' => 'Saint Pierre and Miquelon',
					'VC' => 'Saint Vincent and the Grenadines',
					'WS' => 'Samoa',
					'SM' => 'San Marino',
					'ST' => 'Sao Tome and Principe',
					'SA' => 'Saudi Arabia',
					'scotland' => 'Scotland',
					'SN' => 'Senegal',
					'RS' => 'Serbia',
					'SC' => 'Seychelles',
					'SL' => 'Sierra Leone',
					'SG' => 'Singapore',
					'SX' => 'Sint Maarten (Dutch part)',
					'SK' => 'Slovakia',
					'SI' => 'Slovenia',
					'SB' => 'Solomon Islands',
					'SO' => 'Somalia',
					'ZA' => 'South Africa',
					'GS' => 'South Georgia and the South Sandwich Islands',
					'SS' => 'South Sudan',
					'ES' => 'Spain',
					'LK' => 'Sri Lanka',
					'SD' => 'Sudan',
					'SR' => 'Suriname',
					'SJ' => 'Svalbard and Jan Mayen',
					'SZ' => 'Swaziland',
					'SE' => 'Sweden',
					'CH' => 'Switzerland',
					'SY' => 'Syrian Arab Republic',
					'TW' => 'Taiwan, Province of China',
					'TJ' => 'Tajikistan',
					'TZ' => 'Tanzania, United Republic of',
					'TH' => 'Thailand',
					'TL' => 'Timor-Leste',
					'TG' => 'Togo',
					'TK' => 'Tokelau',
					'TO' => 'Tonga',
					'TT' => 'Trinidad and Tobago',
					'TN' => 'Tunisia',
					'TR' => 'Turkey',
					'TM' => 'Turkmenistan',
					'TC' => 'Turks and Caicos Islands',
					'TV' => 'Tuvalu',
					'UG' => 'Uganda',
					'UA' => 'Ukraine',
					'AE' => 'United Arab Emirates',
					'UK' => 'United Kingdom',
					'US' => 'United States',
					'UM' => 'United States Minor Outlying Islands',
					'UY' => 'Uruguay',
					'UZ' => 'Uzbekistan',
					'VU' => 'Vanuatu',
					'VE' => 'Venezuela, Bolivarian Republic of',
					'VN' => 'Viet Nam',
					'VG' => 'Virgin Islands, British',
					'VI' => 'Virgin Islands, U.S.',
					'WF' => 'Wallis and Futuna',
					'EH' => 'Western Sahara',
					'YE' => 'Yemen',
					'ZM' => 'Zambia',
					'ZW' => 'Zimbabwe'
				);
				break;
		
		}
		return $array = apply_filters( 'userpro_filter_dropdown_list', $array, $filter );
	}
// Get countries and loop through them to add translation support
$countriesTranslate = userpro_filter_to_array( 'country' );

foreach( $countriesTranslate as $countryTranslate ) :
    $temp = __( $countryTranslate, 'userpro' );
endforeach;