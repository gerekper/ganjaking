<?php

class PAFE_Form_Builder_Field extends \Elementor\Widget_Base {

	public function get_name() {
		return 'pafe-form-builder-field';
	}

	public function get_title() {
		return __( 'Field', 'pafe' );
	}

	public function get_icon() {
		return 'icon-w-field';
	}

	public function get_categories() {
		return [ 'pafe-form-builder' ];
	}

	public function get_keywords() {
		return [ 'input', 'form', 'field' ];
	}

	public function get_script_depends() {
		return [ 
			'pafe-form-builder',
		];
	}

	public function get_style_depends() {
		return [ 
			'pafe-form-builder-style', 'elementor-icons'
		];
	}

	protected function _register_controls() {

		$field_types = [
			'text' => __( 'Text', 'pafe' ),
			'email' => __( 'Email', 'pafe' ),
			'textarea' => __( 'Textarea', 'pafe' ),
			'url' => __( 'URL', 'pafe' ),
			'tel' => __( 'Tel', 'pafe' ),
			'radio' => __( 'Radio', 'pafe' ),
			'select' => __( 'Select', 'pafe' ),
			'terms_select' => __( 'Terms Select', 'pafe' ),
			'image_select' => __( 'Image Select', 'pafe' ),
			'checkbox' => __( 'Checkbox', 'pafe' ),
			'acceptance' => __( 'Acceptance', 'pafe' ),
			'number' => __( 'Number', 'pafe' ),
			'date' => __( 'Date', 'pafe' ),
			'time' => __( 'Time', 'pafe' ),
			'image_upload' => __( 'Image Upload', 'pafe' ),
			'upload' => __( 'File Upload', 'pafe' ),
			'password' => __( 'Password', 'pafe' ),
			'html' => __( 'HTML', 'pafe' ),
			'hidden' => __( 'Hidden', 'pafe' ),
			'range_slider' => __( 'Range Slider', 'pafe' ),
			'coupon_code' => __( 'Coupon Code', 'pafe' ),
			'calculated_fields' => __( 'Calculated Fields', 'pafe' ),
			'stripe_payment' => __( 'Stripe Payment', 'pafe' ),
			'honeypot' => __( 'Honeypot', 'pafe' ),
			'color' => __( 'Color Picker', 'pafe' ),
			'iban' => __( 'Iban', 'pafe' ),
			'confirm' => __( 'Confirm', 'pafe' ),
		];

		if( get_option( 'pafe-features-submit-post', 2 ) == 2 || get_option( 'pafe-features-submit-post', 2 ) == 1 ) {
			$field_types['tinymce'] = __('TinyMCE', 'pafe');
		}

		if( get_option( 'pafe-features-select-autocomplete-field', 2 ) == 2 || get_option( 'pafe-features-select-autocomplete-field', 2 ) == 1 ) {
			$field_types['select_autocomplete'] = __( 'Select Autocomplete', 'pafe' );
		}

		if( get_option( 'pafe-features-address-autocomplete-field', 2 ) == 2 || get_option( 'pafe-features-address-autocomplete-field', 2 ) == 1 ) {
			$field_types['address_autocomplete'] = __( 'Address Autocomplete', 'pafe' );
		}

		if( get_option( 'pafe-features-signature-field', 2 ) == 2 || get_option( 'pafe-features-signature-field', 2 ) == 1 ) {
			$field_types['signature'] = __( 'Signature', 'pafe' );
		}

		$this->start_controls_section(
			'section_general',
			[
				'label' => __( 'General', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$pafe_forms = get_post_type() == 'pafe-forms' ? true : false;

		$this->add_control(
			'form_id',
			[
				'label' => __( 'Form ID* (Required)', 'pafe' ),
				'type' => $pafe_forms ? \Elementor\Controls_Manager::HIDDEN : \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Enter the same form id for all fields in a form, with latin character and no space. E.g order_form', 'pafe' ),
				'render_type' => 'none',
				'default' => $pafe_forms ? get_the_ID() : '',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'field_type',
			[
				'label' => __( 'Type', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $field_types,
				'default' => 'text',
				'description' => 'TinyMCE only works on the frontend.'
			]
		);

		$this->add_control(
			'field_id',
			[
				'label' => __( 'Field ID* (Required)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'description' => __( 'Field ID have to be unique in a form, with latin character and no space, no number. Please do not enter Field ID = product. E.g your_field_id', 'pafe' ),
				'render_type' => 'none',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'confirm_type',
			[
				'label' => esc_html__( 'Confirm Type', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'text',
				'options' => [
					'text'  => esc_html__( 'Text', 'pafe' ),
					'email' => esc_html__( 'Email', 'pafe' ),
					'textarea' => esc_html__( 'Textarea', 'pafe' ),
					'url' => esc_html__( 'Url', 'pafe' ),
					'tel' => esc_html__( 'Tel', 'pafe' ),
				],
				'condition' => [
					'field_type' => 'confirm'
				]
			]
		);

		$this->add_control(
			'confirm_field_name',
			[
				'label' => esc_html__( 'Confirm Field ID*', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Type confirm field ID here', 'pafe' ),
				'condition' => [
					'field_type' => 'confirm'
				]
			]
		);

		$this->add_control(
			'confirm_error_msg',
			[
				'label' => esc_html__( 'Confirm error messenger ', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Type confirm error messenger here', 'pafe' ),
				'default' => "Field don't match",
				'condition' => [
					'field_type' => 'confirm'
				]
			]
		);

		$this->add_control(
			'field_type_repassword',
			[
				'label' => __( 'Is Field Repassword?', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'field_type' => 'password'
				]
			]
		);
		$this->add_control(
			'field_type_password_shortcode',
			[
				'label' => __( 'Field Password ID', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Type your password id here', 'pafe' ),
				'description' => __( 'Enter the password ID to compare.', 'pafe' ),
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => '==',
							'value' => 'password'
						],
						[
							'name' => 'field_type_repassword',
							'operator' => '==',
							'value' => 'yes'
						]
					]
				]
			]
		);
		$this->add_control(
			'msg_password_dont_match',
			[
				'label' => __( "Msg Passwords Don't Match", 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Type your msg', 'pafe' ),
				'description' => __( 'Enter the password ID to compare.', 'pafe' ),
				'default' => __( "Passwords Don't Match", 'pafe' ),
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => '==',
							'value' => 'password'
						],
						[
							'name' => 'field_type_repassword',
							'operator' => '==',
							'value' => 'yes'
						]
					]
				]
			]
		);
		$this->add_control(
			'field_type_show_password_options',
			[
				'label' => __( 'Show Password Icon?', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'field_type' => 'password'
				]
			]
		);

		if( get_option( 'pafe-features-address-autocomplete-field', 2 ) == 2 || get_option( 'pafe-features-address-autocomplete-field', 2 ) == 1 ) {
			$this->add_control(
				'google_maps',
				[
					'label' => __( 'Google Maps', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'description' => __( 'This feature only works on the frontend.', 'pafe' ),
					'label_on' => __( 'Show', 'elementor-pro' ),
					'label_off' => __( 'Hide', 'elementor-pro' ),
					'default' => '',
					'condition' => [
						'field_type' => 'address_autocomplete',
					],
				]
			);

			$this->add_control(
				'country',
				[
					'label' => __( 'Country', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT2,
					'multiple' => true,
					'description' => __( 'Choose your country.', 'pafe' ),
					'default' => 'All',
					'options' => [
					'All'=>__('All','pafe'),
					"AF" => "Afghanistan",
					"AX" => "Åland Islands",
					"AL" => "Albania",
					"DZ" => "Algeria",
					"AS" => "American Samoa",
					"AD" => "Andorra",
					"AO" => "Angola",
					"AI" => "Anguilla",
					"AQ" => "Antarctica",
					"AG" => "Antigua and Barbuda",
					"AR" => "Argentina",
					"AM" => "Armenia",
					"AW" => "Aruba",
					"AU" => "Australia",
					"AT" => "Austria",
					"AZ" => "Azerbaijan",
					"BS" => "Bahamas",
					"BH" => "Bahrain",
					"BD" => "Bangladesh",
					"BB" => "Barbados",
					"BY" => "Belarus",
					"BE" => "Belgium",
					"BZ" => "Belize",
					"BJ" => "Benin",
					"BM" => "Bermuda",
					"BT" => "Bhutan",
					"BO" => "Bolivia, Plurinational State of",
					"BQ" => "Bonaire, Sint Eustatius and Saba",
					"BA" => "Bosnia and Herzegovina",
					"BW" => "Botswana",
					"BV" => "Bouvet Island",
					"BR" => "Brazil",
					"IO" => "British Indian Ocean Territory",
					"BN" => "Brunei Darussalam",
					"BG" => "Bulgaria",
					"BF" => "Burkina Faso",
					"BI" => "Burundi",
					"KH" => "Cambodia",
					"CM" => "Cameroon",
					"CA" => "Canada",
					"CV" => "Cape Verde",
					"KY" => "Cayman Islands",
					"CF" => "Central African Republic",
					"TD" => "Chad",
					"CL" => "Chile",
					"CN" => "China",
					"CX" => "Christmas Island",
					"CC" => "Cocos (Keeling) Islands",
					"CO" => "Colombia",
					"KM" => "Comoros",
					"CG" => "Congo",
					"CD" => "Congo, the Democratic Republic of the",
					"CK" => "Cook Islands",
					"CR" => "Costa Rica",
					"CI" => "Côte d'Ivoire",
					"HR" => "Croatia",
					"CU" => "Cuba",
					"CW" => "Curaçao",
					"CY" => "Cyprus",
					"CZ" => "Czech Republic",
					"DK" => "Denmark",
					"DJ" => "Djibouti",
					"DM" => "Dominica",
					"DO" => "Dominican Republic",
					"EC" => "Ecuador",
					"EG" => "Egypt",
					"SV" => "El Salvador",
					"GQ" => "Equatorial Guinea",
					"ER" => "Eritrea",
					"EE" => "Estonia",
					"ET" => "Ethiopia",
					"FK" => "Falkland Islands (Malvinas)",
					"FO" => "Faroe Islands",
					"FJ" => "Fiji",
					"FI" => "Finland",
					"FR" => "France",
					"GF" => "French Guiana",
					"PF" => "French Polynesia",
					"TF" => "French Southern Territories",
					"GA" => "Gabon",
					"GM" => "Gambia",
					"GE" => "Georgia",
					"DE" => "Germany",
					"GH" => "Ghana",
					"GI" => "Gibraltar",
					"GR" => "Greece",
					"GL" => "Greenland",
					"GD" => "Grenada",
					"GP" => "Guadeloupe",
					"GU" => "Guam",
					"GT" => "Guatemala",
					"GG" => "Guernsey",
					"GN" => "Guinea",
					"GW" => "Guinea-Bissau",
					"GY" => "Guyana",
					"HT" => "Haiti",
					"HM" => "Heard Island and McDonald Islands",
					"VA" => "Holy See (Vatican City State)",
					"HN" => "Honduras",
					"HK" => "Hong Kong",
					"HU" => "Hungary",
					"IS" => "Iceland",
					"IN" => "India",
					"ID" => "Indonesia",
					"IR" => "Iran, Islamic Republic of",
					"IQ" => "Iraq",
					"IE" => "Ireland",
					"IM" => "Isle of Man",
					"IL" => "Israel",
					"IT" => "Italy",
					"JM" => "Jamaica",
					"JP" => "Japan",
					"JE" => "Jersey",
					"JO" => "Jordan",
					"KZ" => "Kazakhstan",
					"KE" => "Kenya",
					"KI" => "Kiribati",
					"KP" => "Korea, Democratic People's Republic of",
					"KR" => "Korea, Republic of",
					"KW" => "Kuwait",
					"KG" => "Kyrgyzstan",
					"LA" => "Lao People's Democratic Republic",
					"LV" => "Latvia",
					"LB" => "Lebanon",
					"LS" => "Lesotho",
					"LR" => "Liberia",
					"LY" => "Libya",
					"LI" => "Liechtenstein",
					"LT" => "Lithuania",
					"LU" => "Luxembourg",
					"MO" => "Macao",
					"MK" => "Macedonia, the former Yugoslav Republic of",
					"MG" => "Madagascar",
					"MW" => "Malawi",
					"MY" => "Malaysia",
					"MV" => "Maldives",
					"ML" => "Mali",
					"MT" => "Malta",
					"MH" => "Marshall Islands",
					"MQ" => "Martinique",
					"MR" => "Mauritania",
					"MU" => "Mauritius",
					"YT" => "Mayotte",
					"MX" => "Mexico",
					"FM" => "Micronesia, Federated States of",
					"MD" => "Moldova, Republic of",
					"MC" => "Monaco",
					"MN" => "Mongolia",
					"ME" => "Montenegro",
					"MS" => "Montserrat",
					"MA" => "Morocco",
					"MZ" => "Mozambique",
					"MM" => "Myanmar",
					"NA" => "Namibia",
					"NR" => "Nauru",
					"NP" => "Nepal",
					"NL" => "Netherlands",
					"NC" => "New Caledonia",
					"NZ" => "New Zealand",
					"NI" => "Nicaragua",
					"NE" => "Niger",
					"NG" => "Nigeria",
					"NU" => "Niue",
					"NF" => "Norfolk Island",
					"MP" => "Northern Mariana Islands",
					"NO" => "Norway",
					"OM" => "Oman",
					"PK" => "Pakistan",
					"PW" => "Palau",
					"PS" => "Palestinian Territory, Occupied",
					"PA" => "Panama",
					"PG" => "Papua New Guinea",
					"PY" => "Paraguay",
					"PE" => "Peru",
					"PH" => "Philippines",
					"PN" => "Pitcairn",
					"PL" => "Poland",
					"PT" => "Portugal",
					"PR" => "Puerto Rico",
					"QA" => "Qatar",
					"RE" => "Réunion",
					"RO" => "Romania",
					"RU" => "Russian Federation",
					"RW" => "Rwanda",
					"BL" => "Saint Barthélemy",
					"SH" => "Saint Helena, Ascension and Tristan da Cunha",
					"KN" => "Saint Kitts and Nevis",
					"LC" => "Saint Lucia",
					"MF" => "Saint Martin (French part)",
					"PM" => "Saint Pierre and Miquelon",
					"VC" => "Saint Vincent and the Grenadines",
					"WS" => "Samoa",
					"SM" => "San Marino",
					"ST" => "Sao Tome and Principe",
					"SA" => "Saudi Arabia",
					"SN" => "Senegal",
					"RS" => "Serbia",
					"SC" => "Seychelles",
					"SL" => "Sierra Leone",
					"SG" => "Singapore",
					"SX" => "Sint Maarten (Dutch part)",
					"SK" => "Slovakia",
					"SI" => "Slovenia",
					"SB" => "Solomon Islands",
					"SO" => "Somalia",
					"ZA" => "South Africa",
					"GS" => "South Georgia and the South Sandwich Islands",
					"SS" => "South Sudan",
					"ES" => "Spain",
					"LK" => "Sri Lanka",
					"SD" => "Sudan",
					"SR" => "Suriname",
					"SJ" => "Svalbard and Jan Mayen",
					"SZ" => "Swaziland",
					"SE" => "Sweden",
					"CH" => "Switzerland",
					"SY" => "Syrian Arab Republic",
					"TW" => "Taiwan, Province of China",
					"TJ" => "Tajikistan",
					"TZ" => "Tanzania, United Republic of",
					"TH" => "Thailand",
					"TL" => "Timor-Leste",
					"TG" => "Togo",
					"TK" => "Tokelau",
					"TO" => "Tonga",
					"TT" => "Trinidad and Tobago",
					"TN" => "Tunisia",
					"TR" => "Turkey",
					"TM" => "Turkmenistan",
					"TC" => "Turks and Caicos Islands",
					"TV" => "Tuvalu",
					"UG" => "Uganda",
					"UA" => "Ukraine",
					"AE" => "United Arab Emirates",
					"GB" => "United Kingdom",
					"US" => "United States",
					"UM" => "United States Minor Outlying Islands",
					"UY" => "Uruguay",
					"UZ" => "Uzbekistan",
					"VU" => "Vanuatu",
					"VE" => "Venezuela, Bolivarian Republic of",
					"VN" => "Viet Nam",
					"VG" => "Virgin Islands, British",
					"VI" => "Virgin Islands, U.S.",
					"WF" => "Wallis and Futuna",
					"EH" => "Western Sahara",
					"YE" => "Yemen",
					"ZM" => "Zambia",
					"ZW" => "Zimbabwe",
				],
					'condition' => [
						'field_type' => 'address_autocomplete',
					],
				]
			);

			$this->add_control(
				'google_maps_lat',
				[
					'label' => __( 'Latitude', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'placeholder' => '21.028511',
					'description' => __( 'Latitude and Longitude Finder https://www.latlong.net/', 'pafe' ),
					'default' => '21.028511',
					'condition' => [
						'field_type' => 'address_autocomplete',
						'google_maps!' => '',
					],
				
				]
			);

			$this->add_control(
				'google_maps_lng',
				[
					'label' => __( 'Longitude', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'placeholder' => '105.804817',
					'description' => __( 'Latitude and Longitude Finder https://www.latlong.net/', 'pafe' ),
					'default' => '105.804817',
					'separator' => true,
					'condition' => [
						'field_type' => 'address_autocomplete',
						'google_maps!' => '',
					],
				
				]
			);

			$this->add_control(
				'google_maps_zoom',
				[
					'label' => __( 'Zoom', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'default' => [
						'size' => 15,
					],
					'range' => [
						'px' => [
							'min' => 1,
							'max' => 25,
						],
				 	],
				 	'condition' => [
						'field_type' => 'address_autocomplete',
						'google_maps!' => '',
					],
				]
		    );

			$this->add_responsive_control(
				'google_maps_height',
				[
					'label' => __( 'Height', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 1000,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 200,
					],
					'selectors' => [
						'{{WRAPPER}} .pafe-form-builder-address-autocomplete-map' => 'height:{{SIZE}}{{UNIT}}',
					],
					'condition' => [
						'field_type' => 'address_autocomplete',
						'google_maps!' => '',
					],
				]
			);
		}

		if( get_option( 'pafe-features-signature-field', 2 ) == 2 || get_option( 'pafe-features-signature-field', 2 ) == 1 ) {

			$this->add_control(
				'signature_clear_text',
				[
					'label' => __( 'Clear Text', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'Clear', 'pafe' ),
					'condition' => [
						'field_type' => 'signature',
					],
				]
			);

			$this->add_responsive_control(
				'signature_max_width',
				[
					'label' => __( 'Max Width', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 2000,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 400,
					],
					'selectors' => [
						'{{WRAPPER}} canvas' => 'max-width:{{SIZE}}{{UNIT}}',
					],
					'condition' => [
						'field_type' => 'signature',
					],
				]
			);

			$this->add_responsive_control(
				'signature_height',
				[
					'label' => __( 'Height', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 1000,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 200,
					],
					'selectors' => [
						'{{WRAPPER}} canvas' => 'height:{{SIZE}}{{UNIT}}',
					],
					'condition' => [
						'field_type' => 'signature',
					],
				]
			);
		}

		$this->add_control(
			'field_label',
			[
				'label' => __( 'Label', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'field_label_show',
			[
				'label' => __( 'Show Label', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'elementor-pro' ),
				'label_off' => __( 'Hide', 'elementor-pro' ),
				'return_value' => 'true',
				'default' => 'true',
				'condition' => [
					'field_type!' => 'html',
				],
			]
		);
		$this->add_control(
			'field_label_inline',
			[
				'label' => __( 'Inline Label', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'true',
				'default' => '',
			]
		);
		$this->add_control(
			'field_label_inline_width',
			[
				'label' => __( 'Label Width', 'pafe' ),
				'type' => Elementor\Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-label-inline' => 'width: {{SIZE}}%;',
					'{{WRAPPER}} .pafe-field-inline' => 'width: calc(100% - {{SIZE}}%)',
				],
				'condition' => [
					'field_label_inline' => 'true'
				]
			]
		);
		$this->add_control(
			'field_placeholder',
			[
				'label' => __( 'Placeholder', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'tel',
								'text',
								'email',
								'textarea',
								'number',
								'url',
								'password',
								'select_autocomplete',
								'address_autocomplete',
								'date',
								'time',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'file_sizes',
			[
				'label' => __( 'Max. File Size', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'condition' => [
					'field_type' => 'upload',
				],
				'options' => $this->get_upload_file_size_options(),
				'description' => __( 'If you need to increase max upload size please contact your hosting.', 'pafe' ),
			]
		);

		$this->add_control(
			'file_sizes_message',
			[
				'label' => __( 'Max. File Size Error Message', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'File size must be less than 1MB', 'pafe' ),
				'condition' => [
					'field_type' => 'upload',
				],
			]
		);

		$this->add_control(
			'file_types',
			[
				'label' => __( 'Allowed File Types', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'field_type' => 'upload',
				],
				'description' => __( 'Enter the allowed file types, separated by a comma (jpg, gif, pdf, etc).', 'pafe' ),
			]
		);

		$this->add_control(
			'file_types_message',
			[
				'label' => __( 'Allowed File Types Error Message', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Please enter a value with a valid mimetype.', 'pafe' ),
				'condition' => [
					'field_type' => 'upload',
				],
			]
		);

		$this->add_control(
			'allow_multiple_upload',
			[
				'label' => __( 'Multiple Files', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'upload',
								'image_upload',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'max_files',
			[
				'label' => __( 'Max Files', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'condition' => [
					'field_type' => 'image_upload',
					'allow_multiple_upload' => 'true',
				],
			]
		);

        $this->add_control(
            'min_files',
            [
                'label' => __( 'Min Files', 'pafe' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'condition' => [
                    'field_type' => 'image_upload',
                    'allow_multiple_upload' => 'true',
                ],
            ]
        );

        $this->add_control(
            'min_files_message',
            [
                'label' => __( 'Min Files Message', 'pafe' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __( 'Please upload the minimum number of images.', 'pafe' ),
                'placeholder' => __( 'Please upload the minimum number of images.', 'pafe' ),
                'label_block' => true,
                'render_type' => 'none',
                'condition' => [
                    'field_type' => 'image_upload',
                    'allow_multiple_upload' => 'true',
                ],
            ]
        );

		// $this->add_control(
		// 	'max_files' => [
		// 		'label' => __( 'Max. Files', 'pafe' ),
		// 		'type' => \Elementor\Controls_Manager::NUMBER,
		// 		'condition' => [
		// 			'field_type' => 'upload',
		// 			'allow_multiple_upload' => 'yes',
		// 		],
		// 		'tab' => 'content',
		// 		'inner_tab' => 'form_fields_content_tab',
		// 		'tabs_wrapper' => 'form_fields_tabs',
		// 	],
		// );

		$this->add_control(
			'attach_files',
			[
				'label' => __( 'Attach files to email, not upload to uploads folder', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'condition' => [
					'field_type' => 'upload',
				],
			]
		);

		$this->add_control(
			'field_required',
			[
				'label' => __( 'Required', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default' => '',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => '!in',
							'value' => [
								'recaptcha',
								'hidden',
								'html',
								'honeypot',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'mark_required',
			[
				'label' => __( 'Required Mark', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'elementor-pro' ),
				'label_off' => __( 'Hide', 'elementor-pro' ),
				'default' => '',
				'conditions'   => [
					'terms' => [
						[
							'name'     => 'field_label',
							'operator' => '!=',
							'value'    => '',
						],
						[
							'name'     => 'field_required',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
			]
		);

		$this->add_control(
			'field_options',
			[
				'label' => __( 'Options', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => '',
				'dynamic' => [
					'active' => true,
				],
				'description' => __( 'Enter each option in a separate line. To differentiate between label and value, separate them with a pipe char ("|"). For example: First Name|f_name.<br>Select option group:<br>[optgroup label="Swedish Cars"]<br>Volvo|volvo<br>Saab|saab<br>[/optgroup]<br>[optgroup label="German Cars"]<br>Mercedes|mercedes<br>Audi|audi<br>[/optgroup]<br><br>The get posts shortcode for ACF Relationship Field [pafe_get_posts post_type="post" value="id"]', 'pafe' ),
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'select',
								'select_autocomplete',
								'image_select',
								'checkbox',
								'radio',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'send_data_by_label',
			[
				'label' => __( 'Send data by Label', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'select',
								'image_select',
								'terms_select',
								'checkbox',
								'radio',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'payment_methods_select_field_enable',
			[
				'label' => __( 'Payment Methods Select Field', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'description' => __( 'If you have multiple payment methods', 'pafe' ),
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'select',
								'image_select',
								'terms_select',
								'checkbox',
								'radio',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'payment_methods_select_field_value_for_stripe',
			[
				'label' => __( 'Payment Methods Field Value For Stripe', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => 'E.g Stripe',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'select',
								'image_select',
								'terms_select',
								'checkbox',
								'radio',
							],
						],
						[
							'name' => 'payment_methods_select_field_enable',
							'operator' => '=',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'payment_methods_select_field_value_for_paypal',
			[
				'label' => __( 'Payment Methods Field Value For Paypal', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => 'E.g Paypal',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'select',
								'image_select',
								'terms_select',
								'checkbox',
								'radio',
							],
						],
						[
							'name' => 'payment_methods_select_field_enable',
							'operator' => '=',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'stripe_heading',
			[
				'label' => __( 'Style', 'pafe' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'field_type' => 'stripe_payment'
				]
			]
		);
		$this->add_control(
			'stripe_icon_color',
			[
				'label' => __( 'Icon Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'value' => 'stripe_payment',
						],
						[
							'name' => 'stripe_custom_style_enable',
							'value' => '',
						]
					],
				],
			]
		);
		$this->add_control(
			'stripe_background_color',
			[
				'label' => __( 'Background Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'value' => 'stripe_payment',
						],
						[
							'name' => 'stripe_custom_style_enable',
							'value' => '',
						]
					],
				],
			]
		);
		$this->add_control(
			'stripe_color',
			[
				'label' => __( 'Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'value' => 'stripe_payment',
						],
						[
							'name' => 'stripe_custom_style_enable',
							'value' => '',
						]
					],
				],
			]
		);
		$this->add_control(
			'stripe_placeholder_color',
			[
				'label' => __( 'Placeholder Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'value' => 'stripe_payment',
						],
						[
							'name' => 'stripe_custom_style_enable',
							'value' => '',
						]
					],
				],
			]
		);
		$this->add_control(
			'stripe_font_size',
			[
				'label' => __( 'Font Size', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 16,
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'value' => 'stripe_payment',
						],
						[
							'name' => 'stripe_custom_style_enable',
							'value' => '',
						]
					],
				],
			]
		);
		$this->add_control(
			'stripe_custom_style_enable',
			[
				'label' => __( 'Custom Style?', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'field_type' => 'stripe_payment'
				]
			]
		);
		$this->add_control(
			'stripe_custom_font_family',
			[
				'label' => __( 'URL Font', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => 'E.g: https://fonts.googleapis.com/css2?family=Condiment&display=swap',
				'condition' => [
					'stripe_custom_style_enable' => 'yes',
					'field_type' => 'stripe_payment'
				]
			]
		);
		$this->add_control(
			'stripe_custom_style',
			[
				'label' => __( 'Custom Style', 'pafe' ),
				'type' => \Elementor\Controls_Manager::CODE,
				'language' => 'html',
				'default' => '{"base":{"color":"#303238","fontSize":"16px","fontFamily":"\"Open Sans\", sans-serif","fontSmoothing":"antialiased","::placeholder":{"color":"#CFD7DF"}},"invalid":{"color":"#e5424d",":focus":{"color":"#303238"}}}',
				'description' => __( 'View options at <a target="_blank" href="https://stripe.com/docs/js/appendix/style">stripe style</a>', 'pafe' ),
				'rows' => 20,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'value' => 'stripe_payment',
						],
						[
							'name' => 'stripe_custom_style_enable',
							'value' => 'yes',
						]
					],
				],
			]
		);
		$this->add_control(
			'field_taxonomy_slug',
			[
				'label' => __( 'Taxonomy Slug', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'category', 'pafe' ),
				'description' => __('E.g: category, post_tag','pafe'),
				'condition' => [
					'field_type' => 'terms_select',
				],
			]
		);


		$this->add_control(
			'terms_select_type',
			[
				'label' => __( 'Terms Select Type', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'select',
				'options' => [
					'select' => __( 'Select', 'pafe' ),
					'select2' => __( 'Select 2', 'pafe' ),
					'autocomplete' => __( 'Select Autocomplete', 'pafe' ),
					'checkbox' => __( 'Checkbox', 'pafe' ),
					'radio' => __( 'Radio', 'pafe' ),
				],
				'condition' => [
					'field_type' => 'terms_select',
				],
			]
		);

		$this->add_control(
			'allow_multiple',
			[
				'label' => __( 'Multiple Selection', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'select',
								'select',
								'image_select',
								'terms_select',
								'select_autocomplete',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'limit_multiple',
			[
				'label' => __( 'Limit Multiple Selects', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 0,
				'condition' => [
					'field_type' => 'image_select',
					'allow_multiple' => 'true',
				],
			]
		);

        $this->add_control(
			'checkbox_limit_multiple',
			[
				'label' => __( 'Limit Multiple Selects', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 0,
				'condition' => [
					'field_type' => 'checkbox',
				],
			]
		);

		$this->add_control(
			'select_size',
			[
				'label' => __( 'Rows', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 2,
				'step' => 1,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'select',
								'select_autocomplete',
								'terms_select',
								'image_select',
							],
						],
						[
							'name' => 'allow_multiple',
							'value' => 'true',
						],
					],
				],
			]
		);

		$this->add_control(
			'inline_list',
			[
				'label' => __( 'Inline List', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'elementor-subgroup-inline',
				'default' => '',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'checkbox',
								'radio',
								'terms_select',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'field_html',
			[
				'label' => __( 'HTML', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'value' => 'html',
						],
					],
				],
			]
		);

		$this->add_control(
			'rows',
			[
				'label' => __( 'Rows', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 4,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'value' => 'textarea',
						],
					],
				],
			]
		);

        $this->add_control(
            'min_select',
            [
                'label'     => __( 'Min Select', 'pafe' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default'   => 0,
                'condition' => [
                    'field_type'     => 'image_select',
                    'allow_multiple' => 'true',
                ],
            ]
        );

        $this->add_control(
			'min_select_required_message',
			[
				'label' => __( 'Required Message', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Please select the minimum number of images.', 'pafe' ),
				'placeholder' => __( 'Please select the minimum number of images.', 'pafe' ),
				'label_block' => true,
				'render_type' => 'none',
				'condition' => [
                    'field_type'     => 'image_select',
                    'allow_multiple' => 'true',
                ],
			]
		);

		$this->add_control(
			'recaptcha_size',
			[
				'label' => __( 'Size', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => [
					'normal' => __( 'Normal', 'pafe' ),
					'compact' => __( 'Compact', 'pafe' ),
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'value' => 'recaptcha',
						],
					],
				],
			]
		);

		$this->add_control(
			'recaptcha_style',
			[
				'label' => __( 'Style', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'light',
				'options' => [
					'light' => __( 'Light', 'pafe' ),
					'dark' => __( 'Dark', 'pafe' ),
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'value' => 'recaptcha',
						],
					],
				],
			]
		);

		$this->add_control(
			'css_classes',
			[
				'label' => __( 'CSS Classes', 'pafe' ),
				'type' => \Elementor\Controls_Manager::HIDDEN,
				'default' => '',
				'title' => __( 'Add your custom class WITHOUT the dot. e.g: my-class', 'pafe' ),
			]
		);

		$this->add_control(
			'field_value',
			[
				'label' => __( 'Default Value', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'text',
								'email',
								'textarea',
								'url',
								'tel',
								'radio',
								'checkbox',
								'select',
								'select_autocomplete',
								'terms_select',
								'image_select',
								'number',
								'date',
								'time',
								'hidden',
								'address_autocomplete',
								'color',
							],
						],
					],
				],
				'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--pafe',
			]
		);
        $this->add_control(
			'tinymce_default_value',
			[
				'label' => esc_html__( 'Default Value', 'pafe' ),
				'type' => \Elementor\Controls_Manager::CODE,
				'language' => 'html',
                'render_type' => 'none',
				'rows' => 20,
                'condition' => [
                    'field_type' => 'tinymce'
                ],
                'description' => 'This feature only works on the frontend.'
			]
		);

        $this->add_control(
			'tinymce_preview_code',
			[
				'label' => esc_html__( 'Preview Code', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'pafe' ),
				'label_off' => esc_html__( 'No', 'pafe' ),
                'render_type' => 'none',
				'return_value' => 'yes',
				'default' => '',
                'condition' => [
                    'field_type' => 'tinymce'
                ],
			]
		);

		$this->add_control(
			'field_value_color_note',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'class' => 'elementor-control-field-description',
				'raw' => __('E.g: #000000. The value must be in seven-character hexadecimal notation.','pafe'),
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'color',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'field_min',
			[
				'name' => 'field_min',
				'label' => __( 'Min. Value', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'number',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'field_max',
			[
				'label' => __( 'Max. Value', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'number',
							],
						],
					],
				],
			]
		);

        $this->add_control(
			'field_step',
			[
				'label' => __( 'Step. Value', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
                'description' => __('Use dots to represent decimal places.', 'pafe'),
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'number',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'number_spiner',
			[
				'label' => __( 'Add (-/+) button', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'number',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'acceptance_text',
			[
				'label' => __( 'Acceptance Text', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'acceptance',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'checked_by_default',
			[
				'label' => __( 'Checked by Default', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'acceptance',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'flatpickr_custom_options_enable',
			[
				'label' => __( 'Flatpickr Custom Options', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'description' => 'https://flatpickr.js.org/examples/',
				'default' => '',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'date',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'flatpickr_custom_options',
			[
				'label' => __( 'Flatpickr Options', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'date',
							],
						],
						[
							'name' => 'flatpickr_custom_options_enable',
							'operator' => '==',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'date_range',
			[
				'label' => __( 'Date Range', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'true',
				'default' => '',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'date',
							],
						],
						[
							'name' => 'flatpickr_custom_options_enable',
							'operator' => '==',
							'value' => '',
						],
					],
				],
			]
		);

		$this->add_control(
			'min_date',
			[
				'label' => __( 'Min. Date', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::DATE_TIME,
				'label_block' => false,
				'picker_options' => [
					'enableTime' => false,
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'date',
							],
						],
						[
							'name' => 'flatpickr_custom_options_enable',
							'operator' => '==',
							'value' => '',
						],
					],
				],
			]
		);

		$this->add_control(
			'min_date_current',
			[
				'label' => __( 'Set Current Date for Min. Date', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'date',
							],
						],
						[
							'name' => 'flatpickr_custom_options_enable',
							'operator' => '==',
							'value' => '',
						],
					],
				],
			]
		);

		$this->add_control(
			'max_date',
			[
				'name' => 'max_date',
				'label' => __( 'Max. Date', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::DATE_TIME,
				'label_block' => false,
				'picker_options' => [
					'enableTime' => false,
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'date',
							],
						],
						[
							'name' => 'flatpickr_custom_options_enable',
							'operator' => '==',
							'value' => '',
						],
					],
				],
			]
		);

		$this->add_control(
			'max_date_current',
			[
				'label' => __( 'Set Current Date for Max. Date', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'date',
							],
						],
						[
							'name' => 'flatpickr_custom_options_enable',
							'operator' => '==',
							'value' => '',
						],
					],
				],
			]
		);

		$date_format = esc_attr( get_option( 'date_format' ) );

		$this->add_control(
			'date_format',
			[
				'label' => __( 'Date Format', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => false,
				'default' => $date_format,
				'dynamic' => [
					'active' => true,
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'date',
							],
						],
						[
							'name' => 'flatpickr_custom_options_enable',
							'operator' => '==',
							'value' => '',
						],
					],
				],
			]
		);

		$this->add_control(
			'date_language',
			[
				'label' => __( 'Date Language', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => false,
				'description' => __( 'This feature only works on the frontend.', 'pafe' ),
				'options' => [
					'ar' 	=>	 'Arabic',
					'at' 	=>	 'Austria',
					'az' 	=>	 'Azerbaijan',
					'be' 	=>	 'Belarusian',
					'bg' 	=>	 'Bulgarian',
					'bn' 	=>	 'Bangla',
					'bs' 	=>	 'Bosnian',
					'cat' 	=>	 'Catalan',
					'cs' 	=>	 'Czech',
					'cy' 	=>	 'Welsh',
					'da' 	=>	 'Danish',
					'de' 	=>	 'German',
					'english' 	=>	 'English',
					'eo' 	=>	 'Esperanto',
					'es' 	=>	 'Spanish',
					'et' 	=>	 'Estonian',
					'fa' 	=>	 'Persian',
					'fi' 	=>	 'Finnish',
					'fo' 	=>	 'Faroese',
					'fr' 	=>	 'French',
					'ga' 	=>	 'Irish',
					'gr' 	=>	 'Greek',
					'he' 	=>	 'Hebrew',
					'hi' 	=>	 'Hindi',
					'hr' 	=>	 'Croatian',
					'hu' 	=>	 'Hungarian',
					'id' 	=>	 'Indonesian',
					'is' 	=>	 'Icelandic',
					'it' 	=>	 'Italian',
					'ja' 	=>	 'Japanese',
					'ka' 	=>	 'Georgian',
					'km' 	=>	 'Khmer',
					'ko' 	=>	 'Korean',
					'kz' 	=>	 'Kazakh',
					'lt' 	=>	 'Lithuanian',
					'lv' 	=>	 'Latvian',
					'mk' 	=>	 'Macedonian',
					'mn' 	=>	 'Mongolian',
					'ms' 	=>	 'Malaysian',
					'my' 	=>	 'Burmese',
					'nl' 	=>	 'Dutch',
					'no' 	=>	 'Norwegian',
					'pa' 	=>	 'Punjabi',
					'pl' 	=>	 'Polish',
					'pt' 	=>	 'Portuguese',
					'ro' 	=>	 'Romanian',
					'ru' 	=>	 'Russian',
					'si' 	=>	 'Sinhala',
					'sk' 	=>	 'Slovak',
					'sl' 	=>	 'Slovenian',
					'sq' 	=>	 'Albanian',
					'sr-cyr' 	=>	 'SerbianCyrillic',
					'sr' 	=>	 'Serbian',
					'sv' 	=>	 'Swedish',
					'th' 	=>	 'Thai',
					'tr' 	=>	 'Turkish',
					'uk' 	=>	 'Ukrainian',
					'vn' 	=>	 'Vietnamese',
					'zh-tw' 	=>	 'MandarinTraditional',
					'zh' 	=>	 'Mandarin',
				],
				'default' => 'english',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'date',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'use_native_date',
			[
				'label' => __( 'Native HTML5', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'date',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'time_format',
			[
				'label' => __( 'Time Format', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => false,
				'default' => 'h:i K',
				'dynamic' => [
					'active' => true,
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'time',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'time_minute_increment',
			[
				'name' => 'time_minute_increment',
				'label' => __( 'Minute Increment', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 5,
				'max' => 60,
				'step' => 5,
				'default' => 5,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'time',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'time_24hr',
			[
				'label' => __( '24 hour', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'time',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'use_native_time',
			[
				'label' => __( 'Native HTML5', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'time',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'pafe_range_slider_field_options',
			[
				'label' => __( 'Range Slider Options', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => 'skin: "round", type: "double", grid: true, min: 0, max: 1000, from: 200, to: 800, prefix: "$"',
				'description' => 'Demo: <a href="http://ionden.com/a/plugins/ion.rangeSlider/demo.html" target="_blank">http://ionden.com/a/plugins/ion.rangeSlider/demo.html</a>',
				'condition' => [
					'field_type' => 'range_slider',
				]
			]
		);

		$this->add_control(
			'pafe_coupon_code_label',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-control-title',
				'raw' => __( 'Coupon Codes', 'pafe' ),
				'condition' => [
					'field_type' => 'coupon_code',
				],
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'pafe_coupon_code',
			[
				'label' => __( 'Coupon Code', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'pafe_coupon_code_discount_type',
			[
				'label' => __( 'Discount Type', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'percentage' => __( 'Percentage', 'pafe' ),
					'flat_amount' => __( 'Flat Amount', 'pafe' ),
				],
				'default' => 'percentage',
			]
		);

		$repeater->add_control(
			'pafe_coupon_code_coupon_amount',
			[
				'label' => __( 'Coupon Amount', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'pafe_coupon_code_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'title_field' => '{{{ pafe_coupon_code }}}',
				'condition' => [
					'field_type' => 'coupon_code',
				],
			)
		);

		$this->add_control(
			'pafe_calculated_fields_form_distance_calculation',
			[
				'label' => __( 'Distance Calculation', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'condition' => [
					'field_type' => 'calculated_fields',
				]
			]
		);

		$this->add_control(
			'pafe_calculated_fields_form_distance_calculation_from_specific_location_enable',
			[
				'label' => __( 'From Specific Location', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'condition' => [
					'field_type' => 'calculated_fields',
					'pafe_calculated_fields_form_distance_calculation' => 'yes',
				]
			]
		);

		$this->add_control(
			'pafe_calculated_fields_form_distance_calculation_from_specific_location',
			[
				'label' => __( 'From Location', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Please go to https://www.google.com/maps and type your address to get exactly location', 'pafe' ),
				'condition' => [
					'field_type' => 'calculated_fields',
					'pafe_calculated_fields_form_distance_calculation' => 'yes',
					'pafe_calculated_fields_form_distance_calculation_from_specific_location_enable' => 'yes',
				]
			]
		);

		$this->add_control(
			'pafe_calculated_fields_form_distance_calculation_from_field_shortcode',
			[
				'label' => __( 'From Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'field_type' => 'calculated_fields',
					'pafe_calculated_fields_form_distance_calculation' => 'yes',
					'pafe_calculated_fields_form_distance_calculation_from_specific_location_enable!' => 'yes',
				]
			]
		);

		$this->add_control(
			'pafe_calculated_fields_form_distance_calculation_to_specific_location_enable',
			[
				'label' => __( 'To Specific Location', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'condition' => [
					'field_type' => 'calculated_fields',
					'pafe_calculated_fields_form_distance_calculation' => 'yes',
				]
			]
		);

		$this->add_control(
			'pafe_calculated_fields_form_distance_calculation_to_specific_location',
			[
				'label' => __( 'To Location', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Please go to https://www.google.com/maps and type your address to get exactly location', 'pafe' ),
				'condition' => [
					'field_type' => 'calculated_fields',
					'pafe_calculated_fields_form_distance_calculation' => 'yes',
					'pafe_calculated_fields_form_distance_calculation_to_specific_location_enable' => 'yes',
				]
			]
		);

		$this->add_control(
			'pafe_calculated_fields_form_distance_calculation_to_field_shortcode',
			[
				'label' => __( 'To Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'field_type' => 'calculated_fields',
					'pafe_calculated_fields_form_distance_calculation' => 'yes',
					'pafe_calculated_fields_form_distance_calculation_to_specific_location_enable!' => 'yes',
				]
			]
		);

		$this->add_control(
			'pafe_calculated_fields_form_distance_calculation_unit',
			[
				'label' => __( 'Distance Unit', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'km' => 'Kilometer',
					'mile' => 'Mile',
				],
				'default' => 'km',
				'condition' => [
					'field_type' => 'calculated_fields',
					'pafe_calculated_fields_form_distance_calculation' => 'yes',
				]
			]
		);

		$this->add_control(
			'pafe_calculated_fields_form_calculation',
			[
				'label' => __( 'Calculation', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'description' => __( 'E.g [field id="quantity"]*[field id="price"]+10', 'pafe' ),
				'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields',
				'condition' => [
					'field_type' => 'calculated_fields',
					'pafe_calculated_fields_form_distance_calculation!' => 'yes',
				]
			]
		);

		$this->add_control(
			'pafe_calculated_fields_form_coupon_code',
			[
				'label' => __( 'Coupon Code Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'description' => __( 'E.g [field id="coupon_code"]', 'pafe' ),
				'condition' => [
					'field_type' => 'calculated_fields',
				]
			]
		);

		$this->add_control(
			'pafe_calculated_fields_form_calculation_rounding_decimals',
			[
				'label' => __( 'Rounding Decimals', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 2,
				'condition' => [
					'field_type' => 'calculated_fields',
				]
			]
		);

		$this->add_control(
			'pafe_calculated_fields_form_calculation_rounding_decimals_show',
			[
				'label' => __( 'Always show decimal places', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'condition' => [
					'field_type' => 'calculated_fields',
				]
			]
		);

		$this->add_control(
			'pafe_calculated_fields_form_calculation_rounding_decimals_decimals_symbol',
			[
				'label' => __( 'Decimal point character', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '.',
				'condition' => [
					'field_type' => 'calculated_fields',
				]
			]
		);

		$this->add_control(
			'pafe_calculated_fields_form_calculation_rounding_decimals_seperators_symbol',
			[
				'label' => __( 'Separator character', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => ',',
				'condition' => [
					'field_type' => 'calculated_fields',
				]
			]
		);

		$this->add_control(
			'pafe_calculated_fields_form_before',
			[
				'label' => __( 'Before Content', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g $', 'pafe' ),
				'condition' => [
					'field_type' => 'calculated_fields',
				]
			]
		);

		$this->add_control(
			'pafe_calculated_fields_form_after',
			[
				'label' => __( 'After Content', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g $', 'pafe' ),
				'condition' => [
					'field_type' => 'calculated_fields',
				]
			]
		);

		$this->add_control(
			'pafe_image_select_field_gallery',
			[
				'label' => __( 'Add Images', 'pafe' ),
				'type' => \Elementor\Controls_Manager::GALLERY,
				'default' => [],
				'condition' => [
					'field_type' => 'image_select',
				]
			]
		);

		$this->add_control(
			'shortcode',
			[
				'label' => __( 'Shortcode', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'classes' => 'pafe-forms-field-shortcode pafe-forms-field-shortcode--shortcode',
			]
		);

		$this->add_control(
			'live_preview_code',
			[
				'label'   => __( 'Live Preview Code', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Paste this code to anywhere to live preview this field value', 'pafe' ),
				'classes' => 'pafe-forms-field-shortcode pafe-forms-field-shortcode--preview',
				'condition' => [
					'field_type!' => 'image_upload'
				]
			]
		);

		$this->add_control(
			'live_preview_show_label',
			[
				'label' => esc_html__( 'Show Label Preview', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'pafe' ),
				'label_off' => esc_html__( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'field_type' => ['select', 'checkbox', 'radio']
				]
			]
		);

        $this->add_control(
            'live_preview_image',
            [
                'label'   => __( 'Live Preview Code', 'pafe' ),
                'label_block' => true,
                'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Paste this code to anywhere to live preview this field value', 'pafe' ),
				'classes' => 'pafe-forms-field-shortcode pafe-forms-field-shortcode--preview-image',
                'condition' => [
                    'field_type' => 'image_upload'
                ]
            ]
        );
        $this->add_control(
            'live_preview_image_width',
            [
                'label' => esc_html__( 'Live Preview Width', 'pafe' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 5,
                'max' => 1000,
                'step' => 1,
                'default' => 150,
                'condition' => [
                    'field_type' => 'image_upload'
                ]
            ]
        );
        $this->add_control(
            'live_preview_image_height',
            [
                'label' => esc_html__( 'Live Preview height', 'pafe' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 5,
                'max' => 1000,
                'step' => 1,
                'default' => 150,
                'condition' => [
                    'field_type' => 'image_upload'
                ]
            ]
        );
        $this->add_control(
			'image_upload_attach_to_email',
			[
				'label' => esc_html__( 'Attach files to email, do not upload to upload folder', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'pafe' ),
				'label_off' => esc_html__( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
                    'field_type' => 'image_upload'
                ]
			]
		);
        $this->add_control(
			'image_upload_attach_to',
			[
				'label' => __( 'Attach to', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => [
					'email' => 'Email',
					'email2' => 'Email 2',
				],
				'default' => [
					'email',
                    'email2'
				],
                'condition' => [
                    'field_type' => 'image_upload',
                    'image_upload_attach_to_email' => 'yes'
                ]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_other_options',
			[
				'label' => __( 'Other Options', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'field_pattern',
			[
				'label' => __( 'Pattern', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '[0-9()#&+*\-.]+',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'tel',
							],
						],
					],
				],
			]
		);

        $this->add_control(
            'field_dial_code',
            [
                'label' => __( 'International Telephone Input', 'elementor-pro' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => '',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_type',
                            'operator' => 'in',
                            'value' => [
                                'tel',
                            ],
                        ],
                    ],
                ],
            ]
        );

		$this->add_control(
			'field_pattern_not_tel',
			[
				'label' => __( 'Pattern', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'text',
								'email',
								'textarea',
								'url',
								'number',
								'password',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'invalid_message',
			[
				'label' => __( 'Invalid Message', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => '!in',
							'value' => [
								'recaptcha',
								'hidden',
								'html',
								'honeypot',
								'iban'
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'iban_invalid_message',
			[
				'label' => __( 'Invalid Message', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'This IBAN is invalid.',
				'condition' => [
					'field_type' => 'iban'
				],
			]
		);

		$this->add_control(
			'field_autocomplete',
			[
				'label' => __( 'Autocomplete', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'pafe' ),
				'label_off' => __( 'Off', 'pafe' ),
				'return_value' => 'true',
				'default' => 'true',
				'condition' => [
					'field_type!' => 'html',
				],
			]
		);

		$this->add_control(
			'min_length',
			[
				'label' => __( 'Min Length', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'tel',
								'textarea',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'max_length',
			[
				'label' => __( 'Max Length', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'text',
								'email',
								'textarea',
								'url',
								'tel',
								'number',
								'password',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'remove_this_field_from_repeater',
			[
				'label' => __( 'Remove this field from the Repeater in the email', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'true',
			]
		);

		$this->add_control(
			'field_remove_option_value',
			[
				'label' => __( 'Remove this field from email message', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);
		$this->add_control(
			'field_value_remove',
			[
				'label' => __( 'If Field Value is equal', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'field_remove_option_value',
							'operator' => '==',
							'value' => 'yes'
						]
					]
				]
			]
		);

		$this->add_control(
			'multi_step_form_autonext',
			[
				'label' => __( 'Automatically move to the next step after selecting - Multi Step Form', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'select',
								'select_autocomplete',
								'image_select',
								'checkbox',
								'radio',
							],
						],
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon',
			[
				'label' => __( 'Icon', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'field_icon_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'pafe' ),
				'label_off' => __( 'Off', 'pafe' ),
				'return_value' => 'true',
				'default' => '',
			]
		);

		$this->add_control(
			'field_icon_type',
			[
				'label' => __( 'Icon Type', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'font_awesome' => __( 'Font Awesome', 'pafe' ),
					'image' => __( 'Image', 'pafe' ),
				],
				'default' => 'font_awesome',
				'condition' => [
					'field_icon_enable!' => '',
				],
			]
		);

		$this->add_control(
			'field_icon_font_awesome',
			[
				'label' => __( 'Choose Icon', 'pafe' ),
				'type' => \Elementor\Controls_Manager::ICON,
				'condition' => [
					'field_icon_enable!' => '',
					'field_icon_type' => 'font_awesome',
				],
			]
		);

		$this->add_control(
			'field_icon_image',
			[
				'label' => __( 'Choose Icon Image', 'pafe' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'condition' => [
					'field_icon_enable!' => '',
					'field_icon_type' => 'image',
				],
			]
		);

		$this->add_responsive_control(
			'field_icon_width',
			[
				'label' => __( 'Icon Width', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)' => 'padding-left: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field .elementor-field-textual' => 'padding-left: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .pafe-field-icon' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'field_icon_enable!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'field_icon_size',
			[
				'label' => __( 'Icon Size', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 18,
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-field-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'field_icon_enable!' => '',
					'field_icon_type' => 'font_awesome',
				],
			]
		);

		$this->add_responsive_control(
			'field_icon_image_width',
			[
				'label' => __( 'Icon Size', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-field-icon img' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'field_icon_enable!' => '',
					'field_icon_type' => 'image',
				],
			]
		);

		$this->add_responsive_control(
			'field_icon_x',
			[
				'label' => __( 'Icon Position X', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-field-icon' => 'left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'field_icon_enable!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'field_icon_x_right',
			[
				'label'      => __( 'Icon Position X from right', 'pafe' ),
				'type'	     => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'selectors'  => [
					'{{WRAPPER}} .pafe-field-icon' => 'right: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'field_icon_enable!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'field_icon_y',
			[
				'label' => __( 'Icon Position Y', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -20,
						'max' => 100,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-field-icon' => 'top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'field_icon_enable!' => '',
				],
			]
		);

		$this->add_control(
			'field_icon_color',
			[
				'label' => __( 'Icon Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pafe-field-icon i' => 'color: {{VALUE}};',
				],
				'condition' => [
					'field_icon_enable!' => '',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'input_mask_section',
			[
				'label' => __( 'Input Mask', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => '!in',
							'value' => [
								'checkbox',
								'acceptance',
								'radio',
							],
						],
					],
				],
			]
		);
		
		$this->add_control(
			'input_mask_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'input_mask',
			[
				'label' => __( 'Mask', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g (00) 0000-0000 . Documents: https://igorescobar.github.io/jQuery-Mask-Plugin/docs.html', 'pafe' ),
				'condition' => [
					'input_mask_enable!' => '',
				],
			]
		);

		$this->add_control(
			'input_mask_reverse',
			[
				'label' => __( 'Reverse', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => 'True',
				'label_off' => 'False',
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'input_mask_enable!' => '',
				],
			]
		);

		$this->end_controls_section();

		// Radio Style

		$this->start_controls_section(
			'section_style_radio',
			[
				'label' => __( 'Radio', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'radio',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'pafe_style_radio_type',
			[
				'label' => __( 'Style', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'native' => __( 'Native', 'pafe' ),
					'option' => __( 'Options', 'pafe' ),
				],
				'default' => 'native',
			]
		);

		$this->add_control(
			'pafe_style_radio_option_size',
			[
				'label' => __( 'Size', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],

				'selectors' => [
					'{{WRAPPER}} span.elementor-field-option input[type="radio"]' => 'position: absolute; top: 50%; left: 0px; transform: translateY(-50%); opacity: 0; z-index: 9;',
					'{{WRAPPER}} span.elementor-field-option label:before' => ' content: " ";
                        display: inline-block; position: relative; top: 5px ;
                        width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}} ; 
                        border-radius: 11px; border: solid; border-style: solid; 
                        border-width: 2px; background-color: transparent;',
				],
				'condition' => [
					'pafe_style_radio_type' => 'option',
				]
			]
		);

		$this->add_control(
			'pafe_style_radio_option_border_width',
			[
				'label' => __( 'Border Width', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} span.elementor-field-option label:before' => 'border-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pafe_style_radio_type' => 'option',
				]
			]
		);

		$this->add_control(
			'pafe_style_radio_option_border_color',
			[
				'label' => __( 'Border Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#23a455',
				'selectors' => [
					'{{WRAPPER}} span.elementor-field-option label:before' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'pafe_style_radio_type' => 'option',
				]
			]
		);

		$this->add_control(
			'pafe_style_radio_option_spacing',
			[
				'label' => __( 'Spacing', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} span.elementor-field-option label:before' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pafe_style_radio_type' => 'option',
				]
			]
		);

		$this->add_control(
			'pafe_style_radio_item_vertical_spacing',
			[
				'label' => __( 'Item Vertical Spacing', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} span.elementor-field-option ' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} span.elementor-field-option:last-child' => 'margin-bottom: 0;',
				],
				'condition' => [
					'pafe_style_radio_type' => 'option',
				]
			]
		);

		 $this->add_control(
			'pafe_style_radio_item_horizontal_spacing',
			[
				'label' => __( 'Item Horizontal Spacing', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} span.elementor-field-option' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} span.elementor-field-option:last-child' => 'margin-right: 0;',
				],
				'condition' => [
					'pafe_style_radio_type' => 'option',
				]
			]
		);

		$this->add_control(
			'pafe_style_radio_option_background_color',
			[
				'label' => __( 'Checked Background Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#23a455',
				'selectors' => [
					'{{WRAPPER}} span.elementor-field-option input[type="radio"]:checked ~ label:before' => 'background: {{VALUE}};',
				],
				'condition' => [
					'pafe_style_radio_type' => 'option',
				]
			]
		);

        
        $this->end_controls_section();

		// Checkbox Style

		$this->start_controls_section(
			'section_style_checkbox',
			[
				'label' => __( 'Checkbox', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'checkbox',
								'acceptance',
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'pafe_style_checkbox_type',
			[
				'label' => __( 'Style', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'native' => __( 'Native', 'pafe' ),
					'square' => __( 'Square', 'pafe' ),
				],
				'default' => 'native',
			]
		);

		$this->add_control(
			'pafe_style_checkbox_square_size',
			[
				'label' => __( 'Size', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 18,
				],
				'selectors' => [
					'{{WRAPPER}} span.elementor-field-option' => 'position: relative;',
					'{{WRAPPER}} span.elementor-field-option input[type="checkbox"]' => 'position: absolute; top: 50%; left: 0px; transform: translateY(-50%); opacity: 0; z-index: 9;',
					'{{WRAPPER}} span.elementor-field-option label' => 'display: block !important; cursor: pointer; margin: 0 auto; padding: 0px 0px 0px 30px;',
					'{{WRAPPER}} span.elementor-field-option label:before' => 'content: ""; display: block; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; position: absolute; top: 50%; left: 0px; transform: translateY(-50%); background: #fff; border-style: solid; border-width: 1px;',
				],
				'condition' => [
					'pafe_style_checkbox_type' => 'square',
				]
			]
		);

		$this->add_control(
			'pafe_style_checkbox_square_border_width',
			[
				'label' => __( 'Border Width', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} span.elementor-field-option label:before' => 'border-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pafe_style_checkbox_type' => 'square',
				]
			]
		);

		$this->add_control(
			'pafe_style_checkbox_square_border_color',
			[
				'label' => __( 'Border Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#23a455',
				'selectors' => [
					'{{WRAPPER}} span.elementor-field-option label:before' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'pafe_style_checkbox_type' => 'square',
				]
			]
		);

		$this->add_control(
			'pafe_style_checkbox_square_background_color',
			[
				'label' => __( 'Checked Background Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#23a455',
				'selectors' => [
					'{{WRAPPER}} span.elementor-field-option input[type="checkbox"]:checked ~ label:before' => 'background: {{VALUE}};',
				],
				'condition' => [
					'pafe_style_checkbox_type' => 'square',
				]
			]
		);

		$this->add_control(
			'pafe_style_checkbox_square_spacing',
			[
				'label' => __( 'Spacing', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} span.elementor-field-option label' => 'padding-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pafe_style_checkbox_type' => 'square',
				]
			]
		);

        $this->add_control(
            'pafe_style_checkbox_square_item_vertical_spacing',
            [
                'label' => __( 'Item Vertical Spacing', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-option' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementor-field-option:last-child' => 'margin-bottom: 0;',
                ],
                'condition' => [
                    'pafe_style_checkbox_type' => 'square',
                ]
            ]
        );
        
        $this->add_control(
            'pafe_style_checkbox_square_item_horizontal_spacing',
            [
                'label' => __( 'Item Horizontal Spacing', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-option' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementor-field-option:last-child' => 'margin-right: 0;',
                ],
                'condition' => [
                    'pafe_style_checkbox_type' => 'square',
                ]
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_spiner',
			[
				'label' => __( '(-/+) Button', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'field_type' => 'number',
					'number_spiner!' => '',
				]
			]
		);

		$this->add_responsive_control(
			'pafe_style_spiner_width',
			[
				'label' => __( 'Width', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 32,
				],
				'selectors' => [
					'{{WRAPPER}} [data-pafe-form-builder-spiner] button' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pafe_style_spiner_height',
			[
				'label' => __( 'Height', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 32,
				],
				'selectors' => [
					'{{WRAPPER}} [data-pafe-form-builder-spiner] button' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pafe_style_spiner_input_width',
			[
				'label' => __( 'Input Width', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} [data-pafe-form-builder-spiner] .nice-number input' => 'width: {{SIZE}}{{UNIT}}!important;',
				],
			]
		);

		$this->add_responsive_control(
			'pafe_style_spiner_border_radius',
			[
				'label' => __( 'Border Radius', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} [data-pafe-form-builder-spiner] button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'pafe_style_spiner_typography',
				'label' => __( 'Typography', 'pafe' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
				'selector' => '{{WRAPPER}} [data-pafe-form-builder-spiner] button',
			]
		);

		$this->start_controls_tabs(
			'pafe_style_spiner_tabs',
			[	
				'condition' => [
					'field_type' => 'number',
					'number_spiner!' => '',
				]
			]
		);

		$this->start_controls_tab(
			'pafe_style_spiner_normal',
			[
				'label' => __( 'Normal', 'elementor' ),
			]
		);

		$this->add_control(
			'pafe_style_spiner_color',
			[
				'label' => __( 'Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} [data-pafe-form-builder-spiner] button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pafe_style_spiner_color_bg',
			[
				'label' => __( 'Background Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} [data-pafe-form-builder-spiner] button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pafe_style_spiner_border',
			[
				'label' => __( 'Border Type', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'' => __( 'None', 'elementor' ),
					'solid' => _x( 'Solid', 'Border Control', 'elementor' ),
					'double' => _x( 'Double', 'Border Control', 'elementor' ),
					'dotted' => _x( 'Dotted', 'Border Control', 'elementor' ),
					'dashed' => _x( 'Dashed', 'Border Control', 'elementor' ),
					'groove' => _x( 'Groove', 'Border Control', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}} [data-pafe-form-builder-spiner] button' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'pafe_style_spiner_border_width',
			[
				'label' => __( 'Border Width', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} [data-pafe-form-builder-spiner] button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'pafe_style_spiner_border_color',
			[
				'label' => __( 'Border Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} [data-pafe-form-builder-spiner] button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pafe_style_spiner_hover',
			[
				'label' => __( 'Hover', 'elementor' ),
			]
		);

		$this->add_control(
			'pafe_style_spiner_color_hover',
			[
				'label' => __( 'Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} [data-pafe-form-builder-spiner] button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pafe_style_spiner_color_bg_hover',
			[
				'label' => __( 'Background Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} [data-pafe-form-builder-spiner] button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pafe_style_spiner_border_hover',
			[
				'label' => __( 'Border Type', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'' => __( 'None', 'elementor' ),
					'solid' => _x( 'Solid', 'Border Control', 'elementor' ),
					'double' => _x( 'Double', 'Border Control', 'elementor' ),
					'dotted' => _x( 'Dotted', 'Border Control', 'elementor' ),
					'dashed' => _x( 'Dashed', 'Border Control', 'elementor' ),
					'groove' => _x( 'Groove', 'Border Control', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}} [data-pafe-form-builder-spiner] button:hover' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'pafe_style_spiner_border_width_hover',
			[
				'label' => __( 'Border Width', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} [data-pafe-form-builder-spiner] button:hover' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'pafe_style_spiner_border_color_hover',
			[
				'label' => __( 'Border Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} [data-pafe-form-builder-spiner] button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		// Image Select Style

		$this->start_controls_section(
			'section_style_image_select',
			[
				'label' => __( 'Image Select', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'field_type' => 'image_select',
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'pafe_image_select_field_typography',
				'label' => __( 'Typography', 'pafe' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
				'selector' => '{{WRAPPER}} .image_picker_selector .thumbnail p',
			]
		);

		$this->add_responsive_control(
			'pafe_image_select_field_image_alignment',
			[
				'label' => __( 'Image Align', 'pafe' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .image_picker_selector .thumbnail' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'pafe_image_select_field_text_align',
			[
				'label' => __( 'Text Align', 'pafe' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],

				],
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .image_picker_selector .thumbnail p' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'pafe_image_select_field_item_width',
			[
				'label' => __( 'Item Width (%)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 25,
				'min' => 1,
				'max' => 100,
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector li' => 'width: {{VALUE}}% !important;',
				],
			]
		);

		$columns_margin = is_rtl() ? '-{{SIZE}}{{UNIT}} -{{SIZE}}{{UNIT}} -{{SIZE}}{{UNIT}} -{{SIZE}}{{UNIT}};' : '-{{SIZE}}{{UNIT}} -{{SIZE}}{{UNIT}} -{{SIZE}}{{UNIT}} -{{SIZE}}{{UNIT}};';
		$columns_padding = is_rtl() ? '{{SIZE}}{{UNIT}} !important;' : '{{SIZE}}{{UNIT}} !important;';

		$this->add_responsive_control(
			'pafe_image_select_field_image_align',
			[
				'label' => __( 'Item Align', 'pafe' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => __( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => __( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-image-select-field .image_picker_selector' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .pafe-image-select-field' => 'justify-content: {{VALUE}};',
                    '{{WRAPPER}} .piotnet-image-select-required' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'pafe_image_select_field_item_spacing',
			[
				'label' => __( 'Item Spacing', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector li' => 'padding:' . $columns_padding,
					'{{WRAPPER}} ul.thumbnails.image_picker_selector' => 'margin: ' . $columns_margin,
				],
			]
		);

		$this->add_responsive_control(
			'pafe_image_select_field_item_border_radius',
			[
				'label' => __( 'Item Border Radius', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector .thumbnail' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pafe_image_select_field_image_border_radius',
			[
				'label' => __( 'Image Border Radius', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector .image_picker_image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pafe_image_select_field_image_padding',
			[
				'label' => __( 'Input Padding', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .image_picker_image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pafe_image_select_field_label_padding',
			[
				'label' => __( 'Input Padding', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails p' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('pafe_image_select_field_normal_active');

		$this->start_controls_tab(
			'pafe_image_select_field_normal',
			[
				'label' => __( 'Normal', 'elementor' ),
			]
		);

		$this->add_control(
			'pafe_image_select_field_border_normal',
			[
				'label' => __( 'Item Border Type', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'' => __( 'None', 'elementor' ),
					'solid' => _x( 'Solid', 'Border Control', 'elementor' ),
					'double' => _x( 'Double', 'Border Control', 'elementor' ),
					'dotted' => _x( 'Dotted', 'Border Control', 'elementor' ),
					'dashed' => _x( 'Dashed', 'Border Control', 'elementor' ),
					'groove' => _x( 'Groove', 'Border Control', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector .thumbnail' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'pafe_image_select_field_border_width_normal',
			[
				'label' => __( 'Item Border Width', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector .thumbnail' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pafe_image_select_field_border_normal!' => '',
				],
			]
		);

		$this->add_control(
			'pafe_image_select_field_border_color_normal',
			[
				'label' => __( 'Item Border Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector .thumbnail' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'pafe_image_select_field_border_normal!' => '',
				],
			]
		);

		$this->add_control(
			'pafe_image_select_field_background_color_normal',
			[
				'label' => __( 'Background Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector .thumbnail' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pafe_image_select_field_text_color_normal',
			[
				'label' => __( 'Text Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector .thumbnail p' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pafe_image_select_field_active',
			[
				'label' => __( 'Active', 'elementor' ),
			]
		);

		$this->add_control(
			'pafe_image_select_field_border_active',
			[
				'label' => __( 'Item Border Type', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'' => __( 'None', 'elementor' ),
					'solid' => _x( 'Solid', 'Border Control', 'elementor' ),
					'double' => _x( 'Double', 'Border Control', 'elementor' ),
					'dotted' => _x( 'Dotted', 'Border Control', 'elementor' ),
					'dashed' => _x( 'Dashed', 'Border Control', 'elementor' ),
					'groove' => _x( 'Groove', 'Border Control', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector .thumbnail.selected' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'pafe_image_select_field_border_width_active',
			[
				'label' => __( 'Item Border Width', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector .thumbnail.selected' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pafe_image_select_field_border_active!' => '',
				],
			]
		);

		$this->add_control(
			'pafe_image_select_field_border_color_active',
			[
				'label' => __( 'Item Border Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector .thumbnail.selected' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'pafe_image_select_field_border_active!' => '',
				],
			]
		);

		$this->add_control(
			'pafe_image_select_field_background_color_active',
			[
				'label' => __( 'Background Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector .thumbnail.selected' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pafe_image_select_field_text_color_active',
			[
				'label' => __( 'Text Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} ul.thumbnails.image_picker_selector .thumbnail.selected p' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_conditional_logic',
			[
				'label' => __( 'Conditional Logic', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'pafe_conditional_logic_form_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'description' => __( 'This feature only works on the frontend.', 'pafe' ),
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'pafe_conditional_logic_form_speed',
			[
				'label' => __( 'Speed', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g 100, 1000, slow, fast' ),
				'default' => 400,
				'condition' => [
					'pafe_conditional_logic_form_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_conditional_logic_form_easing',
			[
				'label' => __( 'Easing', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g swing, linear' ),
				'default' => 'swing',
				'condition' => [
					'pafe_conditional_logic_form_enable' => 'yes',
				],
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'pafe_conditional_logic_form_action',
			[
				'label' => __( 'Action', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => [
					'show' => 'Show this field',
					'set_value' => 'Set Value',
				],
				'default' => [
					'show',
				],
			]
		);

		$repeater->add_control(
			'pafe_conditional_logic_form_set_value',
			[
				'label' => __( 'Value', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g 10, John, unchecked, checked', 'pafe' ),
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'pafe_conditional_logic_form_action' => 'set_value',
				],
			]
		);

		$repeater->add_control(
			'pafe_conditional_logic_form_set_value_for',
			[
				'label' => __( 'Set Value For', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'get_fields_include_itself' => true,
				'condition' => [
					'pafe_conditional_logic_form_action' => 'set_value',
				],
			]
		);

		$repeater->add_control(
			'pafe_conditional_logic_form_if',
			[
				'label' => __( 'If', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'placeholder' => __( 'Field Shortcode', 'pafe' ),
				'get_fields' => true,
			]
		);

		$repeater->add_control(
			'pafe_conditional_logic_form_comparison_operators',
			[
				'label' => __( 'Comparison Operators', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'options' => [
					'not-empty' => __( 'not empty', 'pafe' ),
					'empty' => __( 'empty', 'pafe' ),
					'=' => __( 'equals', 'pafe' ),
					'!=' => __( 'not equals', 'pafe' ),
					'>' => __( '>', 'pafe' ),
					'>=' => __( '>=', 'pafe' ),
					'<' => __( '<', 'pafe' ),
					'<=' => __( '<=', 'pafe' ),
					'checked' => __( 'checked', 'pafe' ),
					'unchecked' => __( 'unchecked', 'pafe' ),
					'contains' => __( 'contains', 'pafe' ),
				],
			]
		);

		$repeater->add_control(
			'pafe_conditional_logic_form_type',
			[
				'label' => __( 'Type Value', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'options' => [
					'string' => __( 'String', 'pafe' ),
					'number' => __( 'Number', 'pafe' ),
				],
				'default' => 'string',
				'condition' => [
					'pafe_conditional_logic_form_comparison_operators' => ['=','!=','>','>=','<','<='],
				],
			]
		);

		$repeater->add_control(
			'pafe_conditional_logic_form_value',
			[
				'label' => __( 'Value', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( '50', 'pafe' ),
				'condition' => [
					'pafe_conditional_logic_form_comparison_operators' => ['=','!=','>','>=','<','<=','contains'],
				],
			]
		);

		$repeater->add_control(
			'pafe_conditional_logic_form_and_or_operators',
			[
				'label' => __( 'OR, AND Operators', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'options' => [
					'or' => __( 'OR', 'pafe' ),
					'and' => __( 'AND', 'pafe' ),
				],
				'default' => 'or',
			]
		);

		$this->add_control(
			'pafe_conditional_logic_form_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'title_field' => '{{{ pafe_conditional_logic_form_if }}} {{{ pafe_conditional_logic_form_comparison_operators }}} {{{ pafe_conditional_logic_form_value }}}',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_piotnet_form_calculated_fields',
			[
				'label' => __( 'Calculated Fields', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'field_type' => 'calculated_fields',
				]
			]
		);

		$this->add_control(
			'calculated_fields_color',
			[
				'label' => __( 'Text Color', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pafe-calculated-fields-form' => 'color: {{VALUE}};',
				],
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_TEXT,
                ],
				'condition' => [
					'field_type' => 'calculated_fields',
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'calculated_fields_typography',
				'selector' => '{{WRAPPER}} .pafe-calculated-fields-form',
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT,
                ],
				'condition' => [
					'field_type' => 'calculated_fields',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_piotnet_form_label',
			[
				'label' => __( 'Label', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_label',
			[
				'label' => __( 'Label', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'label_spacing',
			[
				'label' => __( 'Spacing', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'selectors' => [
					'body.rtl {{WRAPPER}} .elementor-labels-inline .elementor-field-group > label' => 'padding-left: {{SIZE}}{{UNIT}};',
					// for the label position = inline option
					'body:not(.rtl) {{WRAPPER}} .elementor-labels-inline .elementor-field-group > label' => 'padding-right: {{SIZE}}{{UNIT}};',
					// for the label position = inline option
					'body {{WRAPPER}} .elementor-labels-above .elementor-field-group > label' => 'padding-bottom: {{SIZE}}{{UNIT}};',
					// for the label position = above option
				],
			]
		);

        $this->add_responsive_control(
            'label_text_align',
            [
                'type'        => \Elementor\Controls_Manager::SELECT,
                'label'       => __( 'Text Align', 'elementor-pro' ),
                'label_block' => true,
                'value'       => '',
                'options'     => [
                    ''       => __( 'Default', 'elementor' ),
                    'left'   => __( 'Left', 'elementor' ),
                    'center' => __( 'Center', 'elementor' ),
                    'right'  => __( 'Right', 'elementor' ),
                ],
                'selectors'   => [
                    '{{WRAPPER}} .elementor-field-label' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
			'mark_required_color',
			[
				'label' => __( 'Mark Color', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-mark-required .elementor-field-label:after' => 'color: {{COLOR}};',
				],
				'condition' => [
					'mark_required' => 'yes',
				],
			]
		);

		$this->add_control(
			'label_animation',
			[
				'label' => __( 'Label Animation', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_responsive_control(
			'label_animation_focus_left',
			[
				'label'     => __( 'Label Animation Left', 'pafe' ),
				'type'      => 'slider',
				'default'   => [
					'size' => '',
					'unit' => 'px',
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'condition'   => [
					'label_animation' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-form-builder-label-animation.pafe-form-builder-label-animated label' => 'left: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'label_animation_focus_spacing',
			[
				'label'     => __( 'Label Animation Focus Spacing', 'pafe' ),
				'type'      => 'slider',
				'default'   => [
					'size' => 24,
					'unit' => 'px',
				],
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 60,
					],
				],
				'condition'   => [
					'label_animation' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-form-builder-label-animation.pafe-form-builder-label-animated label' => 'transform: translate3d(0,-{{SIZE}}{{UNIT}},10px);',
				]
			]
		);

		$this->start_controls_tabs('label_animation_tabs');

		$this->start_controls_tab(
			'label_normal_tab',
			[
				'label' => __( 'Normal', 'elementor' ),
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => __( 'Text Color', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group > label, {{WRAPPER}} .elementor-field-subgroup label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .elementor-field-group > label',
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT,
                ],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'label_focus_tab',
			[
				'label' => __( 'Focus', 'elementor' ),
			]
		);

		$this->add_control(
			'label_color_focus',
			[
				'label' => __( 'Text Color', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pafe-form-builder-label-animated .elementor-field-group > label, {{WRAPPER}} .elementor-field-subgroup label:not(.pafe-checkbox-label)' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography_focus',
				'selector' => '{{WRAPPER}} .pafe-form-builder-label-animated .elementor-field-group > label',
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT,
                ],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_piotnet_form_field',
			[
				'label' => __( 'Field', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_responsive_control(
            'field_text_align',
            [
                'type'        => \Elementor\Controls_Manager::SELECT,
                'label'       => __( 'Text Align', 'elementor-pro' ),
                'label_block' => true,
                'value'       => '',
                'options'     => [
                    ''       => __( 'Default', 'elementor' ),
                    'left'   => __( 'Left', 'elementor' ),
                    'center' => __( 'Center', 'elementor' ),
                    'right'  => __( 'Right', 'elementor' ),
                ],
                'selectors'   => [
                    '{{WRAPPER}} .elementor-field-group .elementor-field' => 'text-align: {{VALUE}};',
                    '{{WRAPPER}} .elementor-field-group .pafe-field-container' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

		$this->add_control(
			'field_text_color',
			[
				'label' => __( 'Text Color', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group .elementor-field .elementor-field-textual option' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-field-group .elementor-field .selectize-control .selectize-dropdown .selectize-dropdown-content' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-field-group .elementor-field .selectize-control .selectize-input' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-field-group .elementor-field' => 'color: {{VALUE}};',
				],
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_TEXT,
                ],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'field_typography',
                'selector' => '{{WRAPPER}} .elementor-field-group .elementor-field, {{WRAPPER}} .elementor-field-subgroup label, {{WRAPPER}} .elementor-field-group .elementor-field .selectize-control .selectize-dropdown .selectize-dropdown-content, {{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .selectize-control .selectize-input input, {{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .selectize-control .selectize-input input::placeholder, {{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .selectize-control .selectize-input .item',
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT,
                ]
            ]
		);

		$this->add_control(
			'field_background_color',
			[
				'label' => __( 'Background Color', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field .elementor-field-textual .selectize-input' => 'background: {{VALUE}};',
                    '{{WRAPPER}} .elementor-field-group .elementor-field .selectize-control .selectize-dropdown .selectize-dropdown-content' => 'background: {{VALUE}}!important;',
                    '{{WRAPPER}} .elementor-field-group .elementor-field .selectize-control .selectize-dropdown' => 'background: {{VALUE}}!important;',
					'{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select' => 'background: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'input_max_width',
			[
				'label' => __( 'Input Max Width', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1500,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group .elementor-field:not(.elementor-select-wrapper)' => 'max-width: {{SIZE}}{{UNIT}}!important;',
					'{{WRAPPER}} .elementor-field-group .elementor-field .elementor-field-textual' => 'max-width: {{SIZE}}{{UNIT}}!important;',
					'{{WRAPPER}} .elementor-field-group .elementor-field.pafe-select-drop-down' => 'max-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-field-group .elementor-field select.elementor-field-textual' => 'max-width: unset !important;',
				],
			]
		);

        $this->add_responsive_control(
            'input_height',
            [
                'label' => __( 'Input Height', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-group .mce-tinymce iframe' => 'height: {{SIZE}}{{UNIT}}!important;',
                ],
                'condition' => [
                    'field_type' => 'tinymce',
                ],
            ]
        );

		$this->add_responsive_control(
			'input_padding',
			[
				'label' => __( 'Input Padding', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field .elementor-field-textual .selectize-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field .selectize-control .selectize-dropdown .selectize-dropdown-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'field_type!' => 'checkbox',
				],
			]
		);

		$this->add_control(
			'input_placeholder_color',
			[
				'label' => __( 'Input Placeholder Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)::placeholder' => 'color: {{VALUE}}; opacity: 1;',
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)::-webkit-input-placeholder' => 'color: {{VALUE}}; opacity: 1;',
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)::-moz-placeholder' => 'color: {{VALUE}}; opacity: 1;',
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper):-ms-input-placeholder' => 'color: {{VALUE}}; opacity: 1;',
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper):-moz-placeholder' => 'color: {{VALUE}}; opacity: 1;',
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field .elementor-field-textual' => 'color: {{VALUE}}!important; opacity: 1;',
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field .selectize-control .selectize-input input::placeholder' => 'color: {{VALUE}}!important; opacity: 1;',
					'{{WRAPPER}} .elementor-field-group .elementor-field .selectize-control .input-active input' => 'color: {{VALUE}}!important; opacity: 1;',
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field.elementor-field-textual::-webkit-input-placeholder' => 'color: {{VALUE}}; opacity: 1;',
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field.elementor-field-textual::-moz-placeholder' => 'color: {{VALUE}}; opacity: 1;',
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field.elementor-field-textual:-ms-input-placeholder' => 'color: {{VALUE}}; opacity: 1;',
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field.elementor-field-textual:-moz-placeholder' => 'color: {{VALUE}}; opacity: 1;',
					'{{WRAPPER}} .pafe-field-container .flatpickr-mobile:before' => 'color: {{VALUE}}; opacity: 1;',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'input_placeholder_typography',
				'label' => __( 'Input Placeholder Typography', 'pafe' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT,
                ],
				'selector' => '{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)::placeholder',	
			]
		);

		$this->add_control(
			'field_border_type',
			[
				'label' => _x( 'Border Type', 'Border Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'' => __( 'None', 'elementor' ),
					'solid' => _x( 'Solid', 'Border Control', 'elementor' ),
					'double' => _x( 'Double', 'Border Control', 'elementor' ),
					'dotted' => _x( 'Dotted', 'Border Control', 'elementor' ),
					'dashed' => _x( 'Dashed', 'Border Control', 'elementor' ),
					'groove' => _x( 'Groove', 'Border Control', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)' => 'border-style: {{VALUE}};',
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field .elementor-field-textual' => 'border-style: {{VALUE}};',
                    '{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field .elementor-field-textual .selectize-input' => 'border-style: {{VALUE}};',
					'{{WRAPPER}} .pafe-signature canvas' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'field_border_width',
			[
				'label' => _x( 'Width', 'Border Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field .elementor-field-textual' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field .elementor-field-textual .selectize-input' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .pafe-signature canvas' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'field_border_type!' => '',
				],
			]
		);

		$this->add_control(
			'field_border_color',
			[
				'label' => _x( 'Color', 'Border Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field .elementor-field-textual' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field .elementor-field-textual .selectize-input' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .pafe-signature canvas' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'field_border_type!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'field_border_radius',
			[
				'label' => __( 'Border Radius', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .elementor-field-textual .selectize-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .pafe-signature canvas' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'field_box_shadow',
				'label' => __( 'Box Shadow', 'pafe' ),
				'selector' => '{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field',
			]
		);

		$this->end_controls_section();
        //Option style for Select Autocompleted
        $this->start_controls_section(
			'pafe_select_auto_completed_style',
			[
				'label' => __( 'Select Autocomplete Options', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' =>[
					'field_type' => 'select_autocomplete'
				]
			]
		);
        $this->start_controls_tabs('pafe_autocomplete_tab');
        $this->start_controls_tab(
			'pafe_style_select_autocomplete_normal',
			[
				'label' => __( 'Normal', 'elementor' ),
			]
		);
        $this->add_control(
			'pafe_select_auto_completed_color',
			[
				'label' => __( 'Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group .elementor-field .selectize-control .selectize-dropdown .selectize-dropdown-content' => 'color: {{VALUE}}',
				],
			]
		);
        $this->add_control(
			'pafe_select_auto_completed_bg_color',
			[
				'label' => __( 'Background Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group .elementor-field .selectize-control .selectize-dropdown.elementor-field-textual' => 'background-color: {{VALUE}} !important;',
                    '{{WRAPPER}} .elementor-field-group .elementor-field .selectize-control .selectize-dropdown.elementor-field-textual .selectize-dropdown-content' => 'background-color: {{VALUE}} !important;',
				],
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'select_autocompleted',
				'label' => __( 'Typography', 'pafe' ),
				'selector' => '{{WRAPPER}} .elementor-field-group .elementor-field .selectize-control .selectize-dropdown .selectize-dropdown-content .option',
			]
		);
        $this->end_controls_tab();
        $this->start_controls_tab(
			'pafe_style_select_autocomplete_hover',
			[
				'label' => __( 'Hover', 'elementor' ),
			]
		);
        $this->add_control(
			'pafe_select_auto_completed_hover_color',
			[
				'label' => __( 'Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group .elementor-field .selectize-control .selectize-dropdown .selectize-dropdown-content:hover' => 'color: {{VALUE}}',
				],
			]
		);
        $this->add_control(
			'pafe_select_auto_completed_hover_bg_color',
			[
				'label' => __( 'Background Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
                    '{{WRAPPER}} .elementor-field-group .elementor-field .selectize-control .selectize-dropdown.elementor-field-textual .selectize-dropdown-content .active' => 'background-color: {{VALUE}} !important;',
				],
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'select_autocompleted_active',
				'label' => __( 'Typography', 'pafe' ),
				'selector' => '{{WRAPPER}} .elementor-field-group .elementor-field .selectize-control .selectize-dropdown .selectize-dropdown-content .option:hover',
			]
		);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        //Tạo tab
        // $this->add_group_control(
		// 	\Elementor\Group_Control_Background::get_type(),
		// 	[
		// 		'name' => 'pafe_select_auto_completed_bg_color',
		// 		'types' => [ 'classic', 'gradient'],
		// 		'selector' => '{{WRAPPER}} ',
		// 	]
		// );
        $this->end_controls_section();
		//
		$this->start_controls_section(
			'section_style_piotnet_form_show_password',
			[
				'label' => __( 'Password Button', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' =>[
					'field_type' => 'password'
				]
			]
		);
		$this->add_control(
			'form_show_password_icon_top',
			[
				'label' => __( 'Top', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-show-password' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'password_typography',
				'label' => __( 'Typography', 'pafe' ),
				'selector' => '{{WRAPPER}} .pafe-show-password i',
			]
		);
		$this->add_control(
			'password_icon_color',
			[
				'label' => __( 'Icon Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pafe-show-password i' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'password_icon_bacground_color',
			[
				'label' => __( 'Background Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pafe-show-password' => 'background-color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'password_icon_padding',
			[
				'label' => __( 'Padding', 'pafe' ),
				'type' =>  \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .pafe-show-password' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'password_icon_border_radius',
			[
				'label' => __( 'Border Radius', 'pafe' ),
				'type' =>  \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .pafe-show-password' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
	}

	protected function make_textarea_field( $item, $item_index, $form_id, $tinymce = false,$i=0) {
		$this->add_render_attribute( 'textarea' . $item_index, [
			'class' => [
				'elementor-field',
				esc_attr( $item['css_classes'] ),
				'elementor-size-' . $item['input_size'],
			],
			'name' => $this->get_attribute_name( $item ),
			'id' => $this->get_attribute_id( $item ),
			'rows' => $item['rows'],
		] );

		if ( $item['field_placeholder'] ) {
			$this->add_render_attribute( 'textarea' . $item_index, 'placeholder', $item['field_placeholder'] );
		}

		if ( $tinymce ) {
			$this->add_render_attribute( 'textarea' . $item_index, 'data-pafe-form-builder-tinymce' );
			$rtl = is_rtl() ? 'rtl' : 'ltr';
			$this->add_render_attribute( 'textarea' . $item_index, 'data-pafe-form-builder-tinymce-rtl',  $rtl);
            $previewCode = !empty($item['tinymce_preview_code']) ? ' | code' : '';
            $this->add_render_attribute( 'textarea' . $item_index, 'data-pafe-form-builder-tinymce-preview-code', $previewCode);
		}

		if ( $item['field_required'] ) {
			$this->add_required_attribute( 'textarea' . $item_index );
		}

        if ( ! empty( $item['invalid_message'] ) ) {
            $this->add_render_attribute( 'textarea' . $i, 'oninvalid', "this.setCustomValidity('" . $item['invalid_message'] . "')" );
            $this->add_render_attribute( 'textarea' . $i, 'onchange', "this.setCustomValidity('')" );
        }

		if ( ! empty( $item['max_length'] ) ) {
			$this->add_render_attribute( 'textarea' . $i, 'maxlength', $item['max_length'] );
		}

		if ( ! empty( $item['min_length'] ) ) {
			$this->add_render_attribute( 'textarea' . $i, 'minlength', $item['min_length'] );
		}

		if ( ! empty( $item['field_pattern_not_tel'] ) ) {
			$this->add_render_attribute( 'textarea' . $i, 'pattern', $item['field_pattern_not_tel'] );
		}

		if ( !empty($item['remove_this_field_from_repeater']) ) {
			$this->add_render_attribute( 'textarea' . $item_index, 'data-pafe-form-builder-remove-this-field-from-repeater' );
		}

		$name = $this->get_field_name_shortcode($this->get_attribute_name( $item ));
		$value = $this->get_value_edit_post($name);

		if (empty($value)) {
			$value = pafe_dynamic_tags($item['field_value']);
			$this->add_render_attribute( 'textarea' . $item_index, 'data-pafe-form-builder-default-value', pafe_dynamic_tags($item['field_value']) );
		}

		// if ( ! empty( $value ) ) {
		// 	$this->add_render_attribute( 'input' . $i, 'value', $value );
		// }
		// $value = empty( $item['field_value'] ) ? '' : $item['field_value'];

		$this->add_render_attribute( 'textarea' . $item_index, 'data-pafe-form-builder-form-id', $form_id );
        if($tinymce && !empty($item['tinymce_default_value'])){
            $value = $item['tinymce_default_value'];
        }
		return '<textarea ' . $this->get_render_attribute_string( 'textarea' . $item_index ) . '>' . $value . '</textarea>';
	}

	protected function make_select_field( $item, $i, $form_id, $image_select = false, $terms_select = false, $select_autocomplete = false, $select2 = false ) {
		$preview_class = !empty($item['live_preview_show_label']) ? ' pafe-preview-label' : '';
		$select_class = $item['field_type'] == 'select' ? 'pafe-select-drop-down' : '';
		$this->add_render_attribute(
			[
				'select-wrapper' . $i => [
					'class' => [
						'elementor-field',
						'elementor-select-wrapper',
						esc_attr( $item['css_classes'] ),
						$select_class
					],
				],
				'select' . $i => [
					'name' => $this->get_attribute_name( $item ) . ( ! empty( $item['allow_multiple'] ) ? '[]' : '' ),
					'id' => $this->get_attribute_id( $item ),
					'class' => [
						'elementor-field-textual' . $preview_class,
						'elementor-size-' . $item['input_size'],
					],
					'data-pafe-field-type' => 'select'
				],
			]
		);

        if ( $select2 ) {
            $this->add_render_attribute(
                [
                   'select' . $i => [
                        'class' => [
                            'pafe-select-type-select2',
                        ],
                    ],
                ]
            );
        }

		if ($image_select) {
			$list = $item['pafe_image_select_field_gallery'];
			$limit_multiple = $item['limit_multiple'];
            $min_select     = $item['min_select'];
            $min_select_message = $item['min_select_required_message'];
			if( !empty($list) ) {
				$this->add_render_attribute(
					[
						'select' . $i => [
							'data-pafe-form-builder-image-select' => json_encode($list),
						],
					]
				);

				if (!empty($limit_multiple)) {
					$this->add_render_attribute(
						[
							'select' . $i => [
								'data-pafe-form-builder-image-select-limit-multiple' => $limit_multiple,
							],
						]
					);
				}

                if ( ! empty( $min_select ) ) {
                    $this->add_render_attribute(
                        [
                            'select' . $i => [
                                'data-pafe-form-builder-image-select-min-select' => $min_select,
                            ],
                        ]
                    );

                    $this->add_render_attribute(
                        [
                            'select' . $i => [
                                'data-pafe-form-builder-image-select-min-select-message' => $min_select_message,
                            ],
                        ]
                    );
                }
			}
		}

		if ( $item['field_required'] ) {
			$this->add_required_attribute( 'select' . $i );
		}

		if ( $item['allow_multiple'] ) {
			$this->add_render_attribute( 'select' . $i, 'multiple' );
			if ( ! empty( $item['select_size'] ) ) {
				$this->add_render_attribute( 'select' . $i, 'size', $item['select_size'] );
			}
		}

		if ( $item['send_data_by_label'] ) {
			$this->add_render_attribute( 'select' . $i, 'data-pafe-form-builder-send-data-by-label' );
		}

		if ( ! empty( $item['invalid_message'] ) ) {
			$this->add_render_attribute( 'select' . $i, 'oninvalid', "this.setCustomValidity('" . $item['invalid_message'] . "')" );
			$this->add_render_attribute( 'select' . $i, 'onchange', "this.setCustomValidity('')" );
		}

		if ( !empty($item['remove_this_field_from_repeater']) ) {
			$this->add_render_attribute( 'select' . $i, 'data-pafe-form-builder-remove-this-field-from-repeater' );
		}

		if ( !empty($item['multi_step_form_autonext']) ) {
			$this->add_render_attribute( 'select' . $i, 'data-pafe-form-builder-multi-step-form-autonext' );
		}

		if ( ! empty( $item['payment_methods_select_field_enable'] ) ) {
			$this->add_render_attribute( 'select' . $i, 'data-pafe-form-builder-payment-methods-select-field', '' );
			$this->add_render_attribute( 'select' . $i, 'data-pafe-form-builder-payment-methods-select-field-value-for-stripe', $item['payment_methods_select_field_value_for_stripe'] );
			$this->add_render_attribute( 'select' . $i, 'data-pafe-form-builder-payment-methods-select-field-value-for-paypal', $item['payment_methods_select_field_value_for_paypal'] );

			wp_enqueue_script( 'pafe-form-builder-advanced-script' );
		}

		$options = preg_split( "/\\r\\n|\\r|\\n/", $item['field_options'] );

		if ($terms_select) {
			if (!empty($item['field_taxonomy_slug'])) {
				$terms = get_terms( array(
				    'taxonomy' => $item['field_taxonomy_slug'],
				    'hide_empty' => false,
				) );

				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
					$options = array();
				    foreach ( $terms as $term ) {
				        $options[] = $term->name . '|' . $term->slug;
				    }
				}
			}
		}

		if ( ! $options ) {
			return '';
		}

		if ($select_autocomplete) {
			$this->add_render_attribute(
				[
					'select' . $i => [
						'data-pafe-form-builder-select-autocomplete' => '',
					],
				]
			);
		}

		ob_start();
		$this->add_render_attribute( 'select' . $i, 'data-pafe-form-builder-form-id', $form_id );

		$name = $this->get_field_name_shortcode($this->get_attribute_name( $item ));
		$value = $this->get_value_edit_post($name);

		if (empty($value)) {
			$this->add_render_attribute( 'select' . $i, 'data-pafe-form-builder-default-value', pafe_dynamic_tags($item['field_value']) );
		}
        if($image_select){
            echo "<div data-pafe-form-builder-required class='piotnet-image-select-required'></div>";
        }
		?>
		<div <?php echo $this->get_render_attribute_string( 'select-wrapper' . $i ); ?>>
			<select <?php echo $this->get_render_attribute_string( 'select' . $i ); ?> data-options='<?php echo json_encode($options); ?>' <?php if ( $select_autocomplete && !empty($item['field_placeholder']) ) { ?> placeholder = <?php echo $item['field_placeholder']; }?> >
				<?php

				if ($select_autocomplete && !empty($item['field_placeholder'])) {
					array_unshift($options,$item['field_placeholder'] . '|' . '');
				}

				foreach ( $options as $key => $option ) {
					$option_id = $key;
					$option_value = esc_attr( $option );
					$option_label = esc_html( $option );

					if ( false !== strpos( $option, '|' ) ) {
						list( $label, $value ) = explode( '|', $option );
						$option_value = esc_attr( $value );
						$option_label = esc_html( $label );
					}

					$this->add_render_attribute( $option_id, 'value', $option_value );

					$name = $this->get_field_name_shortcode($this->get_attribute_name( $item ));
					$value = $this->get_value_edit_post($name);

					if (empty($value)) {
						$value = pafe_dynamic_tags($item['field_value']);
					}

					if ( ! empty( $value ) && $option_value === $value ) {
						$this->add_render_attribute( $option_id, 'selected', 'selected' );
					}

					// if (!$select_autocomplete) {
						$values = explode(',', $value);

                        foreach ($values as $value_item) {
                            if ( $option_value === $value_item ) {
                                $this->add_render_attribute( $option_id, 'selected', 'selected' );
                            }
                        }
					// }

					if ( $item['send_data_by_label'] ) {
						$this->add_render_attribute( $option_id, 'data-pafe-form-builder-send-data-by-label', $option_label );
					}

					if ( !empty($item['remove_this_field_from_repeater']) ) {
						$this->add_render_attribute( $option_id, 'data-pafe-form-builder-remove-this-field-from-repeater', $option_label );
					}

					if ($key == (count($options) - 1) && trim($option_value) == '') {
						# code...
					} else {
						if ( false !== strpos( $option_value, '[optgroup' ) ) {
							$optgroup = str_replace('&quot;', '', str_replace(']', '', str_replace('[optgroup label=', '', $option_value) ) ); // fix alert ]
							echo '<optgroup label="' . $optgroup . '">';
						} elseif ( false !== strpos( $option_value, '[/optgroup]' ) ) {
							echo '</optgroup>';
						} else {
							echo '<option ' . $this->get_render_attribute_string( $option_id ) . '>' . $option_label . '</option>';
						}
					}
				}
				?>
			</select>
		</div>
		<?php

        return ob_get_clean();
	}

	protected function make_radio_checkbox_field( $item, $item_index, $type, $form_id, $terms_select = false ) {
		$options = preg_split( "/\\r\\n|\\r|\\n/", $item['field_options'] );

		if ($terms_select) {
			if (!empty($item['field_taxonomy_slug'])) {
				$terms = get_terms( array(
				    'taxonomy' => $item['field_taxonomy_slug'],
				    'hide_empty' => false,
				) );

				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
					$options = array();
				    foreach ( $terms as $term ) {
				        $options[] = $term->name . '|' . $term->slug;
				    }
				}
			}
		}

		$html = '';
		if ( $options ) {
			$html .= '<form>';
			$html .= '<div class="elementor-field-subgroup ' . esc_attr( $item['css_classes'] ) . ' ' . $item['inline_list'] . '">';
			$index = 0;
			foreach ( $options as $key => $option ) {
				$index++;
				$element_id = $item['field_id'] . $key;
				$html_id = $this->get_attribute_id( $item ) . '-' . $key;
				$option_label = $option;
				$option_value = $option;
				if ( false !== strpos( $option, '|' ) ) {
					list( $option_label, $option_value ) = explode( '|', $option );
				}

				$this->add_render_attribute(
					$element_id,
					[
						'type' => $type,
						'value' => $option_value,
						'data-value' => $option_value,
						'id' => $html_id,
						'name' => $this->get_attribute_name( $item ) . ( ( 'checkbox' === $type && count( $options ) > 1 ) ? '[]' : '' ),
					]
				);

				$name = $this->get_field_name_shortcode($this->get_attribute_name( $item ));
				$value = $this->get_value_edit_post($name);

                if ( ! empty( $item['checkbox_limit_multiple'] ) ) {
                    $this->add_render_attribute( $element_id, 'data-pafe-checkbox-limit-multiple', $item['checkbox_limit_multiple'] );
                    wp_enqueue_script( 'pafe-form-builder-advanced2-script' );
                }

				if (empty($value)) {
					$value = pafe_dynamic_tags($item['field_value']);
					$this->add_render_attribute( $element_id, 'data-pafe-form-builder-default-value', pafe_dynamic_tags($item['field_value']) );
				}

				if(!empty($item['live_preview_show_label'])){
					$this->add_render_attribute( $element_id, 'class', "pafe-preview-label" );
				}

				if ( ! empty( $item['invalid_message'] ) ) {
					// if ($index == 1) {
					// 	$this->add_render_attribute( $element_id, 'oninvalid', "this.setCustomValidity('" . $item['invalid_message'] . "')" );
					// 	$this->add_render_attribute( $element_id, 'onchange', "this.setCustomValidity('')" );
					// } else {
						$this->add_render_attribute( $element_id, 'onclick', "clearValidity(this)" );
						$this->add_render_attribute( $element_id, 'oninvalid', "this.setCustomValidity('" . $item['invalid_message'] . "')" );
						$this->add_render_attribute( $element_id, 'onchange', "this.setCustomValidity('')" );
					// }
				}

				if ( ! empty( $item['payment_methods_select_field_enable'] ) ) {
					$this->add_render_attribute( $element_id, 'data-pafe-form-builder-payment-methods-select-field', '' );
					$this->add_render_attribute( $element_id, 'data-pafe-form-builder-payment-methods-select-field-value-for-stripe', $item['payment_methods_select_field_value_for_stripe'] );
					$this->add_render_attribute( $element_id, 'data-pafe-form-builder-payment-methods-select-field-value-for-paypal', $item['payment_methods_select_field_value_for_paypal'] );
				}

				if ( ! empty( $value ) && $option_value === $value ) {
					$this->add_render_attribute( $element_id, 'checked', 'checked' );
					$this->add_render_attribute( $element_id, 'data-checked', 'checked' );			
				}

				$values = explode(',', $value);
				foreach ($values as $value_item) {
					if ( $option_value === $value_item ) {
						$this->add_render_attribute( $element_id, 'checked', 'checked' );
						$this->add_render_attribute( $element_id, 'data-checked', 'checked' );
					}
				}

				if ( $item['send_data_by_label'] ) {
					$this->add_render_attribute( $element_id, 'data-pafe-form-builder-send-data-by-label', $option_label );
				}

				if ( !empty($item['remove_this_field_from_repeater']) ) {
					$this->add_render_attribute( $element_id, 'data-pafe-form-builder-remove-this-field-from-repeater', $option_label );
				}

				if ( $item['field_required'] && 'radio' === $type ) {
					$this->add_required_attribute( $element_id );
				}

				if ( !empty($item['multi_step_form_autonext']) && 'radio' === $type ) {
					$this->add_render_attribute( $element_id, 'data-pafe-form-builder-multi-step-form-autonext' );
				}
				
				$this->add_render_attribute( $element_id, 'data-pafe-form-builder-form-id', $form_id );

				$html .= '<span class="elementor-field-option"><input ' . $this->get_render_attribute_string( $element_id ) . '> <label for="' . $html_id . '" class="pafe-checkbox-label">' . $option_label . '</label></span>';
			}

			$html .= '</div>';
			$html .= '</form>';
		}

		return $html;
	}

	protected function form_fields_render_attributes( $i, $instance, $item ) {
		if(!empty($item['pafe_range_slider_field_options'])){
			if($this->pafe_is_json($item['pafe_range_slider_field_options'])){
				$rage_setting_encode = $item['pafe_range_slider_field_options'];
			}else{
				$range_slider_set = explode(',', $item['pafe_range_slider_field_options']);
				$range_slider_options = [];
				foreach($range_slider_set as $val){
					$slider_item = explode(':', $val);
					$range_slider_options[str_replace(['"', ' '], '', $slider_item[0])] = str_replace(['"'," "], '', $slider_item[1]);
				}
				$rage_setting_encode = wp_json_encode($range_slider_options);
			}
		}else{
			$rage_setting_encode = '';
		}
		$label_inline = !empty($item['field_label_inline']) ? ' pafe-label-inline' : '';
		$this->add_render_attribute(
			[
				'field-group' . $i => [
					'class' => [
						'elementor-field-type-' . $item['field_type'],
						'elementor-field-group',
						'elementor-column',
						'elementor-field-group-' . $item['field_id'],
					],
				],
				'input' . $i => [
					'class' => [
						'elementor-field',
						'elementor-size-' . $item['input_size'],
						empty( $item['css_classes'] ) ? '' : esc_attr( $item['css_classes'] ),
					],
				],
				'range_slider' . $i => [
					'type' => 'text',
					'name' => $this->get_attribute_name( $item ),
					'id' => $this->get_attribute_id( $item ),
					'class' => [
						'elementor-field',
						'elementor-size-' . $item['input_size'],
						empty( $item['css_classes'] ) ? '' : esc_attr( $item['css_classes'] ),
					],
					'data-pafe-form-builder-range-slider' => $item['pafe_range_slider_field_options'],
					'data-pafe-form-builder-range-slider-options' => $rage_setting_encode
				],
				'calculated_fields' . $i => [
					'type' => 'text',
					'name' => $this->get_attribute_name( $item ),
					'id' => $this->get_attribute_id( $item ),
					'class' => [
						'elementor-field',
						'elementor-size-' . $item['input_size'],
						empty( $item['css_classes'] ) ? '' : esc_attr( $item['css_classes'] ),
					],
					'data-pafe-form-builder-calculated-fields' => $item['pafe_calculated_fields_form_calculation'],
					'data-pafe-form-builder-calculated-fields-before' => $item['pafe_calculated_fields_form_before'],
					'data-pafe-form-builder-calculated-fields-after' => $item['pafe_calculated_fields_form_after'],
					'data-pafe-form-builder-calculated-fields-rounding-decimals' => $item['pafe_calculated_fields_form_calculation_rounding_decimals'],
					'data-pafe-form-builder-calculated-fields-rounding-decimals-decimals-symbol' => $item['pafe_calculated_fields_form_calculation_rounding_decimals_decimals_symbol'],
					'data-pafe-form-builder-calculated-fields-rounding-decimals-seperators-symbol' => $item['pafe_calculated_fields_form_calculation_rounding_decimals_seperators_symbol'],
					'data-pafe-form-builder-calculated-fields-rounding-decimals-show' => $item['pafe_calculated_fields_form_calculation_rounding_decimals_show'],
				],
				'label' . $i => [
					'for' => $this->get_attribute_id( $item ),
					'class' => 'elementor-field-label'.$label_inline,
				],
			]
		);

		if ($item['field_type'] == 'address_autocomplete' || $item['field_type'] == 'iban') {
			$this->add_render_attribute(
				[
					'input' . $i => [
						'type' => 'text',
						'name' => $this->get_attribute_name( $item ),
						'id' => $this->get_attribute_id( $item ),
					],
				]
			);
		} else {
			$this->add_render_attribute(
				[
					'input' . $i => [
						'type' => $item['field_type'] != 'confirm' ? $item['field_type'] : $item['confirm_type'],
						'name' => $this->get_attribute_name( $item ),
						'id' => $this->get_attribute_id( $item ),
					],
				]
			);
		}

		if ( empty( $item['width'] ) ) {
			$item['width'] = '100';
		}

		$this->add_render_attribute( 'field-group' . $i, 'class', 'elementor-col-' . $item['width'] );

		if ( ! empty( $item['width_tablet'] ) ) {
			$this->add_render_attribute( 'field-group' . $i, 'class', 'elementor-md-' . $item['width_tablet'] );
		}

		if ( $item['allow_multiple'] ) {
			$this->add_render_attribute( 'field-group' . $i, 'class', 'elementor-field-type-' . $item['field_type'] . '-multiple' );
		}

		if ( ! empty( $item['width_mobile'] ) ) {
			$this->add_render_attribute( 'field-group' . $i, 'class', 'elementor-sm-' . $item['width_mobile'] );
		}

		if ( ! empty( $item['field_placeholder'] ) ) {
			$this->add_render_attribute( 'input' . $i, 'placeholder', $item['field_placeholder'] );
		}

		if ( ! empty( $item['max_length'] ) ) {
			$this->add_render_attribute( 'input' . $i, 'maxlength', $item['max_length'] );
		}

		if ( ! empty( $item['min_length'] ) ) {
			$this->add_render_attribute( 'input' . $i, 'minlength', $item['min_length'] );
		}

		if ( ! empty( $item['field_pattern_not_tel'] && $item['field_type'] != 'tel' ) ) {
			$this->add_render_attribute( 'input' . $i, 'pattern', $item['field_pattern_not_tel'] );
		}

		if ( ! empty( $item['input_mask_enable'] ) ) {
			wp_enqueue_script( 'pafe-form-builder-input-mask-script' );
			if (!empty($item['input_mask'])) {
				$this->add_render_attribute( 'input' . $i, 'data-mask', $item['input_mask'] );
			}
			if (!empty($item['input_mask_reverse'])) {
				$this->add_render_attribute( 'input' . $i, 'data-mask-reverse', 'true' );
			}
		}

		if ( ! empty( $item['invalid_message'] ) ) {
			$this->add_render_attribute( 'input' . $i, 'oninvalid', "this.setCustomValidity('" . $item['invalid_message'] . "')" );
			$this->add_render_attribute( 'input' . $i, 'onchange', "this.setCustomValidity('')" );
		}

		if ( ! empty( $item['field_autocomplete'] ) ) {
			$this->add_render_attribute( 'input' . $i, 'autocomplete', 'on' );
		} else {
			$this->add_render_attribute( 'input' . $i, 'autocomplete', 'off' );
		}

		$name = $this->get_field_name_shortcode($this->get_attribute_name( $item ));
		$value = $this->get_value_edit_post($name);

		if (empty($value)) {
			$value = pafe_dynamic_tags($item['field_value']);
			$this->add_render_attribute( 'input' . $i, 'data-pafe-form-builder-default-value', pafe_dynamic_tags($item['field_value']) );
		}

		if ( ! empty( $value ) || $value == 0 ) {
			$this->add_render_attribute( 'input' . $i, 'value', $value );
			$this->add_render_attribute( 'range_slider' . $i, 'value', $value );
			$this->add_render_attribute( 'input' . $i, 'data-pafe-form-builder-value', $value );
		}

		if ( ! empty( $item['field_required'] ) ) {
			$class = 'elementor-field-required';
			if ( ! empty( $item['mark_required'] ) ) {
				$class .= ' elementor-mark-required';
			}
			$this->add_render_attribute( 'field-group' . $i, 'class', $class );
			$this->add_required_attribute( 'input' . $i );
		}

		if ( ! empty( $item['allow_multiple_upload'] ) ) {
			$this->add_render_attribute( 'input' . $i, 'multiple', 'multiple' );
			//$this->add_render_attribute( 'input' . $i, 'name', $this->get_attribute_name( $item ) . '[]', true );
		}

		if ( $item['field_type'] == 'upload' ) {
			$this->add_render_attribute( 'input' . $i, 'name', 'upload_field', true );
		}

		if ( ! empty( $item['attach_files'] ) ) {
			$this->add_render_attribute( 'input' . $i, 'data-attach-files', true );
		}

		if ( ! empty( $item['file_sizes'] ) ) {
			$this->add_render_attribute(
				'input' . $i,
				[
					'data-maxsize' => $item['file_sizes'],  //MB
					'data-maxsize-message' => $item['file_sizes_message'],
				]
			);
		}

		if ( ! empty( $item['file_types'] ) ) {
			$file_types = explode(',', $item['file_types']);
			$file_accepts = array('jpg','jpeg','png','gif','pdf','doc','docx','ppt','pptx','odt','avi','ogg','m4a','mov','mp3','mp4','mpg','wav','wmv','zip','xls','xlsx');

			if (is_array($file_types)) {
				$file_types_output = '';
				foreach ($file_types as $file_type) {
					$file_type = trim($file_type);
					// if (in_array($file_type, $file_accepts)) {
					// 	$file_types_output .= '.' . $file_type . ',';
					// }
					$file_types_output .= '.' . $file_type . ',';
				}

				//$this->add_render_attribute( 'input' . $i, 'accept', rtrim($file_types_output,',') );
				$this->add_render_attribute( 'input' . $i, 'data-accept', str_replace('.', '', rtrim($file_types_output,',')) );
			}

			$this->add_render_attribute(
				'input' . $i,
				[
					'data-types-message' => $item['file_types_message'],
				]
			);
			
		}

		if ( !empty($item['remove_this_field_from_repeater']) ) {
			$this->add_render_attribute( 'input' . $i, 'data-pafe-form-builder-remove-this-field-from-repeater', '', true );
			$this->add_render_attribute( 'range_slider' . $i, 'data-pafe-form-builder-remove-this-field-from-repeater', '', true );
			$this->add_render_attribute( 'calculated_fields' . $i, 'data-pafe-form-builder-remove-this-field-from-repeater', '', true );
		}

	}

	public function get_field_name_shortcode($content) {
		$field_name = str_replace('[field id=', '', $content);
		$field_name = str_replace(']', '', $field_name);
		$field_name = str_replace('"', '', $field_name);
		$field_name = str_replace('form_fields[', '', $field_name);
		//fix alert ]
		return trim($field_name);
	}

	public function get_value_edit_post($name) {
		$value = '';
		if (!empty($_GET['edit'])) {
			$post_id = intval($_GET['edit']);
			if( is_user_logged_in() && get_post($post_id) != null ) {
				if (current_user_can( 'edit_others_posts' ) || get_current_user_id() == get_post($post_id)->post_author) {
					$sp_post_id = get_post_meta($post_id,'_submit_post_id',true);
					$form_id = get_post_meta($post_id,'_submit_button_id',true);

					if (!empty($_GET['smpid'])) {
						$sp_post_id = sanitize_text_field($_GET['smpid']);
					}

					if (!empty($_GET['sm'])) {
						$form_id = sanitize_text_field($_GET['sm']);
					}

					$elementor = \Elementor\Plugin::$instance;
					
					if ( version_compare( ELEMENTOR_VERSION, '2.6.0', '>=' ) ) {
						$meta = $elementor->documents->get( $sp_post_id )->get_elements_data();
					} else {
						$meta = $elementor->db->get_plain_editor( $sp_post_id );
					}

					$form = find_element_recursive( $meta, $form_id );

					if ( !empty($form)) {

						$widget = $elementor->elements_manager->create_element_instance( $form );
						$form['settings'] = $widget->get_active_settings();

						if(!empty($form['settings'])) {
							$sp_post_taxonomy = $form['settings']['submit_post_taxonomy'];
							$sp_title = $this->get_field_name_shortcode( $form['settings']['submit_post_title'] );
							$sp_content = $this->get_field_name_shortcode( $form['settings']['submit_post_content'] );
							$sp_terms = $form['settings']['submit_post_terms_list'];
							$sp_term = $this->get_field_name_shortcode( $form['settings']['submit_post_term'] );
							$sp_featured_image = $this->get_field_name_shortcode( $form['settings']['submit_post_featured_image'] );
							$sp_custom_fields = $form['settings']['submit_post_custom_fields_list'];
							$sp_post_type = $form['settings']['submit_post_type'];

							if ($name == $sp_title) {
								$value = get_the_title($post_id);
							}

							if ($name == $sp_content) {
								$value = get_the_content(null,false,$post_id);
							}

							if ($name == $sp_term) {
								if (!empty($sp_post_taxonomy)) {
									$sp_post_taxonomy = explode('|', $sp_post_taxonomy);
									$sp_post_taxonomy = $sp_post_taxonomy[0];
									$terms = get_the_terms($post_id,$sp_post_taxonomy);
									if (!empty($terms) && ! is_wp_error( $terms )) {
										$value = $terms[0]->slug;
									}
								}
								
							}

							if (!empty($sp_terms)) {
								foreach ($sp_terms as $sp_terms_item) {
									$sp_post_taxonomy = explode('|', $sp_terms_item['submit_post_taxonomy']);
									$sp_post_taxonomy = $sp_post_taxonomy[0];
									$sp_term_slug = $sp_terms_item['submit_post_terms_slug'];
									$sp_term = get_field_name_shortcode( $sp_terms_item['submit_post_terms_field_id'] );

									if ($name == $sp_term) {
										$terms = get_the_terms($post_id,$sp_post_taxonomy);
										if (!empty($terms) && ! is_wp_error( $terms )) {
											foreach ($terms as $term) {
												$value .= $term->slug . ',';
											}
										}
									}
								}

								$value = rtrim($value, ',');
							}

							if ($name == $sp_featured_image) {
								$value = get_the_post_thumbnail_url($post_id,'full');
							}

							foreach ($sp_custom_fields as $sp_custom_field) {
								if ( !empty( $sp_custom_field['submit_post_custom_field'] ) ) {
									if ($name == $this->get_field_name_shortcode( $sp_custom_field['submit_post_custom_field_id'])) {

										$meta_type = $sp_custom_field['submit_post_custom_field_type'];

										if (function_exists('get_field') && $form['settings']['submit_post_custom_field_source'] == 'acf_field') {
											$value = get_field($sp_custom_field['submit_post_custom_field'],$post_id);

											if ($meta_type == 'image') {
												if (is_array($value)) {
													$value = $value['url'];
												}
											}

											if ($meta_type == 'gallery') {
												if (is_array($value)) {
													$images = '';
													foreach ($value as $item) {
														if (is_array($item)) {
															$images .= $item['url'] . ',';
														}
													}
													$value = rtrim($images, ',');
												}
											}

											if ($meta_type == 'select' || $meta_type == 'checkbox' || $meta_type == 'acf_relationship') {
												if (is_array($value)) {
													$value_string = '';
													foreach ($value as $item) {
														if (is_object($item)) {
															$item = $item->ID;
														}
														$value_string .= $item . ',';
													}
													$value = rtrim($value_string, ',');
												}
											}

											if ($meta_type == 'date') {
												$value = get_post_meta($post_id,$sp_custom_field['submit_post_custom_field'],true);
												$time = strtotime( $value );
												$value = date(get_option( 'date_format' ),$time);
											}

										} elseif ($form['settings']['submit_post_custom_field_source'] == 'toolset_field') {

											$meta_key = 'wpcf-' . $sp_custom_field['submit_post_custom_field'];

											$value = get_post_meta($post_id,$meta_key,false);

											if ($meta_type == 'gallery') {
												if (!empty($value)) {
													$images = '';
													foreach ($value as $item) {
														$images .= $item . ',';
													}
													$value = rtrim($images, ',');
												}
											} elseif ($meta_type == 'checkbox') {
												if (is_array($value)) {
													$value_string = '';
													foreach ($value as $item) {
														foreach ($item as $item_item) {
															$value_string .= $item_item[0] . ',';
														}
													}
													$value = rtrim($value_string, ',');
												}
											} elseif ($meta_type == 'date') {
												$value = date(get_option( 'date_format' ),$value[0]);
											} else {
												$value = $value[0];
											}

										} elseif ($form['settings']['submit_post_custom_field_source'] == 'jet_engine_field') {
											$value = get_post_meta($post_id,$sp_custom_field['submit_post_custom_field'],true);

                                            if ($meta_type == 'image') {
												if (!empty($value)) {
													$value = wp_get_attachment_url( $value );
												}
											}

											if ($meta_type == 'gallery') {
												if (!empty($value)) {
													$images = '';
													$images_id = explode(',', $value);
													foreach ($images_id as $item) {
														$images .= wp_get_attachment_url( $item ) . ',';
													}
													$value = rtrim($images, ',');
												}
											}

											if ($meta_type == 'select') {
												if (is_array($value)) {
													$value_string = '';
													foreach ($value as $item) {
														$value_string .= $item . ',';
													}
													$value = rtrim($value_string, ',');
												}
											}

											if ($meta_type == 'checkbox') {
												if (is_array($value)) {
													$value_string = '';
													foreach ($value as $key => $item) {
														if ($item == 'true') {
															$value_string .= $key . ',';
														}
													}
													$value = rtrim($value_string, ',');
												}
											}

											if ($meta_type == 'date') {
												$value = get_post_meta($post_id,$sp_custom_field['submit_post_custom_field'],true);
												$time = strtotime( $value );
												$value = date(get_option( 'date_format' ),$time);
											}

										} elseif ( function_exists( 'pods_field' ) && $form['settings']['submit_post_custom_field_source'] == 'pods_field' ) {
											$value = pods_field( $sp_post_type, $post_id, $sp_custom_field['submit_post_custom_field'],true );

											if ( $meta_type == 'image' ) {
												if ( is_array( $value ) ) {
													$value = $value['guid'];
												}
											}

											if ( $meta_type == 'gallery' ) {
												if ( is_array( $value ) ) {
													$images = '';
													foreach ( $value as $item ) {
														if ( is_array( $item ) ) {
															$images .= $item['guid'] . ',';
														}
													}
													$value = rtrim( $images, ',' );
												}
											}

											if ( $meta_type == 'date' ) {
												$value = get_post_meta( $post_id, $sp_custom_field['submit_post_custom_field'], true );
												$time  = strtotime( $value );
												$value = date( get_option( 'date_format' ), $time );
											}
                                        } elseif ( function_exists( 'rwmb_get_value' ) && $form['settings']['submit_post_custom_field_source'] == 'metabox_field' ) {
                                            $value = rwmb_get_value( $sp_custom_field['submit_post_custom_field'], array(), $post_id );

                                            if ( $meta_type == 'image' ) {

                                                $images = rwmb_get_value( $sp_custom_field['submit_post_custom_field'], array( 'limit' => 1, 'size' => 'large' ), $post_id );
                                                if ( is_array( $value ) ) {
                                                    $value = $images['url'];
                                                }
                                            }

                                            if ( $meta_type == 'gallery' ) {
                                                $value = rwmb_get_value( $sp_custom_field['submit_post_custom_field'], array( 'size' => 'large' ), $post_id );
                                                if ( is_array( $value ) ) {
                                                    $images = '';
                                                    foreach ( $value as $item ) {
                                                        if ( is_array( $item ) ) {
                                                            $images .= $item['url'] . ',';
                                                        }
                                                    }
                                                    $value = rtrim( $images, ',' );
                                                }
                                            }

                                            if ( $meta_type == 'select' || $meta_type == 'checkbox' ) {
                                                if ( is_array( $value ) ) {
                                                    $value_string = '';
                                                    foreach ( $value as $item ) {
                                                        $value_string .= $item . ',';
                                                    }
                                                    $value = rtrim( $value_string, ',' );
                                                };
                                            }

                                            if ( $meta_type == 'date' ) {
                                                $value = get_post_meta( $post_id, $sp_custom_field['submit_post_custom_field'], true );
                                                $time  = strtotime( $value );
                                                $value = date( get_option( 'date_format' ), $time );
                                            }
                                        } else {
											$value = get_post_meta($post_id,$sp_custom_field['submit_post_custom_field'],true);
										}
									}
								}
							}

						}
					}
				}
			}
		}

		return $value;

	}

	public function render_plain_content() {}

	public function get_attribute_name( $item ) {
		return "form_fields[" . trim($item['field_id']) . "]";
	}

	public function get_attribute_id( $item ) {
		return 'form-field-' . trim($item['field_id']);
	}

	private function add_required_attribute( $element ) {
		$this->add_render_attribute( $element, 'required', 'required' );
		$this->add_render_attribute( $element, 'aria-required', 'true' );
	}

	private function get_upload_file_size_options() {
		$max_file_size = wp_max_upload_size() / pow( 1024, 2 ); //MB

		$sizes = [];

		for ( $file_size = 1; $file_size <= $max_file_size; $file_size++ ) {
			$sizes[ $file_size ] = $file_size . 'MB';
		}

		return $sizes;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$editor = \Elementor\Plugin::$instance->editor->is_edit_mode();
		if (!empty($GLOBALS['pafe_editor'])) {
			$editor = true;
		}
		$item_index = 0;
		$settings['field_id'] = !empty($settings['field_id']) ? $settings['field_id'] : str_replace(['0','1','2','3','4','5','6','7','8','9'], ['a','b','c','d','e','f','g','h','i','j'], $this->get_id());
		$field_type = $settings['field_type'];
		$field_id = $settings['field_id'];
		$pafe_forms = get_post_type() == 'pafe-forms' ? true : false;
		$form_id = $pafe_forms ? get_the_ID() : $settings['form_id'];
        $form_id = !empty($form_id) ? $form_id : get_the_ID();
		$form_id = !empty($GLOBALS['pafe_form_id']) ? $GLOBALS['pafe_form_id'] : $form_id;
		$country = !(empty($settings['country'])) ? json_encode($settings['country']) : '["All"]';
		$latitude = !(empty($settings['google_maps_lat'])) ? $settings['google_maps_lat'] : '';
        $longitude = !(empty($settings['google_maps_lng'])) ? $settings['google_maps_lng'] : '';
		$zoom = !(empty($settings['google_maps_zoom'])) ? $settings['google_maps_zoom']['size'] : '';
		$field_placeholder = !(empty($settings['field_placeholder'])) ? $settings['field_placeholder'] : '';
		$field_value = pafe_dynamic_tags($settings['field_value']);
		$field_required = !(empty($settings['field_required'])) ? ' required="required" ' : '';
		$item = $settings;
		$item['input_size'] = '';
		$this->form_fields_render_attributes( $item_index, '', $item );

		$this->add_render_attribute( 'wrapper' , [
			'class' => 'elementor-form-fields-wrapper elementor-labels-above pafe-form-builder-field',
		] );

		$list_conditional = $settings['pafe_conditional_logic_form_list'];
		if( !empty($settings['pafe_conditional_logic_form_enable']) && !empty($list_conditional[0]['pafe_conditional_logic_form_if']) && !empty($list_conditional[0]['pafe_conditional_logic_form_comparison_operators']) ) {
			//$this->add_render_attribute( 'field-group' . $item_index, 'data-pafe-form-builder-conditional-logic', json_encode($list_conditional) );
			$this->add_render_attribute( 'field-group' . $item_index, [
				'data-pafe-form-builder-conditional-logic' => str_replace('\"]','', str_replace('[field id=\"','', json_encode($list_conditional))),
				'data-pafe-form-builder-conditional-logic-speed' => $settings['pafe_conditional_logic_form_speed'],
				'data-pafe-form-builder-conditional-logic-easing' => $settings['pafe_conditional_logic_form_easing'],
			] );

			wp_enqueue_script( 'pafe-form-builder-advanced-script' );
		}

		if( !empty($item['number_spiner']) && $item['field_type'] == 'number' ) {
			$this->add_render_attribute( 'field-group' . $item_index, [
				'data-pafe-form-builder-spiner' => '',
			] );
			wp_enqueue_script( 'pafe-form-builder-nice-number-script' );
		}

		if ($editor) {
			$this->add_render_attribute( 'field-group' . $item_index, [
				'data-pafe-form-builder-field' => json_encode(
					[
						'field_label' => !empty($item['field_label']) ? $item['field_label'] : '',
						'field_id' => $field_id,
						'widget_id' => $this->get_id(),
					]
				),
			] );
		}

		if ( !empty( $settings['label_animation'] ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'pafe-form-builder-label-animation' );
			wp_enqueue_script( 'pafe-form-builder-advanced2-script' );
		}
	?>
		
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'field-group' . $item_index ); ?>>
				<?php
				if ( $item['field_label'] && 'html' !== $item['field_type'] ) {
					echo '<label ';
					if (empty($item['field_label_show'])) {
						echo 'style="display:none" ';
					}
					echo $this->get_render_attribute_string( 'label' . $item_index );
					if ('honeypot' == $item['field_type']) {
						echo ' data-pafe-form-builder-honeypot';
					}
                    if(!empty($item['label_color_focus'])){
                        echo ' data-label-focus-color="'.$item['label_color_focus'].'"';
                    }
					echo '>' . $item['field_label'] . '</label>';
				}
				if(empty($settings['field_label_inline']) && $item['field_type'] != 'image_select'){
                    echo '<div data-pafe-form-builder-required></div>';
					$field_inline = '';
				}else{
					$field_inline = !empty($item['field_label_inline']) ? ' pafe-field-inline' : '';
				}

				echo '<div class="pafe-field-container'.$field_inline.'">';

				if ( ! empty( $item['field_icon_enable'] ) ) {
					echo '<div class="pafe-field-icon">';
					if ($item['field_icon_type'] == 'font_awesome') {
						if ( ! empty( $item['field_icon_font_awesome'] ) ) {
							echo '<i class="' . $item['field_icon_font_awesome'] . '"></i>';
						}
					} else {
						if ( ! empty( $item['field_icon_image'] ) ) {
							echo '<img src="' . $item['field_icon_image']['url'] . '">';
						}
					}
					echo '</div>';
				}

				switch ( $item['field_type'] ) :
					case 'html':
						echo '<div class="elementor-field elementor-size- " data-pafe-form-builder-html data-pafe-form-builder-form-id="' . $form_id . '" ' . 'id="form-field-' . $item['field_id'] . '" name="form_fields[' .  $item['field_id'] . ']">' . $item['field_html'] . '</div>';
						break;
					case 'textarea':
						echo $this->make_textarea_field( $item, $item_index, $form_id );
						break;

					case 'tinymce':
						echo $this->make_textarea_field( $item, $item_index, $form_id, true );

						if(is_rtl()) {
							$rtl="rtl";
						} else {
							$rtl="ltr";
						}

						wp_enqueue_script( 'pafe-form-builder-tinymce-script' );

						break;

					case 'select':
						echo $this->make_select_field( $item, $item_index, $form_id );
						break;

					case 'confirm':
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-form-id', $form_id );
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-confirm-field', $item['confirm_field_name'] );
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-confirm-msg', $item['confirm_error_msg'] );
						echo '<input size="1" ' . $this->get_render_attribute_string( 'input' . $item_index ) . '>';
						break;

					case 'select_autocomplete':
						echo $this->make_select_field( $item, $item_index, $form_id, false, false, true );
						wp_enqueue_script( 'pafe-form-builder-selectize-script' );
						wp_enqueue_style( 'pafe-form-builder-selectize-style' );
						wp_enqueue_script( 'pafe-form-builder-advanced2-script' );
						break;

					case 'image_select':
						wp_enqueue_script( 'pafe-form-builder-image-picker-script' );
						wp_enqueue_style( 'pafe-form-builder-image-picker-style' );
                        echo '<div data-pafe-image_select_min_select_check></div>';
						echo $this->make_select_field( $item, $item_index, $form_id, true );
						break;

					case 'terms_select':
						if ($item['terms_select_type'] == 'select') {
							echo $this->make_select_field( $item, $item_index, $form_id, false, true );
                        } else if ($item['terms_select_type'] == 'select2') {
                            wp_enqueue_script( 'pafe-select2', plugin_dir_url( __DIR__ ) . 'assets/js/minify/select2.min.js', array('jquery'), null );
                            wp_enqueue_style( 'pafe-select2-style', plugin_dir_url( __DIR__ ) . 'assets/css/select2.css');
                            echo $this->make_select_field( $item, $item_index, $form_id, false, true, false, true );
						} else if ($item['terms_select_type'] == 'autocomplete') {
                            wp_enqueue_script( 'pafe-form-builder-selectize-script' );
						    wp_enqueue_style( 'pafe-form-builder-selectize-style' );
						    wp_enqueue_script( 'pafe-form-builder-advanced2-script' );
                            echo $this->make_select_field( $item, $item_index, $form_id, false, true, true, false );
                        } else {
							echo $this->make_radio_checkbox_field( $item, $item_index, $item['terms_select_type'], $form_id, true );
						}
						
						break;

					case 'radio':
					case 'checkbox':
						echo $this->make_radio_checkbox_field( $item, $item_index, $field_type, $form_id );
						break;
					case 'text':
					case 'email':
					case 'url':
					case 'password':
					case 'hidden':
					case 'color':
					case 'iban':
						if(!empty($item['field_type_repassword']) && !empty($item['field_type_password_shortcode'])){
							$this->add_render_attribute( 'input' . $item_index, 'data-pafe-is-repassword', $item['field_type_password_shortcode'] );
							$msg_dont_match = !empty($item['msg_password_dont_match']) ? $item['msg_password_dont_match'] : "Passwords Don't Match";
							$this->add_render_attribute( 'input' . $item_index, 'data-pafe-repassword-msg', $msg_dont_match );
						}
						if($item['field_type'] == 'iban'){
							$iban_mesg = !empty($settings['iban_invalid_message']) ? $settings['iban_invalid_message'] : 'This IBAN is invalid.';
							$this->add_render_attribute( 'input' . $item_index, 'data-pafe-iban-field');
							$this->add_render_attribute( 'input' . $item_index, 'data-pafe-iban-msg', $iban_mesg);
							wp_enqueue_script( 'pafe-form-builder-iban-script' );
						}
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-form-id', $form_id );

                        if(!empty($item['field_remove_option_value'])){
                            $remove_val = !empty($item['field_value_remove']) ? $item['field_value_remove'] : '';
                            $this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-remove-value', $remove_val );
                        }

						echo '<input size="1" ' . $this->get_render_attribute_string( 'input' . $item_index ) . '>';
						if($item['field_type'] == 'password' && !empty($item['field_type_show_password_options'])){
							echo '<label for="form-field-'.$item['field_id'].'" class="pafe-show-password" data-pafe-show_password-icon="true" data-pafe-password-name="'.$item['field_id'].'"><i id="eyeIcon-'.$item['field_id'].'" class="fa fa-eye"></i></label>';
							wp_enqueue_script( 'pafe-form-builder-advanced2-script' );
						}
						break;
					case 'coupon_code':
					$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-form-id', $form_id );
					$this->remove_render_attribute( 'input' . $item_index, 'type' );
					$this->add_render_attribute( 'input' . $item_index, 'type', 'text' );
					if (!empty($item['pafe_coupon_code_list'])) {
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-coupon-code-list', json_encode($item['pafe_coupon_code_list']) );
					}
					echo '<input size="1" ' . $this->get_render_attribute_string( 'input' . $item_index ) . '>';	
					break;
					case 'honeypot':
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-form-id', $form_id );
						echo '<input size="1" ' . $this->get_render_attribute_string( 'input' . $item_index ) . ' style="display:none !important;">';	
						break;
					case 'address_autocomplete':
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-form-id', $form_id );
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-address-autocomplete', $form_id );
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-address-autocomplete-country', $country );

						$name = $this->get_field_name_shortcode($this->get_attribute_name( $item ));
						$value = $this->get_value_edit_post($name);

						if(!empty($value)) {
							$this->remove_render_attribute( 'input' . $item_index, 'value' );
							$this->remove_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-value' );
							$this->add_render_attribute( 'input' . $item_index, 'value', $value['address'] );
							$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-value', $value['address'] );
							$latitude = $value['lat'];
							$longitude = $value['lng'];
							$zoom = $value['zoom'];
						}

						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-google-maps-lat', $latitude );
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-google-maps-lng', $longitude );
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-google-maps-formatted-address', '' );
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-google-maps-zoom', $zoom );

						echo '<input size="1" ' . $this->get_render_attribute_string( 'input' . $item_index ) . '>';
						if ( ! empty( $item['google_maps'] ) ) {
							echo '<div class="pafe-form-builder-address-autocomplete-map" style="width: 100%;" data-pafe-form-builder-address-autocomplete-map></div><div class="infowindow-content"><img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" width="16" height="16" id="place-icon"><span id="place-name"  class="title"></span><br><span id="place-address"></span></div>';
						}
						if (empty(esc_attr( get_option('piotnet-addons-for-elementor-pro-google-maps-api-key') ))) {
							echo __('Please go to Dashboard > Piotnet Addons > Google Maps Integration > Enter Google Maps API Key > Save Settings', 'pafe');
						} else {
							wp_enqueue_script( 'pafe-form-builder-google-maps-init-script' );
							wp_enqueue_script( 'pafe-form-builder-google-maps-script' );
						}
						break;
					case 'image_upload':
                        echo '<div data-pafe-form-builder-image-upload-check></div>';
						$name = $this->get_field_name_shortcode($this->get_attribute_name( $item ));
						$value = $this->get_value_edit_post($name);

						if(!empty($value)) {
							$images = explode(',', $value);
							foreach ($images as $image) {
								echo '<div class="pafe-form-builder-image-upload-placeholder pafe-form-builder-image-upload-uploaded" style="background-image:url('.$image.')" data-pafe-form-builder-image-upload-placeholder=""><input type="text" style="display:none;" data-pafe-form-builder-image-upload-item value="'.$image.'"><span class="pafe-form-builder-image-upload-button pafe-form-builder-image-upload-button--remove" data-pafe-form-builder-image-upload-button-remove><i class="fa fa-times" aria-hidden="true"></i></span><span class="pafe-form-builder-image-upload-button pafe-form-builder-image-upload-button--uploading" data-pafe-form-builder-image-upload-button-uploading><i class="fa fa-spinner fa-spin"></i></span></div>';
							}
						}

						echo '<label style="width: 25%" data-pafe-form-builder-image-upload-label ';
						if ( ! empty( $item['allow_multiple_upload'] ) ) {
							echo 'multiple="multiple"';
						} else {
							if(!empty($value)) {
								echo ' class="pafe-form-builder-image-upload-label-hidden" ';
							}
						}

						if ( ! empty( $item['max_files'] ) ) {
							echo 'data-pafe-form-builder-image-upload-max-files="' . $item['max_files'] . '" ';
						}

                        if ( ! empty( $item['min_files'] ) ) {
							echo 'data-pafe-form-builder-image-upload-min-files="' . $item['min_files'] . '" ';
						}

                        if ( ! empty( $item['min_files_message'] ) ) {
                            echo 'data-pafe-form-builder-image-upload-min-files-message="' . $item['min_files_message'] . '" ';
                        }

						echo '>';
						echo '<input type="file" accept="image/*" name="upload" style="display:none;"';	
						if ( ! empty( $item['allow_multiple_upload'] ) ) {
							echo 'multiple="multiple"';
						}
						echo ' data-pafe-form-builder-image-upload>';
						echo '<div class="pafe-form-builder-image-upload-placeholder">';
						echo '<span class="pafe-form-builder-image-upload-button pafe-form-builder-image-upload-button--add" data-pafe-form-builder-image-upload-button-add><i class="fa fa-plus" aria-hidden="true"></i></span>';
						echo '<span class="pafe-form-builder-image-upload-button pafe-form-builder-image-upload-button--remove" data-pafe-form-builder-image-upload-button-remove><i class="fa fa-times" aria-hidden="true"></i></span>';
						echo '<span class="pafe-form-builder-image-upload-button pafe-form-builder-image-upload-button--uploading" data-pafe-form-builder-image-upload-button-uploading><i class="fa fa-spinner fa-spin"></i></span>';
						echo '</div>';
						echo "</label>";
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-form-id', $form_id );
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-field-type', 'image_upload' );

                        if(!empty($item['image_upload_attach_to_email'])){
                            $this->add_render_attribute( 'input' . $item_index, 'data-pafe-attach-files', json_encode($item['image_upload_attach_to']) );
                        }

						echo '<div style="display: none">';
						echo '<input type="text" ' . $item_index . ' ' . $this->get_render_attribute_string( 'input' . $item_index ) . '>';
						echo '</div>';

						wp_enqueue_script( 'pafe-form-builder-image-upload-script' );
						break;
					case 'upload':
						echo "<form action='#' class='pafe-form-builder-upload' data-pafe-form-builder-upload enctype='multipart/form-data'>";
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-form-id', $form_id );
						echo '<input type="file" ' . $this->get_render_attribute_string( 'input' . $item_index ) . '>';
						echo "</form>";
						wp_enqueue_script( 'pafe-form-builder-jquery-validation-script' );
						break;
					case 'stripe_payment':
						?>
						<script src="https://js.stripe.com/v3/"></script>
						<?php
						wp_enqueue_script( 'pafe-form-builder-stripe-script' );
						if(!empty($settings['stripe_custom_style']) && !empty($settings['stripe_custom_style_enable'])){
							$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-stripe-custom-style', $foo = preg_replace('/\s+/', '', $settings['stripe_custom_style']));
						}else{
							$stripe_style = [
								'backgroundColor' => !empty($settings['stripe_background_color']) ? $settings['stripe_background_color'] : '#fff',
								'color' => !empty($settings['stripe_color']) ? $settings['stripe_color'] : '#303238',
								'placeholderColor' => !empty($settings['stripe_placeholder_color']) ? $settings['stripe_placeholder_color'] : '#aab7c4',
								'fontSize' => !empty($settings['stripe_font_size']) ? $settings['stripe_font_size']['size'].'px' : '16px',
								'iconColor' => !empty($settings['stripe_icon_color']) ? $settings['stripe_icon_color'] : ''
							];
							$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-stripe-style', json_encode($stripe_style) );
						}
						$stripe_font = !empty($settings['stripe_custom_font_family']) ? $settings['stripe_custom_font_family'] : '';
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-stripe-font-family', $stripe_font );
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-form-id', $form_id );
						$this->add_render_attribute( 'input' . $item_index, 'class', 'pafe-form-builder-stripe');
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-stripe', '' );
						echo '<div ' . $this->get_render_attribute_string( 'input' . $item_index ) . '></div><div class="card-errors"></div>';	
						break;
					case 'range_slider':
						$this->add_render_attribute( 'range_slider' . $item_index, 'data-pafe-form-builder-form-id', $form_id );
						echo '<input size="1" ' . $this->get_render_attribute_string( 'range_slider' . $item_index ) . '>';
						wp_enqueue_script( 'pafe-form-builder-range-slider-script' );
						wp_enqueue_style( 'pafe-form-builder-range-slider-style' );
					?>
						<script>
							(function ($) {
								var WidgetPafeFormBuilderHandlerRangeSlider<?php echo str_replace('-', '_', $item['field_id']); ?> = function ($scope, $) {

								    var $elements = $scope.find('[data-pafe-form-builder-range-slider]');

									if (!$elements.length) {
										return;
									}

									$.each($elements, function (i, $element) {
										let rangerOptions = $(this).attr('data-pafe-form-builder-range-slider-options');
										if ($($element).siblings('.irs').length == 0) {
                                            <?php if($this->pafe_is_json($item['pafe_range_slider_field_options'])){ ?>
											    $('#form-field-<?php echo $item['field_id']; ?>').ionRangeSlider(JSON.parse(rangerOptions));
                                            <?php }else{ ?>
                                                $('#form-field-<?php echo $item['field_id']; ?>').ionRangeSlider({
                                                    <?php echo $item['pafe_range_slider_field_options']; ?>
                                                });
                                            <?php } ?>
										}

										$($element).change();
									});

								};

								$(window).on('elementor/frontend/init', function () {
							        elementorFrontend.hooks.addAction('frontend/element_ready/pafe-form-builder-field.default', WidgetPafeFormBuilderHandlerRangeSlider<?php echo $item['field_id']; ?>);
							    });

							}(jQuery)); 
						</script>
					<?php
						break;
					case 'calculated_fields':
						echo '<div class="pafe-calculated-fields-form" style="width: 100%">' . $item['pafe_calculated_fields_form_before'] . '<span class="pafe-calculated-fields-form__value"></span>' . $item['pafe_calculated_fields_form_after'] . '</div>';
						$this->add_render_attribute( 'calculated_fields' . $item_index, 'data-pafe-form-builder-form-id', $form_id );
						
						if (!empty($item['pafe_calculated_fields_form_coupon_code'])) {
							$this->add_render_attribute( 'calculated_fields' . $item_index, 'data-pafe-form-builder-calculated-fields-coupon-code', $item['pafe_calculated_fields_form_coupon_code'] );
						}

						if (!empty($item['pafe_calculated_fields_form_distance_calculation'])) {
							$this->add_render_attribute( 'calculated_fields' . $item_index, 'data-pafe-form-builder-calculated-fields-distance-calculation', '' );
							$this->add_render_attribute( 'calculated_fields' . $item_index, 'data-pafe-form-builder-calculated-fields-distance-calculation-from-field-shortcode', $item['pafe_calculated_fields_form_distance_calculation_from_field_shortcode'] );
							$this->add_render_attribute( 'calculated_fields' . $item_index, 'data-pafe-form-builder-calculated-fields-distance-calculation-to-field-shortcode', $item['pafe_calculated_fields_form_distance_calculation_to_field_shortcode'] );
							$this->add_render_attribute( 'calculated_fields' . $item_index, 'data-pafe-form-builder-calculated-fields-distance-calculation-unit', $item['pafe_calculated_fields_form_distance_calculation_unit'] );

							if (!empty($item['pafe_calculated_fields_form_distance_calculation_from_specific_location'])) {
								$this->add_render_attribute( 'calculated_fields' . $item_index, 'data-pafe-form-builder-calculated-fields-distance-calculation-from', $item['pafe_calculated_fields_form_distance_calculation_from_specific_location'] );
							}

							if (!empty($item['pafe_calculated_fields_form_distance_calculation_to_specific_location'])) {
								$this->add_render_attribute( 'calculated_fields' . $item_index, 'data-pafe-form-builder-calculated-fields-distance-calculation-to', $item['pafe_calculated_fields_form_distance_calculation_to_specific_location'] );
							}
						}

						echo '<input style="display:none!important;" size="1" ' . $this->get_render_attribute_string( 'calculated_fields' . $item_index ) . '>';

						wp_enqueue_script( 'pafe-form-builder-advanced-script' );

						break;
					case 'tel':
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-form-id', $form_id );
						$this->add_render_attribute( 'input' . $item_index, 'pattern', esc_attr( $item['field_pattern'] ) );
						$this->add_render_attribute( 'input' . $item_index, 'title', __( 'Only numbers and phone characters (#, -, *, etc) are accepted.', 'elementor-pro' ) );
                        if ( !empty($item['field_dial_code']) ) {
                            $this->add_render_attribute( 'input' . $item_index, 'data-pafe-tel-field');
                            wp_enqueue_script( 'pafe-form-builder-international-tel-script' );
                        }
                        echo '<input size="1" '. $this->get_render_attribute_string( 'input' . $item_index ) . '>';

                        break;
					case 'number':
                        $step = !empty($item['field_step']) ? $item['field_step'] : 'any';
						if(!empty($settings['field_value_remove']) || $settings['field_value_remove'] == '0'){
							$remove_value = $settings['field_value_remove'];
						}else{
							$remove_value = 'false';
						}
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-form-id', $form_id );
						$this->add_render_attribute( 'input' . $item_index, 'class', 'elementor-field-textual' );
						$this->add_render_attribute( 'input' . $item_index, 'step', $step );
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-remove-value', $remove_value);
                        if ( !empty( $item['field_min'] ) || $item['field_min'] === 0 ) {
							$this->add_render_attribute( 'input' . $item_index, 'min', esc_attr( $item['field_min'] ) );
						}

						if ( !empty( $item['field_max'] ) || $item['field_max'] === 0 ) {
							$this->add_render_attribute( 'input' . $item_index, 'max', esc_attr( $item['field_max'] ) );
						}

						echo '<input ' . $this->get_render_attribute_string( 'input' . $item_index ) . '>';

						wp_enqueue_script( 'pafe-form-builder-advanced2-script' );
						break;
					case 'acceptance':
						$label = '';
						$this->add_render_attribute( 'input' . $item_index, 'class', 'elementor-acceptance-field' );
						$this->add_render_attribute( 'input' . $item_index, 'type', 'checkbox', true );
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-form-id', $form_id );
						$this->add_render_attribute( 'input' . $item_index, 'value', 'on' );

						if ( ! empty( $item['acceptance_text'] ) ) {
							$label = '<label for="' . $this->get_attribute_id( $item ) . '" class="pafe-checkbox-label">' . $item['acceptance_text'] . '</label>';
						}

						if ( ! empty( $item['checked_by_default'] ) ) {
							$this->add_render_attribute( 'input' . $item_index, 'checked', 'checked' );
						}

						echo '<div class="elementor-field-subgroup"><span class="elementor-field-option"><input ' . $this->get_render_attribute_string( 'input' . $item_index ) . '> ' . $label . '</span></div>';
						break;
					case 'date':
					   	wp_enqueue_style( 'pafe-form-builder-flatpickr-style' );
						wp_enqueue_script( 'pafe-form-builder-date-time-script' );
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-form-id', $form_id );
						$this->add_render_attribute( 'input' . $item_index, 'class', 'elementor-field-textual elementor-date-field' );

						if ( isset( $item['use_native_date'] ) && 'yes' === $item['use_native_date'] ) {
							$this->add_render_attribute( 'input' . $item_index, 'class', 'elementor-use-native' );
						}
                        wp_enqueue_script( 'pafe-form-builder-flatpickr-script' );
						if ( $item['date_language'] != 'english' ) {
							wp_enqueue_script( 'pafe-flatpickr-language-' . $item['date_language'], plugin_dir_url( __DIR__ ) . 'languages/date/' . $item['date_language'] . ".js", array('pafe-form-builder-flatpickr-script'), null );
						}

						if ( empty( $item['flatpickr_custom_options_enable'] ) ) {

							if ( ! empty( $item['min_date'] ) && empty( $item['min_date_current'] ) ) {
								$this->add_render_attribute( 'input' . $item_index, 'min', esc_attr( $item['min_date'] ) );
							}

							if ( ! empty( $item['min_date_current'] ) ) {
								$this->add_render_attribute( 'input' . $item_index, 'min', esc_attr( wp_date( 'Y-m-d' ) ) );
							}

							if ( ! empty( $item['max_date'] )  && empty( $item['max_date_current'] ) ) {
								$this->add_render_attribute( 'input' . $item_index, 'max', esc_attr( $item['max_date'] ) );
							}

							if ( ! empty( $item['max_date_current'] ) ) {
								$this->add_render_attribute( 'input' . $item_index, 'max', esc_attr( wp_date( 'Y-m-d' ) ) );
							}

							if ( ! empty( $item['date_range'] ) ) {
								$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-date-range', '' );
								$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-date-range-days', '' );
							}

							$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-date-language', esc_attr( $item['date_language'] ) );

							$this->add_render_attribute( 'input' . $item_index, 'data-date-format', esc_attr( $item['date_format'] ) );

						} else {
							$this->add_render_attribute( 'input' . $item_index, 'class' , 'flatpickr-custom-options' );
							$this->add_render_attribute( 'input' . $item_index, 'data-pafe-flatpickr-custom-options', esc_attr( $item['flatpickr_custom_options'] ) );
						}

						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-date-calculate', 0 );

						echo '<input ' . $this->get_render_attribute_string( 'input' . $item_index ) . '>';
							if ( !empty( $item['flatpickr_custom_options_enable'] ) && !empty( $item['flatpickr_custom_options'] ) ) :
						?>
								<script>
									(function ($) {
										var WidgetPafeFormBuilderHandlerDate<?php echo str_replace('-', '_', $item['field_id']); ?> = function ($scope, $) {

										    var $elements = $scope.find('.elementor-date-field');

											if (!$elements.length) {
												return;
											}

											var $elements = $scope.find('#form-field-<?php echo $item['field_id']; ?>');

											if (!$elements.length) {
												return;
											}
                                            var options = <?php echo $item['flatpickr_custom_options']; ?>;
                                            <?php if ($item['field_required']) : ?>
                                                options['allowInput'] = true;
                                            <?php endif;?>
											$.each($elements, function (i, $element) {
												$element.flatpickr(options);
											});

										};

										$(window).on('elementor/frontend/init', function () {
									        elementorFrontend.hooks.addAction('frontend/element_ready/pafe-form-builder-field.default', WidgetPafeFormBuilderHandlerDate<?php echo $item['field_id']; ?>);
									    });

									}(jQuery)); 
								</script>
						<?php
							endif;
						break;
					case 'time':
						wp_enqueue_script( 'pafe-form-builder-flatpickr-script' );
						wp_enqueue_style( 'pafe-form-builder-flatpickr-style' );
						wp_enqueue_script( 'pafe-form-builder-date-time-script' );

                        $time_format = !empty($item['time_format']) ? $item['time_format'] : null;
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-form-id', $form_id );
                        $time_step = $time_format == 'H:i:s' ? 1 : 60;
                        $this->add_render_attribute( 'input' . $item_index, 'step', $time_step );
						$this->add_render_attribute( 'input' . $item_index, 'class', 'elementor-field-textual elementor-time-field' );
						if ( isset( $item['use_native_time'] ) && 'yes' === $item['use_native_time'] ) {
							$this->add_render_attribute( 'input' . $item_index, 'class', 'elementor-use-native' );
						}
						$this->add_render_attribute( 'input' . $item_index, 'data-time-format', esc_attr( $item['time_format'] ) );

						$this->add_render_attribute( 'input' . $item_index, 'data-time-minute-increment', esc_attr( $item['time_minute_increment'] ) );

						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-time-calculate', 0 );

						if ( ! empty( $item['time_24hr'] ) ) {
							$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-time-24hr', '' );
						}
						echo '<input ' . $this->get_render_attribute_string( 'input' . $item_index ) . '>';
						break;
					case 'signature':
						echo '<div class="pafe-signature" data-pafe-signature><canvas class="not-resize" width="' . $item['signature_max_width']['size'] . '" height="' . $item['signature_height']['size'] . '"></canvas>';
						$this->add_render_attribute( 'input' . $item_index, 'data-pafe-form-builder-form-id', $form_id );
						echo '<input ' . $this->get_render_attribute_string( 'input' . $item_index ) . '>';
						echo '<div>';
						echo '<button type="button" class="pafe-signature-clear" data-pafe-signature-clear>' . $item['signature_clear_text'] . '</button>';
						echo '<button type="button" class="pafe-signature-export" data-pafe-signature-export style="display:none"></button>';
						echo '</div>';
						echo '</div>';
						wp_enqueue_script( 'pafe-form-builder-signature-script' );
						break;
					default:
						$field_type = $item['field_type'];

						/**
						 * Elementor form field render.
						 *
						 * Fires when a field is rendered.
						 *
						 * The dynamic portion of the hook name, `$field_type`, refers to the field type.
						 *
						 * @since 1.0.0
						 *
						 * @param array $item       The field value.
						 * @param int   $item_index The field index.
						 * @param Form  $this       An instance of the form.
						 */
						do_action( "elementor_pro/forms/render_field/{$field_type}", $item, $item_index, $this );
				endswitch;

				echo '</div>';
				if(!empty($settings['field_label_inline'])){
					echo '<div data-pafe-form-builder-required></div>';
				}
				?>
			</div>
		</div>
	<?php
	}

	public function add_wpml_support() {
		add_filter( 'wpml_elementor_widgets_to_translate', [ $this, 'wpml_widgets_to_translate_filter' ] );
	}

	public function wpml_widgets_to_translate_filter( $widgets ) {
		$widgets[ $this->get_name() ] = [
			'conditions' => [ 'widgetType' => $this->get_name() ],
			'fields'     => [
				[
					'field'       => 'field_label',
					'type'        => __( 'Field Label', 'pafe' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'field_placeholder',
					'type'        => __( 'Field Placeholder', 'pafe' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'invalid_message',
					'type'        => __( 'Invalid Message', 'pafe' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'field_options',
					'type'        => __( 'Field Options', 'pafe' ),
					'editor_type' => 'AREA'
				],
				[	
					'field'       => 'pafe_calculated_fields_form_before',
					'type'        => __( 'Calculated Fields Before Content', 'pafe' ),
					'editor_type' => 'LINE'
				],
				[	
					'field'       => 'pafe_calculated_fields_form_after',
					'type'        => __( 'Calculated Fields After Content', 'pafe' ),
					'editor_type' => 'LINE'
				],
			],
		];

		return $widgets;
	}
	public function pafe_is_json($string){
		json_decode($string);
		return json_last_error() === JSON_ERROR_NONE;
	}
}
