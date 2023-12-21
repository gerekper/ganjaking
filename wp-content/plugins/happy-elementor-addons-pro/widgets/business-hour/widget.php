<?php
/**
 * Business Hour widget class
 *
 * @package Happy_Addons
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

defined('ABSPATH') || die();

class Business_Hour extends Base {
	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __('Business Hour', 'happy-addons-pro');
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
		return 'hm hm-hand-watch';
	}

	public function get_keywords() {
		return ['list', 'watch', 'business', 'hour', 'time', 'business-hour', 'time-list'];
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {
		$this->__business_hour_content_controls();
		$this->__settings_content_controls();
	}

	protected function __business_hour_content_controls() {

		$this->start_controls_section(
			'_section_business_hour',
			[
				'label' => __('Business Hour', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __('Title', 'happy-addons-pro'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __('Working Hour', 'happy-addons-pro'),
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'day',
			[
				'label' => __('Day', 'happy-addons-pro'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __('Monday', 'happy-addons-pro'),
				'placeholder' => __('Monday', 'happy-addons-pro'),
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$repeater->add_control(
			'time',
			[
				'label' => __('Time', 'happy-addons-pro'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __('10:00AM - 07:00PM', 'happy-addons-pro'),
				'placeholder' => __('10:00AM - 07:00PM', 'happy-addons-pro'),
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$repeater->add_control(
			'individual_style',
			[
				'label' => __('Individual Style?', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'happy-addons-pro'),
				'label_off' => __('No', 'happy-addons-pro'),
				'return_value' => 'yes',
				'default' => 'no',
                'style_transfer' => true,
			]
		);

		$repeater->add_control(
			'day_time_color',
			[
				'label' => __('Text Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha-business-hour-item' => 'color: {{VALUE}};',
				],
				'condition' => [
					'individual_style' => 'yes'
				],
				'separator' => 'before',
                'style_transfer' => true,
			]
		);

		$repeater->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'day_time_border',
				'label' => __('Border', 'happy-addons-pro'),
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.ha-business-hour-item',
                'style_transfer' => true,
				'condition' => [
					'individual_style' => 'yes'
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'day_time_background',
				'label' => __('Background', 'happy-addons-pro'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.ha-business-hour-item',
				'condition' => [
					'individual_style' => 'yes'
				],
				'separator' => 'before',
                'style_transfer' => true,
			]
		);

		$repeater->add_control(
			'day_time_border_radius',
			[
				'label' => __('Border Radius', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha-business-hour-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'individual_style' => 'yes'
				],
                'style_transfer' => true,
			]
		);

		$repeater->add_control(
			'day_time_margin',
			[
				'label' => __('Margin', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha-business-hour-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'individual_style' => 'yes'
				],
                'style_transfer' => true,
			]
		);

		$this->add_control(
			'business_hour_list',
			[
				'show_label' => false,
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ day }}}',
				'default' => [
					[
						'day' => __('Monday', 'happy-addons-pro'),
						'time' => __('10:00AM - 07:00PM', 'happy-addons-pro'),
					],
					[
						'day' => __('Tuesday', 'happy-addons-pro'),
						'time' => __('10:00AM - 07:00PM', 'happy-addons-pro'),
					],
					[
						'day' => __('Wednesday', 'happy-addons-pro'),
						'time' => __('10:00AM - 07:00PM', 'happy-addons-pro'),
					],
					[
						'day' => __('Thursday', 'happy-addons-pro'),
						'time' => __('10:00AM - 07:00PM', 'happy-addons-pro'),
					],
					[
						'day' => __('Friday', 'happy-addons-pro'),
						'time' => __('10:00AM - 07:00PM', 'happy-addons-pro'),
					],
					[
						'day' => __('Saturday', 'happy-addons-pro'),
						'time' => __('10:00AM - 07:00PM', 'happy-addons-pro'),
					],
					[
						'day' => __('Sunday', 'happy-addons-pro'),
						'time' => __('Closed', 'happy-addons-pro'),
					],
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __settings_content_controls() {

		$this->start_controls_section(
			'_section_business_settings',
			[
				'label' => __('Settings', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title_alignment',
			[
				'label' => __( 'Title Alignment', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
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
				'toggle' => false,
				'selectors' => [
					'{{WRAPPER}} .ha-business-hour-title' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'day_alignment',
			[
				'label' => __( 'Day Alignment', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
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
				'toggle' => false,
				'selectors' => [
					'{{WRAPPER}} .ha-business-hour-item .ha-business-hour-day' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'time_alignment',
			[
				'label' => __( 'Time Alignment', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
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
				'toggle' => false,
				'selectors' => [
					'{{WRAPPER}} .ha-business-hour-item .ha-business-hour-time' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->__title_style_controls();
		$this->__hour_list_style_controls();
		$this->__container_style_controls();
	}

	protected function __title_style_controls() {

		$this->start_controls_section(
			'_section_business_hour_title_style',
			[
				'label' => __('Title', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __('Text Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-business-hour-title h3' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .ha-business-hour-title h3',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'title_border',
				'label' => __('Border', 'happy-addons-pro'),
				'selector' => '{{WRAPPER}} .ha-business-hour-title',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'title_background',
				'label' => __('Background', 'happy-addons-pro'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ha-business-hour-title',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'title_border_radius',
			[
				'label' => __('Border Radius', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ha-business-hour-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_padding',
			[
				'label' => __('Padding', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ha-business-hour-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_margin',
			[
				'label' => __('Margin', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ha-business-hour-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __hour_list_style_controls() {

		$this->start_controls_section(
			'_section_business_hour_list_style',
			[
				'label' => __('Hour List', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'list_color',
			[
				'label' => __('Text Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-business-hour-item' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'list_typography',
				'selector' => '{{WRAPPER}} .ha-business-hour-item',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'list_border',
				'label' => __('Border', 'happy-addons-pro'),
				'selector' => '{{WRAPPER}} .ha-business-hour-item',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'list_background',
				'label' => __('Background', 'happy-addons-pro'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ha-business-hour-item',
				'separator' => 'before'
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'list_shadow',
				'label' => __( 'Box Shadow', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-business-hour-item',
			]
		);

		$this->add_control(
			'list_border_radius',
			[
				'label' => __('Border Radius', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ha-business-hour-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_control(
			'list_padding',
			[
				'label' => __('Padding', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ha-business-hour-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_control(
			'list_margin',
			[
				'label' => __('Margin', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ha-business-hour-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_section();
	}

	protected function __container_style_controls() {

		$this->start_controls_section(
			'_section_business_hour_container_style',
			[
				'label' => __('Container', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'container_border',
				'label' => __('Border', 'happy-addons-pro'),
				'selector' => '{{WRAPPER}} .ha-business-hour-wrapper ul',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'container_background',
				'label' => __('Background', 'happy-addons-pro'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ha-business-hour-wrapper ul',
				'separator' => 'before'
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'container_shadow',
				'label' => __( 'Box Shadow', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-business-hour-wrapper ul',
			]
		);

		$this->add_control(
			'container_border_radius',
			[
				'label' => __('Border Radius', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ha-business-hour-wrapper ul' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_control(
			'container_padding',
			[
				'label' => __('Padding', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ha-business-hour-wrapper ul' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="ha-business-hour-wrapper">
			<ul>
				<?php if ($settings['title']) : ?>
					<li class="ha-business-hour-title">
						<?php printf('<h3>%s</h3>', esc_html($settings['title'])) ?>
					</li>
				<?php endif; ?>
				<?php if (is_array($settings['business_hour_list']) && 0 != count($settings['business_hour_list'])):
					foreach ($settings['business_hour_list'] as $key => $item) :
						// Day
						$day_key = $this->get_repeater_setting_key('day', 'business_hour_list', $key);
						// $this->add_inline_editing_attributes($day_key, 'basic');
						$this->add_render_attribute($day_key, 'class', 'ha-business-hour-day');
						// Time
						$time_key = $this->get_repeater_setting_key('time', 'business_hour_list', $key);
						// $this->add_inline_editing_attributes($time_key, 'basic');
						$this->add_render_attribute($time_key, 'class', 'ha-business-hour-time');
						?>
						<li class="ha-business-hour-item elementor-repeater-item-<?php echo $item['_id']; ?>">
							<?php if ($item['day']) : ?>
								<span <?php echo $this->get_render_attribute_string($day_key); ?>><?php echo esc_html($item['day']) ?></span>
							<?php endif; ?>
							<?php if ($item['time']) : ?>
								<span <?php echo $this->get_render_attribute_string($time_key); ?>><?php echo esc_html($item['time']) ?></span>
							<?php endif; ?>
						</li>
					<?php
					endforeach;
				endif;
				?>
			</ul>
		</div>
		<?php
	}


}
