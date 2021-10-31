<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Steps Widget
 *
 * Porto Elementor widget to display steps, history or timeline
 *
 * @since 6.3.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Steps_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_steps';
	}

	public function get_title() {
		return __( 'Porto Steps', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'step', 'timeline', 'history' );
	}

	public function get_icon() {
		return 'eicon-history';
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_steps',
			array(
				'label' => __( 'Steps', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'type',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Type', 'porto-functionality' ),
				'options' => array(
					'schedule' => __( 'Schedule', 'porto-functionality' ),
					'history'  => __( 'History', 'porto-functionality' ),
					'step'     => __( 'Step', 'porto-functionality' ),
				),
				'default' => 'schedule',
			)
		);

		$this->add_control(
			'title',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Title', 'porto-functionality' ),
				'condition' => array(
					'type' => 'schedule',
				),
			)
		);

		$this->add_control(
			'subtitle',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Sub Title', 'porto-functionality' ),
				'condition' => array(
					'type' => 'schedule',
				),
			)
		);

		$this->add_control(
			'circle_type',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Circle Type', 'porto-functionality' ),
				'options'   => array(
					'filled' => __( 'Filled', 'porto-functionality' ),
					'simple' => __( 'Simple', 'porto-functionality' ),
				),
				'default'   => 'filled',
				'condition' => array(
					'type' => 'schedule',
				),
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'      => 'title_typography',
				'scheme'    => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'     => __( 'Title Typograhy', 'porto-functionality' ),
				'selector'  => '{{WRAPPER}} .step-title',
				'condition' => array(
					'type' => 'schedule',
				),
			)
		);

		$this->add_control(
			'title_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Title Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .step-title' => 'color: {{VALUE}} !important;',
				),
				'condition' => array(
					'type' => 'schedule',
				),
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'      => 'subtitle_typography',
				'scheme'    => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'     => __( 'Sub Title Typograhy', 'porto-functionality' ),
				'selector'  => '{{WRAPPER}} .step-subtitle',
				'condition' => array(
					'type' => 'schedule',
				),
			)
		);

		$this->add_control(
			'subtitle_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Sub Title Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .step-subtitle' => 'color: {{VALUE}} !important;',
				),
				'condition' => array(
					'type' => 'schedule',
				),
			)
		);

		$this->add_control(
			'is_horizontal',
			array(
				'type'        => Controls_Manager::SWITCHER,
				'label'       => __( 'Is Horizontal?', 'porto-functionality' ),
				'description' => __( 'Default layout is vertical.', 'porto-functionality' ),
				'condition'   => array(
					'type' => 'step',
				),
			)
		);

		$repeater = new Elementor\Repeater();

		$repeater->start_controls_tabs(
			'step_item_tabs'
		);

		$repeater->start_controls_tab(
			'step_item_content',
			array(
				'label' => esc_html__( 'Content', 'porto-functionality' ),
			)
		);

		$repeater->add_control(
			'subtitle',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Item Title', 'porto-functionality' ),
			)
		);
		$repeater->add_control(
			'icon_type',
			array(
				'label'       => __( 'Icon to display', 'porto-functionality' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'icon'   => __( 'Icon Fonts', 'porto-functionality' ),
					'custom' => __( 'Custom Image Icon', 'porto-functionality' ),
				),
				'default'     => 'icon',
				'description' => __( 'Use an existing font icon or upload a custom image.', 'porto-functionality' ),
			)
		);
		$repeater->add_control(
			'icon_cl',
			array(
				'type'             => Controls_Manager::ICONS,
				'label'            => __( 'Icon', 'porto-functionality' ),
				'fa4compatibility' => 'icon',
				'default'          => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'icon_type' => 'icon',
				),
			)
		);
		$repeater->add_control(
			'image_id',
			array(
				'type'        => Controls_Manager::MEDIA,
				'label'       => __( 'Upload Image Icon:', 'porto-functionality' ),
				'description' => __( 'Upload the custom image icon.', 'porto-functionality' ),
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'icon_type' => array( 'custom' ),
				),
			)
		);
		$repeater->add_control(
			'image_url',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'External Image Url', 'porto-functionality' ),
				'condition' => array(
					'icon_type' => array( 'custom' ),
				),
			)
		);
		$repeater->add_control(
			'heading',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Heading', 'porto-functionality' ),
			)
		);
		$repeater->add_control(
			'content',
			array(
				'type'      => Controls_Manager::WYSIWYG,
				'label'     => __( 'Details', 'porto-functionality' ),
			)
		);
		$repeater->add_control(
			'shadow',
			array(
				'type'        => Controls_Manager::SWITCHER,
				'label'       => __( 'Shadow', 'porto-functionality' ),
				'condition'   => array(
					'type' => 'schedule',
				),
			)
		);
		$repeater->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'      => 'item_title_typography',
				'scheme'    => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'     => __( 'Title Typograhy', 'porto-functionality' ),
				'selector'  => '{{WRAPPER}} .step-item-title',
			)
		);
		$repeater->add_control(
			'item_title_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Title Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .step-item-title' => 'color: {{VALUE}} !important;',
				),
			)
		);
		$repeater->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'      => 'item_subtitle_typography',
				'scheme'    => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'     => __( 'Sub Title Typograhy', 'porto-functionality' ),
				'selector'  => '{{WRAPPER}} .step-item-subtitle',
				'condition'   => array(
					'type!' => 'step',
				),
			)
		);
		$repeater->add_control(
			'item_subtitle_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Sub Title Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .step-item-subtitle' => 'color: {{VALUE}} !important;',
				),
				'condition'   => array(
					'type!' => 'step',
				),
			)
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$presets = array(
			array(
				'subtitle' => 'Item Sub Title',
			),
		);
		$this->add_control(
			'step_item_list',
			array(
				'label'       => esc_html__( 'Step Items', 'porto-functionality' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $presets,
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		if ( $template = porto_shortcode_template( 'porto_schedule_timeline_container' ) ) {
			include $template;
		}
	}
}
