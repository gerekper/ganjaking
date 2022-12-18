<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Events Widget
 *
 * Porto Elementor widget to display events.
 *
 * @since 1.7.3
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Events_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_events';
	}

	public function get_title() {
		return __( 'Porto Events', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'custom post type', 'posts', 'cpt' );
	}

	public function get_icon() {
		return 'eicon-time-line';
	}

	public function get_script_depends() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return array( 'porto-elementor-widgets-js' );
		} else {
			return array();
		}
	}

	protected function register_controls() {
		$order_by_values  = array_slice( porto_vc_order_by(), 1 );
		$order_way_values = array_slice( porto_vc_woo_order_way(), 1 );

		$this->start_controls_section(
			'section_events',
			array(
				'label' => __( 'Events Layout', 'porto-functionality' ),
			)
		);

			$this->add_control(
				'event_type',
				array(
					'label'   => __( 'Event Type', 'porto-functionality' ),
					'type'    => Controls_Manager::SELECT,
					'default' => '',
					'options' => array(
						''         => __( 'Default', 'porto-functionality' ),
						'next'     => __( 'Next', 'porto-functionality' ),
						'upcoming' => __( 'Upcoming', 'porto-functionality' ),
						'past'     => __( 'Past', 'porto-functionality' ),
					),
				)
			);

			$this->add_control(
				'event_numbers',
				array(
					'type'    => Controls_Manager::NUMBER,
					'label'   => __( 'Number of Events', 'porto-functionality' ),
					'default' => 1,
				)
			);

			$this->add_control(
				'event_skip',
				array(
					'type'        => Controls_Manager::NUMBER,
					'label'       => __( 'Skip Number of Events', 'porto-functionality' ),
					'description' => __( 'Controls how many upcoming events is to be skipped.', 'porto-functionality' ),
					'condition'   => array(
						'event_type' => array( 'upcoming' ),
					),
				)
			);

			$this->add_control(
				'event_column',
				array(
					'type'      => Controls_Manager::NUMBER,
					'label'     => __( 'Numbers of Columns (<=2)', 'porto-functionality' ),
					'min'       => 1,
					'max'       => 2,
					'step'      => 1,
					'condition' => array(
						'event_type' => array( 'upcoming', 'past', 'next' ),
					),
				)
			);

			$this->add_control(
				'event_countdown',
				array(
					'type'      => Controls_Manager::SELECT,
					'label'     => __( 'Display Countdown', 'porto-functionality' ),
					'options'   => array(
						'show' => __( 'Yes', 'porto-functionality' ),
						'hide' => __( 'No', 'porto-functionality' ),
					),
					'default'   => 'show',
					'condition' => array(
						'event_type' => array( 'next' ),
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_event_style',
			array(
				'label' => esc_html__( 'Event', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
			$this->add_control(
				'event_margin',
				array(
					'label'     => __( 'Margin', 'porto-functionality' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'.elementor-element-{{ID}} .custom-post-event' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					),
					'condition' => array(
						'event_type!' => 'next',
					),
				)
			);

			$this->add_control(
				'event_padding',
				array(
					'label'     => __( 'Padding', 'porto-functionality' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'.elementor-element-{{ID}} .thumb-info-caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					),
					'condition' => array(
						'event_type' => 'next',
					),
				)
			);
			$this->add_control(
				'event_caption_padding',
				array(
					'label'     => __( 'Caption Text Padding', 'porto-functionality' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'.elementor-element-{{ID}} .thumb-info-caption-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					),
					'condition' => array(
						'event_type' => 'next',
					),
				)
			);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_event_name',
			array(
				'label' => esc_html__( 'Event Name', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'name_font',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Name', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} h4 a',
				)
			);
			$this->add_control(
				'name_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} h4 a' => 'color: {{VALUE}} !important;',
					),
				)
			);
			$this->add_control(
				'name_margin',
				array(
					'label'     => __( 'Margin', 'porto-functionality' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'.elementor-element-{{ID}} h4' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					),
				)
			);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_event_excerpt',
			array(
				'label' => esc_html__( 'Excerpt', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'excerpt_font',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Excerpt', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} p.post-excerpt',
				)
			);
			$this->add_control(
				'excerpt_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .post-excerpt' => 'color: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'excerpt_margin',
				array(
					'label'     => __( 'Margin', 'porto-functionality' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'.elementor-element-{{ID}} .post-excerpt' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					),
				)
			);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_event_meta',
			array(
				'label' => esc_html__( 'Meta', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'meta_font',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Meta', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} .custom-event-infos',
				)
			);
			$this->add_control(
				'meta_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .custom-event-infos' => 'color: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'meta_icon_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Icon Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .custom-event-infos li i' => 'color: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'meta_margin',
				array(
					'label'     => __( 'Margin', 'porto-functionality' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'.elementor-element-{{ID}} .custom-event-infos' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					),
				)
			);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_event_readmore',
			array(
				'label' => esc_html__( 'Read More', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
			$this->add_control(
				'description_readmore',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( esc_html__( 'Please see \'Show Read More\' Option in %1$sTheme Options -> Event -> Event Archives%2$s panel.', 'porto-functionality' ), '<b>', '</b>' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				)
			);
			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'read_more_font',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Button Typography', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} .read-more',
				)
			);
			$this->add_control(
				'read_more_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .read-more' => 'color: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'read_more_margin',
				array(
					'label'     => __( 'Margin', 'porto-functionality' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'.elementor-element-{{ID}} .read-more' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};display: block;',
					),
				)
			);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_event_countdown',
			array(
				'label'     => esc_html__( 'CountDown', 'porto-functionality' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'event_type'      => array( 'next' ),
					'event_countdown' => 'show',
				),
			)
		);
			$this->add_control(
				'countdown_margin',
				array(
					'label'     => __( 'Margin', 'porto-functionality' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'.elementor-element-{{ID}} .custom-thumb-info-wrapper-box' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					),
				)
			);
		$this->end_controls_section();
	}

	protected function render() {
		$atts                 = $this->get_settings_for_display();
		$atts['page_builder'] = 'elementor';
		if ( $template = porto_shortcode_template( 'porto_events' ) ) {
			include $template;
		}
	}

	protected function content_template() {}
}
