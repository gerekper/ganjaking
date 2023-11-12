<?php
/**
 * UAEL ContentToggle.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\ContentToggle\Widgets;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class ContentToggle.
 */
class ContentToggle extends Common_Widget {

	/**
	 * Retrieve Radio Button Switcher Widget name.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'ContentToggle' );
	}

	/**
	 * Retrieve Radio Button Switcher Widget title.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'ContentToggle' );
	}

	/**
	 * Retrieve Radio Button Switcher Widget icon.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'ContentToggle' );
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
		return parent::get_widget_keywords( 'ContentToggle' );
	}

	/**
	 * Retrieve the list of scripts the Radio Button Switcher widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array( 'uael-frontend-script', 'uael-content-toggle' );
	}

	/**
	 * Register General Content controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {
		$this->register_general_content_controls();
		$this->register_helpful_information();
	}

	/**
	 * Render button widget classes names.
	 *
	 * @since 0.0.1
	 * @param array  $settings The settings array.
	 * @param int    $node_id The node id.
	 * @param string $section Section one or two.
	 * @return string Concatenated string of classes
	 * @access public
	 */
	public function get_modal_content( $settings, $node_id, $section ) {

		$normal_content_1 = $this->get_settings_for_display( 'section_content_1' );
		$normal_content_2 = $this->get_settings_for_display( 'section_content_2' );
		$content_type     = $settings[ $section ];
		$output           = '';
		if ( 'rbs_select_section_1' === $section ) {
			switch ( $content_type ) {
				case 'content':
					global $wp_embed;
					$output = '<div>' . wpautop( $wp_embed->autoembed( $normal_content_1 ) ) . '</div>';
					break;
				case 'saved_rows':
					$output = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $settings['section_saved_rows_1'] );
					break;
				case 'saved_container':
						$output = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $settings['section_saved_container_1'] );
					break;
				case 'saved_page_templates':
					$output = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $settings['section_saved_pages_1'] );
					break;
				default:
					break;
			}
		} else {
			switch ( $content_type ) {
				case 'content':
					global $wp_embed; // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.VariableRedeclaration
					$output = '<div>' . wpautop( $wp_embed->autoembed( $normal_content_2 ) ) . '</div>';
					break;
				case 'saved_rows':
					$output = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $settings['section_saved_rows_2'] );
					break;
				case 'saved_container':
					$output = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $settings['section_saved_container_2'] );
					break;
				case 'saved_page_templates':
					$output = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $settings['section_saved_pages_2'] );
					break;
				default:
					break;
			}
		}

		return $output;
	}

	/**
	 * Registers all controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_general_content_controls() {
		// Rbs heading section starts.
		$this->start_controls_section(
			'rbs_section_content_1',
			array(
				'label' => __( 'Content 1', 'uael' ),
			)
		);

		// Rbs section 1 heading text.
		$this->add_control(
			'rbs_section_heading_1',
			array(
				'label'   => __( 'Heading', 'uael' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
				'default' => __( 'Heading 1', 'uael' ),
			)
		);

		// Rbs content section 1.
		$this->add_control(
			'rbs_select_section_1',
			array(
				'label'   => __( 'Section', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'content',
				'options' => $this->get_content_type(),
			)
		);

		// Rbs content section 1 - content.
		$this->add_control(
			'section_content_1',
			array(
				'label'      => __( 'Description', 'uael' ),
				'type'       => Controls_Manager::WYSIWYG,
				'default'    => __( 'This is your first content. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.​ Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'uael' ),
				'rows'       => 10,
				'show_label' => false,
				'dynamic'    => array(
					'active' => true,
				),
				'condition'  => array(
					'rbs_select_section_1' => 'content',
				),
			)
		);

		// Rbs content section 1 - saved rows.
		$this->add_control(
			'section_saved_rows_1',
			array(
				'label'     => __( 'Select Section', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => UAEL_Helper::get_saved_data( 'section' ),
				'default'   => '-1',
				'condition' => array(
					'rbs_select_section_1' => 'saved_rows',
				),
			)
		);

		// Rbs content section 1 - saved rows.
		$this->add_control(
			'section_saved_container_1',
			array(
				'label'     => __( 'Select Container', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => UAEL_Helper::get_saved_data( 'container' ),
				'default'   => '-1',
				'condition' => array(
					'rbs_select_section_1' => 'saved_container',
				),
			)
		);

		// Rbs content section 1 - saved pages.
		$this->add_control(
			'section_saved_pages_1',
			array(
				'label'     => __( 'Select Page', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => UAEL_Helper::get_saved_data( 'page' ),
				'default'   => '-1',
				'condition' => array(
					'rbs_select_section_1' => 'saved_page_templates',
				),
			)
		);

		// Rbs heading section ends.
		$this->end_controls_section();

		// Rbs content sections starts.
		$this->start_controls_section(
			'rbs_sections_content_2',
			array(
				'label' => __( 'Content 2', 'uael' ),
			)
		);

		// Rbs section 2 heading text.
		$this->add_control(
			'rbs_section_heading_2',
			array(
				'label'   => __( 'Heading', 'uael' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
				'default' => __( 'Heading 2', 'uael' ),
			)
		);

		// Rbs content section 2.
		$this->add_control(
			'rbs_select_section_2',
			array(
				'label'   => __( 'Section', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'content',
				'options' => $this->get_content_type(),
			)
		);

		// Rbs content section 2 - content.
		$this->add_control(
			'section_content_2',
			array(
				'label'      => __( 'Description', 'uael' ),
				'type'       => Controls_Manager::WYSIWYG,
				'default'    => __( 'This is your second content. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.​ Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'uael' ),
				'rows'       => 10,
				'show_label' => false,
				'dynamic'    => array(
					'active' => true,
				),
				'condition'  => array(
					'rbs_select_section_2' => 'content',
				),
			)
		);

		// Rbs content section 2 - saved rows.
		$this->add_control(
			'section_saved_rows_2',
			array(
				'label'     => __( 'Select Section', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => UAEL_Helper::get_saved_data( 'section' ),
				'default'   => '-1',
				'condition' => array(
					'rbs_select_section_2' => 'saved_rows',
				),
			)
		);

		// Rbs content section 2 - saved rows.
		$this->add_control(
			'section_saved_container_2',
			array(
				'label'     => __( 'Select Container', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => UAEL_Helper::get_saved_data( 'container' ),
				'default'   => '-1',
				'condition' => array(
					'rbs_select_section_2' => 'saved_container',
				),
			)
		);

		// Rbs content section 2 - saved pages.
		$this->add_control(
			'section_saved_pages_2',
			array(
				'label'     => __( 'Select Page', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => UAEL_Helper::get_saved_data( 'page' ),
				'default'   => '-1',
				'condition' => array(
					'rbs_select_section_2' => 'saved_page_templates',
				),
			)
		);

		// Rbs heading section ends.
		$this->end_controls_section();

		// Switch style starts.
		$this->start_controls_section(
			'rbs_switch_style',
			array(
				'label' => __( 'Switcher', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		// Rbs default switch mode.
		$this->add_control(
			'rbs_default_switch',
			array(
				'label'        => __( 'Default Display', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'off',
				'return_value' => 'on',
				'options'      => array(
					'off' => 'Content 1',
					'on'  => 'Content 2',
				),
				'separator'    => 'before',
			)
		);

		// Rbs select switch.
		$this->add_control(
			'rbs_select_switch',
			array(
				'label'   => __( 'Switch Style', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'round_1',
				'options' => $this->get_switch_type(),
			)
		);

		// Switch - Off color.
		$this->add_control(
			'rbs_switch_color_off',
			array(
				'label'     => __( 'Color 1', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_ACCENT,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-rbs-slider' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .uael-toggle input[type="checkbox"] + label:before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .uael-toggle input[type="checkbox"] + label:after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .uael-label-box-active .uael-label-box-switch' => 'background: {{VALUE}};',

				),
			)
		);

		// Switch - On color.
		$this->add_control(
			'rbs_switch_color_on',
			array(
				'label'     => __( 'Color 2', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),

				'selectors' => array(
					'{{WRAPPER}} .uael-rbs-switch:checked + .uael-rbs-slider' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .uael-rbs-switch:focus + .uael-rbs-slider'     => '-webkit-box-shadow: 0 0 1px {{VALUE}};box-shadow: 0 0 1px {{VALUE}};',
					'{{WRAPPER}} .uael-toggle input[type="checkbox"]:checked + label:before'     => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .uael-toggle input[type="checkbox"]:checked + label:after'     => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .uael-label-box-inactive .uael-label-box-switch' => 'background: {{VALUE}};',
				),
			)
		);

		// Switch - Controller Color.
		$this->add_control(
			'rbs_switch_controller',
			array(
				'label'     => __( 'Controller Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_ACCENT,
				),
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .uael-rbs-slider:before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .uael-toggle input[type="checkbox"] + label:after' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} span.uael-label-box-switch' => 'color: {{VALUE}};',
				),
			)
		);

		// Switch size.
		$this->add_responsive_control(
			'rds_switch_size',
			array(
				'label'              => __( 'Switch Size', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'size' => 15,
				),
				'range'              => array(
					'px' => array(
						'min'  => 10,
						'max'  => 35,
						'step' => 1,
					),
				),
				'selectors'          => array(
					// General.
					'{{WRAPPER}} .uael-main-btn' => 'font-size: {{SIZE}}px;',
				),
				'frontend_available' => true,
			)
		);

		// Switch style ends.
		$this->end_controls_section();

		// Section heading style starts.
		$this->start_controls_section(
			'section_style_heading',
			array(
				'label' => __( 'Headings', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		// Heading 1 - heading.
		$this->add_control(
			'section_heading_1_style',
			array(
				'label'     => __( 'Heading 1', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		// Heading 1 - color.
		$this->add_control(
			'section_heading_1_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-rbs-head-1' => 'color: {{VALUE}};',
				),
				'separator' => 'none',
			)
		);

		// Heading 1 - typography.
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'section_heading_1_typo',
				'selector' => '{{WRAPPER}} .uael-rbs-head-1',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
			)
		);

		// Heading 2 - heading.
		$this->add_control(
			'section_heading_2_style',
			array(
				'label'     => __( 'Heading 2', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		// Heading 2 - color.
		$this->add_control(
			'section_heading_2_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-rbs-head-2' => 'color: {{VALUE}};',
				),
				'separator' => 'none',
			)
		);

		// Heading 2 - typography.
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'section_heading_2_typo',
				'selector' => '{{WRAPPER}} .uael-rbs-head-2',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
			)
		);

		$this->add_control(
			'rbs_header_size',
			array(
				'label'     => __( 'HTML Tag', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				),
				'default'   => 'h5',
				'separator' => 'before',
			)
		);

		// heading alignment content Alignment.
		$this->add_responsive_control(
			'rds_heading_alignment',
			array(
				'label'              => __( 'Alignment', 'uael' ),
				'type'               => Controls_Manager::CHOOSE,
				'default'            => 'center',
				'options'            => array(
					'flex-start' => array(
						'title' => __( 'Left', 'uael' ),
						'icon'  => 'fa fa-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'uael' ),
						'icon'  => 'fa fa-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'uael' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-rbs-toggle' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .uael-ct-desktop-stack--yes .uael-rbs-toggle' => 'align-items: {{VALUE}};',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'heading_layout',
			array(
				'label'        => __( 'Layout', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Stack', 'uael' ),
				'label_off'    => __( 'Inline', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		$this->add_control(
			'heading_stack_on',
			array(
				'label'        => __( 'Stack on', 'uael' ),
				'description'  => __( 'Choose on what breakpoint the heading will stack.', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'mobile',
				'options'      => array(
					'none'   => __( 'None', 'uael' ),
					'tablet' => __( 'Tablet (1023px >)', 'uael' ),
					'mobile' => __( 'Mobile (767px >)', 'uael' ),
				),
				'condition'    => array(
					'heading_layout!' => 'yes',
				),
				'prefix_class' => 'uael-ct-stack--',
			)
		);

		$this->add_control(
			'rbs_advance_setting',
			array(
				'label'     => __( 'Advanced', 'uael' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'OFF', 'uael' ),
				'label_on'  => __( 'ON', 'uael' ),
				'default'   => 'no',
				'return'    => 'yes',
			)
		);

		// Heading background color.
		$this->add_control(
			'section_heading_bg_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-rbs-toggle' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'rbs_advance_setting' => 'yes',
				),
			)
		);

		// Heading - Border.
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'heading_border',
				'label'     => __( 'Border', 'uael' ),
				'selector'  => '{{WRAPPER}} .uael-rbs-toggle',
				'condition' => array(
					'rbs_advance_setting' => 'yes',
				),
			)
		);

		$this->add_control(
			'heading_border_radius',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-rbs-toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'rbs_advance_setting' => 'yes',
				),
			)
		);

		// Overall Heading - padding.
		$this->add_responsive_control(
			'rbs_heading_padding',
			array(
				'label'              => __( 'Padding', 'uael' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px' ),
				'selectors'          => array(
					'{{WRAPPER}} .uael-rbs-toggle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'          => array(
					'rbs_advance_setting' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		// Section heading style ends.
		$this->end_controls_section();

		// Content style starts.
		$this->start_controls_section(
			'rbs_content_style',
			array(
				'label' => __( 'Content', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		// Content 1 - heading.
		$this->add_control(
			'section_content_1_style',
			array(
				'label'     => __( 'Content 1', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'rbs_select_section_1' => 'content',
				),
			)
		);

		// Content 1 Color.
		$this->add_control(
			'section_content_1_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'condition' => array(
					'rbs_select_section_1' => 'content',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-rbs-content-1.uael-rbs-section-1' => 'color: {{VALUE}};',
				),
			)
		);

		// Content 1 Typo.
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'section_content_1_typo',
				'selector'  => '{{WRAPPER}} .uael-rbs-content-1.uael-rbs-section-1',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'condition' => array(
					'rbs_select_section_1' => 'content',
				),
				'separator' => 'after',
			)
		);

		// Content 2 - heading.
		$this->add_control(
			'section_content_2_style',
			array(
				'label'     => __( 'Content 2', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'rbs_select_section_2' => 'content',
				),
			)
		);

		// Content 2 Color.
		$this->add_control(
			'section_content_2_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'condition' => array(
					'rbs_select_section_2' => 'content',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-rbs-content-2.uael-rbs-section-2' => 'color: {{VALUE}};',
				),
			)
		);

		// Content 2 Typo.
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'section_content_2_typo',
				'selector'  => '{{WRAPPER}} .uael-rbs-content-2.uael-rbs-section-2',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'condition' => array(
					'rbs_select_section_2' => 'content',
				),
				'separator' => 'after',
			)
		);

		$this->add_control(
			'rbs_content_advance_setting',
			array(
				'label'     => __( 'Advanced', 'uael' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'OFF', 'uael' ),
				'label_on'  => __( 'ON', 'uael' ),
				'default'   => 'no',
				'return'    => 'yes',
			)
		);

		// Content background color.
		$this->add_control(
			'rbs_content_bg_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-rbs-toggle-sections'     => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'rbs_content_advance_setting' => 'yes',
				),
			)
		);

		// Content - Border.
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'content_border',
				'label'     => __( 'Border', 'uael' ),
				'selector'  => '{{WRAPPER}} .uael-rbs-toggle-sections',
				'condition' => array(
					'rbs_content_advance_setting' => 'yes',
				),
			)
		);

		$this->add_control(
			'content_border_radius',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-rbs-toggle-sections' => 'overflow: hidden;border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'rbs_content_advance_setting' => 'yes',
				),
			)
		);

		// Content padding.
		$this->add_responsive_control(
			'rbs_content_padding',
			array(
				'label'              => __( 'Padding', 'uael' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px' ),
				'selectors'          => array(
					'{{WRAPPER}} .uael-rbs-toggle-sections' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'          => array(
					'rbs_content_advance_setting' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		// Content style ends.
		$this->end_controls_section();

		// Spacing style starts.
		$this->start_controls_section(
			'rbs_switch_spacing',
			array(
				'label' => __( 'Spacing', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		// Spacing Headings and toggle button.
		$this->add_responsive_control(
			'rds_button_headings_spacing',
			array(
				'label'              => __( 'Button & Headings', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'range'              => array(
					'%' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'            => array(
					'size' => 5,
				),
				'frontend_available' => true,
				'selectors'          => array(
					// General.
					'{{WRAPPER}} .uael-ct-desktop-stack--no .uael-sec-1'         => 'margin-right: {{SIZE}}%;',
					'{{WRAPPER}} .uael-ct-desktop-stack--no .uael-sec-2'         => 'margin-left: {{SIZE}}%;',

					'.rtl {{WRAPPER}} .uael-ct-desktop-stack--no .uael-sec-1'         => 'margin-left: {{SIZE}}%; margin-right: 0%;',
					'.rtl {{WRAPPER}} .uael-ct-desktop-stack--no .uael-sec-2'         => 'margin-right: {{SIZE}}%; margin-left: 0%',

					'{{WRAPPER}} .uael-ct-desktop-stack--yes .uael-sec-1'         => 'margin-bottom: {{SIZE}}%;',
					'{{WRAPPER}} .uael-ct-desktop-stack--yes .uael-sec-2'         => 'margin-top: {{SIZE}}%;',

					'(tablet){{WRAPPER}}.uael-ct-stack--tablet .uael-ct-desktop-stack--no .uael-sec-1'         => 'margin-bottom: {{SIZE}}%;margin-right: 0px;',
					'(tablet){{WRAPPER}}.uael-ct-stack--tablet .uael-ct-desktop-stack--no .uael-sec-2'         => 'margin-top: {{SIZE}}%;margin-left: 0px;',

					'(tablet){{WRAPPER}}.uael-ct-stack--tablet .uael-ct-desktop-stack--no .uael-rbs-toggle'         => 'flex-direction: column;',

					'(mobile){{WRAPPER}}.uael-ct-stack--mobile .uael-ct-desktop-stack--no .uael-sec-1'         => 'margin-bottom: {{SIZE}}%;margin-right: 0px;',
					'(mobile){{WRAPPER}}.uael-ct-stack--mobile .uael-ct-desktop-stack--no .uael-sec-2'         => 'margin-top: {{SIZE}}%;margin-left: 0px;',

					'(mobile){{WRAPPER}}.uael-ct-stack--mobile .uael-ct-desktop-stack--no .uael-rbs-toggle'         => 'flex-direction: column;',
				),
			)
		);

		// Spacing Headings and content.
		$this->add_responsive_control(
			'rds_headings_content_spacing',
			array(
				'label'              => __( 'Content & Headings', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'size' => 10,
				),
				'range'              => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors'          => array(
					// General.
					'{{WRAPPER}} .uael-rbs-toggle' => 'margin-bottom: {{SIZE}}px;',
				),
				'frontend_available' => true,
			)
		);

		// Spacing style ends.
		$this->end_controls_section();
	}

	/**
	 * Helpful Information.
	 *
	 * @since 1.1.0
	 * @access protected
	 */
	protected function register_helpful_information() {

		$help_link_2 = UAEL_DOMAIN . 'docs/content-toggle-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';
		$help_link_3 = UAEL_DOMAIN . 'docs/filters-actions-for-content-toggle-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';
		$help_link_4 = UAEL_DOMAIN . 'docs/open-specific-section-from-a-remote-link/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';

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
					'raw'             => sprintf( __( '%1$s Getting started video » %2$s', 'uael' ), '<a href="https://www.youtube.com/watch?v=kaGfSpGFcnw&list=PL1kzJGWGPrW_7HabOZHb6z88t_S8r-xAc&index=1" target="_blank" rel="noopener">', '</a>' ),
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

			$this->add_control(
				'help_doc_3',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Filters/Actions » %2$s', 'uael' ), '<a href=' . $help_link_3 . ' target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_4',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s How to open a specific content from a remote link? » %2$s', 'uael' ), '<a href=' . $help_link_4 . ' target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->end_controls_section();
		}
	}

	/**
	 * Render content type list.
	 *
	 * @since 0.0.1
	 * @return array Array of content type
	 * @access public
	 */
	public function get_content_type() {

		$content_type = array(
			'content'              => __( 'Content', 'uael' ),
			'saved_rows'           => __( 'Saved Section', 'uael' ),
			'saved_container'      => __( 'Saved Container', 'uael' ),
			'saved_page_templates' => __( 'Saved Page', 'uael' ),
		);

		return $content_type;
	}

	/**
	 * Render content type list.
	 *
	 * @since 0.0.1
	 * @return array Array of content type
	 * @access public
	 */
	public function get_switch_type() {

		$switch_type = array(
			'round_1'   => __( 'Round 1', 'uael' ),
			'round_2'   => __( 'Round 2', 'uael' ),
			'rectangle' => __( 'Rectangle', 'uael' ),
			'label_box' => __( 'Label Box', 'uael' ),
		);

		return $switch_type;
	}

	/**
	 * Render Radio Button output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render() {

		$settings  = $this->get_settings();
		$node_id   = $this->get_id();
		$is_editor = \Elementor\Plugin::instance()->editor->is_edit_mode();
		ob_start();
		include UAEL_MODULES_DIR . 'content-toggle/widgets/template.php';
		$html = ob_get_clean();
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Render Content Toggle output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.22.1
	 * @access protected
	 */
	protected function content_template() {}
}
