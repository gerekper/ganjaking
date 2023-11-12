<?php
/**
 * UAEL Fancy Heading.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Headings\Widgets;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;

// UltimateElementor Classes.
use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Fancy_Heading.
 */
class Fancy_Heading extends Common_Widget {

	/**
	 * Retrieve Fancy Heading Widget name.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'Fancy_Heading' );
	}

	/**
	 * Retrieve Fancy Heading Widget title.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Fancy_Heading' );
	}

	/**
	 * Retrieve Fancy Heading Widget icon.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Fancy_Heading' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Fancy_Heading' );
	}

	/**
	 * Retrieve the list of scripts the Fancy Heading widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array( 'uael-frontend-script', 'uael-fancytext-typed', 'uael-fancytext-slidev' );
	}

	/**
	 * Register Fancy Heading controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {

		$this->register_presets_control( 'Fancy_Heading', $this );

		$this->register_headingtext_content_controls();
		$this->register_effect_content_controls();
		$this->register_general_content_controls();
		$this->register_style_content_controls();
		$this->register_helpful_information();
	}

	/**
	 * Register Fancy Heading Text Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_headingtext_content_controls() {
		$this->start_controls_section(
			'section_general_field',
			array(
				'label' => __( 'Heading Text', 'uael' ),
			)
		);

		$this->add_control(
			'fancytext_prefix',
			array(
				'label'    => __( 'Before Text', 'uael' ),
				'type'     => Controls_Manager::TEXT,
				'selector' => '{{WRAPPER}} .uael-fancy-text-prefix',
				'dynamic'  => array(
					'active' => true,
				),
				'default'  => __( 'I am', 'uael' ),
			)
		);

		$this->add_control(
			'fancytext',
			array(
				'label'       => __( 'Fancy Text', 'uael' ),
				'type'        => Controls_Manager::TEXTAREA,
				'placeholder' => __( 'Enter each word in a separate line', 'uael' ),
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => "Creative\nAmazing\nPassionate",
			)
		);
		$this->add_control(
			'fancytext_suffix',
			array(
				'label'    => __( 'After Text', 'uael' ),
				'type'     => Controls_Manager::TEXT,
				'selector' => '{{WRAPPER}} .uael-fancy-text-suffix',
				'dynamic'  => array(
					'active' => true,
				),
				'default'  => __( 'Designer', 'uael' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Fancy Heading Effect Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_effect_content_controls() {
		$this->start_controls_section(
			'section_effect_field',
			array(
				'label' => __( 'Effect', 'uael' ),
			)
		);
		$this->add_control(
			'fancytext_effect_type',
			array(
				'label'       => __( 'Select Effect', 'uael' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'type'       => __( 'Type', 'uael' ),
					'slide'      => __( 'Slide Up', 'uael' ),
					'slide_down' => __( 'Slide Down', 'uael' ),
					'rotate'     => __( 'Rotate', 'uael' ),
					'clip'       => __( 'Clip', 'uael' ),
					'push'       => __( 'Push', 'uael' ),
					'drop_in'    => __( 'Drop In', 'uael' ),
				),
				'default'     => 'type',
				'label_block' => false,
			)
		);
		$this->add_control(
			'fancytext_type_loop',
			array(
				'label'        => __( 'Enable Loop', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'fancytext_effect_type' => 'type',
				),
			)
		);
		$this->add_control(
			'fancytext_type_show_cursor',
			array(
				'label'        => __( 'Show Cursor', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => array(
					'fancytext_effect_type' => array( 'type', 'clip' ),
				),
			)
		);

		$this->add_control(
			'clip_line_color',
			array(
				'label'     => __( 'Line Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_ACCENT,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-clip-cursor-yes .uael-fancy-text-clip .uael-slide-main_ul::after' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'fancytext_effect_type'      => 'clip',
					'fancytext_type_show_cursor' => 'yes',
				),
			)
		);
		$this->add_control(
			'fancytext_type_cursor_text',
			array(
				'label'     => __( 'Cursor Text', 'uael' ),
				'type'      => Controls_Manager::TEXT,
				'selector'  => '{{WRAPPER}}',
				'default'   => __( '|', 'uael' ),
				'condition' => array(
					'fancytext_effect_type'      => 'type',
					'fancytext_type_show_cursor' => 'yes',
				),
				'selector'  => '{{WRAPPER}} .typed-cursor',
			)
		);
		$this->add_control(
			'fancytext_type_cursor_blink',
			array(
				'label'        => __( 'Cursor Blink Effect', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'fancytext_effect_type'      => 'type',
					'fancytext_type_show_cursor' => 'yes',
				),
				'prefix_class' => 'uael-show-cursor-',
			)
		);
		$this->add_control(
			'fancytext_type_fields',
			array(
				'label'        => __( 'Advanced Settings', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => array(
					'fancytext_effect_type' => 'type',
				),
			)
		);

		$this->add_control(
			'fancytext_slide_pause_hover',
			array(
				'label'        => __( 'Pause on Hover', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'fancytext_effect_type' => 'slide',
				),
			)
		);
		$this->add_control(
			'fancytext_slide_anim_speed',
			array(
				'label'       => __( 'Animation Speed (ms)', 'uael' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'ms' ),
				'range'       => array(
					'ms' => array(
						'min' => 1,
						'max' => 5000,
					),
				),
				'default'     => array(
					'size' => '500',
					'unit' => 'ms',
				),
				'label_block' => true,
				'condition'   => array(
					'fancytext_effect_type' => 'slide',
				),
			)
		);
		$this->add_control(
			'fancytext_rotate_anim_speed',
			array(
				'label'       => __( 'Animation Speed (ms)', 'uael' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'ms' ),
				'range'       => array(
					'ms' => array(
						'min' => 1,
						'max' => 5000,
					),
				),
				'default'     => array(
					'size' => '2500',
					'unit' => 'ms',
				),
				'label_block' => true,
				'condition'   => array(
					'fancytext_effect_type!' => array( 'type', 'slide', 'clip' ),
				),
			)
		);
		$this->add_control(
			'fancytext_slide_pause_time',
			array(
				'label'       => __( 'Pause Time (ms)', 'uael' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'ms' ),
				'range'       => array(
					'ms' => array(
						'min' => 1,
						'max' => 5000,
					),
				),
				'default'     => array(
					'size' => '2000',
					'unit' => 'ms',
				),
				'label_block' => true,
				'condition'   => array(
					'fancytext_effect_type' => 'slide',
				),
			)
		);
		$this->add_control(
			'fancytext_clip_anim_speed',
			array(
				'label'       => __( 'Animation Speed (ms)', 'uael' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'ms' ),
				'range'       => array(
					'ms' => array(
						'min' => 1,
						'max' => 5000,
					),
				),
				'default'     => array(
					'size' => '600',
					'unit' => 'ms',
				),
				'label_block' => true,
				'condition'   => array(
					'fancytext_effect_type' => array( 'clip' ),
				),
			)
		);
		$this->add_control(
			'fancytext_clip_pause_time',
			array(
				'label'       => __( 'Pause Time (ms)', 'uael' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'ms' ),
				'range'       => array(
					'ms' => array(
						'min' => 1,
						'max' => 5000,
					),
				),
				'default'     => array(
					'size' => '1500',
					'unit' => 'ms',
				),
				'label_block' => true,
				'condition'   => array(
					'fancytext_effect_type' => 'clip',
				),
			)
		);
		$this->add_control(
			'fancytext_type_speed',
			array(
				'label'       => __( 'Typing Speed (ms)', 'uael' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'ms' ),
				'range'       => array(
					'ms' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'default'     => array(
					'size' => '120',
					'unit' => 'ms',
				),
				'label_block' => true,
				'condition'   => array(
					'fancytext_effect_type' => 'type',
					'fancytext_type_fields' => 'yes',
				),
			)
		);
		$this->add_control(
			'fancytext_type_backspeed',
			array(
				'label'       => __( 'Backspeed (ms)', 'uael' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'ms' ),
				'range'       => array(
					'ms' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'default'     => array(
					'size' => '60',
					'unit' => 'ms',
				),
				'label_block' => true,
				'condition'   => array(
					'fancytext_effect_type' => 'type',
					'fancytext_type_fields' => 'yes',
				),
			)
		);

		$this->add_control(
			'fancytext_type_start_delay',
			array(
				'label'       => __( 'Start Delay (ms)', 'uael' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'ms' ),
				'range'       => array(
					'ms' => array(
						'min' => 0,
						'max' => 5000,
					),
				),
				'default'     => array(
					'size' => '0',
					'unit' => 'ms',
				),
				'label_block' => true,
				'condition'   => array(
					'fancytext_effect_type' => 'type',
					'fancytext_type_fields' => 'yes',
				),
			)
		);
		$this->add_control(
			'fancytext_type_back_delay',
			array(
				'label'       => __( 'Back Delay (ms)', 'uael' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'ms' ),
				'range'       => array(
					'ms' => array(
						'min' => 0,
						'max' => 5000,
					),
				),
				'default'     => array(
					'size' => '1200',
					'unit' => 'ms',
				),
				'label_block' => true,
				'condition'   => array(
					'fancytext_effect_type' => 'type',
					'fancytext_type_fields' => 'yes',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Register Fancy Heading General Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_general_content_controls() {
		$this->start_controls_section(
			'section_structure_field',
			array(
				'label' => __( 'General', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'fancytext_title_tag',
			array(
				'label'   => __( 'Title Tag', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'h1'  => __( 'H1', 'uael' ),
					'h2'  => __( 'H2', 'uael' ),
					'h3'  => __( 'H3', 'uael' ),
					'h4'  => __( 'H4', 'uael' ),
					'h5'  => __( 'H5', 'uael' ),
					'h6'  => __( 'H6', 'uael' ),
					'div' => __( 'div', 'uael' ),
					'p'   => __( 'p', 'uael' ),
				),
				'default' => 'h3',
			)
		);
		$this->add_responsive_control(
			'fancytext_align',
			array(
				'label'              => __( 'Alignment', 'uael' ),
				'type'               => Controls_Manager::CHOOSE,
				'options'            => array(
					'left'   => array(
						'title' => __( 'Left', 'uael' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'uael' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'uael' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'default'            => 'left',
				'selectors'          => array(
					'{{WRAPPER}} .uael-fancy-text-wrap ' => 'text-align: {{VALUE}};',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'fancytext_layout',
			array(
				'label'        => __( 'Layout', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Stack', 'uael' ),
				'label_off'    => __( 'Inline', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'prefix_class' => 'uael-fancytext-stack-',
			)
		);
		$this->add_responsive_control(
			'fancytext_space_prefix',
			array(
				'label'              => __( 'Before Spacing', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => array( 'px' ),
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'default'            => array(
					'size' => '0',
					'unit' => 'px',
				),
				'selectors'          => array(
					'{{WRAPPER}}.uael-fancytext-stack-yes .uael-fancy-stack ' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.uael-fancytext-stack-yes .uael-fancy-stack .uael-fancy-heading.uael-fancy-text-main' => ' margin-left: 0px;',
					'{{WRAPPER}} .uael-fancy-text-main' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'frontend_available' => true,
			)
		);
		$this->add_responsive_control(
			'fancytext_space_suffix',
			array(
				'label'              => __( 'After Spacing', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => array( 'px' ),
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'default'            => array(
					'size' => '0',
					'unit' => 'px',
				),
				'condition'          => array(
					'fancytext_suffix!' => '',
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-fancy-text-main' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.uael-fancytext-stack-yes .uael-fancy-stack ' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.uael-fancytext-stack-yes .uael-fancy-stack .uael-fancy-heading.uael-fancy-text-main' => ' margin-right: 0px;',
				),
				'frontend_available' => true,
			)
		);
		$this->add_responsive_control(
			'fancytext_min_height',
			array(
				'label'              => __( 'Minimum Height', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => array( 'px' ),
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-fancy-text-wrap' => 'min-height: {{SIZE}}{{UNIT}};',
				),
				'condition'          => array(
					'fancytext_effect_type' => 'type',
				),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Fancy Heading Style Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_style_content_controls() {

		$this->start_controls_section(
			'section_typography_field',
			array(
				'label' => __( 'Style', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->start_controls_tabs( 'tabs_fancytext' );

			$this->start_controls_tab(
				'tab_heading',
				array(
					'label' => __( 'Heading Text', 'uael' ),
				)
			);
			$this->add_control(
				'prefix_suffix_color',
				array(
					'label'     => __( 'Text Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_PRIMARY,
					),
					'default'   => '',
					'selectors' => array(
						'{{WRAPPER}} .uael-fancy-heading' => 'color: {{VALUE}};',
					),
				)
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'prefix_suffix_typography',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
					),
					'selector' => '{{WRAPPER}} .uael-fancy-heading, {{WRAPPER}} .uael-fancy-heading .uael-slide_text',
				)
			);
			$this->add_control(
				'text_adv_options',
				array(
					'label'        => __( 'Advanced', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'uael' ),
					'label_off'    => __( 'No', 'uael' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				array(
					'name'      => 'text_bg_color',
					'label'     => __( 'Background Color', 'uael' ),
					'types'     => array( 'classic', 'gradient' ),
					'selector'  => '{{WRAPPER}} .uael-fancy-heading',
					'condition' => array(
						'text_adv_options' => 'yes',
					),
				)
			);
			$this->add_responsive_control(
				'text_padding',
				array(
					'label'              => __( 'Padding', 'uael' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', '%' ),
					'selectors'          => array(
						'{{WRAPPER}} .uael-fancy-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'          => array(
						'text_adv_options' => 'yes',
					),
					'frontend_available' => true,
				)
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'        => 'text_border',
					'label'       => __( 'Border', 'uael' ),
					'placeholder' => '1px',
					'default'     => '1px',
					'selector'    => '{{WRAPPER}} .uael-fancy-heading',
					'condition'   => array(
						'text_adv_options' => 'yes',
					),
				)
			);
			$this->add_control(
				'text_border_radius',
				array(
					'label'      => __( 'Border Radius', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .uael-fancy-heading' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'text_adv_options' => 'yes',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name'      => 'text_shadow',
					'selector'  => '{{WRAPPER}} .uael-fancy-heading',
					'condition' => array(
						'text_adv_options' => 'yes',
					),
				)
			);
			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_fancy',
				array(
					'label' => __( 'Fancy Text', 'uael' ),
				)
			);
			$this->add_control(
				'fancytext_color',
				array(
					'label'     => __( 'Fancy Text Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_SECONDARY,
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-fancy-heading.uael-fancy-text-main' => 'color: {{VALUE}};',
					),
				)
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'fancytext_typography',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
					),
					'selector' => '{{WRAPPER}} .uael-fancy-heading.uael-fancy-text-main, {{WRAPPER}} .uael-fancy-heading.uael-fancy-text-main .uael-slide_text',
				)
			);
			$this->add_control(
				'fancy_adv_options',
				array(
					'label'        => __( 'Advanced', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'uael' ),
					'label_off'    => __( 'No', 'uael' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);
			$this->add_control(
				'fancytext_bg_color',
				array(
					'label'     => __( 'Background Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .uael-fancy-heading.uael-fancy-text-main' => 'background-color: {{VALUE}};',
					),
					'condition' => array(
						'fancy_adv_options' => 'yes',
					),
				)
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				array(
					'name'      => 'fancytext_bg_color',
					'label'     => __( 'Background Color', 'uael' ),
					'types'     => array( 'classic', 'gradient' ),
					'selector'  => '{{WRAPPER}} .uael-fancy-heading.uael-fancy-text-main',
					'condition' => array(
						'fancy_adv_options' => 'yes',
					),
				)
			);
			$this->add_responsive_control(
				'fancytext_padding',
				array(
					'label'              => __( 'Padding', 'uael' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', '%' ),
					'selectors'          => array(
						'{{WRAPPER}} .uael-fancy-heading.uael-fancy-text-main' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'          => array(
						'fancy_adv_options' => 'yes',
					),
					'frontend_available' => true,
				)
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'        => 'fancytext_border',
					'label'       => __( 'Border', 'uael' ),
					'placeholder' => '1px',
					'default'     => '1px',
					'selector'    => '{{WRAPPER}} .uael-fancy-heading.uael-fancy-text-main',
					'condition'   => array(
						'fancy_adv_options' => 'yes',
					),
				)
			);
			$this->add_control(
				'fancytext_border_radius',
				array(
					'label'      => __( 'Border Radius', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .uael-fancy-heading.uael-fancy-text-main' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'fancy_adv_options' => 'yes',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name'      => 'fancytext_shadow',
					'selector'  => '{{WRAPPER}} .uael-fancy-heading.uael-fancy-text-main',
					'condition' => array(
						'fancy_adv_options' => 'yes',
					),
				)
			);
			$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	/**
	 * Helpful Information.
	 *
	 * @since 1.1.0
	 * @access protected
	 */
	protected function register_helpful_information() {

		$help_link_2 = UAEL_DOMAIN . 'docs/fancy-heading-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin';

		if ( parent::is_internal_links() ) {
			$this->start_controls_section(
				'section_helpful_info',
				array(
					'label' => __( 'Helpful Information', 'uael' ),
				)
			);

			$this->add_control(
				'help_doc_1',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Getting started video » %2$s', 'uael' ), '<a href="https://www.youtube.com/watch?v=BqXOvmpulQQ&index=7&list=PL1kzJGWGPrW_7HabOZHb6z88t_S8r-xAc" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_2',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Getting started article » %2$s', 'uael' ), '<a href=' . $help_link_2 . ' target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->end_controls_section();
		}
	}

