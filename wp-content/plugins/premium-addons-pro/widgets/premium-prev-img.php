<?php
/**
 * Class: Premium_Prev_Img
 * Name: Preview Window
 * Slug: premium-addon-preview-image
 */

namespace PremiumAddonsPro\Widgets;

// PremiumAddonsPro Classes.
use PremiumAddonsPro\Includes\PAPRO_Helper;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Icons_Manager;

// PremiumAddons Classes.
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Prev_Img
 */
class Premium_Prev_Img extends Widget_Base {

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

		$is_enabled = Admin_Helper::check_svg_draw( 'premium-prev-img' );
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
		return 'premium-addon-preview-image';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Preview Window', 'premium-addons-pro' );
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
		return 'pa-pro-preview-window';
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
	 * Retrieve Widget Dependent CSS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array CSS script handles.
	 */
	public function get_style_depends() {
		return array(
			'tooltipster',
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
			'pa-tweenmax',
			'pa-motionpath',
		) : array();

		return array_merge(
			$draw_scripts,
			array(
				'tooltipster-bundle',
				'pa-anime',
				'lottie-js',
				'premium-pro',
			)
		);
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
		return array( 'pa', 'premium', 'cta', 'lightbox', 'popup', 'modal' );
	}

	/**
	 * Retrieve Widget Support URL.
	 *
	 * @access public
	 *
	 * @return string support URL.
	 */
	public function get_custom_help_url() {
		return 'https://premiumaddons.com/support/';
	}

