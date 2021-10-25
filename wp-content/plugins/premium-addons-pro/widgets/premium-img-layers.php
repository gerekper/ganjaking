<?php
/**
 * Class: Premium_Img_Layers
 * Name: Image Layers
 * Slug: premium-img-layers-addon
 */

namespace PremiumAddonsPro\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Premium_Img_Layers
 */
class Premium_Img_Layers extends Widget_Base {

	/**
	 * Get Elementor Helper Instance.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function getTemplateInstance() {
		$this->template_instance = Premium_Template_Tags::getInstance();
		return $this->template_instance;
	}

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-img-layers-addon';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return sprintf( '%1$s %2$s', Helper_Functions::get_prefix(), __( 'Image Layers', 'premium-addons-pro' ) );
	}

	/**
	 * Retrieve Widget Icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string widget icon.
	 */
	public function get_icon() {
		return 'pa-pro-image-layers';
	}

	/**
	 * Retrieve Widget Categories.
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'premium-elements' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return array( 'float', 'parallax', 'mouse', 'interactive', 'advanced' );
	}

	/**
	 * Retrieve Widget Dependent CSS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array CSS script handles.
	 */
	public function get_style_depends() {
		return array(
			'premium-addons',
		);
	}

	/**
	 * Retrieve Widget Dependent JS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array JS script handles.
	 */
	public function get_script_depends() {
		return array(
			'pa-tweenmax',
			'tilt-js',
			'elementor-waypoints',
			'pa-anime',
			'lottie-js',
			'premium-pro',
		);
	}

	/**
	 * Widget preview refresh button.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Retrieve Widget Support URL.
	 *
	 * @access public
	 *
	 * @return string support URL.
	 */
	public function get_custom_help_url() {
		return 'https://www.youtube.com/watch?v=D3INxWw_jKI&list=PLLpZVOYpMtTArB4hrlpSnDJB36D2sdoTv';
	}

