<?php

namespace ElementPack\Modules\SvgMaps\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if (!defined('ABSPATH')) {
	exit;
}

class Svg_Maps extends Module_Base {

	public function get_name() {
		return 'bdt-svg-maps';
	}

	public function get_title() {
		return BDTEP . esc_html__('SVG Maps', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-svg-maps';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['geochart', 'svg-maps', 'map', 'google-map', 'svg-map', 'svg', 'map-chart', 'chart'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-svg-maps'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['gs-charts', 'ep-scripts'];
		} else {
			return ['gs-charts', 'ep-svg-maps'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/07WomY1e9-U';
	}

	protected function register_controls() {
		$this->section_content_map_settings();
		$this->section_content_regions();
		$this->section_style_map();
		$this->section_style_tooltip();
	}


	private function section_content_map_settings() {

		$this->start_controls_section('section_content_map_settings', [
			'label' => esc_html__('Map settings', 'bdthemes-element-pack'),
			'tab'   => Controls_Manager::TAB_CONTENT
		]);

		$this->add_control(
			'svg_maps_region_type',
			[
				'label'      => __('Region Type', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SELECT,
				'options'    => [
					'world'   => __('World (Default)', 'bdthemes-element-pack'),
					'continent'  => __('Continent', 'bdthemes-element-pack'),
					'subcontinent' => __('Sub Continent', 'bdthemes-element-pack'),
					'countries' => __('Countries', 'bdthemes-element-pack'),
				],
				'default'    => 'world',
				'dynamic'    => ['active' => true],
				'frontend_available' => true,
				'render_type' => 'none',
			]
		);
		$this->add_control(
			'svg_maps_display_region_continent',
			[
				'label'      => __('Select Continent', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SELECT,
				'options'    => [
					'002'   => __('Africa', 'bdthemes-element-pack'),
					'150'  => __('Europe', 'bdthemes-element-pack'),
					'019' => __('Americas', 'bdthemes-element-pack'),
					'142' => __('Asia', 'bdthemes-element-pack'),
					'009' => __('Oceania', 'bdthemes-element-pack'),
				],
				'condition'     => [
					'svg_maps_region_type' => 'continent',
				],
				'frontend_available' => true,
				'render_type' => 'none',
				'default'    => '002',
				'dynamic'    => ['active' => true],
			]
		);

		$this->add_control(
			'svg_maps_display_region_sub_continent',
			[
				'label'      => __('Select Sub Continent', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SELECT,
				'options'    => [
					// AFRICA
					'015' => __('Northern Africa', 'bdthemes-element-pack'),
					'011'  => __('Western Africa', 'bdthemes-element-pack'),
					'017' => __('Middle Africa', 'bdthemes-element-pack'),
					'014' => __('Eastern Africa', 'bdthemes-element-pack'),
					'018' => __('Southern Africa', 'bdthemes-element-pack'),
					//AMERICA
					'154' => __('Northern Europe', 'bdthemes-element-pack'),
					'155' => __('Western Europe', 'bdthemes-element-pack'),
					'151' => __('Eastern Europe', 'bdthemes-element-pack'),
					'039' => __('Southern Europe', 'bdthemes-element-pack'),
					//AMERICA
					'021' => __('Northern America', 'bdthemes-element-pack'),
					'029' => __('Caribbean', 'bdthemes-element-pack'),
					'013' => __('Central America', 'bdthemes-element-pack'),
					'005' => __('South America', 'bdthemes-element-pack'),
					//ASIA
					'143' => __('Central Asia', 'bdthemes-element-pack'),
					'030' => __('Eastern Asia', 'bdthemes-element-pack'),
					'034' => __('Southern Asia', 'bdthemes-element-pack'),
					'035' => __('South-Eastern Asia', 'bdthemes-element-pack'),
					'145' => __('Western Asia', 'bdthemes-element-pack'),
					//OCEANIA
					// '009' => __('Oceania', 'bdthemes-element-pack'),
					'053' => __('Australia and New Zealand', 'bdthemes-element-pack'),
					'054' => __('Melanesia', 'bdthemes-element-pack'),
					'057' => __('Micronesia', 'bdthemes-element-pack'),
					'061' => __('Polynesia', 'bdthemes-element-pack'),
				],
				'default'    => '015',
				'frontend_available' => true,
				'render_type' => 'none',
				'condition'     => [
					'svg_maps_region_type' => 'subcontinent',
				],
			]
		);
		$this->add_control(
			'svg_maps_display_region_countries',
			[
				'label'      => __('Select Country', 'bdthemes-element-pack'),
				'label_block' => true,
				'type'       => Controls_Manager::SELECT2,
				'options'    => [
					// NORTHERN AFRICA
					'DZ'  => __('Algeria (Northern Africa)', 'bdthemes-element-pack'),
					'EG'  => __('Egypt (Northern Africa)', 'bdthemes-element-pack'),
					'EH' => __('Western Sahara (Northern Africa)', 'bdthemes-element-pack'),
					'LY' => __('Libya (Northern Africa)', 'bdthemes-element-pack'),
					'MA' => __('Morocco (Northern Africa)', 'bdthemes-element-pack'),
					'SD' => __('Sudan (Northern Africa)', 'bdthemes-element-pack'),
					'SS' => __('South Sudan (Northern Africa)', 'bdthemes-element-pack'),
					'SH' => __('Saint Helena (Northern Africa)', 'bdthemes-element-pack'),
					'TD' => __('Chad (Northern Africa)', 'bdthemes-element-pack'),
					'TN' => __('Tunisia (Northern Africa)', 'bdthemes-element-pack'),
					// WESTERN AFRICA
					'BF' => __('Burkina Faso (Western Africa)', 'bdthemes-element-pack'),
					'BJ' => __('Benin (Western Africa)', 'bdthemes-element-pack'),
					'CI' => __('Ivory Coast (Western Africa)', 'bdthemes-element-pack'),
					'CV' => __('Cape Verde (Western Africa)', 'bdthemes-element-pack'),
					'GH' => __('Ghana (Western Africa)', 'bdthemes-element-pack'),
					'GM' => __('Gambia (Western Africa)', 'bdthemes-element-pack'),
					'GN' => __('Guinea (Western Africa)', 'bdthemes-element-pack'),
					'GW' => __('Guinea-Bissau (Western Africa)', 'bdthemes-element-pack'),
					'LR' => __('Liberia (Western Africa)', 'bdthemes-element-pack'),
					'ML' => __('Mali (Western Africa)', 'bdthemes-element-pack'),
					'MR' => __('Mauritania (Western Africa)', 'bdthemes-element-pack'),
					'NE' => __('Niger (Western Africa)', 'bdthemes-element-pack'),
					'NG' => __('Nigeria (Western Africa)', 'bdthemes-element-pack'),
					'SL' => __('Sierra Leone (Western Africa)', 'bdthemes-element-pack'),
					'SN' => __('Senegal (Western Africa)', 'bdthemes-element-pack'),
					'TG' => __('Togo (Western Africa)', 'bdthemes-element-pack'),
					// MIDDLE AFRICA
					'AO' => __('Angola (Middle Africa)', 'bdthemes-element-pack'),
					'CD' => __('Democratic Republic of the Congo (Middle Africa)', 'bdthemes-element-pack'),
					'CF' => __('Central African Republic (Middle Africa)', 'bdthemes-element-pack'),
					'CG' => __('Congo (Brazzaville) (Middle Africa)', 'bdthemes-element-pack'),
					'CM' => __('Cameroon (Middle Africa)', 'bdthemes-element-pack'),
					'GA' => __('Gabon (Middle Africa)', 'bdthemes-element-pack'),
					'GQ' => __('Equatorial Guinea (Middle Africa)', 'bdthemes-element-pack'),
					'ST' => __('Sao Tome and Principe (Middle Africa)', 'bdthemes-element-pack'),
					'TD' => __('Chad (Middle Africa)', 'bdthemes-element-pack'),
					// EASTERN AFRICA
					'BI' => __('Burundi (Eastern Africa)', 'bdthemes-element-pack'),
					'DJ' => __('Djibouti (Eastern Africa)', 'bdthemes-element-pack'),
					'ER' => __('Eritrea (Eastern Africa)', 'bdthemes-element-pack'),
					'ET' => __('Ethiopia (Eastern Africa)', 'bdthemes-element-pack'),
					'KE' => __('Kenya (Eastern Africa)', 'bdthemes-element-pack'),
					'KM' => __('Comoros (Eastern Africa)', 'bdthemes-element-pack'),
					'MG' => __('Madagascar (Eastern Africa)', 'bdthemes-element-pack'),
					'MW' => __('Malawi (Eastern Africa)', 'bdthemes-element-pack'),
					'MU' => __('Mauritius (Eastern Africa)', 'bdthemes-element-pack'),
					'MZ' => __('Mozambique (Eastern Africa)', 'bdthemes-element-pack'),
					'RE' => __('Reunion (Eastern Africa)', 'bdthemes-element-pack'),
					'RW' => __('Rwanda (Eastern Africa)', 'bdthemes-element-pack'),
					'SC' => __('Seychelles (Eastern Africa)', 'bdthemes-element-pack'),
					'SO' => __('Somalia (Eastern Africa)', 'bdthemes-element-pack'),
					'TZ' => __('Tanzania (Eastern Africa)', 'bdthemes-element-pack'),
					'UG' => __('Uganda (Eastern Africa)', 'bdthemes-element-pack'),
					'YT' => __('Mayotte (Eastern Africa)', 'bdthemes-element-pack'),
					'ZM' => __('Zambia (Eastern Africa)', 'bdthemes-element-pack'),
					'ZW' => __('Zimbabwe (Eastern Africa)', 'bdthemes-element-pack'),
					// SOUTHERN AFRICA
					'BW' => __('Botswana (Southern Africa)', 'bdthemes-element-pack'),
					'LS' => __('Lesotho (Southern Africa)', 'bdthemes-element-pack'),
					'NA' => __('Namibia (Southern Africa)', 'bdthemes-element-pack'),
					'SZ' => __('Swaziland (Southern Africa)', 'bdthemes-element-pack'),
					'ZA' => __('South Africa (Southern Africa)', 'bdthemes-element-pack'),
					//CENTEAL ASIA
					'KZ' => __('Kazakhstan (Central Asia)', 'bdthemes-element-pack'),
					'KG' => __('Kyrgyzstan (Central Asia)', 'bdthemes-element-pack'),
					'TJ' => __('Tajikistan (Central Asia)', 'bdthemes-element-pack'),
					'TM' => __('Turkmenistan (Central Asia)', 'bdthemes-element-pack'),
					'UZ' => __('Uzbekistan (Central Asia)', 'bdthemes-element-pack'),
					// WESTERN ASIA
					'AE' => __('United Arab Emirates (Western Asia)', 'bdthemes-element-pack'),
					'AM' => __('Armenia (Western Asia)', 'bdthemes-element-pack'),
					'AZ' => __('Azerbaijan (Western Asia)', 'bdthemes-element-pack'),
					'BH' => __('Bahrain (Western Asia)', 'bdthemes-element-pack'),
					'CY' => __('Cyprus (Western Asia)', 'bdthemes-element-pack'),
					'GE' => __('Georgia (Western Asia)', 'bdthemes-element-pack'),
					'IQ' => __('Iraq (Western Asia)', 'bdthemes-element-pack'),
					// SOUTHERN EUROPE
					'AL' => __('Albania (Southern Europe)', 'bdthemes-element-pack'),
					'AD' => __('Andorra (Southern Europe)', 'bdthemes-element-pack'),
					'BA' => __('Bosnia and Herzegovina (Southern Europe)', 'bdthemes-element-pack'),
					'HR' => __('Croatia (Southern Europe)', 'bdthemes-element-pack'),
					'GI' => __('Gibraltar (Southern Europe)', 'bdthemes-element-pack'),
					'GR' => __('Greece (Southern Europe)', 'bdthemes-element-pack'),
					'VA' => __('Vatican City (Southern Europe)', 'bdthemes-element-pack'),
					'IT' => __('Italy (Southern Europe)', 'bdthemes-element-pack'),
					'MK' => __('Macedonia (Southern Europe)', 'bdthemes-element-pack'),
					'MT' => __('Malta (Southern Europe)', 'bdthemes-element-pack'),
					// WESTERN EUROPE
					'AT' => __('Austria (Western Europe)', 'bdthemes-element-pack'),
					'BE' => __('Belgium (Western Europe)', 'bdthemes-element-pack'),
					'FR' => __('France (Western Europe)', 'bdthemes-element-pack'),
					'DE' => __('Germany (Western Europe)', 'bdthemes-element-pack'),
					'LI' => __('Liechtenstein (Western Europe)', 'bdthemes-element-pack'),
					'LU' => __('Luxembourg (Western Europe)', 'bdthemes-element-pack'),
					'MC' => __('Monaco (Western Europe)', 'bdthemes-element-pack'),
					'NL' => __('Netherlands (Western Europe)', 'bdthemes-element-pack'),
					'CH' => __('Switzerland (Western Europe)', 'bdthemes-element-pack'),
					// EASTERN EUROPE
					'BY' => __('Belarus (Eastern Europe)', 'bdthemes-element-pack'),
					'BG' => __('Bulgaria (Eastern Europe)', 'bdthemes-element-pack'),
					'CZ' => __('Czech Republic (Eastern Europe)', 'bdthemes-element-pack'),
					'HU' => __('Hungary (Eastern Europe)', 'bdthemes-element-pack'),
					'MD' => __('Moldova (Eastern Europe)', 'bdthemes-element-pack'),
					'PL' => __('Poland (Eastern Europe)', 'bdthemes-element-pack'),
					'RO' => __('Romania (Eastern Europe)', 'bdthemes-element-pack'),
					'RU' => __('Russia (Eastern Europe)', 'bdthemes-element-pack'),
					'SK' => __('Slovakia (Eastern Europe)', 'bdthemes-element-pack'),
					'UA' => __('Ukraine (Eastern Europe)', 'bdthemes-element-pack'),
					// NORTHERN EUROPE
					'DK' => __('Denmark (Northern Europe)', 'bdthemes-element-pack'),
					'EE' => __('Estonia (Northern Europe)', 'bdthemes-element-pack'),
					'FO' => __('Faroe Islands (Northern Europe)', 'bdthemes-element-pack'),
					'FI' => __('Finland (Northern Europe)', 'bdthemes-element-pack'),
					'GG' => __('Guernsey (Northern Europe)', 'bdthemes-element-pack'),
					'IS' => __('Iceland (Northern Europe)', 'bdthemes-element-pack'),
					'IE' => __('Ireland (Northern Europe)', 'bdthemes-element-pack'),
					// SOUTHERN AMERICA
					'AR' => __('Argentina (Southern America)', 'bdthemes-element-pack'),
					'BO' => __('Bolivia (Southern America)', 'bdthemes-element-pack'),
					'BR' => __('Brazil (Southern America)', 'bdthemes-element-pack'),
					'CL' => __('Chile (Southern America)', 'bdthemes-element-pack'),
					'CO' => __('Colombia (Southern America)', 'bdthemes-element-pack'),
					'EC' => __('Ecuador (Southern America)', 'bdthemes-element-pack'),
					'FK' => __('Falkland Islands (Southern America)', 'bdthemes-element-pack'),
					'GF' => __('French Guiana (Southern America)', 'bdthemes-element-pack'),
					'GY' => __('Guyana (Southern America)', 'bdthemes-element-pack'),
					'PY' => __('Paraguay (Southern America)', 'bdthemes-element-pack'),
					'PE' => __('Peru (Southern America)', 'bdthemes-element-pack'),
					'SR' => __('Suriname (Southern America)', 'bdthemes-element-pack'),
					'UY' => __('Uruguay (Southern America)', 'bdthemes-element-pack'),
					'VE' => __('Venezuela (Southern America)', 'bdthemes-element-pack'),
					// NORTHERN AMERICA
					'BM' => __('Bermuda (Northern America)', 'bdthemes-element-pack'),
					'CA' => __('Canada (Northern America)', 'bdthemes-element-pack'),
					'GL' => __('Greenland (Northern America)', 'bdthemes-element-pack'),
					'PM' => __('Saint Pierre and Miquelon (Northern America)', 'bdthemes-element-pack'),
					'US' => __('United States (Northern America)', 'bdthemes-element-pack'),
					// CARIBBEAN
					'AI' => __('Anguilla (Caribbean)', 'bdthemes-element-pack'),
					'AG' => __('Antigua and Barbuda (Caribbean)', 'bdthemes-element-pack'),
					'AW' => __('Aruba (Caribbean)', 'bdthemes-element-pack'),
					'BS' => __('Bahamas (Caribbean)', 'bdthemes-element-pack'),
					'BB' => __('Barbados (Caribbean)', 'bdthemes-element-pack'),
					'BQ' => __('Bonaire, Sint Eustatius and Saba (Caribbean)', 'bdthemes-element-pack'),
					'KY' => __('Cayman Islands (Caribbean)', 'bdthemes-element-pack'),
					'CU' => __('Cuba (Caribbean)', 'bdthemes-element-pack'),
					'CW' => __('Curaçao (Caribbean)', 'bdthemes-element-pack'),
					'DM' => __('Dominica (Caribbean)', 'bdthemes-element-pack'),
					'DO' => __('Dominican Republic (Caribbean)', 'bdthemes-element-pack'),
					'GD' => __('Grenada (Caribbean)', 'bdthemes-element-pack'),
					'GP' => __('Guadeloupe (Caribbean)', 'bdthemes-element-pack'),
					'HT' => __('Haiti (Caribbean)', 'bdthemes-element-pack'),
					'JM' => __('Jamaica (Caribbean)', 'bdthemes-element-pack'),
					'MQ' => __('Martinique (Caribbean)', 'bdthemes-element-pack'),
					'MS' => __('Montserrat (Caribbean)', 'bdthemes-element-pack'),
					'PR' => __('Puerto Rico (Caribbean)', 'bdthemes-element-pack'),
					'BL' => __('Saint Barthélemy (Caribbean)', 'bdthemes-element-pack'),
					'KN' => __('Saint Kitts and Nevis (Caribbean)', 'bdthemes-element-pack'),
					'LC' => __('Saint Lucia (Caribbean)', 'bdthemes-element-pack'),
					'MF' => __('Saint Martin (French part) (Caribbean)', 'bdthemes-element-pack'),
					'VC' => __('Saint Vincent and the Grenadines (Caribbean)', 'bdthemes-element-pack'),
					'SX' => __('Sint Maarten (Dutch part) (Caribbean)', 'bdthemes-element-pack'),
					'TT' => __('Trinidad and Tobago (Caribbean)', 'bdthemes-element-pack'),
					'TC' => __('Turks and Caicos Islands (Caribbean)', 'bdthemes-element-pack'),
					'VG' => __('Virgin Islands (British) (Caribbean)', 'bdthemes-element-pack'),
					'VI' => __('Virgin Islands (U.S.) (Caribbean)', 'bdthemes-element-pack'),
					//CENTRAL AMERICA
					'BZ' => __('Belize (Central America)', 'bdthemes-element-pack'),
					'CR' => __('Costa Rica (Central America)', 'bdthemes-element-pack'),
					'SV' => __('El Salvador (Central America)', 'bdthemes-element-pack'),
					'GT' => __('Guatemala (Central America)', 'bdthemes-element-pack'),
					'HN' => __('Honduras (Central America)', 'bdthemes-element-pack'),
					'MX' => __('Mexico (Central America)', 'bdthemes-element-pack'),
					'NI' => __('Nicaragua (Central America)', 'bdthemes-element-pack'),
					'PA' => __('Panama (Central America)', 'bdthemes-element-pack'),
					// CENTRAL ASIA
					'KZ' => __('Kazakhstan (Central Asia)', 'bdthemes-element-pack'),
					'KG' => __('Kyrgyzstan (Central Asia)', 'bdthemes-element-pack'),
					'TJ' => __('Tajikistan (Central Asia)', 'bdthemes-element-pack'),
					'TM' => __('Turkmenistan (Central Asia)', 'bdthemes-element-pack'),
					'UZ' => __('Uzbekistan (Central Asia)', 'bdthemes-element-pack'),
					// EASTERN ASIA
					'CN' => __('China (Eastern Asia)', 'bdthemes-element-pack'),
					'HK' => __('Hong Kong (Eastern Asia)', 'bdthemes-element-pack'),
					'JP' => __('Japan (Eastern Asia)', 'bdthemes-element-pack'),
					'KP' => __('North Korea (Eastern Asia)', 'bdthemes-element-pack'),
					'KR' => __('South Korea (Eastern Asia)', 'bdthemes-element-pack'),
					'MO' => __('Macau (Eastern Asia)', 'bdthemes-element-pack'),
					'MN' => __('Mongolia (Eastern Asia)', 'bdthemes-element-pack'),
					'TW' => __('Taiwan (Eastern Asia)', 'bdthemes-element-pack'),
					// SOUTHERN ASIA
					'AF' => __('Afghanistan (Southern Asia)', 'bdthemes-element-pack'),
					'BD' => __('Bangladesh (Southern Asia)', 'bdthemes-element-pack'),
					'BT' => __('Bhutan (Southern Asia)', 'bdthemes-element-pack'),
					'IN' => __('India (Southern Asia)', 'bdthemes-element-pack'),
					'IR' => __('Iran (Southern Asia)', 'bdthemes-element-pack'),
					'MV' => __('Maldives (Southern Asia)', 'bdthemes-element-pack'),
					'NP' => __('Nepal (Southern Asia)', 'bdthemes-element-pack'),
					'PK' => __('Pakistan (Southern Asia)', 'bdthemes-element-pack'),
					'LK' => __('Sri Lanka (Southern Asia)', 'bdthemes-element-pack'),
					// SOUTHEASTERN ASIA
					'BN' => __('Brunei (Southeastern Asia)', 'bdthemes-element-pack'),
					'KH' => __('Cambodia (Southeastern Asia)', 'bdthemes-element-pack'),
					'ID' => __('Indonesia (Southeastern Asia)', 'bdthemes-element-pack'),
					'LA' => __('Laos (Southeastern Asia)', 'bdthemes-element-pack'),
					'MY' => __('Malaysia (Southeastern Asia)', 'bdthemes-element-pack'),
					'MM' => __('Myanmar (Southeastern Asia)', 'bdthemes-element-pack'),
					'PH' => __('Philippines (Southeastern Asia)', 'bdthemes-element-pack'),
					'SG' => __('Singapore (Southeastern Asia)', 'bdthemes-element-pack'),
					'TH' => __('Thailand (Southeastern Asia)', 'bdthemes-element-pack'),
					'TL' => __('Timor-Leste (Southeastern Asia)', 'bdthemes-element-pack'),
					'VN' => __('Vietnam (Southeastern Asia)', 'bdthemes-element-pack'),
					// WESTERN ASIA
					'AM' => __('Armenia (Western Asia)', 'bdthemes-element-pack'),
					'AZ' => __('Azerbaijan (Western Asia)', 'bdthemes-element-pack'),
					'BH' => __('Bahrain (Western Asia)', 'bdthemes-element-pack'),
					'CY' => __('Cyprus (Western Asia)', 'bdthemes-element-pack'),
					'GE' => __('Georgia (Western Asia)', 'bdthemes-element-pack'),
					'IQ' => __('Iraq (Western Asia)', 'bdthemes-element-pack'),
					'IL' => __('Israel (Western Asia)', 'bdthemes-element-pack'),
					'JO' => __('Jordan (Western Asia)', 'bdthemes-element-pack'),
					'KW' => __('Kuwait (Western Asia)', 'bdthemes-element-pack'),
					'LB' => __('Lebanon (Western Asia)', 'bdthemes-element-pack'),
					'OM' => __('Oman (Western Asia)', 'bdthemes-element-pack'),
					'PS' => __('Palestine (Western Asia)', 'bdthemes-element-pack'),
					'QA' => __('Qatar (Western Asia)', 'bdthemes-element-pack'),
					'SA' => __('Saudi Arabia (Western Asia)', 'bdthemes-element-pack'),
					'SY' => __('Syria (Western Asia)', 'bdthemes-element-pack'),
					'TR' => __('Turkey (Western Asia)', 'bdthemes-element-pack'),
					'AE' => __('United Arab Emirates (Western Asia)', 'bdthemes-element-pack'),
					'YE' => __('Yemen (Western Asia)', 'bdthemes-element-pack'),
					// OCEANIA
					'AS' => __('American Samoa (Oceania)', 'bdthemes-element-pack'),
					'AU' => __('Australia (Oceania)', 'bdthemes-element-pack'),
					'CK' => __('Cook Islands (Oceania)', 'bdthemes-element-pack'),
					'FJ' => __('Fiji (Oceania)', 'bdthemes-element-pack'),
					'PF' => __('French Polynesia (Oceania)', 'bdthemes-element-pack'),
					'GU' => __('Guam (Oceania)', 'bdthemes-element-pack'),
					'KI' => __('Kiribati (Oceania)', 'bdthemes-element-pack'),
					'MH' => __('Marshall Islands (Oceania)', 'bdthemes-element-pack'),
					'FM' => __('Micronesia (Oceania)', 'bdthemes-element-pack'),
					'NR' => __('Nauru (Oceania)', 'bdthemes-element-pack'),
					'NC' => __('New Caledonia (Oceania)', 'bdthemes-element-pack'),
					'NZ' => __('New Zealand (Oceania)', 'bdthemes-element-pack'),
					'NU' => __('Niue (Oceania)', 'bdthemes-element-pack'),
					'MP' => __('Northern Mariana Islands (Oceania)', 'bdthemes-element-pack'),
					'PW' => __('Palau (Oceania)', 'bdthemes-element-pack'),
					'PG' => __('Papua New Guinea (Oceania)', 'bdthemes-element-pack'),
					'PN' => __('Pitcairn Islands (Oceania)', 'bdthemes-element-pack'),
					'WS' => __('Samoa (Oceania)', 'bdthemes-element-pack'),
					'SB' => __('Solomon Islands (Oceania)', 'bdthemes-element-pack'),
					'TK' => __('Tokelau (Oceania)', 'bdthemes-element-pack'),
					'TO' => __('Tonga (Oceania)', 'bdthemes-element-pack'),
					'TV' => __('Tuvalu (Oceania)', 'bdthemes-element-pack'),
					'UM' => __('United States Minor Outlying Islands (Oceania)', 'bdthemes-element-pack'),
					'VU' => __('Vanuatu (Oceania)', 'bdthemes-element-pack'),
					'WF' => __('Wallis and Futuna (Oceania)', 'bdthemes-element-pack'),
				],
				'default'    => 'AU',
				'dynamic'    => ['active' => true],
				'condition' => [
					'svg_maps_region_type' => 'countries'
				],
				'frontend_available' => true,
				'render_type' => 'none',
			]
		);

		/**
		 * ?TODO : this controls need to remove from version 7.5.0
		 */
		$this->add_control(
			'svg_maps_display_region',
			[
				'label'      => __('Continent (EX)', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::HIDDEN,
				'options'    => [
					'world'      => __('World (Default)', 'bdthemes-element-pack'),
					// AFRICA
					'002'   => __('Africa', 'bdthemes-element-pack'),
					'015' => __('Northern Africa', 'bdthemes-element-pack'),
					'011'  => __('Western Africa', 'bdthemes-element-pack'),
					'017' => __('Middle Africa', 'bdthemes-element-pack'),
					'014' => __('Eastern Africa', 'bdthemes-element-pack'),
					'018' => __('Southern Africa', 'bdthemes-element-pack'),
					//AMERICA
					'150'  => __('Europe', 'bdthemes-element-pack'),
					'154' => __('Northern Europe', 'bdthemes-element-pack'),
					'155' => __('Western Europe', 'bdthemes-element-pack'),
					'151' => __('Eastern Europe', 'bdthemes-element-pack'),
					'039' => __('Southern Europe', 'bdthemes-element-pack'),
					//AMERICA
					'019' => __('Americas', 'bdthemes-element-pack'),
					'021' => __('Northern America', 'bdthemes-element-pack'),
					'029' => __('Caribbean', 'bdthemes-element-pack'),
					'013' => __('Central America', 'bdthemes-element-pack'),
					'005' => __('South America', 'bdthemes-element-pack'),
					//ASIA
					'142' => __('Asia', 'bdthemes-element-pack'),
					'143' => __('Central Asia', 'bdthemes-element-pack'),
					'030' => __('Eastern Asia', 'bdthemes-element-pack'),
					'034' => __('Southern Asia', 'bdthemes-element-pack'),
					'035' => __('South-Eastern Asia', 'bdthemes-element-pack'),
					'145' => __('Western Asia', 'bdthemes-element-pack'),
					//OCEANIA
					'009' => __('Oceania', 'bdthemes-element-pack'),
					'053' => __('Australia and New Zealand', 'bdthemes-element-pack'),
					'054' => __('Melanesia', 'bdthemes-element-pack'),
					'057' => __('Micronesia', 'bdthemes-element-pack'),
					'061' => __('Polynesia', 'bdthemes-element-pack'),
				],
				'default'    => 'world',
				'frontend_available' => true,
				'render_type' => 'none',
				// 'dynamic'    => ['active' => true],
			]
		);

		$this->add_control(
			'svg_maps_display_mode',
			[
				'label' => esc_html__('Display mode', 'bdthemes-element-pack'),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'regions',
				'options' => [
					'regions'  => esc_html__('Regions', 'bdthemes-element-pack'),
					'markers' => esc_html__('Markers', 'bdthemes-element-pack'),
				],
				'frontend_available' => true,
				'render_type' => 'none',
			]
		);
		$this->add_control(
			'svg_maps_display_type',
			[
				'label' => esc_html__('Display type', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SELECT,
				'default' => 'custom',
				'options' => [
					'data-visual'  => esc_html__('Visualization', 'bdthemes-element-pack'),
					'custom' => esc_html__('Custom', 'bdthemes-element-pack'),
				],
				'description' => esc_html__('Choose visualization or custom data to display. If you choose visualization that means change colors depending on values', 'bdthemes-element-pack'),
				'frontend_available' => true,
				'render_type' => 'none',
				'separator' => 'before'
			]
		);
		$this->add_control(
			'svg_maps_additional_settings',
			[
				'label'     => __('Additional Settings', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'svg_maps_show_legend',
			[
				'label'         => __('Show Legend', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::SWITCHER,
				'label_on'      => __('Yes', 'bdthemes-element-pack'),
				'label_off'     => __('No', 'bdthemes-element-pack'),
				'return_value'  => 'yes',
				'frontend_available' => true,
				'render_type' => 'none',
				'condition'     => [
					'svg_maps_display_type' => 'data-visual',
				],
			]
		);
		$this->add_control(
			'svg_maps_legend_font_weight',
			[
				'label'         => __('Bold', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::SWITCHER,
				'label_on'      => __('Yes', 'bdthemes-element-pack'),
				'label_off'     => __('No', 'bdthemes-element-pack'),
				'return_value'  => 'yes',
				'condition'     => [
					'svg_maps_show_legend' => 'yes',
				],
				'frontend_available' => true,
				'render_type' => 'none',
			]
		);
		$this->add_control(
			'svg_maps_legend_font_style',
			[
				'label'         => __('Italic', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::SWITCHER,
				'label_on'      => __('Yes', 'bdthemes-element-pack'),
				'label_off'     => __('No', 'bdthemes-element-pack'),
				'return_value'  => 'yes',
				'condition'     => [
					'svg_maps_show_legend' => 'yes',
				],
				'frontend_available' => true,
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'svg_maps_legend_font_size',
			[
				'label'         => __('Font size', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::NUMBER,
				'min'           => 5,
				'max'           => 100,
				'step'          => 1,
				'default'       => 16,
				'condition'     => [
					'svg_maps_show_legend' => 'yes',
				],
				'frontend_available' => true,
				'render_type' => 'none',
				'dynamic'       => ['active' => true],
			]
		);
		$this->add_control(
			'svg_maps_tooltip_trigger',
			[
				'label' => esc_html__('Tooltip trigger', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => esc_html__('None', 'bdthemes-element-pack'),
					'focus'  => esc_html__('Hover', 'bdthemes-element-pack'),
					'selection' => esc_html__('Click', 'bdthemes-element-pack'),
				],
				'frontend_available' => true,
				'render_type' => 'none',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'svg_maps_tooltip_font_size',
			[
				'label'         => __('Font size', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::NUMBER,
				'min'           => 5,
				'max'           => 100,
				'step'          => 1,
				'default'       => 16,
				'condition'     => [
					'svg_maps_tooltip_trigger!' => 'none',
				],
				'frontend_available' => true,
				'render_type' => 'none',
				'dynamic'       => ['active' => true],
			]
		);

		$this->add_control(
			'svg_maps_tooltip_font_weight',
			[
				'label'         => __('Bold', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::SWITCHER,
				'label_on'      => __('Yes', 'bdthemes-element-pack'),
				'label_off'     => __('No', 'bdthemes-element-pack'),
				'return_value'  => 'yes',
				'condition'     => [
					'svg_maps_tooltip_trigger!' => 'none',
				],
				'frontend_available' => true,
				'render_type' => 'none',
			]
		);
		$this->add_control(
			'svg_maps_tooltip_font_style',
			[
				'label'         => __('Italic', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::SWITCHER,
				'label_on'      => __('Yes', 'bdthemes-element-pack'),
				'label_off'     => __('No', 'bdthemes-element-pack'),
				'return_value'  => 'yes',
				'condition'     => [
					'svg_maps_tooltip_trigger!' => 'none',
				],
				'frontend_available' => true,
				'render_type' => 'none',
			]
		);


		$this->end_controls_section();
	}

	public function section_style_map() {
		$this->start_controls_section(
			'section_style_svg_maps',
			[
				'label' => __('Map Style', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'svg_maps_width',
			[
				'label'         => __('Width', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::SLIDER,
				'size_units'    => ['px'],
				'range'         => [
					'px'        => [
						'min'   => 0,
						'max'   => 1200,
						'step'  => 1,
					],
					'%'         => [
						'min'   => 0,
						'max'   => 100,
					],
				],
				'frontend_available' => true,
				'render_type' => 'none',
			]
		);
		$this->add_control(
			'svg_maps_height',
			[
				'label'         => __('Height', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::SLIDER,
				'size_units'    => ['px'],
				'range'         => [
					'px'        => [
						'min'   => 0,
						'max'   => 1200,
						'step'  => 1,
					],
					'%'         => [
						'min'   => 0,
						'max'   => 100,
					],
				],
				'frontend_available' => true,
				'render_type' => 'none',
			]
		);
		// $this->add_control(
		// 	'svg_maps_default_color',
		// 	[
		// 		'label'     => __('Color', 'bdthemes-element-pack'),
		// 		'type'      => Controls_Manager::COLOR,
		// 		'frontend_available' => true,
		// 		'render_type' => 'none',
		// 		'separator' => 'before',
		// 	]
		// );
		$this->add_control(
			'svg_maps_legend_color',
			[
				'label'     => __('Legend Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'frontend_available' => true,
				'render_type' => 'none',
				'separator' => 'before',
				'condition' => [
					'svg_maps_show_legend' => 'yes'
				]
			]
		);
		$this->add_control(
			'svg_maps_background_color',
			[
				'label'     => __('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'frontend_available' => true,
				'render_type' => 'none',
			]
		);
		$this->add_control(
			'svg_maps_dataless_region_color',
			[
				'label'     => __('Inactive Region Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'frontend_available' => true,
				'render_type' => 'none',
			]
		);
		$this->add_control(
			'borders_color',
			[
				'label' => esc_html__('Borders color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					"{{WRAPPER}} path" => 'stroke: {{VALUE}} !important;',
				],
			]
		);
		$this->end_controls_section();
	}


	private function section_content_regions() {

		$this->start_controls_section('section_content_regions', [
			'label' => esc_html__('Regions', 'bdthemes-element-pack'),
			'tab' => Controls_Manager::TAB_CONTENT,
			'condition' => [
				'svg_maps_display_mode' => 'regions'
			]
		]);

		$this->add_control(
			'svg_maps_region_value_title',
			[
				'label' => esc_html__('Value title', 'bdthemes-element-pack'),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__('population', 'bdthemes-element-pack'),
				'default' => esc_html__('Population', 'bdthemes-element-pack'),
				'condition' => [
					'svg_maps_display_type' => 'data-visual'
				],
				'frontend_available' => true,
				'render_type' => 'none',
				// 'separator' => 'after',
			]
		);

		$repeater_color_axis = new Repeater();

		$repeater_color_axis->add_control(
			'axis_color',
			[
				'label' => esc_html__('Color axis', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '#f5f5f5',
			]
		);

		$this->add_control(
			'svg_maps_region_axis_color',
			[
				'label' => esc_html__('Regions axis color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater_color_axis->get_controls(),
				// 'default' => [
				// 	'axis_color' => '#f5f5f5'
				// ],
				'title_field' => '{{{ axis_color }}}',
				'frontend_available' => true,
				'render_type' => 'none',
				'condition' => [
					'svg_maps_display_type' => 'data-visual'
				],
			]
		);


		// // repeater for data visual

		$repeaterVisualData = new Repeater();
		$repeaterVisualData->add_control(
			'visual_data_region_is_linkable',
			[
				'label'         => __('is linkable?', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::SWITCHER,
				'label_on'      => __('Yes', 'bdthemes-element-pack'),
				'label_off'     => __('No', 'bdthemes-element-pack'),
				'return_value'  => 'yes',
			]
		);

		$repeaterVisualData->add_control(
			'visual_data_region_link',
			[
				'label' => esc_html__('Link', 'bdthemes-element-pack'),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__('https://your-link.com', 'bdthemes-element-pack'),
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
					'custom_attributes' => '',
				],
				'condition' => [
					'visual_data_region_is_linkable' => 'yes'
				]
			]
		);

		// $repeaterVisualData->add_control(
		// 	'visual_data_region_name',
		// 	[
		// 		'label' => esc_html__('Region name', 'bdthemes-element-pack'),
		// 		'type' => Controls_Manager::TEXT,
		// 		'default' => esc_html__('Australia', 'bdthemes-element-pack'),
		// 		'placeholder' => esc_html__('Type region name here', 'bdthemes-element-pack'),
		// 	]
		// );
		$repeaterVisualData->add_control(
			'visual_data_region_name',
			[
				'label'      => __('Select Country', 'bdthemes-element-pack'),
				'label_block' => true,
				'type'       => Controls_Manager::SELECT2,
				'options'    => [
					// NORTHERN AFRICA
					'DZ'  => __('Algeria (Northern Africa)', 'bdthemes-element-pack'),
					'EG'  => __('Egypt (Northern Africa)', 'bdthemes-element-pack'),
					'EH' => __('Western Sahara (Northern Africa)', 'bdthemes-element-pack'),
					'LY' => __('Libya (Northern Africa)', 'bdthemes-element-pack'),
					'MA' => __('Morocco (Northern Africa)', 'bdthemes-element-pack'),
					'SD' => __('Sudan (Northern Africa)', 'bdthemes-element-pack'),
					'SS' => __('South Sudan (Northern Africa)', 'bdthemes-element-pack'),
					'SH' => __('Saint Helena (Northern Africa)', 'bdthemes-element-pack'),
					'TD' => __('Chad (Northern Africa)', 'bdthemes-element-pack'),
					'TN' => __('Tunisia (Northern Africa)', 'bdthemes-element-pack'),
					// WESTERN AFRICA
					'BF' => __('Burkina Faso (Western Africa)', 'bdthemes-element-pack'),
					'BJ' => __('Benin (Western Africa)', 'bdthemes-element-pack'),
					'CI' => __('Ivory Coast (Western Africa)', 'bdthemes-element-pack'),
					'CV' => __('Cape Verde (Western Africa)', 'bdthemes-element-pack'),
					'GH' => __('Ghana (Western Africa)', 'bdthemes-element-pack'),
					'GM' => __('Gambia (Western Africa)', 'bdthemes-element-pack'),
					'GN' => __('Guinea (Western Africa)', 'bdthemes-element-pack'),
					'GW' => __('Guinea-Bissau (Western Africa)', 'bdthemes-element-pack'),
					'LR' => __('Liberia (Western Africa)', 'bdthemes-element-pack'),
					'ML' => __('Mali (Western Africa)', 'bdthemes-element-pack'),
					'MR' => __('Mauritania (Western Africa)', 'bdthemes-element-pack'),
					'NE' => __('Niger (Western Africa)', 'bdthemes-element-pack'),
					'NG' => __('Nigeria (Western Africa)', 'bdthemes-element-pack'),
					'SL' => __('Sierra Leone (Western Africa)', 'bdthemes-element-pack'),
					'SN' => __('Senegal (Western Africa)', 'bdthemes-element-pack'),
					'TG' => __('Togo (Western Africa)', 'bdthemes-element-pack'),
					// MIDDLE AFRICA
					'AO' => __('Angola (Middle Africa)', 'bdthemes-element-pack'),
					'CD' => __('Democratic Republic of the Congo (Middle Africa)', 'bdthemes-element-pack'),
					'CF' => __('Central African Republic (Middle Africa)', 'bdthemes-element-pack'),
					'CG' => __('Congo (Brazzaville) (Middle Africa)', 'bdthemes-element-pack'),
					'CM' => __('Cameroon (Middle Africa)', 'bdthemes-element-pack'),
					'GA' => __('Gabon (Middle Africa)', 'bdthemes-element-pack'),
					'GQ' => __('Equatorial Guinea (Middle Africa)', 'bdthemes-element-pack'),
					'ST' => __('Sao Tome and Principe (Middle Africa)', 'bdthemes-element-pack'),
					'TD' => __('Chad (Middle Africa)', 'bdthemes-element-pack'),
					// EASTERN AFRICA
					'BI' => __('Burundi (Eastern Africa)', 'bdthemes-element-pack'),
					'DJ' => __('Djibouti (Eastern Africa)', 'bdthemes-element-pack'),
					'ER' => __('Eritrea (Eastern Africa)', 'bdthemes-element-pack'),
					'ET' => __('Ethiopia (Eastern Africa)', 'bdthemes-element-pack'),
					'KE' => __('Kenya (Eastern Africa)', 'bdthemes-element-pack'),
					'KM' => __('Comoros (Eastern Africa)', 'bdthemes-element-pack'),
					'MG' => __('Madagascar (Eastern Africa)', 'bdthemes-element-pack'),
					'MW' => __('Malawi (Eastern Africa)', 'bdthemes-element-pack'),
					'MU' => __('Mauritius (Eastern Africa)', 'bdthemes-element-pack'),
					'MZ' => __('Mozambique (Eastern Africa)', 'bdthemes-element-pack'),
					'RE' => __('Reunion (Eastern Africa)', 'bdthemes-element-pack'),
					'RW' => __('Rwanda (Eastern Africa)', 'bdthemes-element-pack'),
					'SC' => __('Seychelles (Eastern Africa)', 'bdthemes-element-pack'),
					'SO' => __('Somalia (Eastern Africa)', 'bdthemes-element-pack'),
					'TZ' => __('Tanzania (Eastern Africa)', 'bdthemes-element-pack'),
					'UG' => __('Uganda (Eastern Africa)', 'bdthemes-element-pack'),
					'YT' => __('Mayotte (Eastern Africa)', 'bdthemes-element-pack'),
					'ZM' => __('Zambia (Eastern Africa)', 'bdthemes-element-pack'),
					'ZW' => __('Zimbabwe (Eastern Africa)', 'bdthemes-element-pack'),
					// SOUTHERN AFRICA
					'BW' => __('Botswana (Southern Africa)', 'bdthemes-element-pack'),
					'LS' => __('Lesotho (Southern Africa)', 'bdthemes-element-pack'),
					'NA' => __('Namibia (Southern Africa)', 'bdthemes-element-pack'),
					'SZ' => __('Swaziland (Southern Africa)', 'bdthemes-element-pack'),
					'ZA' => __('South Africa (Southern Africa)', 'bdthemes-element-pack'),
					//CENTEAL ASIA
					'KZ' => __('Kazakhstan (Central Asia)', 'bdthemes-element-pack'),
					'KG' => __('Kyrgyzstan (Central Asia)', 'bdthemes-element-pack'),
					'TJ' => __('Tajikistan (Central Asia)', 'bdthemes-element-pack'),
					'TM' => __('Turkmenistan (Central Asia)', 'bdthemes-element-pack'),
					'UZ' => __('Uzbekistan (Central Asia)', 'bdthemes-element-pack'),
					// WESTERN ASIA
					'AE' => __('United Arab Emirates (Western Asia)', 'bdthemes-element-pack'),
					'AM' => __('Armenia (Western Asia)', 'bdthemes-element-pack'),
					'AZ' => __('Azerbaijan (Western Asia)', 'bdthemes-element-pack'),
					'BH' => __('Bahrain (Western Asia)', 'bdthemes-element-pack'),
					'CY' => __('Cyprus (Western Asia)', 'bdthemes-element-pack'),
					'GE' => __('Georgia (Western Asia)', 'bdthemes-element-pack'),
					'IQ' => __('Iraq (Western Asia)', 'bdthemes-element-pack'),
					// SOUTHERN EUROPE
					'AL' => __('Albania (Southern Europe)', 'bdthemes-element-pack'),
					'AD' => __('Andorra (Southern Europe)', 'bdthemes-element-pack'),
					'BA' => __('Bosnia and Herzegovina (Southern Europe)', 'bdthemes-element-pack'),
					'HR' => __('Croatia (Southern Europe)', 'bdthemes-element-pack'),
					'GI' => __('Gibraltar (Southern Europe)', 'bdthemes-element-pack'),
					'GR' => __('Greece (Southern Europe)', 'bdthemes-element-pack'),
					'VA' => __('Vatican City (Southern Europe)', 'bdthemes-element-pack'),
					'IT' => __('Italy (Southern Europe)', 'bdthemes-element-pack'),
					'MK' => __('Macedonia (Southern Europe)', 'bdthemes-element-pack'),
					'MT' => __('Malta (Southern Europe)', 'bdthemes-element-pack'),
					// WESTERN EUROPE
					'AT' => __('Austria (Western Europe)', 'bdthemes-element-pack'),
					'BE' => __('Belgium (Western Europe)', 'bdthemes-element-pack'),
					'FR' => __('France (Western Europe)', 'bdthemes-element-pack'),
					'DE' => __('Germany (Western Europe)', 'bdthemes-element-pack'),
					'LI' => __('Liechtenstein (Western Europe)', 'bdthemes-element-pack'),
					'LU' => __('Luxembourg (Western Europe)', 'bdthemes-element-pack'),
					'MC' => __('Monaco (Western Europe)', 'bdthemes-element-pack'),
					'NL' => __('Netherlands (Western Europe)', 'bdthemes-element-pack'),
					'CH' => __('Switzerland (Western Europe)', 'bdthemes-element-pack'),
					// EASTERN EUROPE
					'BY' => __('Belarus (Eastern Europe)', 'bdthemes-element-pack'),
					'BG' => __('Bulgaria (Eastern Europe)', 'bdthemes-element-pack'),
					'CZ' => __('Czech Republic (Eastern Europe)', 'bdthemes-element-pack'),
					'HU' => __('Hungary (Eastern Europe)', 'bdthemes-element-pack'),
					'MD' => __('Moldova (Eastern Europe)', 'bdthemes-element-pack'),
					'PL' => __('Poland (Eastern Europe)', 'bdthemes-element-pack'),
					'RO' => __('Romania (Eastern Europe)', 'bdthemes-element-pack'),
					'RU' => __('Russia (Eastern Europe)', 'bdthemes-element-pack'),
					'SK' => __('Slovakia (Eastern Europe)', 'bdthemes-element-pack'),
					'UA' => __('Ukraine (Eastern Europe)', 'bdthemes-element-pack'),
					// NORTHERN EUROPE
					'DK' => __('Denmark (Northern Europe)', 'bdthemes-element-pack'),
					'EE' => __('Estonia (Northern Europe)', 'bdthemes-element-pack'),
					'FO' => __('Faroe Islands (Northern Europe)', 'bdthemes-element-pack'),
					'FI' => __('Finland (Northern Europe)', 'bdthemes-element-pack'),
					'GG' => __('Guernsey (Northern Europe)', 'bdthemes-element-pack'),
					'IS' => __('Iceland (Northern Europe)', 'bdthemes-element-pack'),
					'IE' => __('Ireland (Northern Europe)', 'bdthemes-element-pack'),
					// SOUTHERN AMERICA
					'AR' => __('Argentina (Southern America)', 'bdthemes-element-pack'),
					'BO' => __('Bolivia (Southern America)', 'bdthemes-element-pack'),
					'BR' => __('Brazil (Southern America)', 'bdthemes-element-pack'),
					'CL' => __('Chile (Southern America)', 'bdthemes-element-pack'),
					'CO' => __('Colombia (Southern America)', 'bdthemes-element-pack'),
					'EC' => __('Ecuador (Southern America)', 'bdthemes-element-pack'),
					'FK' => __('Falkland Islands (Southern America)', 'bdthemes-element-pack'),
					'GF' => __('French Guiana (Southern America)', 'bdthemes-element-pack'),
					'GY' => __('Guyana (Southern America)', 'bdthemes-element-pack'),
					'PY' => __('Paraguay (Southern America)', 'bdthemes-element-pack'),
					'PE' => __('Peru (Southern America)', 'bdthemes-element-pack'),
					'SR' => __('Suriname (Southern America)', 'bdthemes-element-pack'),
					'UY' => __('Uruguay (Southern America)', 'bdthemes-element-pack'),
					'VE' => __('Venezuela (Southern America)', 'bdthemes-element-pack'),
					// NORTHERN AMERICA
					'BM' => __('Bermuda (Northern America)', 'bdthemes-element-pack'),
					'CA' => __('Canada (Northern America)', 'bdthemes-element-pack'),
					'GL' => __('Greenland (Northern America)', 'bdthemes-element-pack'),
					'PM' => __('Saint Pierre and Miquelon (Northern America)', 'bdthemes-element-pack'),
					'US' => __('United States (Northern America)', 'bdthemes-element-pack'),
					// CARIBBEAN
					'AI' => __('Anguilla (Caribbean)', 'bdthemes-element-pack'),
					'AG' => __('Antigua and Barbuda (Caribbean)', 'bdthemes-element-pack'),
					'AW' => __('Aruba (Caribbean)', 'bdthemes-element-pack'),
					'BS' => __('Bahamas (Caribbean)', 'bdthemes-element-pack'),
					'BB' => __('Barbados (Caribbean)', 'bdthemes-element-pack'),
					'BQ' => __('Bonaire, Sint Eustatius and Saba (Caribbean)', 'bdthemes-element-pack'),
					'KY' => __('Cayman Islands (Caribbean)', 'bdthemes-element-pack'),
					'CU' => __('Cuba (Caribbean)', 'bdthemes-element-pack'),
					'CW' => __('Curaçao (Caribbean)', 'bdthemes-element-pack'),
					'DM' => __('Dominica (Caribbean)', 'bdthemes-element-pack'),
					'DO' => __('Dominican Republic (Caribbean)', 'bdthemes-element-pack'),
					'GD' => __('Grenada (Caribbean)', 'bdthemes-element-pack'),
					'GP' => __('Guadeloupe (Caribbean)', 'bdthemes-element-pack'),
					'HT' => __('Haiti (Caribbean)', 'bdthemes-element-pack'),
					'JM' => __('Jamaica (Caribbean)', 'bdthemes-element-pack'),
					'MQ' => __('Martinique (Caribbean)', 'bdthemes-element-pack'),
					'MS' => __('Montserrat (Caribbean)', 'bdthemes-element-pack'),
					'PR' => __('Puerto Rico (Caribbean)', 'bdthemes-element-pack'),
					'BL' => __('Saint Barthélemy (Caribbean)', 'bdthemes-element-pack'),
					'KN' => __('Saint Kitts and Nevis (Caribbean)', 'bdthemes-element-pack'),
					'LC' => __('Saint Lucia (Caribbean)', 'bdthemes-element-pack'),
					'MF' => __('Saint Martin (French part) (Caribbean)', 'bdthemes-element-pack'),
					'VC' => __('Saint Vincent and the Grenadines (Caribbean)', 'bdthemes-element-pack'),
					'SX' => __('Sint Maarten (Dutch part) (Caribbean)', 'bdthemes-element-pack'),
					'TT' => __('Trinidad and Tobago (Caribbean)', 'bdthemes-element-pack'),
					'TC' => __('Turks and Caicos Islands (Caribbean)', 'bdthemes-element-pack'),
					'VG' => __('Virgin Islands (British) (Caribbean)', 'bdthemes-element-pack'),
					'VI' => __('Virgin Islands (U.S.) (Caribbean)', 'bdthemes-element-pack'),
					//CENTRAL AMERICA
					'BZ' => __('Belize (Central America)', 'bdthemes-element-pack'),
					'CR' => __('Costa Rica (Central America)', 'bdthemes-element-pack'),
					'SV' => __('El Salvador (Central America)', 'bdthemes-element-pack'),
					'GT' => __('Guatemala (Central America)', 'bdthemes-element-pack'),
					'HN' => __('Honduras (Central America)', 'bdthemes-element-pack'),
					'MX' => __('Mexico (Central America)', 'bdthemes-element-pack'),
					'NI' => __('Nicaragua (Central America)', 'bdthemes-element-pack'),
					'PA' => __('Panama (Central America)', 'bdthemes-element-pack'),
					// CENTRAL ASIA
					'KZ' => __('Kazakhstan (Central Asia)', 'bdthemes-element-pack'),
					'KG' => __('Kyrgyzstan (Central Asia)', 'bdthemes-element-pack'),
					'TJ' => __('Tajikistan (Central Asia)', 'bdthemes-element-pack'),
					'TM' => __('Turkmenistan (Central Asia)', 'bdthemes-element-pack'),
					'UZ' => __('Uzbekistan (Central Asia)', 'bdthemes-element-pack'),
					// EASTERN ASIA
					'CN' => __('China (Eastern Asia)', 'bdthemes-element-pack'),
					'HK' => __('Hong Kong (Eastern Asia)', 'bdthemes-element-pack'),
					'JP' => __('Japan (Eastern Asia)', 'bdthemes-element-pack'),
					'KP' => __('North Korea (Eastern Asia)', 'bdthemes-element-pack'),
					'KR' => __('South Korea (Eastern Asia)', 'bdthemes-element-pack'),
					'MO' => __('Macau (Eastern Asia)', 'bdthemes-element-pack'),
					'MN' => __('Mongolia (Eastern Asia)', 'bdthemes-element-pack'),
					'TW' => __('Taiwan (Eastern Asia)', 'bdthemes-element-pack'),
					// SOUTHERN ASIA
					'AF' => __('Afghanistan (Southern Asia)', 'bdthemes-element-pack'),
					'BD' => __('Bangladesh (Southern Asia)', 'bdthemes-element-pack'),
					'BT' => __('Bhutan (Southern Asia)', 'bdthemes-element-pack'),
					'IN' => __('India (Southern Asia)', 'bdthemes-element-pack'),
					'IR' => __('Iran (Southern Asia)', 'bdthemes-element-pack'),
					'MV' => __('Maldives (Southern Asia)', 'bdthemes-element-pack'),
					'NP' => __('Nepal (Southern Asia)', 'bdthemes-element-pack'),
					'PK' => __('Pakistan (Southern Asia)', 'bdthemes-element-pack'),
					'LK' => __('Sri Lanka (Southern Asia)', 'bdthemes-element-pack'),
					// SOUTHEASTERN ASIA
					'BN' => __('Brunei (Southeastern Asia)', 'bdthemes-element-pack'),
					'KH' => __('Cambodia (Southeastern Asia)', 'bdthemes-element-pack'),
					'ID' => __('Indonesia (Southeastern Asia)', 'bdthemes-element-pack'),
					'LA' => __('Laos (Southeastern Asia)', 'bdthemes-element-pack'),
					'MY' => __('Malaysia (Southeastern Asia)', 'bdthemes-element-pack'),
					'MM' => __('Myanmar (Southeastern Asia)', 'bdthemes-element-pack'),
					'PH' => __('Philippines (Southeastern Asia)', 'bdthemes-element-pack'),
					'SG' => __('Singapore (Southeastern Asia)', 'bdthemes-element-pack'),
					'TH' => __('Thailand (Southeastern Asia)', 'bdthemes-element-pack'),
					'TL' => __('Timor-Leste (Southeastern Asia)', 'bdthemes-element-pack'),
					'VN' => __('Vietnam (Southeastern Asia)', 'bdthemes-element-pack'),
					// WESTERN ASIA
					'AM' => __('Armenia (Western Asia)', 'bdthemes-element-pack'),
					'AZ' => __('Azerbaijan (Western Asia)', 'bdthemes-element-pack'),
					'BH' => __('Bahrain (Western Asia)', 'bdthemes-element-pack'),
					'CY' => __('Cyprus (Western Asia)', 'bdthemes-element-pack'),
					'GE' => __('Georgia (Western Asia)', 'bdthemes-element-pack'),
					'IQ' => __('Iraq (Western Asia)', 'bdthemes-element-pack'),
					'IL' => __('Israel (Western Asia)', 'bdthemes-element-pack'),
					'JO' => __('Jordan (Western Asia)', 'bdthemes-element-pack'),
					'KW' => __('Kuwait (Western Asia)', 'bdthemes-element-pack'),
					'LB' => __('Lebanon (Western Asia)', 'bdthemes-element-pack'),
					'OM' => __('Oman (Western Asia)', 'bdthemes-element-pack'),
					'PS' => __('Palestine (Western Asia)', 'bdthemes-element-pack'),
					'QA' => __('Qatar (Western Asia)', 'bdthemes-element-pack'),
					'SA' => __('Saudi Arabia (Western Asia)', 'bdthemes-element-pack'),
					'SY' => __('Syria (Western Asia)', 'bdthemes-element-pack'),
					'TR' => __('Turkey (Western Asia)', 'bdthemes-element-pack'),
					'AE' => __('United Arab Emirates (Western Asia)', 'bdthemes-element-pack'),
					'YE' => __('Yemen (Western Asia)', 'bdthemes-element-pack'),
					// OCEANIA
					'AS' => __('American Samoa (Oceania)', 'bdthemes-element-pack'),
					'AU' => __('Australia (Oceania)', 'bdthemes-element-pack'),
					'CK' => __('Cook Islands (Oceania)', 'bdthemes-element-pack'),
					'FJ' => __('Fiji (Oceania)', 'bdthemes-element-pack'),
					'PF' => __('French Polynesia (Oceania)', 'bdthemes-element-pack'),
					'GU' => __('Guam (Oceania)', 'bdthemes-element-pack'),
					'KI' => __('Kiribati (Oceania)', 'bdthemes-element-pack'),
					'MH' => __('Marshall Islands (Oceania)', 'bdthemes-element-pack'),
					'FM' => __('Micronesia (Oceania)', 'bdthemes-element-pack'),
					'NR' => __('Nauru (Oceania)', 'bdthemes-element-pack'),
					'NC' => __('New Caledonia (Oceania)', 'bdthemes-element-pack'),
					'NZ' => __('New Zealand (Oceania)', 'bdthemes-element-pack'),
					'NU' => __('Niue (Oceania)', 'bdthemes-element-pack'),
					'MP' => __('Northern Mariana Islands (Oceania)', 'bdthemes-element-pack'),
					'PW' => __('Palau (Oceania)', 'bdthemes-element-pack'),
					'PG' => __('Papua New Guinea (Oceania)', 'bdthemes-element-pack'),
					'PN' => __('Pitcairn Islands (Oceania)', 'bdthemes-element-pack'),
					'WS' => __('Samoa (Oceania)', 'bdthemes-element-pack'),
					'SB' => __('Solomon Islands (Oceania)', 'bdthemes-element-pack'),
					'TK' => __('Tokelau (Oceania)', 'bdthemes-element-pack'),
					'TO' => __('Tonga (Oceania)', 'bdthemes-element-pack'),
					'TV' => __('Tuvalu (Oceania)', 'bdthemes-element-pack'),
					'UM' => __('United States Minor Outlying Islands (Oceania)', 'bdthemes-element-pack'),
					'VU' => __('Vanuatu (Oceania)', 'bdthemes-element-pack'),
					'WF' => __('Wallis and Futuna (Oceania)', 'bdthemes-element-pack'),
				],
				'default'    => 'AU',
				'dynamic'    => ['active' => true],
			]
		);


		$repeaterVisualData->add_control(
			'visual_data_value',
			[
				'label' => esc_html__('Value', 'bdthemes-element-pack'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'step' => 1,
				'default' => 26364790,
			]
		);
		$this->add_control(
			'hr_visual_data',
			[
				'type' => Controls_Manager::DIVIDER,
				'description' => 'Data visual settings',
			]
		);
		$this->add_control(
			'svg_maps_data_visual_array_regions',
			[
				'label' => esc_html__('Regions values', 'bdthemes-element-pack'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeaterVisualData->get_controls(),
				'default' => [
					[
						'visual_data_region_name' => esc_html__('Australia', 'bdthemes-element-pack'),
					],
				],
				'condition' => [
					'svg_maps_display_type' => 'data-visual'
				],
				'title_field' => '{{{ visual_data_region_name }}}',
				'frontend_available' => true,
				'render_type' => 'none',
			]
		);

		//  repeater for active regions custom data
		$repeaterRegion = new Repeater();
		$repeaterRegion->add_control(
			'active_region_code',
			[
				'label'      => __('Select Country', 'bdthemes-element-pack'),
				'label_block' => true,
				'type'       => Controls_Manager::SELECT2,
				'options'    => [
					// NORTHERN AFRICA
					'DZ'  => __('Algeria (Northern Africa)', 'bdthemes-element-pack'),
					'EG'  => __('Egypt (Northern Africa)', 'bdthemes-element-pack'),
					'EH' => __('Western Sahara (Northern Africa)', 'bdthemes-element-pack'),
					'LY' => __('Libya (Northern Africa)', 'bdthemes-element-pack'),
					'MA' => __('Morocco (Northern Africa)', 'bdthemes-element-pack'),
					'SD' => __('Sudan (Northern Africa)', 'bdthemes-element-pack'),
					'SS' => __('South Sudan (Northern Africa)', 'bdthemes-element-pack'),
					'SH' => __('Saint Helena (Northern Africa)', 'bdthemes-element-pack'),
					'TD' => __('Chad (Northern Africa)', 'bdthemes-element-pack'),
					'TN' => __('Tunisia (Northern Africa)', 'bdthemes-element-pack'),
					// WESTERN AFRICA
					'BF' => __('Burkina Faso (Western Africa)', 'bdthemes-element-pack'),
					'BJ' => __('Benin (Western Africa)', 'bdthemes-element-pack'),
					'CI' => __('Ivory Coast (Western Africa)', 'bdthemes-element-pack'),
					'CV' => __('Cape Verde (Western Africa)', 'bdthemes-element-pack'),
					'GH' => __('Ghana (Western Africa)', 'bdthemes-element-pack'),
					'GM' => __('Gambia (Western Africa)', 'bdthemes-element-pack'),
					'GN' => __('Guinea (Western Africa)', 'bdthemes-element-pack'),
					'GW' => __('Guinea-Bissau (Western Africa)', 'bdthemes-element-pack'),
					'LR' => __('Liberia (Western Africa)', 'bdthemes-element-pack'),
					'ML' => __('Mali (Western Africa)', 'bdthemes-element-pack'),
					'MR' => __('Mauritania (Western Africa)', 'bdthemes-element-pack'),
					'NE' => __('Niger (Western Africa)', 'bdthemes-element-pack'),
					'NG' => __('Nigeria (Western Africa)', 'bdthemes-element-pack'),
					'SL' => __('Sierra Leone (Western Africa)', 'bdthemes-element-pack'),
					'SN' => __('Senegal (Western Africa)', 'bdthemes-element-pack'),
					'TG' => __('Togo (Western Africa)', 'bdthemes-element-pack'),
					// MIDDLE AFRICA
					'AO' => __('Angola (Middle Africa)', 'bdthemes-element-pack'),
					'CD' => __('Democratic Republic of the Congo (Middle Africa)', 'bdthemes-element-pack'),
					'CF' => __('Central African Republic (Middle Africa)', 'bdthemes-element-pack'),
					'CG' => __('Congo (Brazzaville) (Middle Africa)', 'bdthemes-element-pack'),
					'CM' => __('Cameroon (Middle Africa)', 'bdthemes-element-pack'),
					'GA' => __('Gabon (Middle Africa)', 'bdthemes-element-pack'),
					'GQ' => __('Equatorial Guinea (Middle Africa)', 'bdthemes-element-pack'),
					'ST' => __('Sao Tome and Principe (Middle Africa)', 'bdthemes-element-pack'),
					'TD' => __('Chad (Middle Africa)', 'bdthemes-element-pack'),
					// EASTERN AFRICA
					'BI' => __('Burundi (Eastern Africa)', 'bdthemes-element-pack'),
					'DJ' => __('Djibouti (Eastern Africa)', 'bdthemes-element-pack'),
					'ER' => __('Eritrea (Eastern Africa)', 'bdthemes-element-pack'),
					'ET' => __('Ethiopia (Eastern Africa)', 'bdthemes-element-pack'),
					'KE' => __('Kenya (Eastern Africa)', 'bdthemes-element-pack'),
					'KM' => __('Comoros (Eastern Africa)', 'bdthemes-element-pack'),
					'MG' => __('Madagascar (Eastern Africa)', 'bdthemes-element-pack'),
					'MW' => __('Malawi (Eastern Africa)', 'bdthemes-element-pack'),
					'MU' => __('Mauritius (Eastern Africa)', 'bdthemes-element-pack'),
					'MZ' => __('Mozambique (Eastern Africa)', 'bdthemes-element-pack'),
					'RE' => __('Reunion (Eastern Africa)', 'bdthemes-element-pack'),
					'RW' => __('Rwanda (Eastern Africa)', 'bdthemes-element-pack'),
					'SC' => __('Seychelles (Eastern Africa)', 'bdthemes-element-pack'),
					'SO' => __('Somalia (Eastern Africa)', 'bdthemes-element-pack'),
					'TZ' => __('Tanzania (Eastern Africa)', 'bdthemes-element-pack'),
					'UG' => __('Uganda (Eastern Africa)', 'bdthemes-element-pack'),
					'YT' => __('Mayotte (Eastern Africa)', 'bdthemes-element-pack'),
					'ZM' => __('Zambia (Eastern Africa)', 'bdthemes-element-pack'),
					'ZW' => __('Zimbabwe (Eastern Africa)', 'bdthemes-element-pack'),
					// SOUTHERN AFRICA
					'BW' => __('Botswana (Southern Africa)', 'bdthemes-element-pack'),
					'LS' => __('Lesotho (Southern Africa)', 'bdthemes-element-pack'),
					'NA' => __('Namibia (Southern Africa)', 'bdthemes-element-pack'),
					'SZ' => __('Swaziland (Southern Africa)', 'bdthemes-element-pack'),
					'ZA' => __('South Africa (Southern Africa)', 'bdthemes-element-pack'),
					//CENTEAL ASIA
					'KZ' => __('Kazakhstan (Central Asia)', 'bdthemes-element-pack'),
					'KG' => __('Kyrgyzstan (Central Asia)', 'bdthemes-element-pack'),
					'TJ' => __('Tajikistan (Central Asia)', 'bdthemes-element-pack'),
					'TM' => __('Turkmenistan (Central Asia)', 'bdthemes-element-pack'),
					'UZ' => __('Uzbekistan (Central Asia)', 'bdthemes-element-pack'),
					// WESTERN ASIA
					'AE' => __('United Arab Emirates (Western Asia)', 'bdthemes-element-pack'),
					'AM' => __('Armenia (Western Asia)', 'bdthemes-element-pack'),
					'AZ' => __('Azerbaijan (Western Asia)', 'bdthemes-element-pack'),
					'BH' => __('Bahrain (Western Asia)', 'bdthemes-element-pack'),
					'CY' => __('Cyprus (Western Asia)', 'bdthemes-element-pack'),
					'GE' => __('Georgia (Western Asia)', 'bdthemes-element-pack'),
					'IQ' => __('Iraq (Western Asia)', 'bdthemes-element-pack'),
					// SOUTHERN EUROPE
					'AL' => __('Albania (Southern Europe)', 'bdthemes-element-pack'),
					'AD' => __('Andorra (Southern Europe)', 'bdthemes-element-pack'),
					'BA' => __('Bosnia and Herzegovina (Southern Europe)', 'bdthemes-element-pack'),
					'HR' => __('Croatia (Southern Europe)', 'bdthemes-element-pack'),
					'GI' => __('Gibraltar (Southern Europe)', 'bdthemes-element-pack'),
					'GR' => __('Greece (Southern Europe)', 'bdthemes-element-pack'),
					'VA' => __('Vatican City (Southern Europe)', 'bdthemes-element-pack'),
					'IT' => __('Italy (Southern Europe)', 'bdthemes-element-pack'),
					'MK' => __('Macedonia (Southern Europe)', 'bdthemes-element-pack'),
					'MT' => __('Malta (Southern Europe)', 'bdthemes-element-pack'),
					// WESTERN EUROPE
					'AT' => __('Austria (Western Europe)', 'bdthemes-element-pack'),
					'BE' => __('Belgium (Western Europe)', 'bdthemes-element-pack'),
					'FR' => __('France (Western Europe)', 'bdthemes-element-pack'),
					'DE' => __('Germany (Western Europe)', 'bdthemes-element-pack'),
					'LI' => __('Liechtenstein (Western Europe)', 'bdthemes-element-pack'),
					'LU' => __('Luxembourg (Western Europe)', 'bdthemes-element-pack'),
					'MC' => __('Monaco (Western Europe)', 'bdthemes-element-pack'),
					'NL' => __('Netherlands (Western Europe)', 'bdthemes-element-pack'),
					'CH' => __('Switzerland (Western Europe)', 'bdthemes-element-pack'),
					// EASTERN EUROPE
					'BY' => __('Belarus (Eastern Europe)', 'bdthemes-element-pack'),
					'BG' => __('Bulgaria (Eastern Europe)', 'bdthemes-element-pack'),
					'CZ' => __('Czech Republic (Eastern Europe)', 'bdthemes-element-pack'),
					'HU' => __('Hungary (Eastern Europe)', 'bdthemes-element-pack'),
					'MD' => __('Moldova (Eastern Europe)', 'bdthemes-element-pack'),
					'PL' => __('Poland (Eastern Europe)', 'bdthemes-element-pack'),
					'RO' => __('Romania (Eastern Europe)', 'bdthemes-element-pack'),
					'RU' => __('Russia (Eastern Europe)', 'bdthemes-element-pack'),
					'SK' => __('Slovakia (Eastern Europe)', 'bdthemes-element-pack'),
					'UA' => __('Ukraine (Eastern Europe)', 'bdthemes-element-pack'),
					// NORTHERN EUROPE
					'DK' => __('Denmark (Northern Europe)', 'bdthemes-element-pack'),
					'EE' => __('Estonia (Northern Europe)', 'bdthemes-element-pack'),
					'FO' => __('Faroe Islands (Northern Europe)', 'bdthemes-element-pack'),
					'FI' => __('Finland (Northern Europe)', 'bdthemes-element-pack'),
					'GG' => __('Guernsey (Northern Europe)', 'bdthemes-element-pack'),
					'IS' => __('Iceland (Northern Europe)', 'bdthemes-element-pack'),
					'IE' => __('Ireland (Northern Europe)', 'bdthemes-element-pack'),
					// SOUTHERN AMERICA
					'AR' => __('Argentina (Southern America)', 'bdthemes-element-pack'),
					'BO' => __('Bolivia (Southern America)', 'bdthemes-element-pack'),
					'BR' => __('Brazil (Southern America)', 'bdthemes-element-pack'),
					'CL' => __('Chile (Southern America)', 'bdthemes-element-pack'),
					'CO' => __('Colombia (Southern America)', 'bdthemes-element-pack'),
					'EC' => __('Ecuador (Southern America)', 'bdthemes-element-pack'),
					'FK' => __('Falkland Islands (Southern America)', 'bdthemes-element-pack'),
					'GF' => __('French Guiana (Southern America)', 'bdthemes-element-pack'),
					'GY' => __('Guyana (Southern America)', 'bdthemes-element-pack'),
					'PY' => __('Paraguay (Southern America)', 'bdthemes-element-pack'),
					'PE' => __('Peru (Southern America)', 'bdthemes-element-pack'),
					'SR' => __('Suriname (Southern America)', 'bdthemes-element-pack'),
					'UY' => __('Uruguay (Southern America)', 'bdthemes-element-pack'),
					'VE' => __('Venezuela (Southern America)', 'bdthemes-element-pack'),
					// NORTHERN AMERICA
					'BM' => __('Bermuda (Northern America)', 'bdthemes-element-pack'),
					'CA' => __('Canada (Northern America)', 'bdthemes-element-pack'),
					'GL' => __('Greenland (Northern America)', 'bdthemes-element-pack'),
					'PM' => __('Saint Pierre and Miquelon (Northern America)', 'bdthemes-element-pack'),
					'US' => __('United States (Northern America)', 'bdthemes-element-pack'),
					// CARIBBEAN
					'AI' => __('Anguilla (Caribbean)', 'bdthemes-element-pack'),
					'AG' => __('Antigua and Barbuda (Caribbean)', 'bdthemes-element-pack'),
					'AW' => __('Aruba (Caribbean)', 'bdthemes-element-pack'),
					'BS' => __('Bahamas (Caribbean)', 'bdthemes-element-pack'),
					'BB' => __('Barbados (Caribbean)', 'bdthemes-element-pack'),
					'BQ' => __('Bonaire, Sint Eustatius and Saba (Caribbean)', 'bdthemes-element-pack'),
					'KY' => __('Cayman Islands (Caribbean)', 'bdthemes-element-pack'),
					'CU' => __('Cuba (Caribbean)', 'bdthemes-element-pack'),
					'CW' => __('Curaçao (Caribbean)', 'bdthemes-element-pack'),
					'DM' => __('Dominica (Caribbean)', 'bdthemes-element-pack'),
					'DO' => __('Dominican Republic (Caribbean)', 'bdthemes-element-pack'),
					'GD' => __('Grenada (Caribbean)', 'bdthemes-element-pack'),
					'GP' => __('Guadeloupe (Caribbean)', 'bdthemes-element-pack'),
					'HT' => __('Haiti (Caribbean)', 'bdthemes-element-pack'),
					'JM' => __('Jamaica (Caribbean)', 'bdthemes-element-pack'),
					'MQ' => __('Martinique (Caribbean)', 'bdthemes-element-pack'),
					'MS' => __('Montserrat (Caribbean)', 'bdthemes-element-pack'),
					'PR' => __('Puerto Rico (Caribbean)', 'bdthemes-element-pack'),
					'BL' => __('Saint Barthélemy (Caribbean)', 'bdthemes-element-pack'),
					'KN' => __('Saint Kitts and Nevis (Caribbean)', 'bdthemes-element-pack'),
					'LC' => __('Saint Lucia (Caribbean)', 'bdthemes-element-pack'),
					'MF' => __('Saint Martin (French part) (Caribbean)', 'bdthemes-element-pack'),
					'VC' => __('Saint Vincent and the Grenadines (Caribbean)', 'bdthemes-element-pack'),
					'SX' => __('Sint Maarten (Dutch part) (Caribbean)', 'bdthemes-element-pack'),
					'TT' => __('Trinidad and Tobago (Caribbean)', 'bdthemes-element-pack'),
					'TC' => __('Turks and Caicos Islands (Caribbean)', 'bdthemes-element-pack'),
					'VG' => __('Virgin Islands (British) (Caribbean)', 'bdthemes-element-pack'),
					'VI' => __('Virgin Islands (U.S.) (Caribbean)', 'bdthemes-element-pack'),
					//CENTRAL AMERICA
					'BZ' => __('Belize (Central America)', 'bdthemes-element-pack'),
					'CR' => __('Costa Rica (Central America)', 'bdthemes-element-pack'),
					'SV' => __('El Salvador (Central America)', 'bdthemes-element-pack'),
					'GT' => __('Guatemala (Central America)', 'bdthemes-element-pack'),
					'HN' => __('Honduras (Central America)', 'bdthemes-element-pack'),
					'MX' => __('Mexico (Central America)', 'bdthemes-element-pack'),
					'NI' => __('Nicaragua (Central America)', 'bdthemes-element-pack'),
					'PA' => __('Panama (Central America)', 'bdthemes-element-pack'),
					// CENTRAL ASIA
					'KZ' => __('Kazakhstan (Central Asia)', 'bdthemes-element-pack'),
					'KG' => __('Kyrgyzstan (Central Asia)', 'bdthemes-element-pack'),
					'TJ' => __('Tajikistan (Central Asia)', 'bdthemes-element-pack'),
					'TM' => __('Turkmenistan (Central Asia)', 'bdthemes-element-pack'),
					'UZ' => __('Uzbekistan (Central Asia)', 'bdthemes-element-pack'),
					// EASTERN ASIA
					'CN' => __('China (Eastern Asia)', 'bdthemes-element-pack'),
					'HK' => __('Hong Kong (Eastern Asia)', 'bdthemes-element-pack'),
					'JP' => __('Japan (Eastern Asia)', 'bdthemes-element-pack'),
					'KP' => __('North Korea (Eastern Asia)', 'bdthemes-element-pack'),
					'KR' => __('South Korea (Eastern Asia)', 'bdthemes-element-pack'),
					'MO' => __('Macau (Eastern Asia)', 'bdthemes-element-pack'),
					'MN' => __('Mongolia (Eastern Asia)', 'bdthemes-element-pack'),
					'TW' => __('Taiwan (Eastern Asia)', 'bdthemes-element-pack'),
					// SOUTHERN ASIA
					'AF' => __('Afghanistan (Southern Asia)', 'bdthemes-element-pack'),
					'BD' => __('Bangladesh (Southern Asia)', 'bdthemes-element-pack'),
					'BT' => __('Bhutan (Southern Asia)', 'bdthemes-element-pack'),
					'IN' => __('India (Southern Asia)', 'bdthemes-element-pack'),
					'IR' => __('Iran (Southern Asia)', 'bdthemes-element-pack'),
					'MV' => __('Maldives (Southern Asia)', 'bdthemes-element-pack'),
					'NP' => __('Nepal (Southern Asia)', 'bdthemes-element-pack'),
					'PK' => __('Pakistan (Southern Asia)', 'bdthemes-element-pack'),
					'LK' => __('Sri Lanka (Southern Asia)', 'bdthemes-element-pack'),
					// SOUTHEASTERN ASIA
					'BN' => __('Brunei (Southeastern Asia)', 'bdthemes-element-pack'),
					'KH' => __('Cambodia (Southeastern Asia)', 'bdthemes-element-pack'),
					'ID' => __('Indonesia (Southeastern Asia)', 'bdthemes-element-pack'),
					'LA' => __('Laos (Southeastern Asia)', 'bdthemes-element-pack'),
					'MY' => __('Malaysia (Southeastern Asia)', 'bdthemes-element-pack'),
					'MM' => __('Myanmar (Southeastern Asia)', 'bdthemes-element-pack'),
					'PH' => __('Philippines (Southeastern Asia)', 'bdthemes-element-pack'),
					'SG' => __('Singapore (Southeastern Asia)', 'bdthemes-element-pack'),
					'TH' => __('Thailand (Southeastern Asia)', 'bdthemes-element-pack'),
					'TL' => __('Timor-Leste (Southeastern Asia)', 'bdthemes-element-pack'),
					'VN' => __('Vietnam (Southeastern Asia)', 'bdthemes-element-pack'),
					// WESTERN ASIA
					'AM' => __('Armenia (Western Asia)', 'bdthemes-element-pack'),
					'AZ' => __('Azerbaijan (Western Asia)', 'bdthemes-element-pack'),
					'BH' => __('Bahrain (Western Asia)', 'bdthemes-element-pack'),
					'CY' => __('Cyprus (Western Asia)', 'bdthemes-element-pack'),
					'GE' => __('Georgia (Western Asia)', 'bdthemes-element-pack'),
					'IQ' => __('Iraq (Western Asia)', 'bdthemes-element-pack'),
					'IL' => __('Israel (Western Asia)', 'bdthemes-element-pack'),
					'JO' => __('Jordan (Western Asia)', 'bdthemes-element-pack'),
					'KW' => __('Kuwait (Western Asia)', 'bdthemes-element-pack'),
					'LB' => __('Lebanon (Western Asia)', 'bdthemes-element-pack'),
					'OM' => __('Oman (Western Asia)', 'bdthemes-element-pack'),
					'PS' => __('Palestine (Western Asia)', 'bdthemes-element-pack'),
					'QA' => __('Qatar (Western Asia)', 'bdthemes-element-pack'),
					'SA' => __('Saudi Arabia (Western Asia)', 'bdthemes-element-pack'),
					'SY' => __('Syria (Western Asia)', 'bdthemes-element-pack'),
					'TR' => __('Turkey (Western Asia)', 'bdthemes-element-pack'),
					'AE' => __('United Arab Emirates (Western Asia)', 'bdthemes-element-pack'),
					'YE' => __('Yemen (Western Asia)', 'bdthemes-element-pack'),
					// OCEANIA
					'AS' => __('American Samoa (Oceania)', 'bdthemes-element-pack'),
					'AU' => __('Australia (Oceania)', 'bdthemes-element-pack'),
					'CK' => __('Cook Islands (Oceania)', 'bdthemes-element-pack'),
					'FJ' => __('Fiji (Oceania)', 'bdthemes-element-pack'),
					'PF' => __('French Polynesia (Oceania)', 'bdthemes-element-pack'),
					'GU' => __('Guam (Oceania)', 'bdthemes-element-pack'),
					'KI' => __('Kiribati (Oceania)', 'bdthemes-element-pack'),
					'MH' => __('Marshall Islands (Oceania)', 'bdthemes-element-pack'),
					'FM' => __('Micronesia (Oceania)', 'bdthemes-element-pack'),
					'NR' => __('Nauru (Oceania)', 'bdthemes-element-pack'),
					'NC' => __('New Caledonia (Oceania)', 'bdthemes-element-pack'),
					'NZ' => __('New Zealand (Oceania)', 'bdthemes-element-pack'),
					'NU' => __('Niue (Oceania)', 'bdthemes-element-pack'),
					'MP' => __('Northern Mariana Islands (Oceania)', 'bdthemes-element-pack'),
					'PW' => __('Palau (Oceania)', 'bdthemes-element-pack'),
					'PG' => __('Papua New Guinea (Oceania)', 'bdthemes-element-pack'),
					'PN' => __('Pitcairn Islands (Oceania)', 'bdthemes-element-pack'),
					'WS' => __('Samoa (Oceania)', 'bdthemes-element-pack'),
					'SB' => __('Solomon Islands (Oceania)', 'bdthemes-element-pack'),
					'TK' => __('Tokelau (Oceania)', 'bdthemes-element-pack'),
					'TO' => __('Tonga (Oceania)', 'bdthemes-element-pack'),
					'TV' => __('Tuvalu (Oceania)', 'bdthemes-element-pack'),
					'UM' => __('United States Minor Outlying Islands (Oceania)', 'bdthemes-element-pack'),
					'VU' => __('Vanuatu (Oceania)', 'bdthemes-element-pack'),
					'WF' => __('Wallis and Futuna (Oceania)', 'bdthemes-element-pack'),
				],
				'default'    => 'AU',
				'dynamic'    => ['active' => true],
			]
		);
		$repeaterRegion->add_control(
			'active_region_name',
			[
				'label' => esc_html__('Country name (optional)', 'bdthemes-element-pack'),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__('AU', 'bdthemes-element-pack'),
				'description' => esc_html__('the default value is top-level domain name of the country', 'bdthemes-element-pack'),
			]
		);
		$repeaterRegion->add_control(
			'active_region_color',
			[
				'label' => esc_html__('Active region color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'alpha' => false,
			]
		);
		$repeaterRegion->add_control(
			'active_tooltip_content',
			[
				'label'       => __('Tooltip Content', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 5,
				'placeholder' => __('Type your Description here', 'bdthemes-element-pack'),
			]
		);

		$repeaterRegion->add_control(
			'region_is_linkable',
			[
				'label'         => __('is linkable?', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::SWITCHER,
				'label_on'      => __('Yes', 'bdthemes-element-pack'),
				'label_off'     => __('No', 'bdthemes-element-pack'),
				'return_value'  => 'yes',
				'separator'     => 'before',
			]
		);

		$repeaterRegion->add_control(
			'region_link',
			[
				'label' => esc_html__('Link', 'bdthemes-element-pack'),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__('https://bdthemes.com', 'bdthemes-element-pack'),
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
					'custom_attributes' => '',
				],
				'condition' => [
					'region_is_linkable' => 'yes'
				]
			]
		);

		$this->add_control(
			'svg_maps_array_regions',
			[
				'label' => esc_html__('Regions', 'bdthemes-element-pack'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeaterRegion->get_controls(),
				'default' => [
					[
						'active_region_name' => esc_html__('Australia', 'bdthemes-element-pack'),
					],
				],
				'condition' => [
					'svg_maps_display_type' => 'custom'
				],
				'frontend_available' => true,
				'render_type' => 'none',
				'title_field' => '{{{ active_region_code }}}',
			]
		);


		$this->end_controls_section();
	}


	private function section_style_tooltip() {

		$this->start_controls_section(
			'section_style_tooltip',
			[
				'label' => esc_html__('Tooltip', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'tooltip_height',
			[
				'label' => esc_html__('Height', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 800,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .google-visualization-tooltip' => 'height: {{SIZE}}{{UNIT}} !important;'
				]
			]
		);

		$this->add_responsive_control(
			'tooltip_width',
			[
				'label' => esc_html__('Width', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 800,
						'step' => 1,
					]
				],
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .google-visualization-tooltip' => 'width: {{SIZE}}{{UNIT}} !important;'
				]
			]
		);
		$this->add_control(
			'tooltip_color',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-svg-maps .google-visualization-tooltip .google-visualization-tooltip-item span' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'tooltip_background',
				'label'     => __('Background', 'bdthemes-element-pack'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .bdt-svg-maps .google-visualization-tooltip',
			]
		);

		$this->add_responsive_control(
			'tooltip_padding',
			[
				'label'                 => __('Padding', 'bdthemes-element-pack'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'default' => [
					'top' => '10',
					'right' => '10',
					'bottom' => '10',
					'left' => '10',
					'unit' => 'px',

				],
				'selectors'             => [
					'{{WRAPPER}} .bdt-svg-maps .google-visualization-tooltip'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'tooltip_margin',
			[
				'label'                 => __('Margin', 'bdthemes-element-pack'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .bdt-svg-maps .google-visualization-tooltip'    => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'tooltip_border',
				'label'     => __('Border', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-svg-maps .google-visualization-tooltip',
			]
		);
		$this->add_responsive_control(
			'tooltip_radius',
			[
				'label'                 => __('Border Radius', 'bdthemes-element-pack'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .bdt-svg-maps .google-visualization-tooltip'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'tooltip_border_border!' => '',
				],
			]
		);
		// $this->add_group_control(
		// 	Group_Control_Typography::get_type(),
		// 	[
		// 		'name'      => 'tooltip_typography',
		// 		'label'     => __('Typography', 'bdthemes-element-pack'),
		// 		'selector'  => '{{WRAPPER}} .bdt-svg-maps .google-visualization-tooltip span',
		// 	]
		// );

		$this->end_controls_section();
	}
	public function render() {
		$this->add_render_attribute('svg-maps', [
			'id' => 'bdt-svg-maps-' . esc_attr($this->get_id()),
			'class' => ['bdt-svg-maps']
		], '', true); ?>
		<div <?php $this->print_render_attribute_string('svg-maps'); ?>></div>
<?php
	}
}
