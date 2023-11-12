<?php
/**
 * UAEL Business Hours.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\BusinessHours\Widgets;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Repeater;

// UltimateElementor Classes.
use UltimateElementor\Base\Common_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Business_Hours.
 */
class Business_Hours extends Common_Widget {

	/**
	 * Retrieve Business Hours Widget name.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'Business_Hours' );
	}

	/**
	 * Retrieve Business Hours Widget title.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Business_Hours' );
	}

	/**
	 * Retrieve Business Hours Widget icon.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Business_Hours' );
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
		return parent::get_widget_keywords( 'Business_Hours' );
	}

	/**
	 * Register Business Hours controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {
		$this->register_presets_control( 'Business_Hours', $this );

		$this->register_general_content_controls();
		$this->register_helpful_information();
	}

	/**
	 * Register Business Hours General Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_general_content_controls() {
		/*
		 * Business Days & Timing Section Starts From here.
		 */
		$this->start_controls_section(
			'section_business_days',
			array(
				'label' => __( 'Business Days & Timings', 'uael' ),
			)
		);

		// Creating object of repeater.
		$repeater = new Repeater();

		$repeater->add_control(
			'enter_day',
			array(
				'label'       => __( 'Enter Day', 'uael' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => 'Monday',
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'enter_time',
			array(
				'label'       => __( 'Enter Time', 'uael' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '8:30 AM - 7:30 PM',
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		// Heading Current Day Styling.
		$repeater->add_control(
			'current_styling_heading',
			array(
				'label'     => __( 'Styling', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		// Highlight This (Current Day) Switcher.
		$repeater->add_control(
			'highlight_this',
			array(
				'label'        => __( 'Style This Day', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'YES', 'uael' ),
				'label_off'    => __( 'NO', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'separator'    => 'before',
			)
		);

		// Single Business Day Color.
		$repeater->add_control(
			'single_business_day_color',
			array(
				'label'     => __( 'Day Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#db6159',
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .uael-business-day-highlight' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'highlight_this' => 'yes',
				),
				'separator' => 'before',
			)
		);

		// Single Business Timing Color.
		$repeater->add_control(
			'single_business_timing_color',
			array(
				'label'     => __( 'Time Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#db6159',
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .uael-business-timing-highlight' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'highlight_this' => 'yes',
				),
				'separator' => 'before',
			)
		);

		// Single Business Background Color.
		$repeater->add_control(
			'single_business_background_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-days {{CURRENT_ITEM}}.top-border-divider' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'highlight_this' => 'yes',
				),
				'separator' => 'before',
			)
		);

		// Default values of repeater.
		$this->add_control(
			'business_days_timings',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'enter_day'  => __( 'Monday', 'uael' ),
						'enter_time' => __( '8:00 AM - 7:00 PM', 'uael' ),
					),
					array(
						'enter_day'  => __( 'Tuesday', 'uael' ),
						'enter_time' => __( '8:00 AM - 7:00 PM', 'uael' ),
					),
					array(
						'enter_day'  => __( 'Wednesday', 'uael' ),
						'enter_time' => __( '8:00 AM - 7:00 PM', 'uael' ),
					),
					array(
						'enter_day'  => __( 'Thursday', 'uael' ),
						'enter_time' => __( '8:00 AM - 7:00 PM', 'uael' ),
					),
					array(
						'enter_day'  => __( 'Friday', 'uael' ),
						'enter_time' => __( '8:00 AM - 7:00 PM', 'uael' ),
					),
					array(
						'enter_day'      => __( 'Saturday', 'uael' ),
						'enter_time'     => __( 'Closed', 'uael' ),
						'highlight_this' => __( 'yes', 'uael' ),
					),
					array(
						'enter_day'      => __( 'Sunday', 'uael' ),
						'enter_time'     => __( 'Closed', 'uael' ),
						'highlight_this' => __( 'yes', 'uael' ),
					),
				),
				'title_field' => '{{ enter_day }}',
			)
		);

		$this->end_controls_section();

		// Divider styling starts from here.
		$this->start_controls_section(
			'section_bs_general',
			array(
				'label' => __( 'General', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		// Day Padding.
		$this->add_responsive_control(
			'section_bs_list_padding',
			array(
				'label'              => __( 'Row Spacing', 'uael' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', 'em', '%' ),
				'selectors'          => array(
					'{{WRAPPER}} div.uael-days div' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();

		// Divider styling starts from here.
		$this->start_controls_section(
			'section_bs_divider',
			array(
				'label' => __( 'Divider', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		// Day Divider Switcher.
		$this->add_control(
			'day_divider',
			array(
				'label'        => __( 'Divider', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'YES', 'uael' ),
				'label_off'    => __( 'NO', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		// Day Divider Style Type.
		$this->add_control(
			'day_divider_style',
			array(
				'label'     => __( 'Style', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'solid'  => __( 'Solid', 'uael' ),
					'dotted' => __( 'Dotted', 'uael' ),
					'dashed' => __( 'Dashed', 'uael' ),
				),
				'default'   => 'solid',
				'selectors' => array(
					'{{WRAPPER}} .uael-business-scheduler-box-wrapper div.uael-days div.top-border-divider:not(:first-child)' => 'border-top-style: {{VALUE}};',
				),
				'condition' => array(
					'day_divider' => 'yes',
				),
			)
		);

		// Day Divider Style Color.
		$this->add_control(
			'day_divider_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d4d4d4',
				'selectors' => array(
					'{{WRAPPER}} .uael-business-scheduler-box-wrapper div.uael-days div.top-border-divider:not(:first-child)' => 'border-top-color: {{VALUE}};',
				),
				'condition' => array(
					'day_divider' => 'yes',
				),
			)
		);

		// Day Divider Style Weight.
		$this->add_control(
			'day_divider_weight',
			array(
				'label'     => __( 'Weight', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 1,
					'unit' => 'px',
				),
				'range'     => array(
					'px' => array(
						'min' => 1,
						'max' => 10,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-business-scheduler-box-wrapper div.uael-days div.top-border-divider:not(:first-child)' => 'border-top-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'day_divider' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		// Business day styling starts from here.
		$this->start_controls_section(
			'section_business_day_style',
			array(
				'label' => __( 'Day and Time', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		// Business Day Alignment.
		$this->add_responsive_control(
			'business_hours_day_align',
			array(
				'label'              => __( 'Day Alignment', 'uael' ),
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
				'selectors'          => array(
					'{{WRAPPER}} div.uael-days .heading-date' => 'text-align: {{VALUE}};',
				),
				'frontend_available' => true,
			)
		);

		// Business Time Alignment.
		$this->add_responsive_control(
			'business_hours_time_align',
			array(
				'label'              => __( 'Time Alignment', 'uael' ),
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
				'selectors'          => array(
					'{{WRAPPER}} div.uael-days .heading-time' => 'text-align: {{VALUE}};',
				),
				'frontend_available' => true,
			)
		);

		// Business Day Color.
		$this->add_control(
			'business_day_color',
			array(
				'label'     => __( 'Day Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-business-day' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-container' => 'overflow: hidden;',
				),
			)
		);

		// Business Day Typography.
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => __( 'Day Typography', 'uael' ),
				'name'     => 'business_day_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .heading-date',
			)
		);

		// Timing Color.
		$this->add_control(
			'business_timing_color',
			array(
				'label'     => __( 'Time Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-business-time' => 'color: {{VALUE}};',
				),
			)
		);

		// Timing Typography.
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => __( 'Time Typography', 'uael' ),
				'name'     => 'business_timings_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .heading-time',
			)
		);

		// Business Day Striped effect.
		$this->add_control(
			'striped_effect_feature',
			array(
				'label'        => __( 'Striped Effect', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'YES', 'uael' ),
				'label_off'    => __( 'NO', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		// Business Day Striped effect (Odd Rows).
		$this->add_control(
			'striped_effect_odd',
			array(
				'label'     => __( 'Striped Odd Rows Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eaeaea',
				'selectors' => array(
					'{{WRAPPER}} .top-border-divider:nth-child(odd)' => 'background: {{VALUE}};',
				),
				'condition' => array(
					'striped_effect_feature' => 'yes',
				),
			)
		);

		// Business Day Striped effect (Even Rows).
		$this->add_control(
			'striped_effect_even',
			array(
				'label'     => __( 'Striped Even Rows Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .top-border-divider:nth-child(even)' => 'background: {{VALUE}};',
				),
				'condition' => array(
					'striped_effect_feature' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Helpful Information.
	 *
	 * @since 1.1.0
	 * @access protected
	 */
	protected function register_helpful_information() {

		$link = UAEL_DOMAIN . 'docs/business-hours-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';

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
					'raw'             => sprintf( __( '%1$s Getting started video » %2$s', 'uael' ), '<a href="https://www.youtube.com/watch?v=IigQsUdvcRo&list=PL1kzJGWGPrW_7HabOZHb6z88t_S8r-xAc&index=5" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_2',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Getting started article » %2$s', 'uael' ), '<a href=' . $link . ' target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->end_controls_section();
		}
	}

	/**
	 * Render Modal Popup output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();
		$node_id  = $this->get_id();
		ob_start();
		include UAEL_MODULES_DIR . 'business-hours/widgets/template.php';
		$html = ob_get_clean();
		echo $html;  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Render Business Hours widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.22.1
	 * @access protected
	 */
	protected function content_template() {
		?>
		<div class="uael-business-scheduler-box-wrapper">
			<div class="uael-days">
			<#  if ( settings.business_days_timings ) {

					var count = 0;

					_.each( settings.business_days_timings, function( item ) {

						var uael_current_item_wrap = 'elementor-repeater-item-' + item._id;
						var uael_bs_background;
						if ( 'yes' == item.highlight_this ) {
							uael_bs_background = 'uael-highlight-background';
						} else if ( 'yes' == settings.striped_effect_feature ) {
							uael_bs_background = 'stripes';
						} else {
							uael_bs_background = 'bs-background';
						}
						var uael_highlight_day;
						var uael_highlight_time;
						if ( 'yes' == item.highlight_this ) {
							uael_highlight_day  = 'uael-business-day-highlight';
							uael_highlight_time = 'uael-business-timing-highlight';
						} else {
							uael_highlight_day  = 'uael-business-day';
							uael_highlight_time = 'uael-business-time';
						}

					#>
					<div class="{{ uael_current_item_wrap }} {{ uael_bs_background }} top-border-divider">
						<div class="uael-inner">
							<span class="{{ uael_highlight_day }} heading-date">
								<span class="elementor-inline-editing" data-elementor-setting-key="business_days_timings.{{ count }}.enter_day" data-elementor-inline-editing-toolbar="basic">{{ item.enter_day }}</span>
							</span>
							<span class="{{ uael_highlight_time }} heading-time">
							<span class="inner-heading-time">
								<span class="elementor-inline-editing" data-elementor-setting-key="business_days_timings.{{ count }}.enter_time" data-elementor-inline-editing-toolbar="basic">{{ item.enter_time }}</span>
							</span>
							</span>
						</div>
					</div>
					<#
					count++;
					});
				}
				#>
			</div>
		</div>
		<?php
	}

}
