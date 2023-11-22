<?php
/**
 * Class: Premium_Img_Layers
 * Name: Image Layers
 * Slug: premium-img-layers-addon
 */

namespace PremiumAddonsPro\Widgets;

// PremiumAddonsPro Classes.
use PremiumAddonsPro\Includes\PAPRO_Helper;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;

// PremiumAddons Classes.
use PremiumAddons\Admin\Includes\Admin_Helper;
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
	 * Check Icon Draw Option.
	 *
	 * @since 2.8.4
	 * @access public
	 */
	public function check_icon_draw() {

		if ( version_compare( PREMIUM_ADDONS_VERSION, '4.9.26', '<' ) ) {
			return false;
		}

		$is_enabled = Admin_Helper::check_svg_draw( 'premium-img-layers' );
		return $is_enabled;
	}

	/**
	 * Template Instance
	 *
	 * @var template_instance
	 */
	protected $template_instance;

	/**
	 * Get Elementor Helper Instance.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function getTemplateInstance() {
		return $this->template_instance = Premium_Template_Tags::getInstance();
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
		return __( 'Image Layers', 'premium-addons-pro' );
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
		return array( 'pa', 'premium', 'float', 'parallax', 'mouse', 'interactive', 'advanced' );
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
			'e-animations',
			'premium-addons',
			'premium-pro',
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
		$draw_scripts = $this->check_icon_draw() ? array(
			'pa-fontawesome-all',
			'pa-motionpath',
		) : array();

		return array_merge(
			array(
				'pa-tweenmax',
				'pa-tilt',
				'pa-anime',
				'lottie-js',
				'premium-pro',
			),
			$draw_scripts
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
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$draw_icon = $this->check_icon_draw();

		$this->start_controls_section(
			'premium_img_layers_content',
			array(
				'label' => __( 'Layers', 'premium-addons-pro' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'hide_layer',
			array(
				'label'    => __( 'Hide This Layer On', 'premium-addons-pro' ),
				'type'     => Controls_Manager::SELECT2,
				'options'  => array(
					'desktop' => __( 'Desktop', 'premium-addons-pro' ),
					'tablet'  => __( 'Tablet', 'premium-addons-pro' ),
					'mobile'  => __( 'Mobile', 'premium-addons-pro' ),
				),
				'multiple' => true,
			)
		);

		$repeater->start_controls_tabs( 'layer_repeater' );

		$repeater->start_controls_tab(
			'layer_content_tab',
			array(
				'label' => esc_html__( 'Content', 'elementor-pro' ),
			)
		);

		$repeater->add_control(
			'media_type',
			array(
				'label'   => __( 'Media Type', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'image'     => __( 'Image', 'premium-addons-pro' ),
					'animation' => __( 'Lottie Animation', 'premium-addons-pro' ),
					'text'      => __( 'Text', 'premium-addons-pro' ),
					'svg'       => __( 'SVG/Icon', 'premium-addons-pro' ),
				),
				'default' => 'image',
			)
		);

		$repeater->add_control(
			'svg_icon',
			array(
				'label'     => __( 'Type', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'svg'  => __( 'SVG', 'premium-addons-pro' ),
					'icon' => __( 'Font Awesome Icon', 'premium-addons-pro' ),
				),
				'default'   => 'svg',
				'condition' => array(
					'media_type' => 'svg',
				),
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
			'font_icon',
			array(
				'label'                  => __( 'Select Icon', 'premium-addons-pro' ),
				'type'                   => Controls_Manager::ICONS,
				'skin'                   => 'inline',
				'exclude_inline_options' => array( 'svg' ),
				'classes'                => 'editor-pa-icon-control',
				'default'                => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'label_block'            => false,
				'condition'              => array(
					'media_type' => 'svg',
					'svg_icon'   => 'icon',
				),
			)
		);

		$repeater->add_control(
			'custom_svg',
			array(
				'label'       => __( 'SVG Code', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => 'You can use these sites to create SVGs: <a href="https://danmarshall.github.io/google-font-to-svg-path/" target="_blank">Google Fonts</a> and <a href="https://boxy-svg.com/" target="_blank">Boxy SVG</a>',
				'condition'   => array(
					'media_type' => 'svg',
					'svg_icon'   => 'svg',
				),
			)
		);

		$repeater->add_control(
			'draw_svg',
			array(
				'label'     => __( 'Draw Icon', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'classes'   => $draw_icon ? '' : 'editor-pa-control-disabled',
				'condition' => array(
					'media_type' => 'svg',
				),
			)
		);

		$animation_conditions = array(
			'relation' => 'or',
			'terms'    => array(
				array(
					'name'  => 'media_type',
					'value' => 'animation',
				),
				array(
					'terms' => array(
						array(
							'name'  => 'media_type',
							'value' => 'svg',
						),
						array(
							'name'  => 'draw_svg',
							'value' => 'yes',
						),
					),
				),
			),
		);

		if ( $draw_icon ) {
			$repeater->add_control(
				'svg_sync',
				array(
					'label'     => __( 'Draw All Paths Together', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'media_type' => 'svg',
						'draw_svg'   => 'yes',
					),
				)
			);

			$repeater->add_control(
				'svg_transparent',
				array(
					'label'     => __( 'Remove All Fill Colors', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'media_type' => 'svg',
						'draw_svg'   => 'yes',
					),
				)
			);

			$repeater->add_control(
				'frames',
				array(
					'label'       => __( 'Speed', 'premium-addons-pro' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => __( 'Larger value means longer animation duration.', 'premium-addons-pro' ),
					'default'     => 5,
					'min'         => 1,
					'max'         => 100,
					'condition'   => array(
						'media_type' => 'svg',
						'draw_svg'   => 'yes',
					),
				)
			);
		} elseif ( method_exists( 'PremiumAddons\Includes\Helper_Functions', 'get_draw_svg_notice' ) ) {

			Helper_Functions::get_draw_svg_notice(
				$repeater,
				'layers',
				array(
					'media_type' => 'svg',
				)
			);

		}

		$repeater->add_control(
			'lottie_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'conditions'   => $animation_conditions,

			)
		);

		if ( $draw_icon ) {
			$repeater->add_control(
				'svg_notice',
				array(
					'raw'             => __( 'Loop and Speed options are overriden when Draw SVGs in Sequence option is enabled.', 'premium-addons-pro' ),
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'condition'       => array(
						'media_type'    => 'svg',
						'draw_svg'      => 'yes',
						'lottie_hover!' => 'true',
					),
				)
			);
		}

		$repeater->add_control(
			'lottie_reverse',
			array(
				'label'        => __( 'Reverse', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'conditions'   => $animation_conditions,
			)
		);

		if ( $draw_icon ) {
			$repeater->add_control(
				'start_point',
				array(
					'label'       => __( 'Start Point (%)', 'premium-addons-pro' ),
					'type'        => Controls_Manager::SLIDER,
					'description' => __( 'Set the point that the SVG should start from.', 'premium-addons-pro' ),
					'default'     => array(
						'unit' => '%',
						'size' => 0,
					),
					'condition'   => array(
						'media_type'      => 'svg',
						'draw_svg'        => 'yes',
						'lottie_reverse!' => 'true',
					),

				)
			);

			$repeater->add_control(
				'end_point',
				array(
					'label'       => __( 'End Point (%)', 'premium-addons-pro' ),
					'type'        => Controls_Manager::SLIDER,
					'description' => __( 'Set the point that the SVG should end at.', 'premium-addons-pro' ),
					'default'     => array(
						'unit' => '%',
						'size' => 0,
					),
					'condition'   => array(
						'media_type'     => 'svg',
						'draw_svg'       => 'yes',
						'lottie_reverse' => 'true',
					),

				)
			);

			$repeater->add_control(
				'restart_draw',
				array(
					'label'        => __( 'Restart Animation on Scroll Up', 'premium-addons-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'condition'    => array(
						'media_type'    => 'svg',
						'draw_svg'      => 'yes',
						'lottie_hover!' => 'true',
					),

				)
			);

			$repeater->add_control(
				'svg_yoyo',
				array(
					'label'     => __( 'Yoyo Effect', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'media_type'  => 'svg',
						'draw_svg'    => 'yes',
						'lottie_loop' => 'true',
					),
				)
			);
		}

		$repeater->add_control(
			'lottie_hover',
			array(
				'label'        => __( 'Only Animate on Hover', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'conditions'   => $animation_conditions,
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
			'mask_image',
			array(
				'label'     => esc_html__( 'Mask Image Shape', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => array(
					'media_type' => 'image',
				),
			)
		);

		$repeater->add_control(
			'mask_shape',
			array(
				'label'       => esc_html__( 'Mask Image', 'premium-addons-pro' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => array(
					'url' => '',
				),
				'description' => esc_html__( 'Use PNG image with the shape you want to mask around feature image.', 'premium-addons-pro' ),
				'selectors'   => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'mask-image: url("{{URL}}"); -webkit-mask-image: url("{{URL}}");',
				),
				'condition'   => array(
					'media_type' => 'image',
					'mask_image' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'mask_size',
			array(
				'label'     => __( 'Mask Size', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'contain' => __( 'Contain', 'premium-addons-pro' ),
					'cover'   => __( 'Cover', 'premium-addons-pro' ),
				),
				'default'   => 'contain',
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'mask-size: {{VALUE}};-webkit-mask-size: {{VALUE}};',
				),
				'condition' => array(
					'media_type' => 'image',
					'mask_image' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'mask_position_cover',
			array(
				'label'     => __( 'Mask Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'center center' => __( 'Center Center', 'premium-addons-pro' ),
					'center left'   => __( 'Center Left', 'premium-addons-pro' ),
					'center right'  => __( 'Center Right', 'premium-addons-pro' ),
					'top center'    => __( 'Top Center', 'premium-addons-pro' ),
					'top left'      => __( 'Top Left', 'premium-addons-pro' ),
					'top right'     => __( 'Top Right', 'premium-addons-pro' ),
					'bottom center' => __( 'Bottom Center', 'premium-addons-pro' ),
					'bottom left'   => __( 'Bottom Left', 'premium-addons-pro' ),
					'bottom right'  => __( 'Bottom Right', 'premium-addons-pro' ),
				),
				'default'   => 'center center',
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'mask-position: {{VALUE}};-webkit-mask-position: {{VALUE}}',
				),
				'condition' => array(
					'media_type' => 'image',
					'mask_image' => 'yes',
					'mask_size'  => 'cover',
				),
			)
		);

		$repeater->add_control(
			'mask_position_contain',
			array(
				'label'     => __( 'Mask Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'center center' => __( 'Center Center', 'premium-addons-pro' ),
					'top center'    => __( 'Top Center', 'premium-addons-pro' ),
					'bottom center' => __( 'Bottom Center', 'premium-addons-pro' ),
				),
				'default'   => 'center center',
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'mask-position: {{VALUE}};-webkit-mask-position: {{VALUE}}',
				),
				'condition' => array(
					'media_type' => 'image',
					'mask_image' => 'yes',
					'mask_size'  => 'contain',
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_zindex',
			array(
				'label'     => __( 'Z-index', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'separator' => 'before',
				'default'   => 1,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}.premium-img-layers-list-item' => 'z-index: {{VALUE}};',
				),
			)
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'layer_style_tab',
			array(
				'label' => esc_html__( 'Style', 'elementor-pro' ),
			)
		);

		$repeater->add_responsive_control(
			'premium_img_layers_hor_position',
			array(
				'label'      => __( 'Horizontal Offset', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => -200,
						'max' => 300,
					),
					'%'  => array(
						'min' => -50,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_img_layers_ver_position',
			array(
				'label'      => __( 'Vertical Offset', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => -200,
						'max' => 300,
					),
					'%'  => array(
						'min' => -50,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_img_layers_width',
			array(
				'label'      => __( 'Width', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'custom' ),
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
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'media_type',
							'operator' => '!==',
							'value'    => 'svg',
						),
						array(
							'name'  => 'svg_icon',
							'value' => 'svg',
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}:not(.premium-svg-drawer):not(.premium-svg-nodraw)' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} {{CURRENT_ITEM}}[class*="premium-svg-"] svg' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$repeater->add_responsive_control(
			'svg_height',
			array(
				'label'      => __( 'Height', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 600,
					),
					'em' => array(
						'min' => 1,
						'max' => 30,
					),
				),
				'default'    => array(
					'size' => 100,
					'unit' => 'px',
				),
				'condition'  => array(
					'media_type' => 'svg',
					'svg_icon'   => 'svg',
				),
				'selectors'  => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} svg' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$repeater->add_responsive_control(
			'svg_icon_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 500,
					),
					'em' => array(
						'min' => 1,
						'max' => 30,
					),
				),
				'default'    => array(
					'size' => 250,
					'unit' => 'px',
				),
				'condition'  => array(
					'media_type' => 'svg',
					'svg_icon'   => 'icon',
				),
				'selectors'  => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$repeater->add_control(
			'text_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#54595F',
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .premium-img-layers-text' => 'color: {{VALUE}}',
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
				'default'   => '#54595F',
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

		if ( $draw_icon ) {
			$repeater->add_control(
				'icon_color',
				array(
					'label'     => __( 'Stroke Color', 'premium-addons-pro' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#6EC1E4',
					'condition' => array(
						'media_type' => 'svg',
					),
					'selectors' => array(
						'{{WRAPPER}} {{CURRENT_ITEM}} svg' => 'color: {{VALUE}};',
						'{{WRAPPER}} {{CURRENT_ITEM}} svg *' => 'stroke: {{VALUE}}',
					),
				)
			);

			$repeater->add_control(
				'fill_color',
				array(
					'label'     => __( 'Fill Color', 'premium-addons-pro' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => 'transparent',
					'condition' => array(
						'media_type' => 'svg',
					),
					'selectors' => array(
						'{{WRAPPER}} {{CURRENT_ITEM}} svg *' => 'fill: {{VALUE}}',
					),
				)
			);

			$repeater->add_control(
				'svg_stroke',
				array(
					'label'     => __( 'After Draw Stroke Color', 'premium-addons-pro' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => false,
					'condition' => array(
						'media_type' => 'svg',
						'draw_svg'   => 'yes',
					),
				)
			);

			$repeater->add_control(
				'svg_color',
				array(
					'label'     => __( 'After Draw Fill Color', 'premium-addons-pro' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => false,
					'condition' => array(
						'media_type' => 'svg',
						'draw_svg'   => 'yes',
					),
				)
			);

			$repeater->add_control(
				'path_width',
				array(
					'label'     => __( 'Path Thickness', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'min'  => 0,
							'max'  => 20,
							'step' => 0.1,
						),
					),
					'default'   => array(
						'size' => 3,
						'unit' => 'px',
					),
					'condition' => array(
						'media_type' => 'svg',
					),
					'selectors' => array(
						'{{WRAPPER}} {{CURRENT_ITEM}} svg path, {{WRAPPER}} {{CURRENT_ITEM}} svg circle, {{WRAPPER}} {{CURRENT_ITEM}} svg square, {{WRAPPER}} {{CURRENT_ITEM}} svg ellipse, {{WRAPPER}} {{CURRENT_ITEM}} svg rect, {{WRAPPER}} {{CURRENT_ITEM}} svg polyline, {{WRAPPER}} {{CURRENT_ITEM}} svg line' => 'stroke-width: {{SIZE}}',
					),
				)
			);
		}

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
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'mix-blend-mode: {{VALUE}}',
				),
			)
		);

		$repeater->add_control(
			'hover_effect',
			array(
				'label'   => __( 'Hover Effect', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'none'    => __( 'None', 'premium-addons-pro' ),
					'zoomin'  => __( 'Zoom In', 'premium-addons-pro' ),
					'zoomout' => __( 'Zoom Out', 'premium-addons-pro' ),
					'scale'   => __( 'Scale', 'premium-addons-pro' ),
					'gray'    => __( 'Grayscale', 'premium-addons-pro' ),
					'blur'    => __( 'Blur', 'premium-addons-pro' ),
					'bright'  => __( 'Bright', 'premium-addons-pro' ),
					'sepia'   => __( 'Sepia', 'premium-addons-pro' ),
				),
			)
		);

		$repeater->add_control(
			'opacity',
			array(
				'label'     => __( 'Opacity', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => .1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'opacity: {{SIZE}}',
				),
			)
		);

		$repeater->add_control(
			'premium_img_layers_rotate',
			array(
				'label' => __( 'Rotate', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$repeater->add_responsive_control(
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

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'layer_animation_tab',
			array(
				'label' => esc_html__( 'Animations', 'elementor-pro' ),
			)
		);

		$repeater->add_control(
			'premium_img_layers_animation_switcher',
			array(
				'label' => __( 'Entrance Animation', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$repeater->add_control(
			'premium_img_layers_animation',
			array(
				'label'              => __( 'Select Animation', 'premium-addons-pro' ),
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
				'label'        => __( 'Minimal Mask Effect', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => __( 'Note: This effect takes place once the element is in the viewport', 'premium-addons-pro' ),
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
				'label'       => __( 'Mask Color', 'premium-addons-pro' ),
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
				'label'       => __( 'Direction', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'tr',
				'render_type' => 'template',
				'options'     => array(
					'tr' => __( 'To Right', 'premium-addons-pro' ),
					'tl' => __( 'To Left', 'premium-addons-pro' ),
					'tt' => __( 'To Top', 'premium-addons-pro' ),
					'tb' => __( 'To Bottom', 'premium-addons-pro' ),
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
				'label'      => __( 'Words Padding', 'premium-addons-pro' ),
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

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

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

		if ( $draw_icon ) {
			$this->add_control(
				'draw_svgs_sequence',
				array(
					'label'        => __( 'Draw SVGs In Sequence', 'premium-addons-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'prefix_class' => 'pa-svg-draw-seq-',
					'render_type'  => 'template',
				)
			);

			$this->add_control(
				'draw_svgs_loop',
				array(
					'label'        => __( 'Loop', 'premium-addons-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'prefix_class' => 'pa-svg-draw-loop-',
					'render_type'  => 'template',
					'condition'    => array(
						'draw_svgs_sequence' => 'yes',
					),
				)
			);

			$this->add_control(
				'frames',
				array(
					'label'       => __( 'Speed', 'premium-addons-pro' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => __( 'Larger value means longer animation duration.', 'premium-addons-pro' ),
					'default'     => 5,
					'min'         => 1,
					'max'         => 100,
					'condition'   => array(
						'draw_svgs_sequence' => 'yes',
					),
				)
			);

			$this->add_control(
				'svg_yoyo',
				array(
					'label'        => __( 'Yoyo Animation', 'premium-addons-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'prefix_class' => 'pa-svg-draw-yoyo-',
					'render_type'  => 'template',
					'condition'    => array(
						'draw_svgs_sequence' => 'yes',
						'draw_svgs_loop'     => 'yes',
					),
				)
			);
		}

		$this->add_control(
			'premium_parallax_layers_devices',
			array(
				'label'       => __( 'Apply Scroll Effects On', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => Helper_Functions::get_all_breakpoints(),
				'default'     => Helper_Functions::get_all_breakpoints( 'keys' ),
				'multiple'    => true,
				'label_block' => true,
			)
		);

		$this->add_control(
			'disable_fe_on_safari',
			array(
				'label'        => __( 'Disable Floating Effects On Safari', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'pa-imglayers-disable-fe-',
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
				'size_units' => array( 'px', 'em', 'vh', 'custom' ),
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
				'class' => 'premium-img-layers-wrapper',
			)
		);

		$scroll_effects = isset( $settings['premium_parallax_layers_devices'] ) ? $settings['premium_parallax_layers_devices'] : array();

		$this->add_render_attribute( 'container', 'data-devices', wp_json_encode( $scroll_effects ) );

		$draw_icon = $this->check_icon_draw();
		if ( $draw_icon && 'yes' === $settings['draw_svgs_sequence'] ) {
			$this->add_render_attribute( 'container', 'data-speed', $settings['frames'] );
		}

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

				$this->add_render_attribute(
					$list_item_key,
					'class',
					array(
						'premium-img-layers-list-item',
						esc_attr( $image['premium_img_layers_class'] ),
						'elementor-repeater-item-' . $image['_id'],
						'premium-img-layer-' . $image['hover_effect'],
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

				$dir_class = '';
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

				if ( 'yes' === $image['premium_img_layers_link_switcher'] ) {

					$list_item_link = 'img_link_' . $index;

					$this->add_render_attribute(
						$list_item_link,
						array(
							'class'      => 'premium-img-layers-link',
							'aria-label' => 'Link ' . ( $index + 1 ),
						)
					);

					if ( 'url' === $image['premium_img_layers_link_selection'] ) {

						$this->add_link_attributes( $list_item_link, $image['premium_img_layers_link'] );

					} else {

						$this->add_render_attribute( $list_item_link, 'href', get_permalink( $image['premium_img_layers_existing_link'] ) );

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

				} elseif ( 'svg' === $image['media_type'] ) {

					$this->add_render_attribute( $list_item_key, 'class', 'elementor-invisible' );

					if ( 'yes' === $image['draw_svg'] ) {

						$this->add_render_attribute(
							$list_item_key,
							array(
								'class'            => 'premium-svg-drawer',
								'data-svg-reverse' => $image['lottie_reverse'],
								'data-svg-loop'    => $image['lottie_loop'],
								'data-svg-hover'   => $image['lottie_hover'],
								'data-svg-sync'    => $image['svg_sync'],
								'data-svg-trans'   => $image['svg_transparent'],
								'data-svg-restart' => $image['restart_draw'],
								'data-svg-fill'    => $image['svg_color'],
								'data-svg-stroke'  => $image['svg_stroke'],
								'data-svg-frames'  => $image['frames'],
								'data-svg-yoyo'    => $image['svg_yoyo'],
								'data-svg-point'   => $image['lottie_reverse'] ? $image['end_point']['size'] : $image['start_point']['size'],
							)
						);

					} else {
						$this->add_render_attribute( $list_item_key, 'class', 'premium-svg-nodraw' );
					}
				}

				$this->add_render_attribute( $list_item_key, 'data-layer-hide', wp_json_encode( $image['hide_layer'] ) );

				?>

				<li <?php echo wp_kses_post( $this->get_render_attribute_string( $list_item_key ) ); ?>>
					<?php
					if ( 'image' === $image['media_type'] ) {

						$image_src = $image['premium_img_layers_image']['url'];

						if ( ! empty( $image_src ) ) {
							$image_id = attachment_url_to_postid( $image_src );

							$settings['image_data'] = Helper_Functions::get_image_data( $image_id, $image['premium_img_layers_image']['url'], $image['thumbnail_size'] );

							if ( 'custom' === $image['thumbnail_size'] ) {
								$settings['image_data']['image_custom_dimension'] = $image['thumbnail_custom_dimension'];
							}

							PAPRO_Helper::get_attachment_image_html( $settings, 'thumbnail', 'image_data', 'premium-img-layers-image' );
						}
						?>

						<?php
					} elseif ( 'text' === $image['media_type'] ) {
						?>
						<p class="premium-img-layers-text <?php echo esc_attr( $dir_class ); ?>" >
							<?php echo wp_kses_post( $image['img_layer_text'] ); ?>
						</p>
						<?php
					} elseif ( 'svg' === $image['media_type'] ) {
						if ( 'svg' === $image['svg_icon'] ) {
							echo $this->print_unescaped_setting( 'custom_svg', 'premium_img_layers_images_repeater', $index );
						} else {
							?>
							<i class="<?php echo esc_attr( $image['font_icon']['value'] ); ?>"></i>
							<?php
						}
					}
					?>

					<?php if ( 'yes' === $image['premium_img_layers_link_switcher'] ) : ?>
						<a <?php echo wp_kses_post( $this->get_render_attribute_string( $list_item_link ) ); ?>>
						</a>
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
				'class': 'premium-img-layers-wrapper',
				'data-devices': JSON.stringify( settings.premium_parallax_layers_devices )
			});

			if ( 'yes' === settings.draw_svgs_sequence ) {
				view.addRenderAttribute( 'container', 'data-speed', settings.frames );
			}


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

				view.addRenderAttribute( listItemKey, 'class',
					[
						'premium-img-layers-list-item',
						image.premium_img_layers_class,
						'elementor-repeater-item-' + image._id,
						'premium-img-layer-' + image.hover_effect,
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

				} else if ( 'svg' === image.media_type ) {

					view.addRenderAttribute( listItemKey, 'class', 'elementor-invisible' );

					if ( 'yes' === image.draw_svg ) {

						view.addRenderAttribute( listItemKey, {
							'class':  'premium-svg-drawer',
							'data-svg-reverse': image.lottie_reverse,
							'data-svg-loop': image.lottie_loop,
							'data-svg-hover': image.lottie_hover,
							'data-svg-sync': image.svg_sync,
							'data-svg-trans': image.svg_transparent,
							'data-svg-restart': image.restart_draw,
							'data-svg-fill': image.svg_color,
							'data-svg-stroke': image.svg_stroke,
							'data-svg-frames': image.frames,
							'data-svg-yoyo': image.svg_yoyo,
							'data-svg-point': image.lottie_reverse ? image.end_point.size : image.start_point.size,
						});

					} else {
						view.addRenderAttribute( listItemKey, 'class', 'premium-svg-nodraw' );
					}

				}

				view.addRenderAttribute( listItemKey, 'data-layer-hide', JSON.stringify( image.hide_layer ) );

				#>

				<li {{{ view.getRenderAttributeString(listItemKey) }}}>
					<# if( 'image' === image.media_type ) { #>
						<img src="{{ image_url }}" class="premium-img-layers-image">
					<# } else if( 'text' === image.media_type ) { #>
						<p class="premium-img-layers-text {{{ dirClass }}}">
							{{{image.img_layer_text}}}
						</p>
					<# } else if ( 'svg' === image.media_type ) {
						if ( 'svg' === image.svg_icon ) { #>
							{{{ image.custom_svg }}}
						<# } else { #>
							<i class="{{ image.font_icon.value }}"></i>
						<#}
					}

					if( 'yes' === image.premium_img_layers_link_switcher ) { #>
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
