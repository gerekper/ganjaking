<?php
/**
 * UAEL Dual Color Heading.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Headings\Widgets;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Background;
use Elementor\Utils;

// UltimateElementor Classes.
use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Dual_Heading.
 */
class Dual_Heading extends Common_Widget {

	/**
	 * Retrieve Dual Color Heading Widget name.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'Dual_Heading' );
	}

	/**
	 * Retrieve Dual Color Heading Widget title.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Dual_Heading' );
	}

	/**
	 * Retrieve Dual Color Heading Widget icon.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Dual_Heading' );
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
		return parent::get_widget_keywords( 'Dual_Heading' );
	}

	/**
	 * Register Dual Color Heading controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {
		$this->register_presets_control( 'Dual_Heading', $this );

		$this->register_heading_content_controls();
		$this->register_general_content_controls();
		$this->register_style_content_controls();
		$this->register_bg_text_controls();
		$this->register_helpful_information();
	}

	/**
	 * Register Dual Color Heading Text Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_heading_content_controls() {
		$this->start_controls_section(
			'section_headings_field',
			array(
				'label' => __( 'Heading Text', 'uael' ),
			)
		);
		$this->add_control(
			'before_heading_text',
			array(

				'label'    => __( 'Before Text', 'uael' ),
				'type'     => Controls_Manager::TEXT,
				'selector' => '{{WRAPPER}} .uael-heading-text',
				'dynamic'  => array(
					'active' => true,
				),
				'default'  => __( 'Be Focused.', 'uael' ),
			)
		);
		$this->add_control(
			'second_heading_text',
			array(
				'label'    => __( 'Highlighted Text', 'uael' ),
				'type'     => Controls_Manager::TEXT,
				'selector' => '{{WRAPPER}} .uael-highlight-text',
				'dynamic'  => array(
					'active' => true,
				),
				'default'  => __( 'Be Determined.', 'uael' ),
			)
		);
		$this->add_control(
			'after_heading_text',
			array(
				'label'    => __( 'After Text', 'uael' ),
				'type'     => Controls_Manager::TEXT,
				'dynamic'  => array(
					'active' => true,
				),
				'selector' => '{{WRAPPER}} .uael-dual-heading-text',
			)
		);

		$this->add_control(
			'show_bg_text',
			array(
				'label'        => __( 'Background Text', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_off'    => __( 'No', 'uael' ),
				'label_on'     => __( 'Yes', 'uael' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'bg_text',
			array(

				'label'     => __( 'Background Text', 'uael' ),
				'type'      => Controls_Manager::TEXT,
				'selector'  => '{{WRAPPER}} .uael-heading-text',
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'show_bg_text' => 'yes',
				),
			)
		);
		$this->add_control(
			'heading_link',
			array(
				'label'       => __( 'Link', 'uael' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'uael' ),
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => array(
					'url' => '',
				),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Register Dual Color Heading General Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_general_content_controls() {

		$this->start_controls_section(
			'section_style_field',
			array(
				'label' => __( 'General', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'dual_tag_selection',
			array(
				'label'   => __( 'Select Tag', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'h1'   => __( 'H1', 'uael' ),
					'h2'   => __( 'H2', 'uael' ),
					'h3'   => __( 'H3', 'uael' ),
					'h4'   => __( 'H4', 'uael' ),
					'h5'   => __( 'H5', 'uael' ),
					'h6'   => __( 'H6', 'uael' ),
					'div'  => __( 'div', 'uael' ),
					'span' => __( 'span', 'uael' ),
					'p'    => __( 'p', 'uael' ),
				),
				'default' => 'h3',
			)
		);

		$this->add_responsive_control(
			'dual_color_alignment',
			array(
				'label'        => __( 'Alignment', 'uael' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => array(
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
				'default'      => 'left',
				'prefix_class' => 'uael%s-dual-heading-align-',
				'selectors'    => array(
					'{{WRAPPER}} .uael-dual-color-heading' => 'text-align: {{VALUE}};',
				),
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
				'label'       => __( 'Responsive Support', 'uael' ),
				'description' => __( 'Choose on what breakpoint the heading will stack.', 'uael' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'none',
				'options'     => array(
					'none'   => __( 'No', 'uael' ),
					'tablet' => __( 'For Tablet & Mobile', 'uael' ),
					'mobile' => __( 'For Mobile Only', 'uael' ),
				),
				'condition'   => array(
					'heading_layout!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'heading_margin',
			array(
				'label'      => __( 'Spacing Between Headings', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'default'    => array(
					'size' => '0',
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-before-heading' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-after-heading'  => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-stack-desktop-yes .uael-before-heading' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-right: 0px; display: inline-block;',
					'{{WRAPPER}} .uael-stack-desktop-yes .uael-after-heading' => 'margin-top: {{SIZE}}{{UNIT}}; margin-left: 0px; display: inline-block;',
					'(tablet){{WRAPPER}} .uael-heading-stack-tablet .uael-before-heading ' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-right: 0px; display: inline-block;',
					'(tablet){{WRAPPER}} .uael-heading-stack-tablet .uael-after-heading ' => 'margin-top: {{SIZE}}{{UNIT}}; margin-left: 0px; display: inline-block;',
					'(mobile){{WRAPPER}} .uael-heading-stack-mobile .uael-before-heading ' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-right: 0px; display: inline-block;',
					'(mobile){{WRAPPER}} .uael-heading-stack-mobile .uael-after-heading ' => 'margin-top: {{SIZE}}{{UNIT}}; margin-left: 0px; display: inline-block;',
				),
			)
		);
		$this->end_controls_section();

	}

	/**
	 * Register Dual Color Heading Style Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_style_content_controls() {
		$this->start_controls_section(
			'heading_style_fields',
			array(
				'label' => __( 'Heading Style', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'tabs_heading' );

		$this->start_controls_tab(
			'tab_heading',
			array(
				'label' => __( 'Normal', 'uael' ),
			)
		);

		$this->add_control(
			'first_heading_color',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-dual-heading-text' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'before_heading_text_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .uael-dual-heading-text',
			)
		);
		$this->add_control(
			'heading_adv_options',
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
				'name'      => 'heading_bg_color',
				'label'     => __( 'Background Color', 'uael' ),
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}} .uael-dual-heading-text',
				'condition' => array(
					'heading_adv_options' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'heading_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-dual-heading-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'default'    => array(
					'top'    => 0,
					'bottom' => 0,
					'left'   => 0,
					'right'  => 0,
					'unit'   => 'px',
				),
				'condition'  => array(
					'heading_adv_options' => 'yes',
				),

			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'heading_text_border',
				'label'       => __( 'Border', 'uael' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .uael-dual-heading-text',
				'condition'   => array(
					'heading_adv_options' => 'yes',
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
					'{{WRAPPER}} .uael-dual-heading-text, {{WRAPPER}} .uael-dual-heading-text.uael-highlight-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'heading_adv_options' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'dual_text_shadow',
				'selector'  => '{{WRAPPER}} .uael-dual-heading-text',
				'condition' => array(
					'heading_adv_options' => 'yes',
				),
			)
		);

		$this->add_control(
			'normal_heading_bg',
			array(
				'label'        => __( 'Fill Background', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'separator'    => 'before',
				'condition'    => array(
					'heading_adv_options' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'heading_bg_color_fill',
				'label'     => __( 'Background Color', 'uael' ),
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}} .uael-dual-heading-fill-yes .uael-first-text,
				{{WRAPPER}} .uael-dual-heading-fill-yes .uael-third-text',
				'condition' => array(
					'heading_adv_options' => 'yes',
					'normal_heading_bg'   => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_highlight',
			array(
				'label' => __( 'Highlight', 'uael' ),
			)
		);

		$this->add_control(
			'second_heading_color',
			array(
				'label'     => __( 'Highlight Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-dual-heading-text.uael-highlight-text' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'second_heading_text_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .uael-dual-heading-text.uael-highlight-text',
			)
		);
		$this->add_control(
			'highlight_adv_options',
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
				'name'      => 'highlight_bg_color',
				'label'     => __( 'Background Color', 'uael' ),
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}} .uael-dual-heading-text.uael-highlight-text',
				'condition' => array(
					'highlight_adv_options' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'heading_highlight_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'default'    => array(
					'top'    => 0,
					'bottom' => 0,
					'left'   => 0,
					'right'  => 0,
					'unit'   => 'px',
				),
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-dual-heading-text.uael-highlight-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'highlight_adv_options' => 'yes',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'highlight_text_border',
				'label'       => __( 'Border', 'uael' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .uael-dual-heading-text.uael-highlight-text',
				'condition'   => array(
					'highlight_adv_options' => 'yes',
				),
			)
		);
		$this->add_control(
			'heading_highlight_radius',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-dual-heading-text.uael-highlight-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'highlight_adv_options' => 'yes',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'dual_highlight_shadow',
				'selector'  => '{{WRAPPER}} .uael-dual-heading-text.uael-highlight-text',
				'condition' => array(
					'highlight_adv_options' => 'yes',
				),
			)
		);

		$this->add_control(
			'highlight_heading_bg',
			array(
				'label'        => __( 'Fill Background', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'separator'    => 'before',
				'condition'    => array(
					'highlight_adv_options' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'highlight_bg_color_fill',
				'label'     => __( 'Background Color', 'uael' ),
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}} .uael-dual-heading-fill-yes.uael-dual-heading-text.uael-highlight-text',
				'condition' => array(
					'highlight_adv_options' => 'yes',
					'highlight_heading_bg'  => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register BG Text controls.
	 *
	 * @since 1.27.1
	 * @access protected
	 */
	protected function register_bg_text_controls() {

		$this->start_controls_section(
			'bg_text_style',
			array(
				'label'     => __( 'Background Text', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'bg_text!'     => '',
					'show_bg_text' => 'yes',
				),
			)
		);

			$this->add_control(
				'bg_text_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .uael-dual-color-heading:before' => 'color: {{VALUE}}',
					),
					'default'   => '#352B2B70',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'bg_text_typography',
					'selector' => '{{WRAPPER}} .uael-dual-color-heading:before',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
				)
			);

			$this->add_control(
				'background_offset_toggle',
				array(
					'label'        => __( 'Offset', 'uael' ),
					'type'         => Controls_Manager::POPOVER_TOGGLE,
					'label_off'    => __( 'None', 'uael' ),
					'label_on'     => __( 'Custom', 'uael' ),
					'return_value' => 'yes',
				)
			);

			$this->start_popover();

				$this->add_responsive_control(
					'bg_text_horizontal_position',
					array(
						'label'      => __( 'Horizontal Position', 'uael' ),
						'type'       => Controls_Manager::SLIDER,
						'size_units' => array( 'px', '%' ),
						'default'    => array(
							'unit' => '%',
						),
						'range'      => array(
							'%' => array(
								'min' => -100,
								'max' => 100,
							),
						),
						'condition'  => array(
							'background_offset_toggle' => 'yes',
						),
						'selectors'  => array(
							'{{WRAPPER}} .uael-dual-color-heading:before' => 'left: {{SIZE}}{{UNIT}};right: unset;',
							'body.rtl {{WRAPPER}} .uael-dual-color-heading:before' => 'right: {{SIZE}}{{UNIT}};left: unset;',
						),
					)
				);

				$this->add_responsive_control(
					'bg_text_vertical_position',
					array(
						'label'      => __( 'Vertical Position', 'uael' ),
						'type'       => Controls_Manager::SLIDER,
						'size_units' => array( 'px', '%' ),
						'default'    => array(
							'unit' => '%',
						),
						'range'      => array(
							'%' => array(
								'min' => -100,
								'max' => 200,
							),
						),
						'condition'  => array(
							'background_offset_toggle' => 'yes',
						),
						'selectors'  => array(
							'{{WRAPPER}} .uael-dual-color-heading:before' => 'top: {{SIZE}}{{UNIT}};',
						),
					)
				);

			$this->end_popover();

		$this->end_controls_section();
	}

	/**
	 * Helpful Information.
	 *
	 * @since 1.3.1
	 * @access protected
	 */
	protected function register_helpful_information() {

		$help_link_2 = UAEL_DOMAIN . 'docs/dual-color-heading-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';

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
					'raw'             => sprintf( __( '%1$s Getting started video » %2$s', 'uael' ), '<a href="https://www.youtube.com/watch?v=WmrSMl5g3ac&list=PL1kzJGWGPrW_7HabOZHb6z88t_S8r-xAc&index=16" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_2',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Getting started article » %2$s', 'uael' ), '<a href=' . $help_link_2 . 'target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->end_controls_section();
		}
	}

	/**
	 * Render Dual Color Heading output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render() {
		$settings     = $this->get_settings_for_display();
		$first_title  = $settings['before_heading_text'];
		$second_title = $settings['second_heading_text'];
		$third_title  = $settings['after_heading_text'];
		ob_start();
		?>
		<?php
		$link = '';
		if ( ! empty( $settings['heading_link']['url'] ) ) {

			$this->add_link_attributes( 'url', $settings['heading_link'] );

			$link = $this->get_render_attribute_string( 'url' );
		}

		$this->add_render_attribute( 'uael-dual-heading', 'class', 'uael-module-content uael-dual-color-heading' );
		if ( 'yes' === $settings['show_bg_text'] && ! empty( $settings['bg_text'] ) ) {
			$this->add_render_attribute( 'uael-dual-heading', 'data-bg_text', $settings['bg_text'] );
		}

		if ( 'yes' === $settings['normal_heading_bg'] ) {
			$this->add_render_attribute( 'uael-dual-heading', 'class', 'uael-dual-heading-fill-yes' );
		}

		if ( 'yes' === $settings['highlight_heading_bg'] ) {
			$this->add_render_attribute( 'uael-dual-heading-highlight-text', 'class', 'uael-dual-heading-fill-yes' );
		}

		$this->add_render_attribute( 'uael-dual-heading-highlight-text', 'class', 'elementor-inline-editing uael-dual-heading-text uael-highlight-text' );

		if ( 'yes' === $settings['heading_layout'] ) {
			$this->add_render_attribute( 'uael-dual-heading', 'class', 'uael-stack-desktop-yes' );
		}

		$this->add_render_attribute( 'uael-dual-heading', 'class', 'uael-heading-stack-' . $settings['heading_stack_on'] );

		$dual_html_tag = UAEL_Helper::validate_html_tag( $settings['dual_tag_selection'] );
		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael-dual-heading' ) ); ?>>
			<<?php echo esc_attr( $dual_html_tag ); ?>>
				<?php if ( ! empty( $settings['heading_link']['url'] ) ) { ?>
					<a <?php echo $link; ?> > <?php //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php } ?>
						<?php
						// Ignore the PHPCS warning about constant declaration.
						// @codingStandardsIgnoreStart
						?>
						<span class="uael-before-heading"><span class="elementor-inline-editing uael-dual-heading-text uael-first-text" data-elementor-setting-key="before_heading_text" data-elementor-inline-editing-toolbar="basic"><?php echo $this->get_settings_for_display( 'before_heading_text'); ?></span></span><span class="uael-adv-heading-stack"><span <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael-dual-heading-highlight-text' ) ); ?> data-elementor-setting-key="second_heading_text" data-elementor-inline-editing-toolbar="basic"><?php echo $this->get_settings_for_display( 'second_heading_text'); ?></span></span><?php if ( ! empty( $settings['after_heading_text'] ) ) { ?><span class="uael-after-heading"><span class="elementor-inline-editing uael-dual-heading-text uael-third-text" data-elementor-setting-key="after_heading_text" data-elementor-inline-editing-toolbar="basic"><?php echo $this->get_settings_for_display( 'after_heading_text'); ?></span></span><?php } ?>
						<?php // @codingStandardsIgnoreEnd ?>
				<?php if ( ! empty( $settings['heading_link']['url'] ) ) { ?>
					</a>
				<?php } ?>
			</<?php echo esc_attr( $dual_html_tag ); ?>>
		</div>
		<?php
		$html = ob_get_clean();
		echo $html; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Render Dual Color Heading widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.22.1
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
			view.addRenderAttribute( 'uael-dual-heading', 'class', 'uael-module-content uael-dual-color-heading' );
			if ( 'yes' == settings.show_bg_text && '' != settings.bg_text ) {
				view.addRenderAttribute( 'uael-dual-heading', 'data-bg_text', settings.bg_text );
			}

			if ( 'yes' === settings.normal_heading_bg ){
				view.addRenderAttribute( 'uael-dual-heading', 'class', 'uael-dual-heading-fill-yes' );
			}

			if ( 'yes' === settings.highlight_heading_bg ){
				view.addRenderAttribute( 'uael-dual-heading-highlight-text', 'class', 'uael-dual-heading-fill-yes' );
			}

			view.addRenderAttribute( 'uael-dual-heading-highlight-text', 'class', 'elementor-inline-editing uael-dual-heading-text uael-highlight-text' );

			if( 'yes' == settings.heading_layout ){
				view.addRenderAttribute( 'uael-dual-heading', 'class', 'uael-stack-desktop-yes' );
			}

			view.addRenderAttribute( 'uael-dual-heading', 'class', 'uael-heading-stack-' + settings.heading_stack_on );

			var dual_html_tag = settings.dual_tag_selection;
			if ( typeof elementor.helpers.validateHTMLTag === "function" ) {
				dual_html_tag = elementor.helpers.validateHTMLTag( dual_html_tag );
			} else if( UAEWidgetsData.allowed_tags ) {
				dual_html_tag = UAEWidgetsData.allowed_tags.includes( dual_html_tag.toLowerCase() ) ? dual_html_tag : 'div';
			}


		#>
		<div {{{ view.getRenderAttributeString( 'uael-dual-heading') }}} > <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
			<{{ dual_html_tag }}>
				<# if ( '' != settings.heading_link.url ) { #>
					<a href= {{ settings.heading_link.url }}>
				<# } #>
				<span class="uael-before-heading"><span class="elementor-inline-editing uael-dual-heading-text uael-first-text" data-elementor-setting-key="before_heading_text" data-elementor-inline-editing-toolbar="basic">{{ settings.before_heading_text }}</span></span><span class="uael-adv-heading-stack"><span {{{ view.getRenderAttributeString( 'uael-dual-heading-highlight-text') }}} data-elementor-setting-key="second_heading_text" data-elementor-inline-editing-toolbar="basic">{{ settings.second_heading_text }}</span></span><# if ( '' != settings.after_heading_text ) { #><span class="uael-after-heading"><span class="elementor-inline-editing uael-dual-heading-text uael-third-text" data-elementor-setting-key="after_heading_text" data-elementor-inline-editing-toolbar="basic">{{ settings.after_heading_text }}</span></span> <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
				<# } #>
				<# if ( '' !== settings.heading_link.url ) { #>
					</a>
				<# } #>
			</{{ dual_html_tag }}>
		</div>
		<?php
	}

}

