<?php
/**
 * Threesixty Rotation widget class
 *
 * @package Happy_Addons
 */

namespace Happy_Addons\Elementor\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;
use Elementor\Group_Control_Background;

defined('ABSPATH') || die();

class Threesixty_Rotation extends Base {

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __('360 Rotation', 'happy-elementor-addons');
	}

	public function get_custom_help_url() {
		return 'https://happyaddons.com/docs/happy-addons-for-elementor/widgets/threesixty-rotation/';
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
		return 'hm hm-3d-rotate';
	}

	public function get_keywords() {
		return ['360 deg view', 'threesixty-rotation', '360', 'slider', 'slider'];
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {
		$this->__threesixty_rotation_content_controls();
		$this->__settings_content_controls();
	}

	protected function __threesixty_rotation_content_controls() {

		$this->start_controls_section(
			'_section_threesixty_rotation',
			[
				'label' => __('Threesixty Rotation', 'happy-elementor-addons'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'images',
			[
				'label' => __('Gallery', 'happy-elementor-addons'),
				'type' => Controls_Manager::GALLERY,
				'default' => [
					[
						'url' => Utils::get_placeholder_image_src(),
					]
				],
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$this->end_controls_section();
	}

	protected function __settings_content_controls() {

		$this->start_controls_section(
			'_section_threesixty_rotation_setting',
			[
				'label' => __('Settings', 'happy-elementor-addons'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'auto_play',
			[
				'label' => __( 'Autoplay', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'autoplay'  => __( 'Autoplay', 'happy-elementor-addons' ),
					'button'  => __( 'Button Play', 'happy-elementor-addons' ),
					'none' => __( 'None', 'happy-elementor-addons' ),
				],
			]
		);

		$this->add_control(
			'button_align',
			[
				'label' => __( 'Button Alignment', 'happy-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}}  .ha-threesixty-rotation-wrapper .ha-threesixty-rotation-autoplay-button' => 'text-align: {{VALUE}};',
				],
				'style_transfer' => true,
				'condition' => [
					'auto_play' => 'button'
				]
			]
		);

		$this->add_control(
			'magnify',
			[
				'label' => __('Magnify', 'happy-elementor-addons'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'happy-elementor-addons'),
				'label_off' => __('Off', 'happy-elementor-addons'),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'zoom',
			[
				'label' => __('Magnify Zoom', 'happy-elementor-addons'),
				'type' => Controls_Manager::NUMBER,
				'default' => '3',
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'magnify' => 'yes'
				]
			]
		);

		/* $this->add_control(
			'wrapper_align',
			[
				'label' => __( 'Alignment', 'happy-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}}.ha-threesixty-rotation .elementor-widget-container' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .ha-threesixty-rotation-wrapper' => 'display:inline-block;',
				],
				'style_transfer' => true,
			]
		); */

		$this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->__wrapper_style_controls();
		$this->__magnify_style_controls();
		$this->__autoplay_btn_style_controls();
	}

	protected function __wrapper_style_controls() {

		$this->start_controls_section(
			'_style_threesixty_rotation_wrapper',
			[
				'label' => __('Wrapper', 'happy-elementor-addons'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'wrapper_width',
			[
				'label' => __( 'Width', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-threesixty-rotation-wrapper' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'wrapper_background',
				'label' => __('Background', 'happy-elementor-addons'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ha-threesixty-rotation-wrapper',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'wrapper_border',
				'label' => __('Border', 'happy-elementor-addons'),
				'selector' => '{{WRAPPER}} .ha-threesixty-rotation-wrapper',
			]
		);

		$this->add_control(
			'wrapper_border_radius',
			[
				'label' => __('Border Radius', 'happy-elementor-addons'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ha-threesixty-rotation-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'wrapper_box_shadow',
				'label' => __('Box Shadow', 'happy-elementor-addons'),
				'selector' => '{{WRAPPER}} .ha-threesixty-rotation-wrapper',
			]
		);

		$this->add_responsive_control(
			'wrapper_padding',
			[
				'label' => __('Padding', 'happy-elementor-addons'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ha-threesixty-rotation-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'sticky_title_position_left',
			[
				'label' => __('Sticky Title Position Left', 'happy-elementor-addons'),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'left',
				'selectors' => [
					'(desktop){{WRAPPER}}  .ha-threesixty-rotation-wrapper  span.ha-threesixty-rotation-sticky-title' => 'left: {{wrapper_padding.LEFT || 0}}{{wrapper_padding.UNIT}}; right:auto;',
					'(tablet){{WRAPPER}}  .ha-threesixty-rotation-wrapper  span.ha-threesixty-rotation-sticky-title' => 'left: {{wrapper_padding_tablet.LEFT}}{{wrapper_padding_tablet.UNIT}}; right:auto;',
					'(mobile){{WRAPPER}}  .ha-threesixty-rotation-wrapper  span.ha-threesixty-rotation-sticky-title' => 'left: {{wrapper_padding_mobile.LEFT}}{{wrapper_padding_mobile.UNIT}}; right:auto;',
				],
				'condition' => [
					'sticky_title!' => '',
					'sticky_title_position' => 'left',
				]
			]
		);

		$this->add_control(
			'sticky_title_position_right',
			[
				'label' => __('Sticky Title Position Right', 'happy-elementor-addons'),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'right',
				'selectors' => [
					'(desktop){{WRAPPER}}  .ha-threesixty-rotation-wrapper  span.ha-threesixty-rotation-sticky-title' => 'right: {{wrapper_padding.RIGHT || 0}}{{wrapper_padding.UNIT}}; left:auto;',
					'(tablet){{WRAPPER}}  .ha-threesixty-rotation-wrapper  span.ha-threesixty-rotation-sticky-title' => 'right: {{wrapper_padding_tablet.RIGHT}}{{wrapper_padding_tablet.UNIT}}; left:auto;',
					'(mobile){{WRAPPER}}  .ha-threesixty-rotation-wrapper  span.ha-threesixty-rotation-sticky-title' => 'right: {{wrapper_padding_mobile.RIGHT}}{{wrapper_padding_mobile.UNIT}}; left:auto;',
				],
				'condition' => [
					'sticky_title!' => '',
					'sticky_title_position' => 'right',
				]
			]
		);

		$this->end_controls_section();
	}

	protected function __magnify_style_controls() {

		$this->start_controls_section(
			'_style_threesixty_rotation_magnify',
			[
				'label' => __('Magnify', 'happy-elementor-addons'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'magnify' => 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'glass_icon_size',
			[
				'label' => __('Icon Size', 'happy-elementor-addons'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-threesixty-rotation-wrapper  .ha-threesixty-rotation-magnify i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'glass_icon_color',
			[
				'label' => __('Icon Color', 'happy-elementor-addons'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-threesixty-rotation-wrapper  .ha-threesixty-rotation-magnify i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'glass_width',
			[
				'label' => __( 'Width', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px',  ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-threesixty-rotation-wrapper .ha-img-magnifier-glass' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'glass_border',
				'label' => __('Border', 'happy-elementor-addons'),
				'selector' => '{{WRAPPER}} .ha-threesixty-rotation-wrapper .ha-img-magnifier-glass',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'glass_box_shadow',
				'label' => __('Box Shadow', 'happy-elementor-addons'),
				'selector' => '{{WRAPPER}} .ha-threesixty-rotation-wrapper .ha-img-magnifier-glass',
			]
		);

		$this->end_controls_section();
	}

	protected function __autoplay_btn_style_controls() {

		$this->start_controls_section(
			'_style_threesixty_rotation_button',
			[
				'label' => __('AutoPlay Button', 'happy-elementor-addons'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'auto_play' => 'button',
				]
			]
		);

		$this->start_controls_tabs('_tabs_button');

		$this->start_controls_tab(
			'_tab_button_normal',
			[
				'label' => __('Normal', 'happy-elementor-addons'),
			]
		);

		$this->add_control(
			'button_color',
			[
				'label' => __('Title Color', 'happy-elementor-addons'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-threesixty-rotation-wrapper  button.ha-threesixty-rotation-play' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_background',
				'label' => __('Background', 'happy-elementor-addons'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ha-threesixty-rotation-wrapper button.ha-threesixty-rotation-play',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_button_hover',
			[
				'label' => __('Hover', 'happy-elementor-addons'),
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label' => __('Title Color', 'happy-elementor-addons'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-threesixty-rotation-wrapper  button.ha-threesixty-rotation-play:hover, {{WRAPPER}} .ha-threesixty-rotation-wrapper  button.ha-threesixty-rotation-play:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_hover_background',
				'label' => __('Background', 'happy-elementor-addons'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ha-threesixty-rotation-wrapper button.ha-threesixty-rotation-play:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'button_border',
				'label' => __('Border', 'happy-elementor-addons'),
				'selector' => '{{WRAPPER}} .ha-threesixty-rotation-wrapper button.ha-threesixty-rotation-play',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label' => __('Border Radius', 'happy-elementor-addons'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ha-threesixty-rotation-wrapper button.ha-threesixty-rotation-play' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'label' => __('Box Shadow', 'happy-elementor-addons'),
				'selector' => '{{WRAPPER}} .ha-threesixty-rotation-wrapper button.ha-threesixty-rotation-play',
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label' => __('Padding', 'happy-elementor-addons'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ha-threesixty-rotation-wrapper button.ha-threesixty-rotation-play' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->add_control(
			'button_space_top',
			[
				'label' => __( 'Space Top', 'happy-elementor-addons'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-threesixty-rotation-wrapper button.ha-threesixty-rotation-play' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {

		$settings = $this->get_settings_for_display();
		if ( empty( $settings['images'] ) ) {
			return;
		}
		$this->add_render_attribute('wrapper', 'class', 'ha-threesixty-rotation-wrapper');
		$this->add_render_attribute(
			'rotation',
			[
				'class' => 'ha-threesixty-rotation-inner',
				'id' => 'ha-threesixty-rotation' . $this->get_id(),
				'data-selector' => 'ha-threesixty-rotation' . $this->get_id()
			]
		);
		if('autoplay' === $settings['auto_play'] ){
			$this->add_render_attribute('rotation', 'data-autoplay', 'on');
		}
		if ( 'yes' === $settings['magnify'] ) {
			$this->add_render_attribute(
				'glass',
				[
					'class' => 'ha-threesixty-rotation-magnify',
					'data-zoom' => esc_html($settings['zoom'])
				]
			);
		}
		$svg_url = HAPPY_ADDONS_ASSETS . 'imgs/360_view.svg';
		?>

		<div <?php $this->print_render_attribute_string('wrapper'); ?>>
			<div <?php $this->print_render_attribute_string('rotation'); ?>>
				<?php if ('yes' === $settings['magnify']): ?>
					<span <?php $this->print_render_attribute_string('glass'); ?>>
						<i class="fas fa-search"></i>
					</span>
				<?php endif; ?>
				<?php foreach ($settings['images'] as $item) : ?>
					<img data-src="<?php echo esc_url($item['url']); ?>">
				<?php endforeach; ?>
				<div class="ha-threesixty-rotation-360img" style='background-image:url("<?php echo esc_url($svg_url);?>")'></div>
			</div>
			<?php if ('autoplay' === $settings['auto_play'] ) : ?>
				<button class="ha-threesixty-rotation-autoplay"></button>
			<?php endif; ?>
			<?php if ('button' === $settings['auto_play'] ) : ?>
			<div class="ha-threesixty-rotation-autoplay-button">
				<button class="ha-threesixty-rotation-play">
					<i aria-hidden="true" class="hm hm-play-button"></i>
				</button>
			</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