	/**
	 * Register Preview Image controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$draw_icon = $this->check_icon_draw();

		$this->start_controls_section(
			'trigger_section',
			array(
				'label' => __( 'Trigger', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'trigger_type',
			array(
				'label'       => __( 'Type', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'image'  => __( 'Image', 'premium-addons-pro' ),
					'text'   => __( 'Text', 'premium-addons-pro' ),
					'icon'   => __( 'Icon', 'premium-addons-pro' ),
					'lottie' => __( 'Lottie Animation', 'premium-addons-pro' ),
					'svg'    => __( 'SVG Code', 'premium-addons-pro' ),
				),
				'default'     => 'image',
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_preview_image_main',
			array(
				'label'     => __( 'Choose Image', 'premium-addons-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => array( 'active' => true ),
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'trigger_type' => 'image',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'premium_preview_image_main_size',
				'default'   => 'full',
				'condition' => array(
					'trigger_type' => 'image',
				),
			)
		);

		$this->add_control(
			'trigger_icon',
			array(
				'label'       => __( 'Icon', 'premium-addons-pro' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'label_block' => true,
				'condition'   => array(
					'trigger_type' => 'icon',
				),
			)
		);

		$this->add_control(
			'custom_svg',
			array(
				'label'       => __( 'SVG Code', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => 'You can use these sites to create SVGs: <a href="https://danmarshall.github.io/google-font-to-svg-path/" target="_blank">Google Fonts</a> and <a href="https://boxy-svg.com/" target="_blank">Boxy SVG</a>',
				'condition'   => array(
					'trigger_type' => 'svg',
				),
			)
		);

		$this->add_control(
			'lottie_url',
			array(
				'label'       => __( 'Animation JSON URL', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'condition'   => array(
					'trigger_type' => 'lottie',
				),
			)
		);

		$this->add_control(
			'draw_svg',
			array(
				'label'     => __( 'Draw Icon', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'classes'   => $draw_icon ? '' : 'editor-pa-control-disabled',
				'condition' => array(
					'trigger_type'           => array( 'icon', 'svg' ),
					'trigger_icon[library]!' => 'svg',
				),
			)
		);

		$animation_conds = array(
			'relation' => 'or',
			'terms'    => array(
				array(
					'name'  => 'trigger_type',
					'value' => 'lottie',
				),
				array(
					'terms' => array(
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'trigger_type',
									'value' => 'icon',
								),
								array(
									'name'  => 'trigger_type',
									'value' => 'svg',
								),
							),
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
			$this->add_control(
				'path_width',
				array(
					'label'     => __( 'Path Thickness', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'min'  => 0,
							'max'  => 50,
							'step' => 0.1,
						),
					),
					'condition' => array(
						'trigger_type' => array( 'icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-preview-image-figure svg *' => 'stroke-width: {{SIZE}}',
					),
				)
			);

			$this->add_control(
				'svg_sync',
				array(
					'label'     => __( 'Draw All Paths Together', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'trigger_type' => array( 'icon', 'svg' ),
						'draw_svg'     => 'yes',
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
						'trigger_type' => array( 'icon', 'svg' ),
						'draw_svg'     => 'yes',
					),
				)
			);

		} elseif ( method_exists( 'PremiumAddons\Includes\Helper_Functions', 'get_draw_svg_notice' ) ) {

			Helper_Functions::get_draw_svg_notice(
				$this,
				'window',
				array(
					'trigger_type'           => array( 'icon', 'svg' ),
					'trigger_icon[library]!' => 'svg',
				)
			);

		}

		$this->add_control(
			'lottie_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'conditions'   => $animation_conds,
			)
		);

		$this->add_control(
			'lottie_reverse',
			array(
				'label'        => __( 'Reverse', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'conditions'   => $animation_conds,
			)
		);

		if ( $draw_icon ) {
			$this->add_control(
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
						'trigger_type'    => array( 'icon', 'svg' ),
						'draw_svg'        => 'yes',
						'lottie_reverse!' => 'true',
					),
				)
			);

			$this->add_control(
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
						'trigger_type'   => array( 'icon', 'svg' ),
						'draw_svg'       => 'yes',
						'lottie_reverse' => 'true',
					),
				)
			);

			$this->add_control(
				'svg_hover',
				array(
					'label'        => __( 'Only Play on Hover', 'premium-addons-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'condition'    => array(
						'trigger_type' => array( 'icon', 'svg' ),
						'draw_svg'     => 'yes',
					),
				)
			);

			$this->add_control(
				'svg_yoyo',
				array(
					'label'     => __( 'Yoyo Effect', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'trigger_type' => array( 'icon', 'svg' ),
						'draw_svg'     => 'yes',
						'lottie_loop'  => 'true',
					),
				)
			);
		}

		$this->add_responsive_control(
			'trigger_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 300,
					),
					'em' => array(
						'min' => 1,
						'max' => 30,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-preview-image-figure i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-preview-image-figure svg' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important',
				),
				'conditions' => array(
					'terms' => array(
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'trigger_type',
									'value' => 'lottie',
								),
								array(
									'name'  => 'trigger_type',
									'value' => 'icon',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'svg_icon_width',
			array(
				'label'      => __( 'Width', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
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
					'trigger_type' => 'svg',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-preview-image-figure svg' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'svg_icon_height',
			array(
				'label'      => __( 'Height', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 300,
					),
					'em' => array(
						'min' => 1,
						'max' => 30,
					),
				),
				'condition'  => array(
					'trigger_type' => 'svg',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-preview-image-figure svg' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hover_image_switcher',
			array(
				'label'     => __( 'Hover Image', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'trigger_type' => 'image',
				),
			)
		);

		$this->add_control(
			'premium_preview_image_hover',
			array(
				'label'     => __( 'Choose Image', 'premium-addons-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => array( 'active' => true ),
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'hover_image_switcher' => 'yes',
					'trigger_type'         => 'image',
				),
			)
		);

		$this->add_control(
			'premium_preview_image_caption',
			array(
				'label'       => __( 'Caption', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'default'     => __( 'Premium Preview Window', 'premium-addons-pro' ),
				'condition'   => array(
					'trigger_type!' => 'text',
				),
			)
		);

		$this->add_control(
			'trigger_text',
			array(
				'label'       => __( 'Text', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'default'     => __( 'Premium Preview Window', 'premium-addons-pro' ),
				'condition'   => array(
					'trigger_type' => 'text',
				),
			)
		);

		$this->add_control(
			'premium_preview_image_link_switcher',
			array(
				'label' => __( 'Link', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_preview_image_link_selection',
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
					'premium_preview_image_link_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_preview_image_link',
			array(
				'label'       => __( 'Link', 'premium-addons-pro' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array( 'active' => true ),
				'default'     => array(
					'url' => '#',
				),
				'placeholder' => 'https://premiumaddons.com/',
				'label_block' => true,
				'condition'   => array(
					'premium_preview_image_link_selection' => 'url',
					'premium_preview_image_link_switcher'  => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_preview_image_existing_link',
			array(
				'label'       => __( 'Existing Page', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_all_posts(),
				'multiple'    => false,
				'label_block' => true,
				'condition'   => array(
					'premium_preview_image_link_selection' => 'link',
					'premium_preview_image_link_switcher'  => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_preview_image_align',
			array(
				'label'     => __( 'Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .premium-preview-image-trig-img-wrap' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'float_effects',
			array(
				'label' => __( 'Floating Effects', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$float_conditions = array(
			'float_effects' => 'yes',
		);

		$this->add_control(
			'float_translate',
			array(
				'label'              => __( 'Translate', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition'          => $float_conditions,
			)
		);

		$this->add_control(
			'float_translatex',
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
						'float_translate' => 'yes',
					)
				),
			)
		);

		$this->add_control(
			'float_translatey',
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
						'float_translate' => 'yes',
					)
				),
			)
		);

		$this->add_control(
			'float_translate_speed',
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
						'float_translate' => 'yes',
					)
				),
			)
		);

		$this->add_control(
			'float_rotate',
			array(
				'label'              => __( 'Rotate', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition'          => $float_conditions,
			)
		);

		$this->add_control(
			'float_rotatex',
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
						'float_rotate' => 'yes',
					)
				),
			)
		);

		$this->add_control(
			'float_rotatey',
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
						'float_rotate' => 'yes',
					)
				),
			)
		);

		$this->add_control(
			'float_rotatez',
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
						'float_rotate' => 'yes',
					)
				),
			)
		);

		$this->add_control(
			'float_rotate_speed',
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
						'float_rotate' => 'yes',
					)
				),
			)
		);

		$this->add_control(
			'float_opacity',
			array(
				'label'              => __( 'Opacity', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition'          => $float_conditions,
			)
		);

		$this->add_control(
			'float_opacity_value',
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
						'float_opacity' => 'yes',
					)
				),
			)
		);

		$this->add_control(
			'float_opacity_speed',
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
						'float_opacity' => 'yes',
					)
				),
			)
		);

		$this->add_control(
			'disable_on_safari',
			array(
				'label'        => __( 'Disable Floating Effects On Safari', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'pa-previmg-disable-fe-',
				'condition'    => array(
					'float_effects' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_preview_image_magnifier',
			array(
				'label' => __( 'Preview Window', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_preview_image_content_selection',
			array(
				'label'       => __( 'Content Type', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'custom'   => __( 'Custom Content', 'premium-addons-pro' ),
					'template' => __( 'Elementor Template', 'premium-addons-pro' ),
				),
				'default'     => 'custom',
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_preview_image_img_switcher',
			array(
				'label'     => __( 'Image', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'premium_preview_image_content_selection'   => 'custom',
				),
			)
		);

		$this->add_control(
			'premium_preview_image_tooltips_image',
			array(
				'label'     => __( 'Choose Image', 'premium-addons-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => array( 'active' => true ),
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'premium_preview_image_content_selection' => 'custom',
					'premium_preview_image_img_switcher' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'premium_preview_image_tooltips_image_size',
				'default'   => 'full',
				'condition' => array(
					'premium_preview_image_content_selection' => 'custom',
					'premium_preview_image_img_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_tooltip_img_align',
			array(
				'label'     => __( 'Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'.premium-prev-img-tooltip-img-wrap-{{ID}}' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'premium_preview_image_content_selection' => 'custom',
					'premium_preview_image_img_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_preview_image_title_switcher',
			array(
				'label'     => __( 'Title', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_preview_image_content_selection'   => 'custom',
				),
			)
		);

		$this->add_control(
			'premium_preview_image_title',
			array(
				'label'       => __( 'Title', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => 'Premium Preview Image',
				'condition'   => array(
					'premium_preview_image_title_switcher' => 'yes',
					'premium_preview_image_content_selection' => 'custom',
				),
			)
		);

		$this->add_control(
			'premium_image_preview_title_heading',
			array(
				'label'     => __( 'HTML Tag', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h3',
				'options'   => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				),
				'condition' => array(
					'premium_preview_image_title_switcher' => 'yes',
					'premium_preview_image_content_selection' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_tooltip_title_align',
			array(
				'label'     => __( 'Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'.premium-prev-img-tooltip-title-wrap-{{ID}}' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'premium_preview_image_content_selection' => 'custom',
					'premium_preview_image_title_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_preview_image_desc_switcher',
			array(
				'label'     => __( 'Description', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_preview_image_content_selection'   => 'custom',
				),
			)
		);

		$this->add_control(
			'premium_preview_image_desc',
			array(
				'label'     => __( 'Content', 'premium-addons-pro' ),
				'type'      => Controls_Manager::WYSIWYG,
				'dynamic'   => array( 'active' => true ),
				'default'   => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
				'condition' => array(
					'premium_preview_image_desc_switcher' => 'yes',
					'premium_preview_image_content_selection' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_tooltip_desc_align',
			array(
				'label'     => __( 'Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'.premium-prev-img-tooltip-desc-wrap-{{ID}}' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'premium_preview_image_content_selection' => 'custom',
					'premium_preview_image_desc_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'live_temp_content',
			array(
				'label'       => __( 'Template Title', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'classes'     => 'premium-live-temp-title control-hidden',
				'label_block' => true,
				'condition'   => array(
					'premium_preview_image_content_selection'   => 'template',
				),
			)
		);

		$this->add_control(
			'premium_preview_image_content_temp_live',
			array(
				'type'        => Controls_Manager::BUTTON,
				'label_block' => true,
				'button_type' => 'default papro-btn-block',
				'text'        => __( 'Create / Edit Template', 'premium-addons-pro' ),
				'event'       => 'createLiveTemp',
				'condition'   => array(
					'premium_preview_image_content_selection'   => 'template',
				),
			)
		);

		$this->add_control(
			'premium_preview_image_content_temp',
			array(
				'label'       => __( 'OR Select Existing Template', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'classes'     => 'premium-live-temp-label',
				'options'     => $this->getTemplateInstance()->get_elementor_page_list(),
				'condition'   => array(
					'premium_preview_image_content_selection'   => 'template',
				),
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_preview_image_advanced',
			array(
				'label' => __( 'Advanced Settings', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_preview_image_interactive',
			array(
				'label'       => __( 'Interactive', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Give users the possibility to interact with the content of the tooltip', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_preview_image_responsive',
			array(
				'label'       => __( 'Responsive', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Resize tooltip image to fit screen', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_preview_image_anim',
			array(
				'label'       => __( 'Animation', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'fade'  => __( 'Fade', 'premium-addons-pro' ),
					'grow'  => __( 'Grow', 'premium-addons-pro' ),
					'swing' => __( 'Swing', 'premium-addons-pro' ),
					'slide' => __( 'Slide', 'premium-addons-pro' ),
					'fall'  => __( 'Fall', 'premium-addons-pro' ),
				),
				'default'     => 'fade',
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_preview_image_anim_dur',
			array(
				'label'       => __( 'Animation Duration', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Set the animation duration in milliseconds, default is 350', 'premium-addons-pro' ),
				'default'     => 350,
			)
		);

		$this->add_control(
			'premium_preview_image_delay',
			array(
				'label'       => __( 'Delay', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Set the animation delay in milliseconds, default is 10' ),
				'default'     => 10,
			)
		);

		$this->add_control(
			'premium_preview_image_arrow',
			array(
				'label'        => __( 'Arrow', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => 'Show',
				'label_off'    => 'Hide',
				'description'  => __( 'Show an arrow beside the tooltip', 'premium-addons-pro' ),
				'return_value' => 'true',
			)
		);

		$this->add_control(
			'premium_preview_image_distance',
			array(
				'label'       => __( 'Spacing', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'The distance between the origin and the tooltip in pixels, default is 6', 'premium-addons-pro' ),
				'default'     => -1,
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_min_width',
			array(
				'label'       => __( 'Min Width', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Set a minimum width for the tooltip in pixels, default: 0 (auto width)', 'premium-addons-pro' ),
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_max_width',
			array(
				'label'       => __( 'Max Width', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Set a maximum width for the tooltip in pixels, default: null (no max width)', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_preview_image_custom_height_switcher',
			array(
				'label'        => __( 'Custom Height', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => 'Show',
				'label_off'    => 'Hide',
				'return_value' => 'true',
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_custom_height',
			array(
				'label'       => __( 'Height', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em', '%', 'custom' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 800,
					),
					'em' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'default'     => array(
					'unit' => 'px',
					'size' => 200,
				),
				'label_block' => true,
				'condition'   => array(
					'premium_preview_image_custom_height_switcher'  => 'true',
				),
				'selectors'   => array(
					'.premium-prev-img-tooltip-wrap-{{ID}}' => 'height: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_control(
			'premium_preview_image_side',
			array(
				'label'       => __( 'Side', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => array(
					'top'    => __( 'Top', 'premium-addons-pro' ),
					'right'  => __( 'Right', 'premium-addons-pro' ),
					'bottom' => __( 'Bottom', 'premium-addons-pro' ),
					'left'   => __( 'Left', 'premium-addons-pro' ),
				),
				'description' => __( 'Sets the side of the tooltip. The value may one of the following: \'top\', \'bottom\', \'left\', \'right\'. It may also be an array containing one or more of these values. When using an array, the order of values is taken into account as order of fallbacks and the absence of a side disables it', 'premium-addons-pro' ),
				'default'     => array( 'right', 'left' ),
				'multiple'    => true,
				'label_block' => true,
			)
		);

		$this->update_control(
			'premium_preview_image_side',
			array(
				'type' => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'premium_preview_image_hide',
			array(
				'label'        => __( 'Hide on Mobiles', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => 'Show',
				'label_off'    => 'Hide',
				'description'  => __( 'Hide tooltips on mobile phones', 'premium-addons-pro' ),
				'return_value' => 'true',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_preview_image_trigger_style_settings',
			array(
				'label' => __( 'Trigger', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'text_typo',
				'selector'  => '{{WRAPPER}} .premium-preview-image-trigger',
				'condition' => array(
					'trigger_type' => 'text',
				),
			)
		);

		$this->add_control(
			'svg_color',
			array(
				'label'     => __( 'After Draw Fill Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => false,
				'separator' => 'after',
				'condition' => array(
					'trigger_type' => array( 'icon', 'svg' ),
					'draw_svg'     => 'yes',
				),
			)
		);

		$this->start_controls_tabs(
			'trigger_style_tabs',
			array(
				'condition' => array(
					'trigger_type!' => 'image',
				),
			)
		);

		$this->start_controls_tab(
			'trigger_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'trigger_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-preview-image-trigger' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-preview-image-figure svg, {{WRAPPER}} .premium-preview-image-figure svg *' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'trigger_type!' => array( 'lottie', 'image' ),
				),
			)
		);

		if ( $draw_icon ) {
			$this->add_control(
				'stroke_color',
				array(
					'label'     => __( 'Stroke Color', 'premium-addons-pro' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_ACCENT,
					),
					'condition' => array(
						'trigger_type!' => array( 'lottie', 'image' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-preview-image-figure svg *' => 'stroke: {{VALUE}};',
					),
				)
			);
		}

		$this->add_control(
			'triggger_background_normal',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-preview-image-trigger, {{WRAPPER}} .premium-preview-image-figure.premium-lottie-animation svg'  => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'trigger_border_normal',
				'selector' => '{{WRAPPER}} .premium-preview-image-trigger',
			)
		);

		$this->add_control(
			'trigger_border_radius_normal',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'separator'  => 'after',
				'selectors'  => array(
					'{{WRAPPER}} .premium-preview-image-trigger' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'trigger_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'trigger_color_hover',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-preview-image-figure:hover .premium-preview-image-trigger'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-preview-image-figure:hover svg, {{WRAPPER}} .premium-preview-image-figure:hover svg *'   => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'trigger_type!' => 'lottie',
				),
			)
		);

		if ( $draw_icon ) {
			$this->add_control(
				'stroke_color_hover',
				array(
					'label'     => __( 'Hover Stroke Color', 'premium-addons-pro' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_ACCENT,
					),
					'condition' => array(
						'trigger_type!' => array( 'lottie', 'image' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-preview-image-figure:hover svg *' => 'stroke: {{VALUE}};',
					),
				)
			);
		}

		$this->add_control(
			'trigger_background_hover',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-preview-image-figure:hover .premium-preview-image-trigger, {{WRAPPER}} .premium-lottie-animation:hover svg' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'trigger_border_hover',
				'selector' => '{{WRAPPER}} .premium-preview-image-figure:hover .premium-preview-image-trigger, {{WRAPPER}} .premium-lottie-animation:hover svg',
			)
		);

		$this->add_control(
			'trigger_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'separator'  => 'after',
				'selectors'  => array(
					'{{WRAPPER}} .premium-preview-image-figure:hover .premium-preview-image-trigger, {{WRAPPER}} .premium-lottie-animation:hover svg' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'premium_preview_image_trigger_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-preview-image-trigger'  => 'background-color:{{VALUE}};',
				),
				'condition' => array(
					'trigger_type' => 'image',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'premium_preview_image_trigger_border',
				'selector'  => '{{WRAPPER}} .premium-preview-image-trigger',
				'condition' => array(
					'trigger_type' => 'image',
				),
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_trigger_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-preview-image-trigger' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-top-right-radius: {{RIGHT}}{{UNIT}}; border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'trigger_type' => 'image',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'trigger_text_Shadow',
				'selector'  => '{{WRAPPER}} .premium-preview-image-trigger',
				'condition' => array(
					'trigger_type' => 'text',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_preview_image_trigger_shadow',
				'selector' => '{{WRAPPER}} .premium-preview-image-trigger',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'      => 'css_filters',
				'selector'  => '{{WRAPPER}} .premium-preview-image-trigger',
				'condition' => array(
					'trigger_type' => 'image',
				),
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_trigger_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-preview-image-trigger:not(.premium-preview-image-hover), {{WRAPPER}} .premium-preview-image-figure svg' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .premium-preview-image-hover' => 'margin: 0 {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_trigger_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-preview-image-trigger, {{WRAPPER}} .premium-preview-image-figure svg' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_preview_image_caption_style',
			array(
				'label'     => __( 'Trigger Caption', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_preview_image_caption!' => '',
				),
			)
		);

		$this->add_control(
			'premium_preview_image_caption_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-preview-image-figcap' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_preview_image_caption_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-preview-image-figcap',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'premium_preview_image_caption_shadow',
				'selector' => '{{WRAPPER}} .premium-preview-image-figcap',
			)
		);

		$this->add_control(
			'premium_preview_image_caption_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-preview-image-figcap'    => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_preview_image_caption_border',
				'selector' => '{{WRAPPER}} .premium-preview-image-figcap',
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_caption_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-preview-image-figcap' => 'border-radius: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_caption_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-preview-image-figcap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_caption_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-preview-image-figcap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_preview_image_tooltip_style_settings',
			array(
				'label' => __( 'Preview Window Content', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_preview_image_tooltip_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'div.premium-prev-img-tooltip-wrap-{{ID}}'  => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_preview_image_tooltip_border',
				'selector' => 'div.premium-prev-img-tooltip-wrap-{{ID}}',
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_tooltip_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'div.premium-prev-img-tooltip-wrap-{{ID}}' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-top-right-radius: {{RIGHT}}{{UNIT}}; border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_preview_image_tooltip_shadow',
				'selector' => 'div.premium-prev-img-tooltip-wrap-{{ID}}',
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_tooltip_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'div.premium-prev-img-tooltip-wrap-{{ID}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_tooltip_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'div.premium-prev-img-tooltip-wrap-{{ID}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_preview_image_tooltip_img_style_settings',
			array(
				'label'     => __( 'Preview Window Image', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_preview_image_content_selection' => 'custom',
					'premium_preview_image_img_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_preview_image_tooltip_img_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'.premium-prev-img-tooltip-img-wrap-{{ID}} .premium-preview-image-tooltips-img'  => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_preview_image_tooltip_img_border',
				'selector' => '.premium-prev-img-tooltip-img-wrap-{{ID}} .premium-preview-image-tooltips-img',
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_tooltip_img_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'.premium-prev-img-tooltip-img-wrap-{{ID}} .premium-preview-image-tooltips-img' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-top-right-radius: {{RIGHT}}{{UNIT}}; border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_preview_image_tooltip_img_shadow',
				'selector' => '.premium-prev-img-tooltip-img-wrap-{{ID}} .premium-preview-image-tooltips-img',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'preview_css_filters',
				'selector' => '.premium-prev-img-tooltip-img-wrap-{{ID}} .premium-preview-image-tooltips-img',
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_tooltip_img_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'.premium-prev-img-tooltip-img-wrap-{{ID}} .premium-preview-image-tooltips-img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_tooltip_img_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'.premium-prev-img-tooltip-img-wrap-{{ID}} .premium-preview-image-tooltips-img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_preview_image_tooltip_title_style_settings',
			array(
				'label'     => __( 'Preview Window Title', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_preview_image_content_selection' => 'custom',
					'premium_preview_image_title_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_preview_image_tooltip_title_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'.premium-prev-img-tooltip-title-wrap-{{ID}} .premium-previmg-tooltip-title'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_preview_image_tooltip_title_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '.premium-prev-img-tooltip-title-wrap-{{ID}} .premium-previmg-tooltip-title',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'premium_preview_image_tooltip_title_shadow',
				'selector' => '.premium-prev-img-tooltip-title-wrap-{{ID}}',
			)
		);

		$this->add_control(
			'premium_preview_image_tooltip_title_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'.premium-prev-img-tooltip-title-wrap-{{ID}}'  => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_preview_image_tooltip_title_border',
				'selector' => '.premium-prev-img-tooltip-title-wrap-{{ID}}',
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_tooltip_title_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'.premium-prev-img-tooltip-title-wrap-{{ID}}' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-top-right-radius: {{RIGHT}}{{UNIT}}; border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_tooltip_title_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'.premium-prev-img-tooltip-title-wrap-{{ID}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_tooltip_title_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'.premium-prev-img-tooltip-title-wrap-{{ID}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_preview_image_tooltip_desc_style_settings',
			array(
				'label'     => __( 'Preview Window Description', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_preview_image_content_selection' => 'custom',
					'premium_preview_image_desc_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_preview_image_tooltip_desc_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'.premium-prev-img-tooltip-desc-wrap-{{ID}}'  => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_preview_image_tooltip_desc_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '.premium-prev-img-tooltip-desc-wrap-{{ID}}',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'premium_preview_image_tooltip_desc_shadow',
				'selector' => '.premium-prev-img-tooltip-desc-wrap-{{ID}}',
			)
		);

		$this->add_control(
			'premium_preview_image_tooltip_desc_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'.premium-prev-img-tooltip-desc-wrap-{{ID}}'  => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_preview_image_tooltip_desc_border',
				'selector' => '.premium-prev-img-tooltip-desc-wrap-{{ID}}',
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_tooltip_desc_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'.premium-prev-img-tooltip-desc-wrap-{{ID}}' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-top-right-radius: {{RIGHT}}{{UNIT}}; border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_tooltip_desc_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'.premium-prev-img-tooltip-desc-wrap-{{ID}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_tooltip_desc_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'.premium-prev-img-tooltip-desc-wrap-{{ID}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_preview_image_tooltip_container',
			array(
				'label' => __( 'Preview Window Container', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_preview_image_tooltip_outer_background',
			array(
				'label'     => __( 'Inner  Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'.tooltipster-sidetip div.tooltipster-box-{{ID}} .tooltipster-content'  => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_preview_image_tooltip_container_background',
			array(
				'label'     => __( 'Outer Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'.tooltipster-sidetip div.tooltipster-box-{{ID}}'  => 'background-color:{{VALUE}};',
					'.premium-tooltipster-base.tooltipster-top div.tooltipster-box-{{ID}} + .tooltipster-arrow .tooltipster-arrow-background' => 'border-top-color: {{VALUE}};',
					'.premium-tooltipster-base.tooltipster-bottom div.tooltipster-box-{{ID}} + .tooltipster-arrow .tooltipster-arrow-background' => 'border-bottom-color: {{VALUE}};',
					'.premium-tooltipster-base.tooltipster-right  div.tooltipster-box-{{ID}} + .tooltipster-arrow .tooltipster-arrow-background' => 'border-right-color: {{VALUE}};',
					'.premium-tooltipster-base.tooltipster-left div.tooltipster-box-{{ID}} + .tooltipster-arrow .tooltipster-arrow-background' => 'border-left-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_preview_image_tooltip_container_border',
				'selector' => '.tooltipster-sidetip div.tooltipster-box-{{ID}}',
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_tooltip_container_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'.tooltipster-sidetip div.tooltipster-box-{{ID}}' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-top-right-radius: {{RIGHT}}{{UNIT}}; border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_preview_image_tooltip_container_shadow',
				'selector' => '.tooltipster-sidetip div.tooltipster-box-{{ID}}',
			)
		);

		$this->add_responsive_control(
			'premium_preview_image_tooltip_containe_rpadding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'.tooltipster-sidetip div.tooltipster-box-{{ID}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render Preview Window output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$id = $this->get_id();

		$content_type = $settings['premium_preview_image_content_selection'];

		if ( 'template' === $content_type ) {
			$template = empty( $settings['premium_preview_image_content_temp'] ) ? $settings['live_temp_content'] : $settings['premium_preview_image_content_temp'];
		} else {

			if ( ! empty( $settings['premium_preview_image_tooltips_image']['url'] ) ) {

				$tooltips_image = $settings['premium_preview_image_tooltips_image'];

				$selected_size = $settings['premium_preview_image_tooltips_image_size_size'];

				$tooltip_image_url = Group_Control_Image_Size::get_attachment_image_src( $tooltips_image['id'], 'premium_preview_image_tooltips_image_size', $settings );

				if ( empty( $tooltip_image_url ) ) {
					$tooltip_image_url = $tooltips_image['url'];
				} else {
					$tooltip_image_url = $tooltip_image_url;
				}

				$this->add_render_attribute( 'tooltip-img-wrap', 'class', 'premium-prev-img-tooltip-img-wrap-' . $id );

				$tooltip_alt = Control_Media::get_image_alt( $settings['premium_preview_image_tooltips_image'] );

				$this->add_render_attribute(
					'tooltip_image',
					array(
						'class' => 'premium-preview-image-tooltips-img',
						'src'   => $tooltip_image_url,
						'alt'   => $tooltip_alt,
					)
				);

			}

			if ( ! empty( $settings['premium_preview_image_desc'] ) ) {
				$this->add_render_attribute(
					'tooltip-desc',
					'class',
					array(
						'premium-prev-img-tooltip-desc-wrap-' . $id,
						'premium-prev-img-tooltip-desc-wrap',
					)
				);
			}
		}

		$tooltip_container = array(
			'background' => $settings['premium_preview_image_tooltip_container_background'],
		);

		if ( ! empty( $settings['premium_preview_image_side'] ) ) {
			if ( is_array( $settings['premium_preview_image_side'] ) ) {
				$tooltip_postion = $settings['premium_preview_image_side'];
			} else {
				$tooltip_postion = explode( ',', str_replace( ' ', '', $settings['premium_preview_image_side'] ) );
			}
		} else {
			$tooltip_postion = array( 'right', 'left' );
		}

		$prev_img_settings = array(
			'anim'         => $settings['premium_preview_image_anim'],
			'animDur'      => ! empty( $settings['premium_preview_image_anim_dur'] ) ? $settings['premium_preview_image_anim_dur'] : 350,
			'delay'        => '' !== $settings['premium_preview_image_delay'] ? $settings['premium_preview_image_delay'] : 10,
			'arrow'        => ( 'true' === $settings['premium_preview_image_arrow'] ) ? true : false,
			'active'       => ( 'yes' === $settings['premium_preview_image_interactive'] ) ? true : false,
			'responsive'   => ( 'yes' === $settings['premium_preview_image_responsive'] ) ? true : false,
			'distance'     => ! empty( $settings['premium_preview_image_distance'] ) ? $settings['premium_preview_image_distance'] : 6,
			'minWidth'     => ! empty( $settings['premium_preview_image_min_width'] ) ? $settings['premium_preview_image_min_width'] : 0,
			'maxWidth'     => ! empty( $settings['premium_preview_image_max_width'] ) ? $settings['premium_preview_image_max_width'] : 'null',
			'minWidthTabs' => ! empty( $settings['premium_preview_image_min_width_tablet'] ) ? $settings['premium_preview_image_min_width_tablet'] : 0,
			'maxWidthTabs' => ! empty( $settings['premium_preview_image_max_width_tablet'] ) ? $settings['premium_preview_image_max_width_tablet'] : 'null',
			'minWidthMobs' => ! empty( $settings['premium_preview_image_min_width_mobile'] ) ? $settings['premium_preview_image_min_width_mobile'] : 0,
			'maxWidthMobs' => ! empty( $settings['premium_preview_image_max_width_mobile'] ) ? $settings['premium_preview_image_max_width_mobile'] : 'null',
			'side'         => $tooltip_postion,
			'container'    => $tooltip_container,
			'hideMobiles'  => ( 'true' === $settings['premium_preview_image_hide'] ) ? true : false,
			'id'           => $id,
			'type'         => $content_type,
		);

		if ( 'yes' === $settings['premium_preview_image_title_switcher'] && ! empty( $settings['premium_preview_image_title'] ) ) {

			$this->add_render_attribute(
				'tooltip-title',
				'class',
				array(
					'premium-prev-img-tooltip-title-wrap-' . $id,
					'premium-prev-img-tooltip-title-wrap',
				)
			);

			$title_tag = PAPRO_Helper::validate_html_tag( $settings['premium_image_preview_title_heading'] );
			$title     = '<' . $title_tag . ' class="premium-previmg-tooltip-title">' . $settings['premium_preview_image_title'] . '</' . $title_tag . '>';
		}

		$this->add_render_attribute(
			'container',
			array(
				'id'            => 'premium-preview-image-main-' . $id,
				'class'         => 'premium-preview-image-wrap',
				'data-settings' => wp_json_encode( $prev_img_settings ),
			)
		);

		if ( 'yes' === $settings['premium_preview_image_link_switcher'] ) {

			if ( 'url' === $settings['premium_preview_image_link_selection'] ) {
				$link = $settings['premium_preview_image_link']['url'];
			} else {
				$link = get_permalink( $settings['premium_preview_image_existing_link'] );
			}

			$this->add_render_attribute(
				'link',
				array(
					'class' => 'premium-preview-img-link',
					'href'  => $link,
				)
			);

			if ( ! empty( $settings['premium_preview_image_link']['is_external'] ) ) {
				$this->add_render_attribute( 'link', 'target', '_blank' );
			}

			if ( ! empty( $settings['premium_preview_image_link']['nofollow'] ) ) {
				$this->add_render_attribute( 'link', 'rel', 'nofollow' );
			}
		}

		$this->add_render_attribute(
			'tooltip',
			array(
				'id'    => 'tooltip_content',
				'class' => array(
					'premium-prev-img-tooltip-wrap',
					'premium-prev-img-tooltip-wrap-' . $id,
				),
			)
		);

		?>

	<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'container' ) ); ?>>
		<div class="premium-preview-image-trig-img-wrap">
			<div class="premium-preview-image-inner-trig-img" data-tooltip-content="#tooltip_content">
				<?php if ( 'yes' === $settings['premium_preview_image_link_switcher'] ) : ?>
					<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'link' ) ); ?>>
				<?php endif; ?>
					<?php $this->render_trigger_image(); ?>
				<?php if ( 'yes' === $settings['premium_preview_image_link_switcher'] ) : ?>
					</a>
				<?php endif; ?>

				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'tooltip' ) ); ?>>
					<?php if ( 'custom' === $content_type ) : ?>
						<?php if ( 'yes' === $settings['premium_preview_image_img_switcher'] ) : ?>
							<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'tooltip-img-wrap' ) ); ?>>
								<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'tooltip_image' ) ); ?>>
							</div>
						<?php endif; ?>

						<?php if ( 'yes' === $settings['premium_preview_image_title_switcher'] && ! empty( $settings['premium_preview_image_title'] ) ) : ?>
							<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'tooltip-title' ) ); ?>>
								<?php echo wp_kses_post( $title ); ?>
							</div>
						<?php endif; ?>

						<?php if ( 'yes' === $settings['premium_preview_image_desc_switcher'] && ! empty( $settings['premium_preview_image_desc'] ) ) : ?>
							<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'tooltip-desc' ) ); ?>>
								<?php echo $this->parse_text_editor( $settings['premium_preview_image_desc'] ); ?>
							</div>
						<?php endif; ?>
						<?php
					else :
						echo $this->getTemplateInstance()->get_template_content( $template ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

		<?php
	}

	/**
	 * Render Trigger Image output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.9.4
	 * @access protected
	 */
	protected function render_trigger_image() {

		$settings = $this->get_settings_for_display();

		if ( 'icon' === $settings['trigger_type'] || 'svg' === $settings['trigger_type'] ) {

			if ( ( 'yes' === $settings['draw_svg'] && 'icon' === $settings['trigger_type'] ) || 'svg' === $settings['trigger_type'] ) {
				$this->add_render_attribute( 'icon', 'class', 'premium-preview-image-trigger' );
			}

			if ( 'yes' === $settings['draw_svg'] ) {

				$this->add_render_attribute( 'container', 'class', 'elementor-invisible' );

				if ( 'icon' === $settings['trigger_type'] ) {

					$this->add_render_attribute( 'icon', 'class', $settings['trigger_icon']['value'] );

				}

				$this->add_render_attribute(
					'icon',
					array(
						'class'            => 'premium-svg-drawer',
						'data-svg-reverse' => $settings['lottie_reverse'],
						'data-svg-loop'    => $settings['lottie_loop'],
						'data-svg-sync'    => $settings['svg_sync'],
						'data-svg-hover'   => $settings['svg_hover'],
						'data-svg-fill'    => $settings['svg_color'],
						'data-svg-frames'  => $settings['frames'],
						'data-svg-yoyo'    => $settings['svg_yoyo'],
						'data-svg-point'   => $settings['lottie_reverse'] ? $settings['end_point']['size'] : $settings['start_point']['size'],
					)
				);

			} else {
				$this->add_render_attribute( 'icon', 'class', 'premium-svg-nodraw' );
			}
		} elseif ( 'image' === $settings['trigger_type'] && ! empty( $settings['premium_preview_image_main']['url'] ) ) {

			$image_main     = $settings['premium_preview_image_main'];
			$image_url_main = Group_Control_Image_Size::get_attachment_image_src( $image_main['id'], 'premium_preview_image_main_size', $settings );
			$image_url      = empty( $image_url_main ) ? $image_main['url'] : $image_url_main;

			$alt = Control_Media::get_image_alt( $settings['premium_preview_image_main'] );

			$this->add_render_attribute(
				'image',
				array(
					'class' => 'premium-preview-image-trigger',
					'alt'   => $alt,
					'src'   => $image_url,
				)
			);

			if ( 'yes' === $settings['hover_image_switcher'] ) {

				$image_hover     = $settings['premium_preview_image_hover'];
				$image_url_hover = Group_Control_Image_Size::get_attachment_image_src( $image_hover['id'], 'premium_preview_image_main_size', $settings );
				$hover_img_src   = empty( $image_url_hover ) ? $image_main['url'] : $image_url_hover;

				$alt_hover = Control_Media::get_image_alt( $settings['premium_preview_image_hover'] );

				$this->add_render_attribute(
					'image_hover',
					array(
						'class' => array( 'premium-preview-image-hover', 'premium-preview-image-trigger' ),
						'alt'   => $alt_hover,
						'src'   => $hover_img_src,
					)
				);
			}
		} elseif ( 'lottie' === $settings['trigger_type'] ) {

			$this->add_render_attribute(
				'trigger_lottie',
				array(
					'class'               => array(
						'premium-prev-image-lottie',
						'premium-preview-image-trigger',
						'premium-lottie-animation',
					),
					'data-lottie-url'     => $settings['lottie_url'],
					'data-lottie-loop'    => $settings['lottie_loop'],
					'data-lottie-reverse' => $settings['lottie_reverse'],
				)
			);

		}

		$this->add_inline_editing_attributes( 'premium_preview_image_caption', 'basic' );
		$this->add_render_attribute( 'premium_preview_image_caption', 'class', 'premium-preview-image-figcap' );

		if ( 'yes' === $settings['float_effects'] ) {

			$this->add_render_attribute( 'container', 'data-float', 'true' );

			if ( 'yes' === $settings['float_translate'] ) {

				$this->add_render_attribute(
					'container',
					array(
						'data-float-translate'       => 'true',
						'data-floatx-start'          => $settings['float_translatex']['sizes']['start'],
						'data-floatx-end'            => $settings['float_translatex']['sizes']['end'],
						'data-floaty-start'          => $settings['float_translatey']['sizes']['start'],
						'data-floaty-end'            => $settings['float_translatey']['sizes']['end'],
						'data-float-translate-speed' => $settings['float_translate_speed']['size'],
					)
				);

			}

			if ( 'yes' === $settings['float_rotate'] ) {

				$this->add_render_attribute(
					'container',
					array(
						'data-float-rotate'       => 'true',
						'data-rotatex-start'      => $settings['float_rotatex']['sizes']['start'],
						'data-rotatex-start'      => $settings['float_rotatex']['sizes']['end'],
						'data-rotatey-start'      => $settings['float_rotatey']['sizes']['start'],
						'data-rotatey-start'      => $settings['float_rotatey']['sizes']['end'],
						'data-rotatez-start'      => $settings['float_rotatez']['sizes']['start'],
						'data-rotatez-start'      => $settings['float_rotatez']['sizes']['end'],
						'data-float-rotate-speed' => $settings['float_rotate_speed']['size'],
					)
				);

			}

			if ( 'yes' === $settings['float_opacity'] ) {

				$this->add_render_attribute(
					'container',
					array(
						'data-float-opacity'       => 'true',
						'data-float-opacity-value' => $settings['float_opacity_value']['size'],
						'data-float-opacity-speed' => $settings['float_opacity_speed']['size'],
					)
				);

			}
		}

		$this->add_render_attribute( 'container', 'class', 'premium-preview-image-figure' );

		$html_tag = PAPRO_Helper::validate_html_tag( 'image' === $settings['trigger_type'] ? 'figure' : 'div' );

		?>

		<<?php echo wp_kses_post( $html_tag ) . ' ' . wp_kses_post( $this->get_render_attribute_string( 'container' ) ); ?>>
			<?php if ( 'image' === $settings['trigger_type'] ) : ?>

				<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'image' ) ); ?>>
				<?php if ( 'yes' === $settings['hover_image_switcher'] ) { ?>
					<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'image_hover' ) ); ?>>
				<?php } ?>

			<?php elseif ( 'text' === $settings['trigger_type'] ) : ?>
				<p class="premium-preview-image-trigger">
					<?php echo wp_kses_post( $settings['trigger_text'] ); ?>
				</p>
				<?php
			elseif ( 'icon' === $settings['trigger_type'] ) :
				if ( 'yes' !== $settings['draw_svg'] ) :
					Icons_Manager::render_icon(
						$settings['trigger_icon'],
						array(
							'class'       => array( 'premium-preview-image-trigger', 'premium-svg-nodraw' ),
							'aria-hidden' => 'true',
						)
					);
				else :
					?>
					<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>></i>
					<?php
				endif;
				?>
			<?php elseif ( 'svg' === $settings['trigger_type'] ) : ?>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>>
					<?php $this->print_unescaped_setting( 'custom_svg' ); ?>
				</div>
			<?php else : ?>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'trigger_lottie' ) ); ?>></div>
			<?php endif; ?>
			<?php if ( ! empty( $settings['premium_preview_image_caption'] ) && 'text' !== $settings['trigger_type'] ) : ?>
				<figcaption <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_preview_image_caption' ) ); ?>>
					<?php echo wp_kses_post( $settings['premium_preview_image_caption'] ); ?>
				</figcaption>
			<?php endif; ?>
			</<?php echo wp_kses_post( $html_tag ); ?>>

		<?php
	}


}
