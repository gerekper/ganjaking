<?php
class PAFE_Responsive_Background extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-responsive-background';
	}

	public function pafe_register_controls( $element, $section_id ) {

		if( $element->get_name() == 'section' || $element->get_name() == 'column' || $element->get_name() == 'container' ) {
			$element->start_controls_section(
				'pafe_responsive_background_section',
				[
					'label' => __( 'PAFE Responsive Background', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
		} else {
			$element->start_controls_section(
				'pafe_responsive_background_section',
				[
					'label' => __( 'PAFE Responsive Background', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
				]
			);
		}
		
		
		$element->add_control(
			'pafe_responsive_background',
			[
				'label' => __( 'Enable Responsive Background', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$element->add_responsive_control(
			'pafe_responsive_background_color',
			[
				'label' => _x( 'Color', 'Background Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'label_block' => true,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'pafe_responsive_background' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_responsive_background_image',
			[
				'label' => _x( 'Image', 'Background Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'title' => _x( 'Background Image', 'Background Control', 'elementor' ),
				'selectors' => [
					'{{WRAPPER}}' => 'background-image: url("{{URL}}");',
				],
				'condition' => [
					'pafe_responsive_background' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_responsive_background_position_2',
			[
				'label' => _x( 'Position', 'Background Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => _x( 'Default', 'Background Control', 'elementor' ),
					'top left' => _x( 'Top Left', 'Background Control', 'elementor' ),
					'top center' => _x( 'Top Center', 'Background Control', 'elementor' ),
					'top right' => _x( 'Top Right', 'Background Control', 'elementor' ),
					'center left' => _x( 'Center Left', 'Background Control', 'elementor' ),
					'center center' => _x( 'Center Center', 'Background Control', 'elementor' ),
					'center right' => _x( 'Center Right', 'Background Control', 'elementor' ),
					'bottom left' => _x( 'Bottom Left', 'Background Control', 'elementor' ),
					'bottom center' => _x( 'Bottom Center', 'Background Control', 'elementor' ),
					'bottom right' => _x( 'Bottom Right', 'Background Control', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}}' => 'background-position: {{VALUE}};',
				],
				'condition' => [
					'pafe_responsive_background' => 'yes',
					'pafe_responsive_background_image[url]!' => '',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_responsive_background_attachment',
			[
				'label' => _x( 'Attachment', 'Background Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => _x( 'Default', 'Background Control', 'elementor' ),
					'scroll' => _x( 'Scroll', 'Background Control', 'elementor' ),
					'fixed' => _x( 'Fixed', 'Background Control', 'elementor' ),
				],
				'selectors' => [
					'(desktop+){{WRAPPER}}' => 'background-attachment: {{VALUE}};',
				],
				'condition' => [
					'pafe_responsive_background' => 'yes',
					'pafe_responsive_background_image[url]!' => '',
				],
			]
		);

		$element->add_control(
			'pafe_responsive_background_attachment_alert',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-control-field-description',
				'raw' => __( 'Note: Attachment Fixed works only on desktop.', 'elementor' ),
				'separator' => 'none',
				'condition' => [
					'pafe_responsive_background' => 'yes',
					'pafe_responsive_background_image[url]!' => '',
					'attachment' => 'fixed',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_responsive_background_repeat',
			[
				'label' => _x( 'Repeat', 'Background Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => _x( 'Default', 'Background Control', 'elementor' ),
					'no-repeat' => _x( 'No-repeat', 'Background Control', 'elementor' ),
					'repeat' => _x( 'Repeat', 'Background Control', 'elementor' ),
					'repeat-x' => _x( 'Repeat-x', 'Background Control', 'elementor' ),
					'repeat-y' => _x( 'Repeat-y', 'Background Control', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}}' => 'background-repeat: {{VALUE}};',
				],
				'condition' => [
					'pafe_responsive_background' => 'yes',
					'pafe_responsive_background_image[url]!' => '',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_responsive_background_size',
			[
				'label' => _x( 'Size', 'Background Control', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => _x( 'Default', 'Background Control', 'elementor' ),
					'auto' => _x( 'Auto', 'Background Control', 'elementor' ),
					'cover' => _x( 'Cover', 'Background Control', 'elementor' ),
					'contain' => _x( 'Contain', 'Background Control', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}}' => 'background-size: {{VALUE}};',
				],
				'condition' => [
					'pafe_responsive_background' => 'yes',
					'pafe_responsive_background_image[url]!' => '',
				],
			]
		);

		$element->add_control(
			'pafe_responsive_background_position',
			[
				'label' => __( 'Enable Responsive Background Position (xpos, ypos)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$element->add_responsive_control(
			'pafe_responsive_background_position_x',
			[
				'label' => __( 'xpos', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => 'E.g 100px, 50%',
				'selectors' => [
					'{{WRAPPER}}' => 'background-position-x: {{VALUE}}',
				],
				'condition' => [
					'pafe_responsive_background_position' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_responsive_background_position_y',
			[
				'label' => __( 'ypos', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => 'E.g 100px, 50%',
				'selectors' => [
					'{{WRAPPER}}' => 'background-position-y: {{VALUE}}',
				],
				'condition' => [
					'pafe_responsive_background_position' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_responsive_background_custom_size',
			[
				'label' => __( 'Enable Responsive Background Custom Size', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$element->add_responsive_control(
			'pafe_responsive_background_custom_size_value',
			[
				'label' => __( 'Size', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => 'E.g 100px 200px, 50%',
				'selectors' => [
					'{{WRAPPER}}' => 'background-size: {{VALUE}}',
				],
				'condition' => [
					'pafe_responsive_background_custom_size' => 'yes',
				],
			]
		);

		$element->end_controls_section();

	}

	protected function init_control() {
		add_action( 'elementor/element/section/section_background/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/container/section_background_overlay/after_section_end', [ $this, 'pafe_register_controls' ], 20, 2 );
		add_action( 'elementor/element/column/section_style/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/common/_section_background/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
	}

}
