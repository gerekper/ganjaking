<?php

/**
 * Image grid widget class
 *
 * @package Happy_Addons
 */

namespace Happy_Addons\Elementor\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Utils;

defined('ABSPATH') || die();

class Image_Hover_Effect extends Base {

	/**
	 * Default filter is the global filter
	 * and can be overriden from settings
	 *
	 * @var string
	 */

	public function get_title() {
		return __('Image Hover Effect', 'happy-elementor-addons');
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'hm hm-cursor-hover-click';
	}

	public function get_keywords() {
		return ['hover', 'image', 'effect'];
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {

		$this->start_controls_section(
			'_section_image_content',
			[
				'label' => __('Image Content', 'happy-elementor-addons'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'hover_image',
			[
				'label' => __('Image', 'happy-elementor-addons'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'dynamic' => [
					'active' => true
				],
			]
		);

		$this->add_control(
			'hover_image_alt_tag',
			[
				'label' => __('Image ALT Tag', 'happy-elementor-addons'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Image hover effect image', 'happy-elementor-addons'),
				'placeholder' => __('Type here image alt tag value', 'happy-elementor-addons'),
				'dynamic' => ['active' => true,],
			]
		);

		$this->add_control(
			'hover_title',
			[
				'label' => __('Title', 'happy-elementor-addons'),
				'type' => Controls_Manager::TEXTAREA,
				'description' => ha_get_allowed_html_desc( 'intermediate' ),
				'rows' => 3,
				'default' => __('Happy <span>Addons</span>', 'happy-elementor-addons'),
				'placeholder' => __('Type your title here', 'happy-elementor-addons'),
				'dynamic' => ['active' => true],
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label' => __( 'Title HTML Tag', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				// 'separator' => 'before',
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h2',
			]
		);

		$this->add_control(
			'hover_description',
			[
				'label' => __('Description', 'happy-elementor-addons'),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 10,
				'default' => __('Best Elementor Addons', 'happy-elementor-addons'),
				'placeholder' => __('Type your description here', 'happy-elementor-addons'),
				'condition' => [
					'hover_effect!' => 'ha-effect-honey',
				],
				'dynamic' => ['active' => true],
			]
		);

		$this->add_control(
			'hover_link',
			[
				'label' => __('Link URL', 'happy-elementor-addons'),
				'type' => Controls_Manager::URL,
				'placeholder' => __('https://your-link.com', 'happy-elementor-addons'),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
				'dynamic' => ['active' => true],
			]
		);

		$this->add_control(
			'hover_effect',
			[
				'label' => __('Hover Effect', 'happy-elementor-addons'),
				'type' => Controls_Manager::SELECT2,
				'options' => [
					'ha-effect-apollo'  => __('Apollo', 'happy-elementor-addons'),
					'ha-effect-bubba'  => __('Bubba', 'happy-elementor-addons'),
					'ha-effect-chico'  => __('Chico', 'happy-elementor-addons'),
					'ha-effect-dexter'  => __('Dexter', 'happy-elementor-addons'),
					'ha-effect-duke'  => __('Duke', 'happy-elementor-addons'),
					'ha-effect-goliath'  => __('Goliath', 'happy-elementor-addons'),
					'ha-effect-honey'  => __('Honey', 'happy-elementor-addons'),
					'ha-effect-jazz'  => __('Jazz', 'happy-elementor-addons'),
					'ha-effect-layla'  => __('Layla', 'happy-elementor-addons'),
					'ha-effect-lexi'  => __('Lexi', 'happy-elementor-addons'),
					'ha-effect-lily'  => __('Lily', 'happy-elementor-addons'),
					'ha-effect-marley'  => __('Marley', 'happy-elementor-addons'),
					'ha-effect-milo'  => __('Milo', 'happy-elementor-addons'),
					'ha-effect-ming'  => __('Ming', 'happy-elementor-addons'),
					'ha-effect-moses'  => __('Moses', 'happy-elementor-addons'),
					'ha-effect-oscar'  => __('Oscar', 'happy-elementor-addons'),
					'ha-effect-romeo'  => __('Romeo', 'happy-elementor-addons'),
					'ha-effect-roxy'  => __('Roxy', 'happy-elementor-addons'),
					'ha-effect-ruby'  => __('Ruby', 'happy-elementor-addons'),
					'ha-effect-sadie'  => __('Sadie', 'happy-elementor-addons'),
					'ha-effect-sarah'  => __('Sarah', 'happy-elementor-addons'),
				],
				'default' => 'ha-effect-apollo',
			]
		);

		$this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->__common_style_controls();
		$this->__overlay_style_controls();
	}

	protected function __common_style_controls() {

		$this->start_controls_section(
			'_section_common_style',
			[
				'label' => __('Common', 'happy-elementor-addons'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'hover_container_height_width_control',
			[
				'label' => __('Container Max Width?', 'happy-elementor-addons'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'happy-elementor-addons'),
				'label_off' => __('No', 'happy-elementor-addons'),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_responsive_control(
			'hover_width',
			[
				'label' => __('Width', 'happy-elementor-addons'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1200,
						'step' => 5,
					],
				],
				'devices' => ['desktop', 'tablet', 'mobile'],
				'desktop_default' => [
					'size' => 480,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 480,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 300,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-ihe-wrapper' => 'width: {{SIZE}}{{UNIT}}; height: calc({{SIZE}}{{UNIT}}/1.34);',
				],
				'condition' => [
					'hover_container_height_width_control' => 'yes'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'hover_border',
				'label' => __('Border', 'happy-elementor-addons'),
				'selector' => '{{WRAPPER}} .ha-ihe-wrapper .ha-ihe-fig',
			]
		);

		$this->add_control(
			'hover_border_radius',
			[
				'label' => __('Border Radius', 'happy-elementor-addons'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ha-ihe-wrapper .ha-ihe-fig' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typo',
				'label' => __('Title Typography', 'happy-elementor-addons'),
				'selector' => '{{WRAPPER}} .ha-ihe-wrapper .ha-ihe-fig .ha-ihe-title',
				'fields_options' => [
					'typography' => ['default' => 'yes'],
					'font_family' => [
						'default' => 'Roboto',
					],
				],
			]
		);


		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typo',
				'label' => __('Description Typography', 'happy-elementor-addons'),
				'selector' => '{{WRAPPER}} .ha-ihe-wrapper .ha-ihe-fig .ha-ihe-desc',
				'fields_options' => [
					'typography' => ['default' => 'yes'],
					'font_family' => [
						'default' => 'Roboto',
					],
				],
			]
		);

		$this->start_controls_tabs('_tabs_style');

		$this->start_controls_tab(
			'_tab_normal',
			[
				'label' => __('Normal', 'happy-elementor-addons'),
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __('Title Color', 'happy-elementor-addons'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ihe-wrapper .ha-ihe-fig .ha-ihe-title' => 'color: {{VALUE}}; border-color: {{VALUE}};',
					'{{WRAPPER}} .ha-ihe-wrapper .ha-ihe-fig .ha-ihe-title::before' => '--ha-ihe-title-before-color: {{VALUE}};',
					'{{WRAPPER}} .ha-ihe-wrapper .ha-ihe-fig .ha-ihe-title::after' => '--ha-ihe-title-after-color: {{VALUE}};',
					'{{WRAPPER}} .ha-ihe-wrapper .ha-ihe-fig .ha-ihe-caption::before' => '--ha-ihe-fig-before-color: {{VALUE}};',
					'{{WRAPPER}} .ha-ihe-wrapper .ha-ihe-fig .ha-ihe-caption::after' => '--ha-ihe-fig-after-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => __('Description Color', 'happy-elementor-addons'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ihe-wrapper .ha-ihe-fig .ha-ihe-desc' => 'color: {{VALUE}}; --ha-ihe-desc-border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_hover',
			[
				'label' => __('Hover', 'happy-elementor-addons'),
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label' => __('Title Color', 'happy-elementor-addons'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ihe-wrapper .ha-ihe-fig:hover .ha-ihe-title' => 'color: {{VALUE}}; border-color: {{VALUE}};',
					'{{WRAPPER}} .ha-ihe-wrapper .ha-ihe-fig:hover .ha-ihe-title::before' => '--ha-ihe-title-before-color: {{VALUE}};',
					'{{WRAPPER}} .ha-ihe-wrapper .ha-ihe-fig:hover .ha-ihe-title::after' => '-ha-ihe-title-after-color: {{VALUE}};',
					'{{WRAPPER}} .ha-ihe-wrapper .ha-ihe-fig:hover .ha-ihe-caption::before' => '--ha-ihe-fig-before-color: {{VALUE}};',
					'{{WRAPPER}} .ha-ihe-wrapper .ha-ihe-fig:hover .ha-ihe-caption::after' => '--ha-ihe-fig-after-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'description_hover_color',
			[
				'label' => __('Description Color', 'happy-elementor-addons'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ihe-wrapper .ha-ihe-fig:hover .ha-ihe-desc' => 'color: {{VALUE}}; --ha-ihe-desc-border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function __overlay_style_controls() {

		$this->start_controls_section(
			'_section_overlay_style',
			[
				'label' => __('Background Overlay', 'happy-elementor-addons'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('_tabs_overlay_style');
		$this->start_controls_tab(
			'_tab_overlay_normal',
			[
				'label' => __('Normal', 'happy-elementor-addons'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'hover_overlay_normal',
				'label' => __('Background', 'happy-elementor-addons'),
				'show_label' => true,
				'types' => ['classic', 'gradient'],
				'exclude' => [
					'classic' => 'image'
				],
				'selector' => '{{WRAPPER}} .ha-ihe-wrapper .ha-ihe-fig, {{WRAPPER}} .ha-ihe-wrapper .ha-ihe-fig.ha-effect-sadie .ha-ihe-caption::before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_overlay_hover',
			[
				'label' => __('Hover', 'happy-elementor-addons'),
			]
		);


		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'hover_overlay_hover',
				'label' => __('Background', 'happy-elementor-addons'),
				'show_label' => true,
				'types' => ['classic', 'gradient'],
				'exclude' => [
					'classic' => 'image'
				],
				'selector' => '{{WRAPPER}} .ha-ihe-wrapper .ha-ihe-fig:hover, {{WRAPPER}} .ha-ihe-wrapper .ha-ihe-fig.ha-effect-sadie:hover .ha-ihe-caption::before',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$url_target = $settings['hover_link']['is_external'] ? ' target="_blank"' : '';
		$url_nofollow = $settings['hover_link']['nofollow'] ? ' rel="nofollow"' : '';
?>
		<div class="ha-ihe-wrapper grid">
			<figure class="ha-ihe-fig <?php echo esc_attr($settings['hover_effect']); ?>">
				<img class="ha-ihe-img" src="<?php echo esc_url($settings['hover_image']['url']); ?>" alt="<?php echo esc_attr($settings['hover_image_alt_tag']); ?>" />
				<figcaption class="ha-ihe-caption">
					<?php if ($settings['hover_effect'] == 'ha-effect-lily') : ?>
						<div>
						<?php endif; ?>
						<?php
						printf( '<%1$s class="ha-ihe-title">%2$s</%1$s>',
							ha_escape_tags( $settings['title_tag'], 'h2' ),
							ha_kses_intermediate($settings['hover_title'])
						);
						?>
						<?php if ($settings['hover_effect'] != 'ha-effect-honey') : ?>
							<p class="ha-ihe-desc"><?php echo ha_kses_intermediate($settings['hover_description']); ?></p>
						<?php endif; ?>
						<?php if ($settings['hover_effect'] == 'ha-effect-lily') : ?>
						</div>
					<?php endif; ?>
					<?php if ($settings['hover_link']['url'] != '') : ?>
						<a href="<?php echo esc_url($settings['hover_link']['url']); ?>" <?php echo esc_attr($url_target . $url_nofollow); ?>></a>
					<?php endif; ?>
				</figcaption>
			</figure>
		</div>
<?php
	}
}
