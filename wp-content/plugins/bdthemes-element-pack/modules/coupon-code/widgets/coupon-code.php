<?php

namespace ElementPack\Modules\CouponCode\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

class Coupon_Code extends Module_Base {
	public function get_name() {
		return 'bdt-coupon-code';
	}

	public function get_title() {
		return BDTEP . esc_html__('Coupon Code', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-coupon-code';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['coupon', 'reveal', 'code'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-coupon-code'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['clipboard', 'ep-scripts'];
		} else {
			return ['clipboard', 'ep-coupon-code'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/xru1Xu3ISZ0';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__('Coupon Code', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'coupon_layout',
			[
				'label'        => esc_html__('Coupon Layout', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'default',
				'options'      => [
					'default' => esc_html__('Default', 'bdthemes-element-pack'),
					'style-1' => esc_html__('Style 1', 'bdthemes-element-pack'),
					'style-2' => esc_html__('Style 2', 'bdthemes-element-pack'),
				],
				'prefix_class' => 'bdt-coupon-code-style--',
			]
		);

		$this->add_control(
			'coupon_text',
			[
				'label'   => esc_html__('Coupon Text', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => ['active' => true],
				'default' => esc_html__('Get 20% discount', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'coupon_text_icon',
			[
				'label'       => esc_html__('Icon', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'skin'        => 'inline'
			]
		);

		$this->add_control(
			'coupon_code',
			[
				'label'   => esc_html__('Coupon Code', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => ['active' => true],
				'default' => esc_html__('EIDADHA40', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'coupon_placeholder',
			[
				'label'     => esc_html__('Coupon Placeholder', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => ['active' => true],
				'default'   => esc_html__('XX-XX-XX', 'bdthemes-element-pack'),
				'condition' => [
					'trigger_by_action' => 'yes'
				]
			]
		);

		$this->add_control(
			'trigger_link',
			[
				'label'       => esc_html__('Trigger Link', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__('This link will be open a new tab when users will click over the coupon.', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'link',
			[
				'label'       => esc_html__('Link', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::URL,
				'dynamic'     => ['active' => true],
				'placeholder' => esc_html__('https://your-link.com', 'bdthemes-element-pack'),

				'default'   => [
					'url' => '#',
					'is_external' => true
				],
				'condition' => [
					'trigger_link' => 'yes'
				]
			]
		);

		$this->add_control(
			'trigger_by_action',
			[
				'label'       => esc_html__('Trigger By Action', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__('Your coupon code will show up when got a trigger from any valid action.', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'trigger_by_form_id',
			[
				'label'       => esc_html__('Form Selector', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'description' => esc_html__('If you will place a selector (without #) here. Then it\'s will work on Advanced mode.', 'bdthemes-element-pack'),
				'condition'   => [
					'trigger_by_action' => 'yes'
				]
			]
		);

		$this->add_control(
			'trigger_by_action_attention',
			[
				'label'       => esc_html__('Trigger Attention', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_coupon',
			[
				'label' => __('Coupon Reveal', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'coupon_reveal_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-coupon-code > div' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'coupon_wrapper_width',
			[
				'label'      => __('Width', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min'  => 400,
						'max'  => 1000,
						'step' => 5,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-coupon-code-wrapper .bdt-coupon-code' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'coupon_space_between',
			[
				'label'      => __('Space Between', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}.bdt-coupon-code-style--default .bdt-coupon-msg' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition'  => ['coupon_layout' => 'default']
			]
		);

		$this->add_responsive_control(
			'coupon_reveal_align',
			[
				'label'     => __('Alignment', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center'     => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'flex-end'   => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-coupon-code-wrapper' => 'justify-content: {{VALUE}};',
				],
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_coupon_msg',
			[
				'label' => __('Coupon Message', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'coupon_msg_align',
			[
				'label'                => __('Text Alignment', 'bdthemes-element-pack'),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors'            => [
					'{{WRAPPER}} .bdt-coupon-code .bdt-coupon-msg' => '{{VALUE}};',
				],
				'selectors_dictionary' => [
					'left'   => 'text-align: left; justify-content: flex-start;',
					'center' => 'text-align: center; justify-content: center;',
					'right'  => 'text-align: right; justify-content: flex-end;'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'msg_border',
				'label'    => __('Border', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-coupon-code .bdt-coupon-msg',
			]
		);

		$this->add_responsive_control(
			'msg_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-coupon-code .bdt-coupon-msg' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'msg_typography',
				'selector' => '{{WRAPPER}} .bdt-coupon-code .bdt-coupon-msg',
			]
		);

		$this->start_controls_tabs(
			'coupon_msg_tabs'
		);

		$this->start_controls_tab(
			'coupon_msg_normal_tab',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);


		$this->add_control(
			'msg_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-coupon-code .bdt-coupon-msg' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-coupon-code .bdt-coupon-msg svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'msg_background',
				'selector' => '{{WRAPPER}} .bdt-coupon-code .bdt-coupon-msg',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'msg_text_shadow',
				'label'    => __('Text Shadow', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-coupon-code .bdt-coupon-msg',
			]
		);


		$this->end_controls_tab();

		$this->start_controls_tab(
			'coupon_msg_hover_tab',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'msg_color_hover',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-coupon-code .bdt-coupon-msg:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-coupon-code .bdt-coupon-msg:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'msg_background_hover',
				'selector' => '{{WRAPPER}} .bdt-coupon-code .bdt-coupon-msg:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'msg_text_shadow_hover',
				'label'    => __('Text Shadow', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-coupon-code .bdt-coupon-msg:hover',
			]
		);


		$this->add_control(
			'msg_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'msg_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-coupon-code .bdt-coupon-msg:hover' => 'border-color: {{VALUE}};',
				],
			]
		);


		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_coupon_code',
			[
				'label' => __('Coupon Code', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'coupon_code_align',
			[
				'label'     => __('Text Alignment', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center'     => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'flex-end'   => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-coupon-code .bdt-coupon-code-final' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'coupon_border',
				'label'    => __('Border', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-coupon-code .bdt-coupon-code-final',
			]
		);

		$this->add_responsive_control(
			'coupon_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-coupon-code .bdt-coupon-code-final' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'coupon_code_typography',
				'selector' => '{{WRAPPER}} .bdt-coupon-code .bdt-coupon-code-final',
			]
		);


		$this->start_controls_tabs(
			'coupon_code_tabs'
		);

		$this->start_controls_tab(
			'coupon_code_normal_tab',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);


		$this->add_control(
			'coupon_code_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-coupon-code .bdt-coupon-code-final' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'coupon_code_background',
				'selector' => '{{WRAPPER}} .bdt-coupon-code .bdt-coupon-code-final',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'coupon_code_text_shadow',
				'label'    => __('Text Shadow', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-coupon-code .bdt-coupon-code-final',
			]
		);


		$this->end_controls_tab();

		$this->start_controls_tab(
			'coupon_code_hover_tab',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);


		$this->add_control(
			'coupon_code_color_hover',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-coupon-code .bdt-coupon-code-final:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'coupon_code_background_hover',
				'selector' => '{{WRAPPER}} .bdt-coupon-code .bdt-coupon-code-final:hover',
			]
		);

		$this->add_control(
			'coupon_code_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'coupon_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-coupon-code .bdt-coupon-code-final:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function coupon_encryption($data) {
		// Store a string into the variable which
		// need to be Encrypted
		$simple_string = $data;

		// Store the cipher method
		$ciphering = "AES-128-CTR";

		// Use OpenSSl Encryption method
		$options = 0;

		// Non-NULL Initialization Vector for encryption
		$encryption_iv = '1234567891011121';

		// Store the encryption key
		$encryption_key = "ElementPack";

		// Use openssl_encrypt() function to encrypt the data
		$encryption = openssl_encrypt(
			$simple_string,
			$ciphering,
			$encryption_key,
			$options,
			$encryption_iv
		);

		return $encryption;
	}

	protected function render() {
		$settings    = $this->get_settings_for_display();
		$coupon_code = $settings['coupon_code'];

		$coupon_code_encoded = $this->coupon_encryption($coupon_code);

		$this->add_render_attribute(
			[
				'coupon-data' => [
					'class'         => 'bdt-coupon-code',
					'data-settings' => [
						wp_json_encode(
							[
								'couponLayout'     => $settings['coupon_layout'],
								'couponId'         => '#bdt-coupon-code-final-' . $this->get_id(),
								'couponMsgId'      => '#bdt-coupon-code-msg-' . $this->get_id(),
								'triggerURL'       => ($settings['trigger_link'] == 'yes') && (!empty($settings['link']['url'])) ? $settings['link']['url'] : false,
								'is_external'      => ($settings['trigger_link'] == 'yes') && (!empty($settings['link']['url'])) ? $settings['link']['is_external'] : false,
								'couponCode'       => $coupon_code_encoded,
								'adminAjaxURL'     => admin_url("admin-ajax.php"),
								'triggerByAction'  => $settings['trigger_by_action'] == 'yes' ? true : false,
								'triggerInputId'   => (isset($settings['trigger_by_form_id']) && !empty($settings['trigger_by_form_id'])) ? '#bdt-sf-' . $settings['trigger_by_form_id'] : false,
								'triggerAttention' => $settings['trigger_by_action_attention'] == 'yes' ? true : false,
							]
						),
					],
				],
			]
		);

?>
		<div class="bdt-coupon-code-wrapper bdt-flex">
			<div <?php echo $this->get_render_attribute_string('coupon-data'); ?>>
				<div class="bdt-coupon-msg" id="bdt-coupon-code-msg-<?php echo esc_attr($this->get_id()); ?>">
					<div class="bdt-coupon-msg-text">
						<?php if ($settings['coupon_text_icon']['value']) : ?>
							<span class="bdt-button-icon-align bdt-margin-small-right">
								<?php
								Icons_Manager::render_icon(
									$settings['coupon_text_icon'],
									[
										'aria-hidden' => 'true',
										'class'       => 'fa-fw'
									]
								);
								?>
							</span>
						<?php endif; ?>
						<?php echo esc_html($settings['coupon_text']); ?>
					</div>
					<div class="bdt-hidden">
						<?php echo __('COPIED', 'bdthemes-element-pack'); ?>
					</div>
				</div>
				<div class="bdt-coupon-code-final" id="bdt-coupon-code-final-<?php echo esc_attr($this->get_id()); ?>">
					<span class="bdt-coupon-code-text">
						<?php
						if ($settings['trigger_by_action'] == 'yes') {
							$placeholder = (isset($settings['coupon_placeholder']) && !empty($settings['coupon_placeholder'])) ? $settings['coupon_placeholder'] : 'XX-XX-XX';
							echo esc_html($placeholder);
						} else {
							echo esc_html($coupon_code);
						}
						?>
					</span>
					<span class="bdt-hidden">
						<?php echo __('COPIED', 'bdthemes-element-pack'); ?>
					</span>
				</div>
			</div>
		</div>

<?php

	}
}
