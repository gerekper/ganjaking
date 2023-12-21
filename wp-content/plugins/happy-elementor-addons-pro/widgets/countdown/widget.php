<?php
/**
 * Countdown
 *
 * @package Happy_Addons
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;
use Elementor\Control_Media;

defined('ABSPATH') || die();

class Countdown extends Base {

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __('Countdown', 'happy-addons-pro');
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'hm hm-refresh-time';
	}

	public function get_keywords() {
		return ['countdown', 'timer'];
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {
		$this->__time_content_controls();
		$this->__settings_content_controls();
		$this->__end_action_content_controls();
	}

	protected function __time_content_controls() {

		$this->start_controls_section(
			'_section_time',
			[
				'label' => __('Time', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'due_date',
			[
				'label' => __('Time', 'happy-addons-pro'),
				'type' => Controls_Manager::DATE_TIME,
				'default' => date("Y-m-d", strtotime("+ 1 day")),
				'description' => esc_html__('Set the due date and time', 'happy-addons-pro'),
			]
		);
		$this->end_controls_section();
	}

	protected function __settings_content_controls() {

		$this->start_controls_section(
			'_section_countdown_settings',
			[
				'label' => __('Countdown Settings', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'label_position',
			[
				'label' => __('Label Position', 'happy-addons-pro'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'right' => [
						'title' => __('Right', 'happy-addons-pro'),
						'icon' => 'eicon-h-align-right',
					],
					'bottom' => [
						'title' => __('Bottom', 'happy-addons-pro'),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'toggle' => false,
				'default' => 'bottom',
				'prefix_class' => 'ha-countdown-label-',
                'style_transfer' => true,
			]
		);
		$this->add_control(
			'label_space',
			[
				'label' => __('Label Space', 'happy-addons-pro'),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'condition' => [
					'label_position' => 'right',
				],
                'style_transfer' => true,
			]
		);
		$this->start_popover();
		$this->add_control(
			'label_space_top',
			[
				'label' => __('Label Space Top', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.ha-countdown-label-right .ha-countdown-item .ha-countdown-label' => 'top: {{SIZE || 0}}{{UNIT}};',
				],
				'condition' => [
					'label_position' => 'right',
				],
                'style_transfer' => true,
			]
		);

		$this->add_control(
			'label_space_left',
			[
				'label' => __('Label Space Left', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.ha-countdown-label-right .ha-countdown-item .ha-countdown-label' => 'left: {{SIZE || 0}}{{UNIT}};',
				],
				'condition' => [
					'label_position' => 'right',
				],
                'style_transfer' => true,
			]
		);
		$this->end_popover(); //End Prover

		$this->add_control(
			'show_label_days',
			[
				'label' => esc_html__('Show Label Days?', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
                'style_transfer' => true,
			]
		);
		$this->add_control(
			'label_days',
			[
				'label' => esc_html__('Label Days', 'happy-addons-pro'),
				'description' => esc_html__('Set the label for days.', 'happy-addons-pro'),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __('Days', 'happy-addons-pro'),
				'default' => 'Days',
				'condition' => [
					'show_label_days' => 'yes',
				],
			]
		);
		$this->add_control(
			'show_label_hours',
			[
				'label' => esc_html__('Show Label Hours?', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
                'style_transfer' => true,
			]
		);
		$this->add_control(
			'label_hours',
			[
				'label' => esc_html__('Label Hours', 'happy-addons-pro'),
				'description' => esc_html__('Set the label for hours.', 'happy-addons-pro'),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __('Hours', 'happy-addons-pro'),
				'default' => 'Hours',
				'condition' => [
					'show_label_hours' => 'yes',
				],
			]
		);
		$this->add_control(
			'show_label_minutes',
			[
				'label' => esc_html__('Show Label Minutes?', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
                'style_transfer' => true,
			]
		);
		$this->add_control(
			'label_minutes',
			[
				'label' => esc_html__('Label Minutes', 'happy-addons-pro'),
				'description' => esc_html__('Set the label for minutes.', 'happy-addons-pro'),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __('Minutes', 'happy-addons-pro'),
				'default' => 'Minutes',
				'condition' => [
					'show_label_minutes' => 'yes',
				],
			]
		);
		$this->add_control(
			'show_label_seconds',
			[
				'label' => esc_html__('Show Label Seconds?', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
                'style_transfer' => true,
			]
		);
		$this->add_control(
			'label_seconds',
			[
				'label' => esc_html__('Label Seconds', 'happy-addons-pro'),
				'description' => esc_html__('Set the label for seconds.', 'happy-addons-pro'),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __('Seconds', 'happy-addons-pro'),
				'default' => 'Seconds',
				'condition' => [
					'show_label_seconds' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-right',
					]
				],
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .elementor-widget-container' => 'text-align: {{VALUE}}'
				]
			]
		);
		$this->add_control(
			'show_separator',
			[
				'label' => esc_html__('Show Separator?', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'on',
				'default' => '',
				'separator' => 'before',
                'style_transfer' => true,
			]
		);
		$this->add_control(
			'separator',
			[
				'label' => __('Separator', 'happy-addons-pro'),
				'type' => Controls_Manager::TEXT,
				'default' => ':',
				'condition' => [
					'show_separator' => 'on',
				],
			]
		);
		$this->add_control(
			'separator_color',
			[
				'label' => __('Separator Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-countdown-item.ha-countdown-separator-on .ha-countdown-separator' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_separator' => 'on',
				],
                'style_transfer' => true,
			]
		);
		$this->add_responsive_control(
			'separator_font',
			[
				'label' => __('Separator Font Size', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ha-countdown-item.ha-countdown-separator-on .ha-countdown-separator' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_separator' => 'on',
				],
                'style_transfer' => true,
			]
		);
		$this->add_control(
			'separator_position',
			[
				'label' => __('Separator Position', 'happy-addons-pro'),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'condition' => [
					'show_separator' => 'on',
				],
                'style_transfer' => true,
			]
		);

		$this->start_popover();
		$this->add_control(
			'separator_position_top',
			[
				'label' => __('Position Top', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-countdown-item.ha-countdown-separator-on .ha-countdown-separator' => 'top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_separator' => 'on'
				],
                'style_transfer' => true,
			]
		);

		$this->add_control(
			'separator_position_right',
			[
				'label' => __('Position Right', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-countdown-item.ha-countdown-separator-on .ha-countdown-separator' => 'right: {{SIZE || -16}}{{UNIT}};',
				],
				'condition' => [
					'show_separator' => 'on'
				],
                'style_transfer' => true,
			]
		);

		$this->end_popover();

		$this->end_controls_section();
	}

	protected function __end_action_content_controls() {

		$this->start_controls_section(
			'_section_end_action',
			[
				'label' => __('End Action', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'end_action_type',
			[
				'label' => esc_html__('End Action Type', 'happy-addons-pro'),
				'label_block' => false,
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__('Choose which action you want to at the end of countdown.', 'happy-addons-pro'),
				'options' => [
					'none' => esc_html__('None', 'happy-addons-pro'),
					'message' => esc_html__('Message', 'happy-addons-pro'),
					'url' => esc_html__('Redirection Link', 'happy-addons-pro'),
					'img' => esc_html__('Image', 'happy-addons-pro'),
				],
				'default' => 'none'
			]
		);
		$this->add_control(
			'end_message',
			[
				'label' => __('Countdown End Message', 'happy-addons-pro'),
				'type' => Controls_Manager::WYSIWYG,
				'default' => __('Countdown End!', 'happy-addons-pro'),
				'placeholder' => __('Type your message here', 'happy-addons-pro'),
				'condition' => [
					'end_action_type' => 'message'
				],
			]
		);
		$this->add_control(
			'end_redirect_link',
			[
				'label' => __('Redirection Link', 'happy-addons-pro'),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __('https://happyaddons.com/', 'happy-addons-pro'),
				'condition' => [
					'end_action_type' => 'url'
				],
			]
		);

		$this->add_control(
			'end_image',
			[
				'label' => __('Image', 'happy-addons-pro'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'end_action_type' => 'img'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'end_image_size',
				'default' => 'large',
				'separator' => 'none',
				'condition' => [
					'end_action_type' => 'img'
				],
			]
		);

		$this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->__common_style_controls();
		$this->__days_style_controls();
		$this->__hours_style_controls();
		$this->__minutes_style_controls();
		$this->__seconds_style_controls();
	}

	protected function __common_style_controls() {

		$this->start_controls_section(
			'_section_common_style',
			[
				'label' => __('Countdown Common Style', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'box_width',
			[
				'label' => __('Box Width', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-countdown-item' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'box_height',
			[
				'label' => __('Box Height', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-countdown-item' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'common_box_bg',
				'label' => __('Background', 'happy-addons-pro'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ha-countdown-item',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'box_border',
				'label' => __('Box Border', 'happy-addons-pro'),
				'selector' => '{{WRAPPER}} .ha-countdown-item',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'box_border_radius',
			[
				'label' => __('Border Radius', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-countdown-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_box_shadow',
				'label' => __('Box Shadow', 'happy-addons-pro'),
				'selector' => '{{WRAPPER}} .ha-countdown-item',
			]
		);
		$this->add_control(
			'common_box_time_color',
			[
				'label' => __('Time Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-countdown-time' => 'color: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'common_box_time_typography',
				'label' => __('Time Typography', 'happy-addons-pro'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'exclude' => [
					'line_height'
				],
				'default' => [
					'font_size' => ['']
				],
				'selector' => '{{WRAPPER}} .ha-countdown-time',
			]
		);
		$this->add_control(
			'common_box_label_color',
			[
				'label' => __('Label Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-countdown-label' => 'color: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'common_box_label_typography',
				'label' => __('Label Typography', 'happy-addons-pro'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'exclude' => [
					'line_height'
				],
				'default' => [
					'font_size' => [''],
				],
				'selector' => '{{WRAPPER}} .ha-countdown-label',
			]
		);
		$this->add_responsive_control(
			'common_box_spacing',
			[
				'label' => __('Spacing Between Box', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-countdown-item' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};'
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'box_padding',
			[
				'label' => __('Box Padding', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-countdown-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __days_style_controls() {

		$this->start_controls_section(
			'_section_days_style',
			[
				'label' => __('Days Style', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'days_bg',
				'label' => __('Background', 'happy-addons-pro'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ha-countdown-item.ha-countdown-item-days',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'days_border',
				'label' => __('Box Border', 'happy-addons-pro'),
				'selector' => '{{WRAPPER}} .ha-countdown-item.ha-countdown-item-days',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'days_time_color',
			[
				'label' => __('Time Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-countdown-item-days .ha-countdown-time' => 'color: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'days_time_typography',
				'label' => __('Time Typography', 'happy-addons-pro'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'exclude' => [
					'line_height'
				],
				'default' => [
					'font_size' => ['']
				],
				'selector' => '{{WRAPPER}} .ha-countdown-item-days .ha-countdown-time',
			]
		);
		$this->add_control(
			'days_label_color',
			[
				'label' => __('Label Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-countdown-item-days .ha-countdown-label' => 'color: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'days_label_typography',
				'label' => __('Label Typography', 'happy-addons-pro'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'exclude' => [
					'line_height'
				],
				'default' => [
					'font_size' => [''],
				],
				'selector' => '{{WRAPPER}} .ha-countdown-item-days .ha-countdown-label',
			]
		);

		$this->end_controls_section();
	}

	protected function __hours_style_controls() {

		$this->start_controls_section(
			'_section_hours_style',
			[
				'label' => __('Hours Style', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'hours_bg',
				'label' => __('Background', 'happy-addons-pro'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ha-countdown-item.ha-countdown-item-hours',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'hours_border',
				'label' => __('Box Border', 'happy-addons-pro'),
				'selector' => '{{WRAPPER}} .ha-countdown-item.ha-countdown-item-hours',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'hours_time_color',
			[
				'label' => __('Time Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-countdown-item-hours .ha-countdown-time' => 'color: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'hours_time_typography',
				'label' => __('Time Typography', 'happy-addons-pro'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'exclude' => [
					'line_height'
				],
				'default' => [
					'font_size' => ['']
				],
				'selector' => '{{WRAPPER}} .ha-countdown-item-hours .ha-countdown-time',
			]
		);
		$this->add_control(
			'hours_label_color',
			[
				'label' => __('Label Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-countdown-item-hours .ha-countdown-label' => 'color: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'hours_label_typography',
				'label' => __('Label Typography', 'happy-addons-pro'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'exclude' => [
					'line_height'
				],
				'default' => [
					'font_size' => [''],
				],
				'selector' => '{{WRAPPER}} .ha-countdown-item-hours .ha-countdown-label',
			]
		);

		$this->end_controls_section();
	}

	protected function __minutes_style_controls() {

		$this->start_controls_section(
			'_section_minutes_style',
			[
				'label' => __('Minutes Style', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'minutes_bg',
				'label' => __('Background', 'happy-addons-pro'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ha-countdown-item.ha-countdown-item-minutes',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'minutes_border',
				'label' => __('Box Border', 'happy-addons-pro'),
				'selector' => '{{WRAPPER}} .ha-countdown-item.ha-countdown-item-minutes',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'minutes_time_color',
			[
				'label' => __('Time Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-countdown-item-minutes .ha-countdown-time' => 'color: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'minutes_time_typography',
				'label' => __('Time Typography', 'happy-addons-pro'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'exclude' => [
					'line_height'
				],
				'default' => [
					'font_size' => ['']
				],
				'selector' => '{{WRAPPER}} .ha-countdown-item-minutes .ha-countdown-time',
			]
		);
		$this->add_control(
			'minutes_label_color',
			[
				'label' => __('Label Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-countdown-item-minutes .ha-countdown-label' => 'color: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'minutes_label_typography',
				'label' => __('Label Typography', 'happy-addons-pro'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'exclude' => [
					'line_height'
				],
				'default' => [
					'font_size' => [''],
				],
				'selector' => '{{WRAPPER}} .ha-countdown-item-minutes .ha-countdown-label',
			]
		);

		$this->end_controls_section();
	}

	protected function __seconds_style_controls() {

		$this->start_controls_section(
			'_section_seconds_style',
			[
				'label' => __('Seconds Style', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'seconds_bg',
				'label' => __('Background', 'happy-addons-pro'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ha-countdown-item.ha-countdown-item-seconds',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'seconds_border',
				'label' => __('Box Border', 'happy-addons-pro'),
				'selector' => '{{WRAPPER}} .ha-countdown-item.ha-countdown-item-seconds',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'seconds_time_color',
			[
				'label' => __('Time Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-countdown-item-seconds .ha-countdown-time' => 'color: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'seconds_time_typography',
				'label' => __('Time Typography', 'happy-addons-pro'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'exclude' => [
					'line_height'
				],
				'default' => [
					'font_size' => ['']
				],
				'selector' => '{{WRAPPER}} .ha-countdown-item-seconds .ha-countdown-time',
			]
		);
		$this->add_control(
			'seconds_label_color',
			[
				'label' => __('Label Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-countdown-item-seconds .ha-countdown-label' => 'color: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'seconds_label_typography',
				'label' => __('Label Typography', 'happy-addons-pro'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'exclude' => [
					'line_height'
				],
				'default' => [
					'font_size' => [''],
				],
				'selector' => '{{WRAPPER}} .ha-countdown-item-seconds .ha-countdown-label',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$due_date = date("M d Y G:i:s", strtotime($settings['due_date']));
		$this->add_render_attribute('ha-countdown', 'class', 'ha-countdown');
		$this->add_render_attribute('ha-countdown', 'data-date', esc_attr($due_date));
		$this->add_render_attribute('ha-countdown', 'data-end-action', esc_attr($settings['end_action_type']));
		if ('url' === $settings['end_action_type'] && $settings['end_redirect_link']) {
			$this->add_render_attribute('ha-countdown', 'data-redirect-link', esc_url($settings['end_redirect_link']));
		}
		$this->add_render_attribute('days', 'class', 'ha-countdown-item ha-countdown-item-days');
		$this->add_render_attribute('hours', 'class', 'ha-countdown-item ha-countdown-item-hours');
		$this->add_render_attribute('minutes', 'class', 'ha-countdown-item ha-countdown-item-minutes');
		$this->add_render_attribute('seconds', 'class', 'ha-countdown-item ha-countdown-item-seconds');
		if ('on' == $settings['show_separator']) {
			$this->add_render_attribute('days', 'class', 'ha-countdown-separator-on');
			$this->add_render_attribute('hours', 'class', 'ha-countdown-separator-on');
			$this->add_render_attribute('minutes', 'class', 'ha-countdown-separator-on');
			$this->add_render_attribute('seconds', 'class', 'ha-countdown-separator-on');
		}
		?>
		<?php if (!empty($due_date)): ?>
			<div class="ha-countdown-wrap">
				<div <?php $this->print_render_attribute_string('ha-countdown'); ?>>
					<div <?php $this->print_render_attribute_string('days'); ?>>
						<span data-days class="ha-countdown-time ha-countdown-days">0</span>
						<?php if ('yes' == $settings['show_label_days'] && !empty($settings['label_days'])): ?>
							<span
								class="ha-countdown-label ha-countdown-label-days"><?php echo esc_html($settings['label_days']); ?></span>
						<?php endif; ?>
						<?php if ('on' == $settings['show_separator'] && !empty($settings['separator'])): ?>
							<span class="ha-countdown-separator"><?php echo esc_attr($settings['separator']); ?></span>
						<?php endif; ?>
					</div>
					<div <?php $this->print_render_attribute_string('hours'); ?>>
						<span class="ha-countdown-time ha-countdown-hours" data-hours>0</span>
						<?php if ('yes' == $settings['show_label_hours'] && !empty($settings['label_hours'])): ?>
							<span
								class="ha-countdown-label ha-countdown-label-hours"><?php echo esc_html($settings['label_hours']); ?></span>
						<?php endif; ?>
						<?php if ('on' == $settings['show_separator'] && !empty($settings['separator'])): ?>
							<span class="ha-countdown-separator"><?php echo esc_attr($settings['separator']); ?></span>
						<?php endif; ?>
					</div>
					<div <?php $this->print_render_attribute_string('minutes'); ?>>
						<span class="ha-countdown-time ha-countdown-minutes" data-minutes>0</span>
						<?php if ('yes' == $settings['show_label_minutes'] && !empty($settings['label_minutes'])): ?>
							<span
								class="ha-countdown-label ha-countdown-label-minutes"><?php echo esc_html($settings['label_minutes']); ?></span>
						<?php endif; ?>
						<?php if ('on' == $settings['show_separator'] && !empty($settings['separator'])): ?>
							<span class="ha-countdown-separator"><?php echo esc_attr($settings['separator']); ?></span>
						<?php endif; ?>
					</div>
					<div <?php $this->print_render_attribute_string('seconds'); ?>>
						<span class="ha-countdown-time ha-countdown-seconds" data-seconds>0</span>
						<?php if ('yes' == $settings['show_label_seconds'] && !empty($settings['label_seconds'])): ?>
							<span
								class="ha-countdown-label ha-countdown-label-seconds"><?php echo esc_html($settings['label_seconds']); ?></span>
						<?php endif; ?>
					</div>
					<!--End action markup-->
					<?php if ('none' != $settings['end_action_type'] && !empty($settings['end_action_type'])): ?>
						<div class="ha-countdown-end-action">
							<?php if ('message' == $settings['end_action_type'] && $settings['end_message']) :
								echo '<div class="ha-countdown-end-message">' . wpautop(wp_kses_post($settings['end_message'])) . '</div>';
							endif; ?>
							<?php if ('img' == $settings['end_action_type'] && ($settings['end_image']['url'] || $settings['end_image']['id'])) :
								$this->add_render_attribute('image', 'src', $settings['end_image']['url']);
								$this->add_render_attribute('image', 'alt', Control_Media::get_image_alt($settings['end_image']));
								$this->add_render_attribute('image', 'title', Control_Media::get_image_title($settings['end_image']));
								?>
								<figure class="ha-countdown-end-action-image">
									<?php echo Group_Control_Image_Size::get_attachment_image_html($settings, 'end_image_size', 'end_image'); ?>
								</figure>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
		<?php

	}
}