	/**
	 * Get Fancy Text data.
	 *
	 * Written in PHP.
	 *
	 * @since 1.3.1
	 * @access protected
	 */
	protected function get_fancytext_data() {
		$settings = $this->get_settings_for_display();

		$fancy_text   = $this->get_settings_for_display( 'fancytext' );
		$fancy_text   = preg_replace( '/[\n\r]/', '|', $fancy_text );
		$data_strings = explode( '|', $fancy_text );

		return $data_strings;

	}

	/**
	 * Render Fancy Text output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render() {
		$html             = '';
		$settings         = $this->get_settings();
		$dynamic_settings = $this->get_settings_for_display();

		// Get Data Attributes.
		$effect_type  = $settings['fancytext_effect_type'];
		$data_strings = $this->get_fancytext_data();
		$fancy_data   = wp_json_encode( $data_strings );

		if ( 'type' === $settings['fancytext_effect_type'] ) {
			$type_speed  = ( '' !== $settings['fancytext_type_speed']['size'] ) ? $settings['fancytext_type_speed']['size'] : 120;
			$back_speed  = ( '' !== $settings['fancytext_type_backspeed']['size'] ) ? $settings['fancytext_type_backspeed']['size'] : 60;
			$start_delay = ( '' !== $settings['fancytext_type_start_delay']['size'] ) ? $settings['fancytext_type_start_delay']['size'] : 0;
			$back_delay  = ( '' !== $settings['fancytext_type_back_delay']['size'] ) ? $settings['fancytext_type_back_delay']['size'] : 1200;
			$loop        = ( 'yes' === $settings['fancytext_type_loop'] ) ? 'true' : 'false';

			if ( 'yes' === $settings['fancytext_type_show_cursor'] ) {
				$show_cursor = 'true';
				$cursor_char = ( '' !== $settings['fancytext_type_cursor_text'] ) ? $settings['fancytext_type_cursor_text'] : '|';
			} else {
				$show_cursor = 'false';
				$cursor_char = '';
			}

			$this->add_render_attribute(
				'fancy-text',
				array(
					'data-type-speed'  => $type_speed,
					'data-animation'   => $effect_type,
					'data-back-speed'  => $back_speed,
					'data-start-delay' => $start_delay,
					'data-back-delay'  => $back_delay,
					'data-loop'        => $loop,
					'data-show-cursor' => $show_cursor,
					'data-cursor-char' => $cursor_char,
					'data-strings'     => $fancy_data,
				)
			);

		} elseif ( 'slide' === $settings['fancytext_effect_type'] ) {
			$speed = ( '' !== $settings['fancytext_slide_anim_speed']['size'] ) ? $settings['fancytext_slide_anim_speed']['size'] : 35;

			$pause = ( '' !== $settings['fancytext_slide_pause_time']['size'] ) ? $settings['fancytext_slide_pause_time']['size'] : 3000;

			$mousepause = ( 'yes' === $settings['fancytext_slide_pause_hover'] ) ? true : false;

			$this->add_render_attribute(
				'fancy-text',
				array(
					'data-animation'  => $effect_type,
					'data-speed'      => $speed,
					'data-pause'      => $pause,
					'data-mousepause' => $mousepause,
					'data-strings'    => $fancy_data,
				)
			);
		} else {
			$speed = ( '' !== $settings['fancytext_rotate_anim_speed']['size'] ) ? $settings['fancytext_rotate_anim_speed']['size'] : 2500;

			$this->add_render_attribute(
				'fancy-text',
				array(
					'data-animation' => $effect_type,
					'data-speed'     => $speed,
				)
			);

			if ( 'clip' === $settings['fancytext_effect_type'] ) {
				$clip_speed = ( '' !== $settings['fancytext_clip_anim_speed']['size'] ) ? $settings['fancytext_clip_anim_speed']['size'] : 600;
				$pause_time = ( '' !== $settings['fancytext_clip_pause_time']['size'] ) ? $settings['fancytext_clip_pause_time']['size'] : 1500;

				$this->add_render_attribute(
					'fancy-text',
					array(
						'data-clip_speed' => $clip_speed,
						'data-pause_time' => $pause_time,
					)
				);
			}
		}

		$node_id      = $this->get_id();
		$cursor_class = ( 'yes' === $settings['fancytext_type_show_cursor'] ) ? 'uael-clip-cursor-yes' : '';
		?>
		<div class="uael-module-content uael-fancy-text-node <?php echo esc_attr( $cursor_class ); ?>" <?php echo wp_kses_post( $this->get_render_attribute_string( 'fancy-text' ) ); ?>>
			<?php if ( ! empty( $settings['fancytext_effect_type'] ) ) { ?>
				<?php $fancytext_title_tag = UAEL_Helper::validate_html_tag( $settings['fancytext_title_tag'] ); ?>
				<?php echo '<' . esc_attr( $fancytext_title_tag ); ?> class="uael-fancy-text-wrap uael-fancy-text-<?php echo esc_attr( $settings['fancytext_effect_type'] ); ?>">
					<?php if ( '' !== $dynamic_settings['fancytext_prefix'] ) { ?>
						<span class="uael-fancy-heading uael-fancy-text-prefix"><?php echo wp_kses_post( $this->get_settings_for_display( 'fancytext_prefix' ) ); ?></span>
					<?php } ?>
						<span class="uael-fancy-stack">
					<?php
					if ( 'type' === $settings['fancytext_effect_type'] ) {
						?>
						<span class="uael-fancy-heading uael-fancy-text-main uael-typed-main-wrap "><span class="uael-typed-main"></span><span class="uael-text-holder">.</span></span>
						<?php
					} else {
							$order       = array( "\r\n", "\n", "\r", '<br/>', '<br>' );
							$replace     = '|';
							$str         = str_replace( $order, $replace, trim( $settings['fancytext'] ) );
							$lines       = explode( '|', $str );
							$count_lines = count( $lines );
							$output      = '';
							$count       = 0;
						?>
							<span class="uael-fancy-heading uael-fancy-text-main uael-slide-main uael-adjust-width">
								<span class="uael-slide-main_ul">
									<?php foreach ( $lines as $key => $line ) { ?>
										<?php
											$count++;
											$dummy_class = ( 1 === $count && 'slide' !== $settings['fancytext_effect_type'] ) ? 'uael-active-heading' : '';
										?>
											<span <?php echo wp_kses_post( 'class="uael-slide-block ' . $dummy_class . '"' ); ?>><span class="uael-slide_text"><?php echo esc_html( wp_strip_all_tags( $line ) ); ?></span>
											</span>
											<?php if ( 1 === $count_lines ) { ?>
												<span class="uael-slide-block"><span class="uael-slide_text"><?php echo esc_html( wp_strip_all_tags( $line ) ); ?></span></span>
											<?php } ?>
										<?php } ?>
								</span>
							</span>
						<?php } ?>
						</span>
					<?php if ( '' !== $dynamic_settings['fancytext_suffix'] ) { ?>
						<span class="uael-fancy-heading uael-fancy-text-suffix"><?php echo wp_kses_post( $this->get_settings_for_display( 'fancytext_suffix' ) ); ?></span>
					<?php } ?>
				<?php echo '</' . esc_attr( $fancytext_title_tag ) . '>'; ?>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Render Fancy Heading widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.22.1
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
		function get_fancytext_data(){
			var ipstr 		= settings.fancytext;
			var strs        = ipstr.split( "\n" );
			return strs;
		}
		var effect_type = settings.fancytext_effect_type;

		if ( 'type' == settings.fancytext_effect_type ) {
			var type_speed  = ( '' != settings.fancytext_type_speed.size ) ? settings.fancytext_type_speed.size : 120;
			var back_speed  = ( '' != settings.fancytext_type_backspeed.size ) ? settings.fancytext_type_backspeed.size : 60;
			var start_delay = ( '' != settings.fancytext_type_start_delay.size ) ? settings.fancytext_type_start_delay.size : 0;
			var back_delay  = ( '' != settings.fancytext_type_back_delay.size ) ? settings.fancytext_type_back_delay.size : 1200;
			var loop        = ( 'yes' == settings.fancytext_type_loop ) ? 'true' : 'false';

			if ( 'yes' == settings.fancytext_type_show_cursor ) {
				var show_cursor = 'true';
				var cursor_char = ( '' != settings.fancytext_type_cursor_text ) ? settings.fancytext_type_cursor_text : '|';
			} else {
				var show_cursor = 'false';
				var cursor_char = '';
			}

			var data_strings = get_fancytext_data();
			var fancy_data = JSON.stringify( data_strings );

			view.addRenderAttribute(
				'fancy-text',
				{
					'data-type-speed'  		: type_speed,
					'data-animation'   		: effect_type,
					'data-back-speed'  		: back_speed,
					'data-start-delay'     	: start_delay,
					'data-back-delay'     	: back_delay,
					'data-loop'   			: loop,
					'data-show-cursor'   	: show_cursor,
					'data-cursor-char'     	: cursor_char,
					'data-strings'     		: fancy_data,
				}
			);
		}
		else if ( 'slide' == settings.fancytext_effect_type ) {

			var speed = ( '' != settings.fancytext_slide_anim_speed.size ) ? settings.fancytext_slide_anim_speed.size : 35;

			var pause = ( '' != settings.fancytext_slide_pause_time.size ) ? settings.fancytext_slide_pause_time.size : 3000;

			var mousepause = ( 'yes' == settings.fancytext_slide_pause_hover ) ? true : false;

			view.addRenderAttribute(
				'fancy-text',
				{
					'data-animation'  		: effect_type,
					'data-speed'   			: speed,
					'data-pause'  			: pause,
					'data-mousepause'     	: mousepause,
					'data-strings'     		: fancy_data,
				}
			);
		} else {
			var speed = ( '' != settings.fancytext_rotate_anim_speed.size ) ? settings.fancytext_rotate_anim_speed.size : 2500;

			view.addRenderAttribute(
				'fancy-text',
				{
					'data-animation'  		: effect_type,
					'data-speed'   			: speed,
				}
			);

			if( 'clip' == settings.fancytext_effect_type ) {
				var speed = ( '' != settings.fancytext_clip_anim_speed.size ) ? settings.fancytext_clip_anim_speed.size : 600;
				var pause_time = ( '' != settings.fancytext_clip_pause_time.size ) ? settings.fancytext_clip_pause_time.size : 1500;

				view.addRenderAttribute(
					'fancy-text',
					{
						'data-clip_speed'  		: speed,
						'data-pause_time'   : pause_time,
					}
				);
			}
		}
		var cursor_class = ( 'yes' == settings.fancytext_type_show_cursor ) ? 'uael-clip-cursor-yes' : '';

		var fancy_text_title_tag = settings.fancytext_title_tag;

		if ( typeof elementor.helpers.validateHTMLTag === "function" ) {
			fancy_text_title_tag = elementor.helpers.validateHTMLTag( fancy_text_title_tag );
		} else if( UAEWidgetsData.allowed_tags ) {
			fancy_text_title_tag = UAEWidgetsData.allowed_tags.includes( fancy_text_title_tag.toLowerCase() ) ? fancy_text_title_tag : 'div';
		}

		#>
			<div class="uael-module-content uael-fancy-text-node {{{ cursor_class }}}" {{{ view.getRenderAttributeString( 'fancy-text' ) }}}> <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
				<# if ( '' != settings.fancytext_effect_type ) { #>
					<{{ fancy_text_title_tag }} class="uael-fancy-text-wrap uael-fancy-text-{{ settings.fancytext_effect_type }}" >

						<# if ( '' != settings.fancytext_prefix ) { #>
							<span class="uael-fancy-heading uael-fancy-text-prefix">{{ settings.fancytext_prefix }}</span>
						<# } #>
						<span class="uael-fancy-stack">
							<# if ( 'type' == settings.fancytext_effect_type ) { #>
								<span class="uael-fancy-heading uael-fancy-text-main uael-typed-main-wrap"><span class="uael-typed-main"></span><span class="uael-text-holder">.</span></span>
							<# }
							else { #>
								<#
								var str 	= settings.fancytext;
								str 		= str.trim();
								str 		= str.replace( /\r?\n|\r/g, "|" );
								var lines 	= str.split("|");
								var count_lines = lines.length;
								var output      = '';
								var count       = 0;
								#>
								<span class="uael-fancy-heading uael-fancy-text-main uael-slide-main uael-adjust-width">
									<span class="uael-slide-main_ul">
										<#
										lines.forEach(function(line){ #>
											<# count++;
												var dummy_class = ( count == 1 && 'slide' !== settings.fancytext_effect_type ) ? 'uael-slide-block uael-active-heading' : 'uael-slide-block'
											#>
											<span class="{{dummy_class}}"><span class="uael-slide_text">{{ line }}</span></span>

											<# if ( 1 == count_lines ) { #>
												<span class="uael-slide-block"><span class="uael-slide_text">{{ line }}</span></span>
											<# }
										});
										#>
									</span>
								</span>
							<# } #>
						</span>
						<# if ( '' != settings.fancytext_suffix ) { #>
							<span class="uael-fancy-heading uael-fancy-text-suffix">{{ settings.fancytext_suffix }}</span>
						<# } #>

					</{{ fancy_text_title_tag }}>
				<# } #>
			</div>
			<# elementorFrontend.hooks.doAction( 'frontend/element_ready/uael-fancy-heading.default' ); #>
		<?php
	}

}
