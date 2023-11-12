<?php
require_once( __DIR__ . '/controls-manager.php' );

class PAFE_Custom_Media_Query_Breakpoints extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-custom-media-query-breakpoints';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_custom_media_query_breakpoints_section',
			[
				'label' => __( 'PAFE Custom Media Query Breakpoints', 'pafe' ),
				'tab' => PAFE_Controls_Manager::TAB_PAFE,
			]
		);

		$element->add_control(
			'pafe_custom_media_query_breakpoints_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'description' => 'This feature only works on the frontend.',
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'pafe_custom_media_query_breakpoints_min_width',
			[
				'label' => __( 'Min Width (px)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 2000,
				'step' => 1,
				'default' => 0,
			]
		);

		$repeater->add_control(
			'pafe_custom_media_query_breakpoints_max_width',
			[
				'label' => __( 'Max Width (px)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 2000,
				'step' => 1,
				'default' => 1200,
			]
		);

		$repeater->add_control(
			'pafe_custom_media_query_breakpoints_hide',
			[
				'label' => __( 'Hide', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$repeater->add_control(
			'pafe_custom_media_query_breakpoints_width',
			[
				'label' => __( 'Width (%)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 1,
			]
		);

		$repeater->add_control(
			'pafe_custom_media_query_breakpoints_margin',
			[
				'label' => __( 'Margin', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
			]
		);

		$repeater->add_control(
			'pafe_custom_media_query_breakpoints_padding',
			[
				'label' => __( 'Padding', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
			]
		);

		$repeater->add_control(
			'pafe_custom_media_query_breakpoints_font_size',
			[
				'label' => _x( 'Font Size', 'Typography Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
					],
				],
			]
		);

		$repeater->add_control(
			'pafe_custom_media_query_breakpoints_line_height',
			[
				'label' => _x( 'Line-Height', 'Typography Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'em',
				],
				'size_units' => [ 'px', 'em' ],
			]
		);

		$repeater->add_control(
			'pafe_custom_media_query_breakpoints_letter_spacing',
			[
				'label' => _x( 'Letter Spacing', 'Typography Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -5,
						'max' => 10,
						'step' => 0.1,
					],
				],
			]
		);

		$repeater->add_control(
			'pafe_custom_media_query_breakpoints_align',
			[
				'label' => __( 'Alignment', 'elementor' ),
				'type' => Elementor\Controls_Manager::CHOOSE,
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
					'justify' => [
						'title' => __( 'Justified', 'elementor' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
			]
		);

		$element->add_control(
			'pafe_custom_media_query_breakpoints_list',
			[
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'title_field' => 'Min: {{{ pafe_custom_media_query_breakpoints_min_width }}}px - Max: {{{ pafe_custom_media_query_breakpoints_max_width }}}px',
				'condition' => [
					'pafe_custom_media_query_breakpoints_enable' => 'yes',
				],
			]
		);

		$repeater = new Elementor\Repeater();

		$field_types = [
			'text' => __( 'Text', 'elementor-pro' ),
			'email' => __( 'Email', 'elementor-pro' ),
			'textarea' => __( 'Textarea', 'elementor-pro' ),
			'url' => __( 'URL', 'elementor-pro' ),
			'tel' => __( 'Tel', 'elementor-pro' ),
			'radio' => __( 'Radio', 'elementor-pro' ),
			'select' => __( 'Select', 'elementor-pro' ),
			'checkbox' => __( 'Checkbox', 'elementor-pro' ),
			'acceptance' => __( 'Acceptance', 'elementor-pro' ),
			'number' => __( 'Number', 'elementor-pro' ),
			'date' => __( 'Date', 'elementor-pro' ),
			'time' => __( 'Time', 'elementor-pro' ),
			'upload' => __( 'File Upload', 'elementor-pro' ),
			'password' => __( 'Password', 'elementor-pro' ),
			'html' => __( 'HTML', 'elementor-pro' ),
			'hidden' => __( 'Hidden', 'elementor-pro' ),
		];

		/**
		 * Forms field types.
		 *
		 * Filters the list of field types displayed in the form `field_type` control.
		 *
		 * @since 1.0.0
		 *
		 * @param array $field_types Field types.
		 */
		$field_types = apply_filters( 'elementor_pro/forms/field_types', $field_types );

		$repeater->start_controls_tabs( 'form_fields_tabs' );

		$repeater->start_controls_tab( 'form_fields_content_tab', [
			'label' => __( 'Content', 'elementor-pro' ),
		] );

		$repeater->add_control(
			'field_type',
			[
				'label' => __( 'Type', 'elementor-pro' ),
				'type' => Elementor\Controls_Manager::SELECT,
				'options' => $field_types,
				'default' => 'text',
			]
		);

		$repeater->add_control(
			'field_label',
			[
				'label' => __( 'Label', 'elementor-pro' ),
				'type' => Elementor\Controls_Manager::TEXT,
				'default' => '',
			]
		);

		$repeater->add_control(
			'placeholder',
			[
				'label' => __( 'Placeholder', 'elementor-pro' ),
				'type' => Elementor\Controls_Manager::TEXT,
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
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'required',
			[
				'label' => __( 'Required', 'elementor-pro' ),
				'type' => Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default' => '',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => '!in',
							'value' => [
								'checkbox',
								'recaptcha',
								'recaptcha_v3',
								'hidden',
								'html',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'field_options',
			[
				'label' => __( 'Options', 'elementor-pro' ),
				'type' => Elementor\Controls_Manager::TEXTAREA,
				'default' => '',
				'description' => __( 'Enter each option in a separate line. To differentiate between label and value, separate them with a pipe char ("|"). For example: First Name|f_name', 'elementor-pro' ),
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'select',
								'checkbox',
								'radio',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'allow_multiple',
			[
				'label' => __( 'Multiple Selection', 'elementor-pro' ),
				'type' => Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'value' => 'select',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'select_size',
			[
				'label' => __( 'Rows', 'elementor-pro' ),
				'type' => Elementor\Controls_Manager::NUMBER,
				'min' => 2,
				'step' => 1,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'value' => 'select',
						],
						[
							'name' => 'allow_multiple',
							'value' => 'true',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'inline_list',
			[
				'label' => __( 'Inline List', 'elementor-pro' ),
				'type' => Elementor\Controls_Manager::SWITCHER,
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
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'field_html',
			[
				'label' => __( 'HTML', 'elementor-pro' ),
				'type' => Elementor\Controls_Manager::TEXTAREA,
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

		$repeater->add_responsive_control(
			'width',
			[
				'label' => __( 'Column Width', 'elementor-pro' ),
				'type' => Elementor\Controls_Manager::SELECT,
				'options' => [
					'' => __( 'Default', 'elementor-pro' ),
					'100' => '100%',
					'80' => '80%',
					'75' => '75%',
					'66' => '66%',
					'60' => '60%',
					'50' => '50%',
					'40' => '40%',
					'33' => '33%',
					'25' => '25%',
					'20' => '20%',
				],
				'default' => '100',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => '!in',
							'value' => [
								'hidden',
								'recaptcha',
								'recaptcha_v3',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'rows',
			[
				'label' => __( 'Rows', 'elementor-pro' ),
				'type' => Elementor\Controls_Manager::NUMBER,
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

		$repeater->add_control(
			'recaptcha_size', [
				'label' => __( 'Size', 'elementor-pro' ),
				'type' => Elementor\Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => [
					'normal' => __( 'Normal', 'elementor-pro' ),
					'compact' => __( 'Compact', 'elementor-pro' ),
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

		$repeater->add_control(
			'recaptcha_style',
			[
				'label' => __( 'Style', 'elementor-pro' ),
				'type' => Elementor\Controls_Manager::SELECT,
				'default' => 'light',
				'options' => [
					'light' => __( 'Light', 'elementor-pro' ),
					'dark' => __( 'Dark', 'elementor-pro' ),
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

		$repeater->add_control(
			'recaptcha_badge', [
				'label' => __( 'Badge', 'elementor-pro' ),
				'type' => Elementor\Controls_Manager::SELECT,
				'default' => 'bottomright',
				'options' => [
					'bottomright' => __( 'Bottom Right', 'elementor-pro' ),
					'bottomleft' => __( 'Bottom Left', 'elementor-pro' ),
					'inline' => __( 'Inline', 'elementor-pro' ),
				],
				'description' => __( 'To view the validation badge, switch to preview mode', 'elementor-pro' ),
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'value' => 'recaptcha_v3',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'css_classes',
			[
				'label' => __( 'CSS Classes', 'elementor-pro' ),
				'type' => Elementor\Controls_Manager::HIDDEN,
				'default' => '',
				'title' => __( 'Add your custom class WITHOUT the dot. e.g: my-class', 'elementor-pro' ),
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'form_fields_advanced_tab',
			[
				'label' => __( 'Advanced', 'elementor-pro' ),
				'condition' => [
					'field_type!' => 'html',
				],
			]
		);

		$repeater->add_control(
			'field_value',
			[
				'label' => __( 'Default Value', 'elementor-pro' ),
				'type' => Elementor\Controls_Manager::TEXT,
				'default' => '',
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
								'select',
								'number',
								'date',
								'time',
								'hidden',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'custom_id',
			[
				'label' => __( 'ID', 'elementor-pro' ),
				'type' => Elementor\Controls_Manager::TEXT,
				'description' => __( 'Please make sure the ID is unique and not used elsewhere in this form. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'elementor-pro' ),
				'render_type' => 'none',
			]
		);

		$repeater->add_control(
			'shortcode',
			[
				'label' => __( 'Shortcode', 'elementor-pro' ),
				'type' => Elementor\Controls_Manager::RAW_HTML,
				'classes' => 'forms-field-shortcode',
				'raw' => '<input class="elementor-form-field-shortcode" readonly />',
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$element->end_controls_section();
	}

	public function before_render_element($element) {
		$settings = $element->get_settings();

		if ( ! empty( $settings['pafe_custom_media_query_breakpoints_enable'] ) ) {

			$css = '';

			if ( array_key_exists( 'pafe_custom_media_query_breakpoints_list',$settings ) ) {
				$list = $settings['pafe_custom_media_query_breakpoints_list'];
				if( !empty($list) ) {
					foreach ($list as $item) {
						$min_width = $item['pafe_custom_media_query_breakpoints_min_width'];
						$max_width = $item['pafe_custom_media_query_breakpoints_max_width'];
						$width = $item['pafe_custom_media_query_breakpoints_width'];
						$hide = $item['pafe_custom_media_query_breakpoints_hide'];
						$margin = $item['pafe_custom_media_query_breakpoints_margin'];
						$padding = $item['pafe_custom_media_query_breakpoints_padding'];
						$font_size = $item['pafe_custom_media_query_breakpoints_font_size'];
						$line_height = $item['pafe_custom_media_query_breakpoints_line_height'];
						$letter_spacing = $item['pafe_custom_media_query_breakpoints_letter_spacing'];
						$align = $item['pafe_custom_media_query_breakpoints_align'];

						if(!empty($min_width) || !empty($max_width)) {
							$css .= '@media ';
							if(!empty($min_width)) {
								$css .= "(min-width:". $min_width ."px)";
							}
							if(!empty($min_width) && !empty($max_width)) {
								$css .= " and ";
							}
							if(!empty($max_width)) {
								$css .= "(max-width:". $max_width ."px)";
							}
							$css .= " { [data-id='" . $element->get_id() . "'] { ";

							if(!empty($width)) {
								$css .= "width:". $width ."% !important;";
							}

							if(!empty($hide)) {
								$css .= "display:none !important;";
							}

							$css .= " } ";

							if(!empty($padding)) {
								$css .= " [data-id='" . $element->get_id() . "'] > .elementor-column-wrap, [data-id='". $element->get_id() ."'] > .elementor-widget-container { padding:". $padding['top'] . $padding['unit'] . " " . $padding['right'] . $padding['unit'] . " " . $padding['bottom'] . $padding['unit'] . " " . $padding['left'] . $padding['unit'] . " !important; }";
							}

							if(!empty($margin)) {
								$css .= " [data-id='" . $element->get_id() . "'] > .elementor-column-wrap, [data-id='". $element->get_id() ."'] > .elementor-widget-container { margin:". $margin['top'] . $margin['unit'] . " " . $margin['right'] . $margin['unit'] . " " . $margin['bottom'] . $margin['unit'] . " " . $margin['left'] . $margin['unit'] . " !important; }";
							}

							if(!empty($font_size['size'])) {
								$css .= " [data-id='" . $element->get_id() . "'] * { font-size:". $font_size['size'] . $font_size['unit'] ."  !important; }";
							}

							if(!empty($line_height['size'])) {
								$css .= " [data-id='" . $element->get_id() . "'] * { line-height:". $line_height['size'] . $line_height['unit'] ."  !important; }";
							}

							if(!empty($align)) {
								$css .= " [data-id='" . $element->get_id() . "'] * { text-align:". $align ."  !important; }";
							}

							$css .= " } ";

						}
						
					}
				}
			}

			if(!empty($css)) {
				$element->add_render_attribute( '_wrapper', [
					'data-pafe-custom-media-query-breakpoints' => $css,
				] );
			}			

		}
	}

	protected function init_control() {
		add_action( 'elementor/element/column/pafe_support_section/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/common/pafe_support_section/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/container/pafe_support_section/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'before_render_element'], 10, 1 );
		add_action( 'elementor/frontend/column/before_render', [ $this, 'before_render_element'], 10, 1 );
		add_action( 'elementor/frontend/container/before_render', [ $this, 'before_render_element'], 10, 1 );
	}

}