	/**
	 * Register Image Comparison controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'premium_img_layers_content',
			array(
				'label' => __( 'Layers', 'premium-addons-pro' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'media_type',
			array(
				'label'   => __( 'Media Type', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'image'     => __( 'Image', 'premium-addons-pro' ),
					'animation' => __( 'Lottie Animation', 'premium-addons-pro' ),
					'text'      => __( 'Text', 'premium-addons-pro' ),
				),
				'default' => 'image',
			)
		);

		$repeater->add_control(
			'premium_img_layers_image',
			array(
				'label'     => __( 'Upload Image', 'premium-addons-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => array( 'active' => true ),
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'media_type' => 'image',
				),
			)
		);

		$repeater->add_control(
			'lottie_url',
			array(
				'label'       => __( 'Animation JSON URL', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'condition'   => array(
					'media_type' => 'animation',
				),
			)
		);

		$repeater->add_control(
			'lottie_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'condition'    => array(
					'media_type'  => 'animation',
					'lottie_url!' => '',
				),
			)
		);

		$repeater->add_control(
			'lottie_reverse',
			array(
				'label'        => __( 'Reverse', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array(
					'media_type'  => 'animation',
					'lottie_url!' => '',
				),
			)
		);

		$repeater->add_control(
			'lottie_hover',
			array(
				'label'        => __( 'Only Play on Hover', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array(
					'media_type'  => 'animation',
					'lottie_url!' => '',
				),
			)
		);

		$repeater->add_control(
			'lottie_renderer',
			array(
				'label'       => __( 'Render As', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'svg'    => __( 'SVG', 'premium-addons-pro' ),
					'canvas' => __( 'Canvas', 'premium-addons-pro' ),
				),
				'default'     => 'svg',
				'render_type' => 'template',
				'label_block' => true,
				'condition'   => array(
					'media_type'  => 'animation',
					'lottie_url!' => '',
				),
			)
		);

		$repeater->add_control(
			'render_notice',
			array(
				'raw'             => __( 'Set render type to canvas if you\'re having performance issues on the page.', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'media_type'  => 'animation',
					'lottie_url!' => '',
				),
			)
		);

		$repeater->add_control(
			'img_layer_text',
			array(
				'label'     => __( 'Text', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'I\'m a Title', 'premium-addons-pro' ),
				'dynamic'   => array( 'active' => true ),
				'condition' => array(
					'media_type' => 'text',
				),
			)
		);

		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'thumbnail',
				'default'   => 'full',
				'condition' => array(
					'media_type' => 'image',
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_position',
			array(
				'label'   => __( 'Position', 'premium-addons-pro' ),
				'type'    => Controls_Manager::HIDDEN,
				'options' => array(
					'relative' => __( 'Relative', 'premium-addons-pro' ),
					'absolute' => __( 'Absolute', 'premium-addons-pro' ),
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_img_layers_hor_position',
			array(
				'label'       => __( 'Horizontal Offset', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'Mousemove Interactivity works only with pixels', 'premium-addons-pro' ),
				'size_units'  => array( 'px', '%' ),
				'range'       => array(
					'px' => array(
						'min' => -200,
						'max' => 300,
					),
					'%'  => array(
						'min' => -50,
						'max' => 100,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}.absolute' => 'left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_img_layers_ver_position',
			array(
				'label'       => __( 'Vertical Offset', 'premium-addons-pro' ),
				'description' => __( 'Mousemove Interactivity works only with pixels', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%' ),
				'range'       => array(
					'px' => array(
						'min' => -200,
						'max' => 300,
					),
					'%'  => array(
						'min' => -50,
						'max' => 100,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}.absolute' => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_img_layers_width',
			array(
				'label'      => __( 'Width', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array(
						'max'  => 1000,
						'step' => 1,
					),
					'%'  => array(
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'width: {{SIZE}}{{UNIT}};',
				),
				'separator'  => 'after',
			)
		);

		$repeater->add_control(
			'text_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .premium-img-layers-text' => 'color:{{VALUE}}',
				),
				'condition' => array(
					'media_type' => 'text',
				),
			)
		);

		$repeater->add_control(
			'text_color_hover',
			array(
				'label'     => __( 'Text Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}:hover .premium-img-layers-text'  => 'color:{{VALUE}}',
				),
				'condition' => array(
					'media_type' => 'text',
				),
			)
		);

		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'text_typography',
				'scheme'    => Typography::TYPOGRAPHY_1,
				'selector'  => '{{WRAPPER}} {{CURRENT_ITEM}} .premium-img-layers-text',
				'condition' => array(
					'media_type' => 'text',
				),
			)
		);

		$repeater->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'text_shadow',
				'selector'  => '{{WRAPPER}} {{CURRENT_ITEM}} .premium-img-layers-text',
				'condition' => array(
					'media_type' => 'text',
				),
			)
		);

		$repeater->add_control(
			'blend_mode',
			array(
				'label'     => __( 'Blend Mode', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''            => __( 'Normal', 'premium-addons-pro' ),
					'multiply'    => 'Multiply',
					'screen'      => 'Screen',
					'overlay'     => 'Overlay',
					'darken'      => 'Darken',
					'lighten'     => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation'  => 'Saturation',
					'color'       => 'Color',
					'luminosity'  => 'Luminosity',
				),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'mix-blend-mode: {{VALUE}}',
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_link_switcher',
			array(
				'label' => __( 'Link', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$repeater->add_control(
			'premium_img_layers_link_selection',
			array(
				'label'       => __( 'Link Type', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'url'  => __( 'URL', 'premium-addons-pro' ),
					'link' => __( 'Existing Page', 'premium-addons-pro' ),
				),
				'default'     => 'url',
				'label_block' => true,
				'condition'   => array(
					'premium_img_layers_link_switcher' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_link',
			array(
				'label'       => __( 'Link', 'premium-addons-pro' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array( 'active' => true ),
				'default'     => array(
					'url' => '#',
				),
				'placeholder' => 'https://premiumaddons.com/',
				'label_block' => true,
				'separator'   => 'after',
				'condition'   => array(
					'premium_img_layers_link_switcher'  => 'yes',
					'premium_img_layers_link_selection' => 'url',
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_existing_link',
			array(
				'label'       => __( 'Existing Page', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_all_posts(),
				'condition'   => array(
					'premium_img_layers_link_switcher'  => 'yes',
					'premium_img_layers_link_selection' => 'link',
				),
				'multiple'    => false,
				'separator'   => 'after',
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'premium_img_layers_rotate',
			array(
				'label' => __( 'Rotate', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$repeater->add_control(
			'premium_img_layers_rotatex',
			array(
				'label'       => __( 'Degrees', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Set rotation value in degrees', 'premium-addons-pro' ),
				'min'         => -180,
				'max'         => 180,
				'condition'   => array(
					'premium_img_layers_rotate' => 'yes',
				),
				'separator'   => 'after',
				'selectors'   => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '-webkit-transform: rotate({{VALUE}}deg); -moz-transform: rotate({{VALUE}}deg); -o-transform: rotate({{VALUE}}deg); transform: rotate({{VALUE}}deg);',
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_animation_switcher',
			array(
				'label' => __( 'Animation', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$repeater->add_control(
			'premium_img_layers_animation',
			array(
				'label'              => __( 'Entrance Animation', 'premium-addons-pro' ),
				'type'               => Controls_Manager::ANIMATION,
				'default'            => '',
				'label_block'        => true,
				'frontend_available' => true,
				'condition'          => array(
					'premium_img_layers_animation_switcher' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_animation_duration',
			array(
				'label'     => __( 'Animation Duration', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array(
					'slow' => __( 'Slow', 'premium-addons-pro' ),
					''     => __( 'Normal', 'premium-addons-pro' ),
					'fast' => __( 'Fast', 'premium-addons-pro' ),
				),
				'condition' => array(
					'premium_img_layers_animation_switcher' => 'yes',
					'premium_img_layers_animation!' => '',
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_animation_delay',
			array(
				'label'              => __( 'Animation Delay', 'premium-addons-pro' ) . ' (s)',
				'type'               => Controls_Manager::NUMBER,
				'default'            => '',
				'min'                => 0,
				'step'               => 0.1,
				'condition'          => array(
					'premium_img_layers_animation_switcher' => 'yes',
					'premium_img_layers_animation!' => '',
				),
				'frontend_available' => true,
				'selectors'          => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}.animated' => '-webkit-animation-delay:{{VALUE}}s; -moz-animation-delay: {{VALUE}}s; -o-animation-delay: {{VALUE}}s; animation-delay: {{VALUE}}s;',
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_mouse',
			array(
				'label'       => __( 'Mousemove Interactivity', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable or disable mousemove interaction', 'premium-addons-pro' ),
			)
		);

		$repeater->add_control(
			'premium_img_layers_mouse_type',
			array(
				'label'       => __( 'Interactivity Style', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'parallax' => __( 'Mouse Parallax', 'premium-addons-pro' ),
					'tilt'     => __( 'Tilt', 'premium-addons-pro' ),
				),
				'default'     => 'parallax',
				'label_block' => true,
				'condition'   => array(
					'premium_img_layers_mouse' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_mouse_reverse',
			array(
				'label'     => __( 'Reverse Direction', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_img_layers_mouse'      => 'yes',
					'premium_img_layers_mouse_type' => 'parallax',
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_mouse_initial',
			array(
				'label'       => __( 'Back To Initial Position', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable this to get back to initial position when mouse leaves the widget.', 'premium-addons-pro' ),
				'condition'   => array(
					'premium_img_layers_mouse'      => 'yes',
					'premium_img_layers_mouse_type' => 'parallax',
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_rate',
			array(
				'label'       => __( 'Rate', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => -10,
				'min'         => -20,
				'max'         => 20,
				'step'        => 1,
				'description' => __( 'Choose the movement rate for the layer image, default: -10', 'premium-addons-pro' ),
				'separator'   => 'after',
				'condition'   => array(
					'premium_img_layers_mouse'      => 'yes',
					'premium_img_layers_mouse_type' => 'parallax',
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_scroll_effects',
			array(
				'label'     => __( 'Scroll Effects', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_img_layers_float_effects!' => 'yes',
				),
			)
		);

		$conditions = array(
			'premium_img_layers_scroll_effects' => 'yes',
		);

		$repeater->add_control(
			'premium_img_layers_opacity',
			array(
				'label'     => __( 'Scroll Fade', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => $conditions,
			)
		);

		$repeater->add_control(
			'premium_img_layers_opacity_effect',
			array(
				'label'     => __( 'Direction', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'down' => __( 'Fade In', 'premium-addons-pro' ),
					'up'   => __( 'Fade Out', 'premium-addons-pro' ),
				),
				'default'   => 'down',
				'condition' => array_merge(
					$conditions,
					array(
						'premium_img_layers_opacity' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_opacity_level',
			array(
				'label'     => __( 'Opacity Level', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 10,
				),
				'range'     => array(
					'px' => array(
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'condition' => array_merge(
					$conditions,
					array(
						'premium_img_layers_opacity' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_opacity_view',
			array(
				'label'     => __( 'Viewport', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'start' => 0,
						'end'   => 100,
					),
					'unit'  => '%',
				),
				'labels'    => array(
					__( 'Bottom', 'premium-addons-pro' ),
					__( 'Top', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array_merge(
					$conditions,
					array(
						'premium_img_layers_opacity' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_vscroll',
			array(
				'label'     => __( 'Vertical Parallax', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => $conditions,
			)
		);

		$repeater->add_control(
			'premium_img_layers_vscroll_direction',
			array(
				'label'     => __( 'Direction', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'up'   => __( 'Up', 'premium-addons-pro' ),
					'down' => __( 'Down', 'premium-addons-pro' ),
				),
				'default'   => 'down',
				'condition' => array_merge(
					$conditions,
					array(
						'premium_img_layers_vscroll' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_vscroll_speed',
			array(
				'label'     => __( 'Speed', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 4,
				),
				'range'     => array(
					'px' => array(
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'condition' => array_merge(
					$conditions,
					array(
						'premium_img_layers_vscroll' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_vscroll_view',
			array(
				'label'     => __( 'Viewport', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'start' => 0,
						'end'   => 100,
					),
					'unit'  => '%',
				),
				'labels'    => array(
					__( 'Bottom', 'premium-addons-pro' ),
					__( 'Top', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array_merge(
					$conditions,
					array(
						'premium_img_layers_vscroll' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_hscroll',
			array(
				'label'     => __( 'Horizontal Parallax', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => $conditions,
			)
		);

		$repeater->add_control(
			'premium_img_layers_hscroll_direction',
			array(
				'label'     => __( 'Direction', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'up'   => __( 'To Left', 'premium-addons-pro' ),
					'down' => __( 'To Right', 'premium-addons-pro' ),
				),
				'default'   => 'down',
				'condition' => array_merge(
					$conditions,
					array(
						'premium_img_layers_hscroll' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_hscroll_speed',
			array(
				'label'     => __( 'Speed', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 4,
				),
				'range'     => array(
					'px' => array(
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'condition' => array_merge(
					$conditions,
					array(
						'premium_img_layers_hscroll' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_hscroll_view',
			array(
				'label'     => __( 'Viewport', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'start' => 0,
						'end'   => 100,
					),
					'unit'  => '%',
				),
				'labels'    => array(
					__( 'Bottom', 'premium-addons-pro' ),
					__( 'Top', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array_merge(
					$conditions,
					array(
						'premium_img_layers_hscroll' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_blur',
			array(
				'label'     => __( 'Blur', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => $conditions,
			)
		);

		$repeater->add_control(
			'premium_img_layers_blur_effect',
			array(
				'label'     => __( 'Direction', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'down' => __( 'Decrease Blur', 'premium-addons-pro' ),
					'up'   => __( 'Increase Blur', 'premium-addons-pro' ),
				),
				'default'   => 'down',
				'condition' => array_merge(
					$conditions,
					array(
						'premium_img_layers_blur' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_blur_level',
			array(
				'label'     => __( 'Blur Level', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 10,
				),
				'range'     => array(
					'px' => array(
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'condition' => array_merge(
					$conditions,
					array(
						'premium_img_layers_blur' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_blur_view',
			array(
				'label'     => __( 'Viewport', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'start' => 0,
						'end'   => 100,
					),
					'unit'  => '%',
				),
				'labels'    => array(
					__( 'Bottom', 'premium-addons-pro' ),
					__( 'Top', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array_merge(
					$conditions,
					array(
						'premium_img_layers_blur' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_rscroll',
			array(
				'label'     => __( 'Rotate', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => $conditions,
			)
		);

		$repeater->add_control(
			'premium_img_layers_rscroll_direction',
			array(
				'label'     => __( 'Direction', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'up'   => __( 'Counter Clockwise', 'premium-addons-pro' ),
					'down' => __( 'Clockwise', 'premium-addons-pro' ),
				),
				'default'   => 'down',
				'condition' => array_merge(
					$conditions,
					array(
						'premium_img_layers_rscroll' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_rscroll_speed',
			array(
				'label'     => __( 'Speed', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 3,
				),
				'range'     => array(
					'px' => array(
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'condition' => array_merge(
					$conditions,
					array(
						'premium_img_layers_rscroll' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_rscroll_view',
			array(
				'label'     => __( 'Viewport', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'start' => 0,
						'end'   => 100,
					),
					'unit'  => '%',
				),
				'labels'    => array(
					__( 'Bottom', 'premium-addons-pro' ),
					__( 'Top', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array_merge(
					$conditions,
					array(
						'premium_img_layers_rscroll' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_scale',
			array(
				'label'     => __( 'Scale', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => $conditions,
			)
		);

		$repeater->add_control(
			'premium_img_layers_scale_direction',
			array(
				'label'     => __( 'Scale', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'up'   => __( 'Shrink', 'premium-addons-pro' ),
					'down' => __( 'Scale', 'premium-addons-pro' ),
				),
				'default'   => 'down',
				'condition' => array_merge(
					$conditions,
					array(
						'premium_img_layers_scale' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_scale_speed',
			array(
				'label'     => __( 'Speed', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 3,
				),
				'range'     => array(
					'px' => array(
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'condition' => array_merge(
					$conditions,
					array(
						'premium_img_layers_scale' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_scale_view',
			array(
				'label'     => __( 'Viewport', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'start' => 0,
						'end'   => 100,
					),
					'unit'  => '%',
				),
				'labels'    => array(
					__( 'Bottom', 'premium-addons-pro' ),
					__( 'Top', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array_merge(
					$conditions,
					array(
						'premium_img_layers_scale' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_gray',
			array(
				'label'     => __( 'Gray Scale', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => $conditions,
			)
		);

		$repeater->add_control(
			'premium_img_layers_gray_effect',
			array(
				'label'     => __( 'Effect', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'up'   => __( 'Increase', 'premium-addons-pro' ),
					'down' => __( 'Decrease', 'premium-addons-pro' ),
				),
				'default'   => 'down',
				'condition' => array_merge(
					$conditions,
					array(
						'premium_img_layers_gray' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_gray_level',
			array(
				'label'     => __( 'Speed', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 10,
				),
				'range'     => array(
					'px' => array(
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'condition' => array_merge(
					$conditions,
					array(
						'premium_img_layers_gray' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_gray_view',
			array(
				'label'     => __( 'Viewport', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'start' => 0,
						'end'   => 100,
					),
					'unit'  => '%',
				),
				'labels'    => array(
					__( 'Bottom', 'premium-addons-pro' ),
					__( 'Top', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array_merge(
					$conditions,
					array(
						'premium_img_layers_gray' => 'yes',
					)
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_img_layerstransform_origin_x',
			array(
				'label'       => __( 'X Anchor Point', 'premium-addons-pro' ),
				'type'        => Controls_Manager::CHOOSE,
				'default'     => 'center',
				'options'     => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-h-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'conditions'  => array(
					'terms' => array(
						array(
							'name'  => 'premium_img_layers_scroll_effects',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'premium_img_layers_rscroll',
									'value' => 'yes',
								),
								array(
									'name'  => 'premium_img_layers_scale',
									'value' => 'yes',
								),
							),
						),
					),
				),
				'label_block' => false,
				'toggle'      => false,
				'render_type' => 'ui',
			)
		);

		$repeater->add_responsive_control(
			'premium_img_layerstransform_origin_y',
			array(
				'label'       => __( 'Y Anchor Point', 'premium-addons-pro' ),
				'type'        => Controls_Manager::CHOOSE,
				'default'     => 'center',
				'options'     => array(
					'top'    => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'conditions'  => array(
					'terms' => array(
						array(
							'name'  => 'premium_img_layers_scroll_effects',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'premium_img_layers_rscroll',
									'value' => 'yes',
								),
								array(
									'name'  => 'premium_img_layers_scale',
									'value' => 'yes',
								),
							),
						),
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}.premium-img-layers-list-item' => 'transform-origin: {{premium_img_layerstransform_origin_x.VALUE}} {{VALUE}}',
				),
				'label_block' => false,
				'toggle'      => false,
			)
		);

		$repeater->add_control(
			'premium_img_layers_float_effects',
			array(
				'label'     => __( 'Floating Effects', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_img_layers_scroll_effects!' => 'yes',
				),
			)
		);

		$float_conditions = array(
			'premium_img_layers_float_effects' => 'yes',
		);

		$repeater->add_control(
			'premium_img_layers_translate_float',
			array(
				'label'              => __( 'Translate', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition'          => $float_conditions,
			)
		);

		$repeater->add_control(
			'premium_img_layers_translatex',
			array(
				'label'     => __( 'Translate X', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'start' => -5,
						'end'   => 5,
					),
					'unit'  => 'px',
				),
				'range'     => array(
					'px' => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'labels'    => array(
					__( 'From', 'premium-addons-pro' ),
					__( 'To', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array_merge(
					$float_conditions,
					array(
						'premium_img_layers_translate_float' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_translatey',
			array(
				'label'     => __( 'Translate Y', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'start' => -5,
						'end'   => 5,
					),
					'unit'  => 'px',
				),
				'range'     => array(
					'px' => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'labels'    => array(
					__( 'From', 'premium-addons-pro' ),
					__( 'To', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array_merge(
					$float_conditions,
					array(
						'premium_img_layers_translate_float' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_translate_speed',
			array(
				'label'     => __( 'Speed', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'default'   => array(
					'size' => 1,
				),
				'condition' => array_merge(
					$float_conditions,
					array(
						'premium_img_layers_translate_float' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_translate_rotate',
			array(
				'label'              => __( 'Rotate', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition'          => $float_conditions,
			)
		);

		$repeater->add_control(
			'premium_img_layers_float_rotatex',
			array(
				'label'     => __( 'Rotate X', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'start' => 0,
						'end'   => 45,
					),
					'unit'  => 'px',
				),
				'range'     => array(
					'px' => array(
						'min' => -180,
						'max' => 180,
					),
				),
				'labels'    => array(
					__( 'From', 'premium-addons-pro' ),
					__( 'To', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array_merge(
					$float_conditions,
					array(
						'premium_img_layers_translate_rotate' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_float_rotatey',
			array(
				'label'     => __( 'Rotate Y', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'start' => 0,
						'end'   => 45,
					),
					'unit'  => 'px',
				),
				'range'     => array(
					'px' => array(
						'min' => -180,
						'max' => 180,
					),
				),
				'labels'    => array(
					__( 'From', 'premium-addons-pro' ),
					__( 'To', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array_merge(
					$float_conditions,
					array(
						'premium_img_layers_translate_rotate' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_float_rotatez',
			array(
				'label'     => __( 'Rotate Z', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'start' => 0,
						'end'   => 45,
					),
					'unit'  => 'px',
				),
				'range'     => array(
					'px' => array(
						'min' => -180,
						'max' => 180,
					),
				),
				'labels'    => array(
					__( 'From', 'premium-addons-pro' ),
					__( 'To', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array_merge(
					$float_conditions,
					array(
						'premium_img_layers_translate_rotate' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_rotate_speed',
			array(
				'label'     => __( 'Speed', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'default'   => array(
					'size' => 1,
				),
				'condition' => array_merge(
					$float_conditions,
					array(
						'premium_img_layers_translate_rotate' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_opacity_float',
			array(
				'label'              => __( 'Opacity', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition'          => $float_conditions,
			)
		);

		$repeater->add_control(
			'premium_img_layers_opacity_value',
			array(
				'label'     => __( 'Value', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					),
				),
				'default'   => array(
					'size' => 0.5,
				),
				'condition' => array_merge(
					$float_conditions,
					array(
						'premium_img_layers_opacity_float' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_opacity_speed',
			array(
				'label'     => __( 'Speed', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'default'   => array(
					'size' => 1,
				),
				'condition' => array_merge(
					$float_conditions,
					array(
						'premium_img_layers_opacity_float' => 'yes',
					)
				),
			)
		);

		$repeater->add_control(
			'mask_switcher',
			array(
				'label'        => __( 'Minimal Mask Effect', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => __( 'Note: This effect takes place once the element is in the viewport', 'premium-addons-for-elementor' ),
				'render_type'  => 'template',
				'prefix_class' => 'premium-mask-',
				'condition'    => array(
					'media_type' => 'text',
				),
			)
		);

		$repeater->add_control(
			'mask_color',
			array(
				'label'       => __( 'Mask Color', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::COLOR,
				'render_type' => 'template',
				'selectors'   => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}.premium-mask-yes .premium-mask-span::after'   => 'background: {{VALUE}};',
				),
				'condition'   => array(
					'media_type'    => 'text',
					'mask_switcher' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'mask_dir',
			array(
				'label'       => __( 'Direction', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'tr',
				'render_type' => 'template',
				'options'     => array(
					'tr' => __( 'To Right', 'premium-addons-for-elementor' ),
					'tl' => __( 'To Left', 'premium-addons-for-elementor' ),
					'tt' => __( 'To Top', 'premium-addons-for-elementor' ),
					'tb' => __( 'To Bottom', 'premium-addons-for-elementor' ),
				),
				'condition'   => array(
					'media_type'    => 'text',
					'mask_switcher' => 'yes',
				),
			)
		);

		$repeater->add_responsive_control(
			'mask_padding',
			array(
				'label'      => __( 'Words Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .premium-mask-span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'media_type'    => 'text',
					'mask_switcher' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_zindex',
			array(
				'label'     => __( 'z-index', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}.premium-img-layers-list-item' => 'z-index: {{VALUE}};',
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_class',
			array(
				'label'       => __( 'CSS Classes', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Separate class with spaces', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_img_layers_images_repeater',
			array(
				'type'   => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
			)
		);

		$this->add_control(
			'premium_parallax_layers_devices',
			array(
				'label'       => __( 'Apply Scroll Effects On', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => array(
					'desktop' => __( 'Desktop', 'premium-addons-pro' ),
					'tablet'  => __( 'Tablet', 'premium-addons-pro' ),
					'mobile'  => __( 'Mobile', 'premium-addons-pro' ),
				),
				'default'     => array( 'desktop', 'tablet', 'mobile' ),
				'multiple'    => true,
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_img_layers_container',
			array(
				'label' => __( 'Container', 'premium-addons-pro' ),
			)
		);

		$this->add_responsive_control(
			'premium_img_layers_height',
			array(
				'label'      => __( 'Minimum Height', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'vh' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 800,
					),
					'em' => array(
						'min' => 1,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-img-layers-wrapper' => 'min-height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_img_layers_overflow',
			array(
				'label'     => __( 'Overflow', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'auto'    => __( 'Auto', 'premium-addons-pro' ),
					'visible' => __( 'Visible', 'premium-addons-pro' ),
					'hidden'  => __( 'Hidden', 'premium-addons-pro' ),
					'scroll'  => __( 'Scroll', 'premium-addons-pro' ),
				),
				'default'   => 'visible',
				'selectors' => array(
					'{{WRAPPER}} .premium-img-layers-wrapper'   => 'overflow: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-pro' ),
			)
		);

		$docs = array(
			'https://premiumaddons.com/docs/premium-image-layers-widget/' => __( 'Getting started »', 'premium-addons-pro' ),
			'https://premiumaddons.com/docs/how-to-speed-up-elementor-pages-with-many-lottie-animations/' => __( 'How to speed up pages with many Lottie animations »', 'premium-addons-pro' ),
			'https://premiumaddons.com/docs/customize-elementor-lottie-widget/' => __( 'How to Customize Lottie Animations »', 'premium-addons-pro' ),
		);

		$doc_index = 1;
		foreach ( $docs as $url => $title ) {

			$doc_url = Helper_Functions::get_campaign_link( $url, 'editor-page', 'wp-editor', 'get-support' );

			$this->add_control(
				'doc_' . $doc_index,
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc_url, $title ),
					'content_classes' => 'editor-pa-doc',
				)
			);

			$doc_index++;

		}

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_img_layers_images_style',
			array(
				'label' => __( 'Image', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_img_layers_images_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-img-layers-list-item .premium-img-layers-image'  => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_img_layers_images_border',
				'selector' => '{{WRAPPER}} .premium-img-layers-list-item .premium-img-layers-image',
			)
		);

		$this->add_responsive_control(
			'premium_img_layers_images_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-img-layers-list-item .premium-img-layers-image' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-top-right-radius: {{RIGHT}}{{UNIT}}; border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_img_layers_images_shadow',
				'selector' => '{{WRAPPER}} .premium-img-layers-list-item .premium-img-layers-image',
			)
		);

		$this->add_responsive_control(
			'premium_img_layers_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-img-layers-list-item .premium-img-layers-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_img_layers_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-img-layers-list-item .premium-img-layers-image' => 'padding:  {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_img_layers_container_style',
			array(
				'label' => __( 'Container', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_img_layers_container_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-img-layers-wrapper'  => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_img_layers_container_border',
				'selector' => '{{WRAPPER}} .premium-img-layers-wrapper',
			)
		);

		$this->add_responsive_control(
			'premium_img_layers_container_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-img-layers-wrapper' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-top-right-radius: {{RIGHT}}{{UNIT}}; border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_img_layers_container_shadow',
				'selector' => '{{WRAPPER}} .premium-img-layers-wrapper',
			)
		);

		$this->add_responsive_control(
			'premium_img_layers_container_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-img-layers-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_img_layers_container_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-img-layers-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render Image Layers widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute(
			'container',
			array(
				'id'    => 'premium-img-layers-wrapper',
				'class' => 'premium-img-layers-wrapper',
			)
		);

		$scroll_effects = isset( $settings['premium_parallax_layers_devices'] ) ? $settings['premium_parallax_layers_devices'] : array();

		$this->add_render_attribute( 'container', 'data-devices', wp_json_encode( $scroll_effects ) );

		?>

	<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'container' ) ); ?>>
		<ul class="premium-img-layers-list-wrapper">
			<?php
			$animation_arr = array();
			foreach ( $settings['premium_img_layers_images_repeater'] as $index => $image ) :
				array_push( $animation_arr, $image['premium_img_layers_animation_switcher'] );
				if ( 'yes' === $animation_arr[ $index ] ) {
					$animation_class = $image['premium_img_layers_animation'];
					if ( '' !== $image['premium_img_layers_animation_duration'] ) {
						$animation_dur = 'animated-' . $image['premium_img_layers_animation_duration'];
					} else {
						$animation_dur = 'animated-';
					}
				} else {
						$animation_class = '';
						$animation_dur   = '';
				}

				$list_item_key = 'img_layer_' . $index;

				$position = ! empty( $image['premium_img_layers_position'] ) ? $image['premium_img_layers_position'] : 'absolute';

				$this->add_render_attribute(
					$list_item_key,
					'class',
					array(
						'premium-img-layers-list-item',
						$position,
						esc_attr( $image['premium_img_layers_class'] ),
						'elementor-repeater-item-' . $image['_id'],
					)
				);

				$this->add_render_attribute(
					$list_item_key,
					'data-layer-animation',
					array(
						$animation_class,
						$animation_dur,
					)
				);

				if ( 'yes' === $image['premium_img_layers_float_effects'] ) {

					$this->add_render_attribute( $list_item_key, 'data-float', 'true' );

					if ( 'yes' === $image['premium_img_layers_translate_float'] ) {

						$this->add_render_attribute(
							$list_item_key,
							array(
								'data-float-translate' => 'true',
								'data-floatx-start'    => $image['premium_img_layers_translatex']['sizes']['start'],
								'data-floatx-end'      => $image['premium_img_layers_translatex']['sizes']['end'],
								'data-floaty-start'    => $image['premium_img_layers_translatey']['sizes']['start'],
								'data-floaty-end'      => $image['premium_img_layers_translatey']['sizes']['end'],
								'data-float-translate-speed' => $image['premium_img_layers_translate_speed']['size'],
							)
						);

					}

					if ( 'yes' === $image['premium_img_layers_translate_rotate'] ) {

						$this->add_render_attribute(
							$list_item_key,
							array(
								'data-float-rotate'       => 'true',
								'data-rotatex-start'      => $image['premium_img_layers_float_rotatex']['sizes']['start'],
								'data-rotatex-end'        => $image['premium_img_layers_float_rotatex']['sizes']['end'],
								'data-rotatey-start'      => $image['premium_img_layers_float_rotatey']['sizes']['start'],
								'data-rotatey-end'        => $image['premium_img_layers_float_rotatey']['sizes']['end'],
								'data-rotatez-start'      => $image['premium_img_layers_float_rotatez']['sizes']['start'],
								'data-rotatez-end'        => $image['premium_img_layers_float_rotatez']['sizes']['end'],
								'data-float-rotate-speed' => $image['premium_img_layers_rotate_speed']['size'],
							)
						);

					}

					if ( 'yes' === $image['premium_img_layers_opacity_float'] ) {

						$this->add_render_attribute(
							$list_item_key,
							array(
								'data-float-opacity'       => 'true',
								'data-float-opacity-value' => $image['premium_img_layers_opacity_value']['size'],
								'data-float-opacity-speed' => $image['premium_img_layers_opacity_speed']['size'],
							)
						);

					}
				} elseif ( 'yes' === $image['premium_img_layers_scroll_effects'] ) {

					$this->add_render_attribute( $list_item_key, 'data-scrolls', 'true' );

					if ( 'yes' === $image['premium_img_layers_vscroll'] ) {

						$speed = ! empty( $image['premium_img_layers_vscroll_speed']['size'] ) ? $image['premium_img_layers_vscroll_speed']['size'] : 4;

						$this->add_render_attribute(
							$list_item_key,
							array(
								'data-vscroll'       => 'true',
								'data-vscroll-speed' => $speed,
								'data-vscroll-dir'   => $image['premium_img_layers_vscroll_direction'],
								'data-vscroll-start' => $image['premium_img_layers_vscroll_view']['sizes']['start'],
								'data-vscroll-end'   => $image['premium_img_layers_vscroll_view']['sizes']['end'],
							)
						);

					}

					if ( 'yes' === $image['premium_img_layers_hscroll'] ) {

						$speed = ! empty( $image['premium_img_layers_hscroll_speed']['size'] ) ? $image['premium_img_layers_hscroll_speed']['size'] : 4;

						$this->add_render_attribute(
							$list_item_key,
							array(
								'data-hscroll'       => 'true',
								'data-hscroll-speed' => $speed,
								'data-hscroll-dir'   => $image['premium_img_layers_hscroll_direction'],
								'data-hscroll-start' => $image['premium_img_layers_hscroll_view']['sizes']['start'],
								'data-hscroll-end'   => $image['premium_img_layers_hscroll_view']['sizes']['end'],
							)
						);

					}

					if ( 'yes' === $image['premium_img_layers_opacity'] ) {

						$level = ! empty( $image['premium_img_layers_opacity_level']['size'] ) ? $image['premium_img_layers_opacity_level']['size'] : 10;

						$this->add_render_attribute(
							$list_item_key,
							array(
								'data-oscroll'        => 'true',
								'data-oscroll-level'  => $level,
								'data-oscroll-effect' => $image['premium_img_layers_opacity_effect'],
								'data-oscroll-start'  => $image['premium_img_layers_opacity_view']['sizes']['start'],
								'data-oscroll-end'    => $image['premium_img_layers_opacity_view']['sizes']['end'],
							)
						);

					}

					if ( 'yes' === $image['premium_img_layers_blur'] ) {

						$level = ! empty( $image['premium_img_layers_blur_level']['size'] ) ? $image['premium_img_layers_blur_level']['size'] : 10;

						$this->add_render_attribute(
							$list_item_key,
							array(
								'data-bscroll'        => 'true',
								'data-bscroll-level'  => $level,
								'data-bscroll-effect' => $image['premium_img_layers_blur_effect'],
								'data-bscroll-start'  => $image['premium_img_layers_blur_view']['sizes']['start'],
								'data-bscroll-end'    => $image['premium_img_layers_blur_view']['sizes']['end'],
							)
						);

					}

					if ( 'yes' === $image['premium_img_layers_rscroll'] ) {

						$speed = ! empty( $image['premium_img_layers_rscroll_speed']['size'] ) ? $image['premium_img_layers_rscroll_speed']['size'] : 3;

						$this->add_render_attribute(
							$list_item_key,
							array(
								'data-rscroll'       => 'true',
								'data-rscroll-speed' => $speed,
								'data-rscroll-dir'   => $image['premium_img_layers_rscroll_direction'],
								'data-rscroll-start' => $image['premium_img_layers_rscroll_view']['sizes']['start'],
								'data-rscroll-end'   => $image['premium_img_layers_rscroll_view']['sizes']['end'],
							)
						);

					}

					if ( 'yes' === $image['premium_img_layers_scale'] ) {

						$speed = ! empty( $image['premium_img_layers_scale_speed']['size'] ) ? $image['premium_img_layers_scale_speed']['size'] : 3;

						$this->add_render_attribute(
							$list_item_key,
							array(
								'data-scale'       => 'true',
								'data-scale-speed' => $speed,
								'data-scale-dir'   => $image['premium_img_layers_scale_direction'],
								'data-scale-start' => $image['premium_img_layers_scale_view']['sizes']['start'],
								'data-scale-end'   => $image['premium_img_layers_scale_view']['sizes']['end'],
							)
						);

					}

					if ( 'yes' === $image['premium_img_layers_gray'] ) {

						$level = ! empty( $image['premium_img_layers_gray_level']['size'] ) ? $image['premium_img_layers_gray_level']['size'] : 10;

						$this->add_render_attribute(
							$list_item_key,
							array(
								'data-gscale'        => 'true',
								'data-gscale-level'  => $level,
								'data-gscale-effect' => $image['premium_img_layers_gray_effect'],
								'data-gscale-start'  => $image['premium_img_layers_gray_view']['sizes']['start'],
								'data-gscale-end'    => $image['premium_img_layers_gray_view']['sizes']['end'],
							)
						);

					}
				}

				if ( 'yes' === $image['mask_switcher'] ) {
					$this->add_render_attribute( $list_item_key, 'class', 'premium-mask-yes' );
					$dir_class = 'premium-mask-' . $image['mask_dir'];
				}

				if ( 'yes' === $image['premium_img_layers_mouse'] ) {

					$this->add_render_attribute( $list_item_key, 'data-' . $image['premium_img_layers_mouse_type'], 'true' );

					if ( 'parallax' === $image['premium_img_layers_mouse_type'] ) {

						if ( 'yes' === $image['premium_img_layers_mouse_reverse'] ) {
							$this->add_render_attribute( $list_item_key, 'data-mparallax-reverse', 'true' );
						}

						if ( 'yes' === $image['premium_img_layers_mouse_initial'] ) {
							$this->add_render_attribute( $list_item_key, 'data-mparallax-init', 'true' );
						}
					}

					$this->add_render_attribute( $list_item_key, 'data-rate', ! empty( $image['premium_img_layers_rate'] ) ? $image['premium_img_layers_rate'] : -10 );

				}

				if ( 'url' === $image['premium_img_layers_link_selection'] ) {
					$image_url = $image['premium_img_layers_link']['url'];
				} else {
					$image_url = get_permalink( $image['premium_img_layers_existing_link'] );
				}

				$list_item_link = 'img_link_' . $index;
				if ( 'yes' === $image['premium_img_layers_link_switcher'] ) {
					$this->add_render_attribute( $list_item_link, 'class', 'premium-img-layers-link' );

					$this->add_render_attribute( $list_item_link, 'href', $image_url );

					if ( ! empty( $image['premium_img_layers_link']['is_external'] ) ) {
						$this->add_render_attribute( $list_item_link, 'target', '_blank' );
					}
					if ( ! empty( $image['premium_img_layers_link']['nofollow'] ) ) {
						$this->add_render_attribute( $list_item_link, 'rel', 'nofollow' );
					}
				}

				if ( 'animation' === $image['media_type'] ) {

					$this->add_render_attribute(
						$list_item_key,
						array(
							'class'               => 'premium-lottie-animation',
							'data-lottie-url'     => $image['lottie_url'],
							'data-lottie-loop'    => $image['lottie_loop'],
							'data-lottie-reverse' => $image['lottie_reverse'],
							'data-lottie-hover'   => $image['lottie_hover'],
							'data-lottie-render'  => $image['lottie_renderer'],
						)
					);

				}

				?>

				<li <?php echo wp_kses_post( $this->get_render_attribute_string( $list_item_key ) ); ?>>
					<?php
					if ( 'image' === $image['media_type'] ) {

						$image_src = $image['premium_img_layers_image'];

						$image_src_size = Group_Control_Image_Size::get_attachment_image_src( $image_src['id'], 'thumbnail', $image );

						if ( empty( $image_src_size ) ) {
							$image_src_size = $image_src['url'];
						} else {
							$image_src_size = $image_src_size;
						}

						$alt = Control_Media::get_image_alt( $image['premium_img_layers_image'] );
						?>
							<img src="<?php echo esc_url( $image_src_size ); ?>" class="premium-img-layers-image" alt="<?php echo esc_attr( $alt ); ?>">
						<?php
					} elseif ( 'text' === $image['media_type'] ) {
						?>
						<p class="premium-img-layers-text <?php echo esc_attr( $dir_class ); ?>" >
							<?php echo wp_kses_post( $image['img_layer_text'] ); ?>
						</p>
					<?php } ?>

					<?php if ( 'yes' === $image['premium_img_layers_link_switcher'] ) : ?>
						<a <?php echo wp_kses_post( $this->get_render_attribute_string( $list_item_link ) ); ?>></a>
					<?php endif; ?>

				</li>
			<?php endforeach; ?>
		</ul>
	</div>

		<?php
	}

	/**
	 * Render Image Layers widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function content_template() {

		?>

		<#

			view.addRenderAttribute( 'container', {
				'id': 'premium-img-layers-wrapper',
				'class': 'premium-img-layers-wrapper',
				'data-devices': JSON.stringify( settings.premium_parallax_layers_devices )
			});


		#>

		<div {{{ view.getRenderAttributeString('container') }}}>
			<ul class="premium-img-layers-list-wrapper">

			<# var animationClass, animationDur, listItemKey, imageUrl, animationArr = [];

			_.each( settings.premium_img_layers_images_repeater, function( image, index ) {

				animationArr.push( image.premium_img_layers_animation_switcher );

				if( 'yes' === animationArr[index] ) {

					animationClass = image.premium_img_layers_animation;

					if( '' != image.premium_img_layers_animation_duration ) {

						animationDur = 'animated-' + image.premium_img_layers_animation_duration;

					} else {
						animationDur = 'animated-';
					}
				} else {

						animationClass = '';

						animationDur = '';

				}

				listItemKey = 'img_layer_' + index;

				var position = '' !== image.premium_img_layers_position ? image.premium_img_layers_position : 'absolute';

				view.addRenderAttribute( listItemKey, 'class',
					[
						'premium-img-layers-list-item',
						position,
						image.premium_img_layers_class,
						'elementor-repeater-item-' + image._id
					]
				);

				view.addRenderAttribute( listItemKey, 'data-layer-animation',
					[
						animationClass,
						animationDur,
					]
				);

				if( 'yes' === image.premium_img_layers_mouse ) {

					var rate = '' != image.premium_img_layers_rate ? image.premium_img_layers_rate : -10;

					view.addRenderAttribute( listItemKey, 'data-' + image.premium_img_layers_mouse_type , 'true' );

					if( 'parallax' === image.premium_img_layers_mouse_type ) {

						if( 'yes' === image.premium_img_layers_mouse_reverse ) {
							view.addRenderAttribute( listItemKey, 'data-mparallax-reverse', 'true' );
						}

						if( 'yes' === image.premium_img_layers_mouse_initial ) {
							view.addRenderAttribute( listItemKey, 'data-mparallax-init', 'true' );
						}

					}


					view.addRenderAttribute( listItemKey, 'data-rate', rate );

				}

				if ( 'yes' === image.mask_switcher ) {
					view.addRenderAttribute( listItemKey, 'class', 'premium-mask-yes' );
					var dirClass = 'premium-mask-' + image.mask_dir;
				}

				if( 'yes' === image.premium_img_layers_float_effects ) {

					view.addRenderAttribute( listItemKey, 'data-float', 'true' );

					if( 'yes' === image.premium_img_layers_translate_float ) {

						view.addRenderAttribute( listItemKey, {
							'data-float-translate': 'true',
							'data-floatx-start': image.premium_img_layers_translatex.sizes.start,
							'data-floatx-end': image.premium_img_layers_translatex.sizes.end,
							'data-floaty-start': image.premium_img_layers_translatey.sizes.start,
							'data-floaty-end': image.premium_img_layers_translatey.sizes.end,
							'data-float-translate-speed': image.premium_img_layers_translate_speed.size
						});

					}

					if( 'yes' === image.premium_img_layers_translate_rotate ) {

						view.addRenderAttribute( listItemKey, {
							'data-float-rotate': 'true',
							'data-rotatex-start': image.premium_img_layers_float_rotatex.sizes.start,
							'data-rotatex-end': image.premium_img_layers_float_rotatex.sizes.end,
							'data-rotatey-start': image.premium_img_layers_float_rotatey.sizes.start,
							'data-rotatey-end': image.premium_img_layers_float_rotatey.sizes.end,
							'data-rotatez-start': image.premium_img_layers_float_rotatez.sizes.start,
							'data-rotatez-end': image.premium_img_layers_float_rotatez.sizes.end,
							'data-float-rotate-speed': image.premium_img_layers_rotate_speed.size
						});

					}

					if( 'yes' === image.premium_img_layers_opacity_float ) {

						view.addRenderAttribute( listItemKey, {
							'data-float-opacity': 'true',
							'data-float-opacity-value': image.premium_img_layers_opacity_value.size,
							'data-float-opacity-speed': image.premium_img_layers_opacity_speed.size
						});

					}

				} else if( 'yes' === image.premium_img_layers_scroll_effects ) {

					view.addRenderAttribute( listItemKey, 'data-scrolls', 'true' );

					if( 'yes' === image.premium_img_layers_vscroll ) {

						var speed = '' !== image.premium_img_layers_vscroll_speed.size ? image.premium_img_layers_vscroll_speed.size : 4;

						view.addRenderAttribute( listItemKey, {
							'data-vscroll': 'true',
							'data-vscroll-speed': speed,
							'data-vscroll-dir': image.premium_img_layers_vscroll_direction,
							'data-vscroll-start': image.premium_img_layers_vscroll_view.sizes.start,
							'data-vscroll-end': image.premium_img_layers_vscroll_view.sizes.end
						});

					}

					if( 'yes' === image.premium_img_layers_hscroll ) {

						var speed = '' !== image.premium_img_layers_hscroll_speed.size ? image.premium_img_layers_hscroll_speed.size : 4;

						view.addRenderAttribute( listItemKey, {
							'data-hscroll': 'true',
							'data-hscroll-speed': speed,
							'data-hscroll-dir': image.premium_img_layers_hscroll_direction,
							'data-hscroll-start': image.premium_img_layers_hscroll_view.sizes.start,
							'data-hscroll-end': image.premium_img_layers_hscroll_view.sizes.end
						});

					}

					if( 'yes' === image.premium_img_layers_opacity ) {

						var level = '' !== image.premium_img_layers_opacity_level.size ? image.premium_img_layers_opacity_level.size : 4;

						view.addRenderAttribute( listItemKey, {
							'data-oscroll': 'true',
							'data-oscroll-level': level,
							'data-oscroll-effect': image.premium_img_layers_opacity_effect,
							'data-oscroll-start': image.premium_img_layers_opacity_view.sizes.start,
							'data-oscroll-end': image.premium_img_layers_opacity_view.sizes.end
						});

					}

					if( 'yes' === image.premium_img_layers_blur ) {

						var level = '' !== image.premium_img_layers_blur_level.size ? image.premium_img_layers_blur_level.size : 4;

						view.addRenderAttribute( listItemKey, {
							'data-bscroll': 'true',
							'data-bscroll-level': level,
							'data-bscroll-effect': image.premium_img_layers_blur_effect,
							'data-bscroll-start': image.premium_img_layers_blur_view.sizes.start,
							'data-bscroll-end': image.premium_img_layers_blur_view.sizes.end
						});

					}

					if( 'yes' === image.premium_img_layers_rscroll ) {

						var speed = '' !== image.premium_img_layers_rscroll_speed.size ? image.premium_img_layers_rscroll_speed.size : 3;

						view.addRenderAttribute( listItemKey, {
							'data-rscroll': 'true',
							'data-rscroll-speed': speed,
							'data-rscroll-dir': image.premium_img_layers_rscroll_direction,
							'data-rscroll-start': image.premium_img_layers_rscroll_view.sizes.start,
							'data-rscroll-end': image.premium_img_layers_rscroll_view.sizes.end
						});

					}

					if( 'yes' === image.premium_img_layers_scale ) {

						var speed = '' !== image.premium_img_layers_scale_speed.size ? image.premium_img_layers_scale_speed.size : 3;

						view.addRenderAttribute( listItemKey, {
							'data-scale': 'true',
							'data-scale-speed': speed,
							'data-scale-dir': image.premium_img_layers_scale_direction,
							'data-scale-start': image.premium_img_layers_scale_view.sizes.start,
							'data-scale-end': image.premium_img_layers_scale_view.sizes.end
						});

					}

					if( 'yes' === image.premium_img_layers_gray ) {

						var level = '' !== image.premium_img_layers_gray_level.size ? image.premium_img_layers_gray_level.size : 10;

						view.addRenderAttribute( listItemKey, {
							'data-gscale': 'true',
							'data-gscale-level': level,
							'data-gscale-effect': image.premium_img_layers_gray_effect,
							'data-gscale-start': image.premium_img_layers_gray_view.sizes.start,
							'data-gscale-end': image.premium_img_layers_gray_view.sizes.end
						});

					}

				}

				if( 'url' === image.premium_img_layers_link_selection ) {

					imageUrl = image.premium_img_layers_link.url;

				} else {

					imageUrl = image.premium_img_layers_existing_link;

				}

				var imageObj = {
					id: image.premium_img_layers_image.id,
					url: image.premium_img_layers_image.url,
					size: image.thumbnail_size,
					dimension: image.thumbnail_custom_dimension,
					model: view.getEditModel()
				},

				image_url = elementor.imagesManager.getImageUrl( imageObj );

				if( 'animation' === image.media_type ) {

					view.addRenderAttribute( listItemKey, {
						'class':  'premium-lottie-animation',
						'data-lottie-url': image.lottie_url,
						'data-lottie-loop': image.lottie_loop,
						'data-lottie-reverse': image.lottie_reverse,
						'data-lottie-hover': image.lottie_hover,
						'data-lottie-render': image.lottie_renderer,
					});

				}

				#>

				<li {{{ view.getRenderAttributeString(listItemKey) }}}>
					<# if( 'image' === image.media_type ) { #>
						<img src="{{ image_url }}" class="premium-img-layers-image">
					<# }  else if( 'text' === image.media_type ) { #>
						<p class="premium-img-layers-text {{{ dirClass }}}">
							{{{image.img_layer_text}}}
						</p>
					<# } #>

					<# if( 'yes' === image.premium_img_layers_link_switcher ) { #>
						<a class="premium-img-layers-link" href="{{ imageUrl }}"></a>
					<# } #>
				</li>

			<# } );

			#>

			</ul>
		</div>

		<?php
	}

}
