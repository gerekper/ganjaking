<?php

namespace ElementPack\Modules\CookieConsent\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use ElementPack\Base\Module_Base;
use ElementPack\Element_Pack_Loader;
use ElementPack\Utils;

if (!defined('ABSPATH')) {
	exit;
}
// Exit if accessed directly

class Cookie_Consent extends Module_Base {
	public function get_name() {
		return 'bdt-cookie-consent';
	}

	public function get_title() {
		return BDTEP . esc_html__('Cookie Consent', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-cookie-consent';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['cookie', 'consent'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-cookie-consent'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['cookieconsent', 'ep-scripts'];
		} else {
			return ['cookieconsent', 'ep-cookie-consent'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/BR4t5ngDzqM';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => __('Layout', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'message',
			[
				'label'   => __('Message', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'This website uses cookies to ensure you get the best experience on our website. ',
				'dynamic' => ['active' => true],
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'   => __('Button Text', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => ['active' => true],
				'default' => 'Got it!',
			]
		);

		$this->add_control(
			'learn_more_text',
			[
				'label'       => __('Learn More Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __('Learn more', 'bdthemes-element-pack'),
				'dynamic'     => ['active' => true],
				'default'     => 'Learn more',
			]
		);

		$this->add_control(
			'learn_more_link',
			[
				'label'         => __('Learn More Link', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::URL,
				'show_external' => false,
				'placeholder'   => __('https://your-link.com', 'bdthemes-element-pack'),
				'default'       => [
					'url' => 'http://cookiesandyou.com/',
				],
				'dynamic' => ['active' => true],
			]
		);

		$this->add_control(
			'position',
			[
				'label'   => __('Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bottom',
				'options' => [
					'bottom'       => esc_html__('Bottom', 'bdthemes-element-pack'),
					'bottom-left'  => esc_html__('Bottom Left', 'bdthemes-element-pack'),
					'bottom-right' => esc_html__('Bottom Right', 'bdthemes-element-pack'),
					'top'          => esc_html__('Top', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'pushdown',
			[
				'label'     => esc_html__('Show Title', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'position' => 'top',
				],
			]
		);

		$this->add_control(
			'expiry_days',
			[
				'label'       => __('Expiry Days', 'bdthemes-element-pack'),
				'description' => 'Specify -1 for no expiry',
				'type'        => Controls_Manager::SLIDER,
				'default'     => [
					'size' => 7,
				],
				'range' => [
					'px' => [
						'min' => -1,
						'max' => 365,
					],
				],
				'dynamic' => ['active' => true],
			]
		);

		$this->add_control(
			'custom_attributes',
			[
				'label'   => __('Custom Attributes', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'rel|noopener noreferrer nofollow',
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __('key|value', 'bdthemes-element-pack'),
				'description' => __('Set custom attributes for the link tag. Each attribute in a separate line.', 'bdthemes-element-pack'),
				'classes'     => 'elementor-control-direction-ltr',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => __('Style', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'background',
			[
				'label'     => __('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3937a3',
				'selectors' => [
					'body .cc-window' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'body .cc-window' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'learn_more_color',
			[
				'label'     => __('Learn More Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4593E3',
				'selectors' => [
					'body .cc-window .cc-link' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'subtitle_typography',
				'selector' => 'body .cc-window *',
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_dismiss_button',
			[
				'label' => esc_html__('Button', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_dismiss_button_style');

		$this->start_controls_tab(
			'tab_dismiss_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'dismiss_button_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'body .cc-window .cc-btn.cc-dismiss' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'dismiss_button_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#41aab9',
				'selectors' => [
					'body .cc-window .cc-btn.cc-dismiss' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'dismiss_button_border_style',
			[
				'label'   => __('Border Style', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'   => __('None', 'bdthemes-element-pack'),
					'solid'  => __('Solid', 'bdthemes-element-pack'),
					'double' => __('Double', 'bdthemes-element-pack'),
					'dotted' => __('Dotted', 'bdthemes-element-pack'),
					'dashed' => __('Dashed', 'bdthemes-element-pack'),
					'groove' => __('Groove', 'bdthemes-element-pack'),
				],
				'selectors' => [
					'body .cc-window .cc-btn.cc-dismiss' => 'border-style: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'dismiss_button_border_width',
			[
				'label'   => __('Border Width', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'min'  => 0,
					'max'  => 20,
					'size' => 1,
				],
				'selectors' => [
					'body .cc-window .cc-btn.cc-dismiss' => 'border-width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_control(
			'dismiss_button_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ccc',
				'selectors' => [
					'body .cc-window .cc-btn.cc-dismiss' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'dismiss_button_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'body .cc-window .cc-btn.cc-dismiss' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'dismiss_button_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'body .cc-window .cc-btn.cc-dismiss' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'dismiss_button_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'body .cc-window .cc-btn.cc-dismiss' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'  => 'dismiss_button_typography',
				'label' => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'    => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => 'body .cc-window .cc-btn.cc-dismiss',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dismiss_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'dismiss_button_hover_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'body .cc-window .cc-btn.cc-dismiss:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'dismiss_button_hover_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'body .cc-window .cc-btn.cc-dismiss:hover' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'dismiss_button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'dismiss_button_border_style!' => 'none',
				],
				'selectors' => [
					'body .cc-window .cc-btn.cc-dismiss:hover' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		$cc_position = $settings['position'];

		if ($cc_position == 'bottom-left') {
			$cc_position = 'cc-bottom cc-left cc-floating';
		} else if ($cc_position == 'bottom-right') {
			$cc_position = 'cc-bottom cc-right cc-floating';
		} else if ($cc_position == 'top') {
			$cc_position = 'cc-top cc-banner';
		} else if ($cc_position == 'bottom') {
			$cc_position = 'cc-bottom cc-banner';
		}

		$this->add_render_attribute('cookie-consent', 'class', ['bdt-cookie-consent', 'bdt-hidden']);

		if (!empty($settings['custom_attributes'])) {
			$attributes = explode("\n", $settings['custom_attributes']);

			$reserved_attr = ['role', 'href'];

			foreach ($attributes as $attribute) {
				if (!empty($attribute)) {
					$attr = explode('|', $attribute, 2);
					if (!isset($attr[1])) {
						$attr[1] = '';
					}

					if (!in_array(strtolower($attr[0]), $reserved_attr)) {
						$this->add_render_attribute('custom-attr', trim($attr[0]), trim($attr[1]));
					}
				}
			}
		}

		$this->add_render_attribute(
			[
				'cookie-consent' => [
					'data-settings' => [
						wp_json_encode([
							'position' => $settings['position'],
							'static' => ('top' == $settings['position'] and $settings['pushdown']) ? true : false,
							'content' => [
								'message' => $settings['message'],
								'dismiss' => $settings['button_text'],
								'link' => $settings['learn_more_text'],
								'href' => esc_url($settings['learn_more_link']['url']),
								'custom_attr' => $this->get_render_attribute_string('custom-attr'),
							],
							'cookie' => [
								'name' => 'element_pack_cookie_widget',
								'domain' => Utils::get_site_domain(),
								'expiryDays' => $settings['expiry_days']['size'],
							],
						]),
					],
				],
			]
		);

		if (Element_Pack_Loader::elementor()->editor->is_edit_mode()) : ?>

			<div role="dialog" aria-live="polite" aria-label="cookieconsent" aria-describedby="cookieconsent:desc" class="cc-window <?php echo esc_attr($cc_position); ?> cc-type-info cc-theme-block cc-color-override--2000495483">

				<!--googleoff: all-->
				<span id="cookieconsent:desc" class="cc-message"><?php echo esc_html($settings['message']); ?><a aria-label="learn more about cookies" role="button" tabindex="0" class="cc-link" href="<?php echo esc_url($settings['learn_more_link']['url']); ?>" rel="noopener noreferrer nofollow" target="_blank" test><?php echo esc_html($settings['learn_more_text']); ?></a></span>
				<div class="cc-compliance">
					<a aria-label="dismiss cookie message" role="button" tabindex="0" class="cc-btn cc-dismiss"><?php echo esc_html($settings['button_text']); ?></a>
				</div>
				<!--googleon: all-->

			</div>

		<?php else : ?>

			<div <?php echo $this->get_render_attribute_string('cookie-consent'); ?>></div>

<?php endif;
	}
}
