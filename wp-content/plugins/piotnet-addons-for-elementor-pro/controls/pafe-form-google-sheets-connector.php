<?php

class PAFE_Form_Google_Sheets_Connector extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-form-google-sheets-connector';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_form_google_sheets_connector_section',
			[
				'label' => __( 'PAFE Form Google Sheets Connector', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$element->add_control(
			'pafe_form_google_sheets_connector_enable',
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

		$element->add_control(
			'pafe_form_google_sheets_connector_id',
			[
				'label' => __( 'Google Sheet ID', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'ID is the value between the "/d/" and the "/edit" in the URL of your spreadsheet. For example: /spreadsheets/d/****/edit#gid=0', 'pafe' ),
				'condition' => [
					'pafe_form_google_sheets_connector_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_form_google_sheets_connector_tab',
			[
				'label' => __( 'Tab Name', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'pafe_form_google_sheets_connector_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_form_google_sheets_connector_field_note',
			[
				'label' => __( 'Connect Field ID to Google Sheets Column', 'pafe' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( '', 'pafe' ),
				'condition' => [
					'pafe_form_google_sheets_connector_enable' => 'yes',
				],
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'pafe_form_google_sheets_connector_field_id',
			[
				'label' => __( 'Field ID if you use Elementor Pro Form, Field Shortcode if you use PAFE Form Builder', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
			]
		);

		$repeater->add_control(
			'pafe_form_google_sheets_connector_field_column',
			[
				'label' => __( 'Column in Google Sheets', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'description' => 'E.g A,B,C,AA,AB,AC,AZ',
			]
		);

		$element->add_control(
			'pafe_form_google_sheets_connector_field_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'title_field' => '{{{ pafe_form_google_sheets_connector_field_id }}}',
			)
		);

		$element->end_controls_section();
	}

	public function before_render_element($element) {
		$settings = $element->get_settings();
		if (!empty($settings['pafe_form_google_sheets_connector_enable']) && !empty($settings['pafe_form_google_sheets_connector_id'])) {
			if ( array_key_exists( 'pafe_form_google_sheets_connector_field_list',$settings ) ) {
				$list = $settings['pafe_form_google_sheets_connector_field_list'];
				$gs_tab = !empty($settings['pafe_form_google_sheets_connector_tab']) ? $settings['pafe_form_google_sheets_connector_tab'] . '!' : '';	
				if( !empty($list[0]['pafe_form_google_sheets_connector_field_id']) && !empty($list[0]['pafe_form_google_sheets_connector_field_column']) ) {

					$element->add_render_attribute( '_wrapper', [
						'data-pafe-form-google-sheets-connector' => $settings['pafe_form_google_sheets_connector_id'],
						'data-pafe-form-google-sheets-connector-clid' => esc_attr( get_option('piotnet-addons-for-elementor-pro-google-sheets-client-id') ),
						'data-pafe-form-google-sheets-connector-clis' => esc_attr( get_option('piotnet-addons-for-elementor-pro-google-sheets-client-secret') ),
						'data-pafe-form-google-sheets-connector-rtok' => esc_attr( get_option('piotnet-addons-for-elementor-pro-google-sheets-refresh-token') ),
						'data-pafe-form-google-sheets-connector-field-list' => json_encode($list),
						'data-pafe-form-google-sheets-connector-tab' => esc_attr($gs_tab),
					] );
				}
			}
		}
	}

	protected function init_control() {
		add_action( 'elementor/element/form/section_form_fields/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/pafe-form-builder-submit/section_conditional_logic/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/pafe-multi-step-form/section_conditional_logic/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'before_render_element'], 10, 1 );
	}

}
