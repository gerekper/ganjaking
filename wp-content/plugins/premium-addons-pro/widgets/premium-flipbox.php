<?php
/**
 * Premium Hover Box.
 */

namespace PremiumAddonsPro\Widgets;

// PremiumAddonsPro Classes.
use PremiumAddonsPro\Includes\PAPRO_Helper;

// Elementor Classes.
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Text_Shadow;

// PremiumAddons Classes.
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Flipbox
 */
class Premium_Flipbox extends Widget_Base {

	/**
	 * Check Icon Draw Option.
	 *
	 * @since 2.8.4
	 * @access public
	 */
	public function check_icon_draw() {

		$is_enabled = Admin_Helper::check_svg_draw( 'premium-flipbox' );
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
		return 'premium-addon-flip-box';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( '3D Hover Box', 'premium-addons-pro' );
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
		return 'pa-pro-flip-box';
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
	 * Retrieve Widget Dependent CSS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array CSS style handles.
	 */
	public function get_style_depends() {
		return array(
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
				'lottie-js',
				'premium-pro',
			)
		);
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
		return array( 'pa', 'premium', 'flip box', '3d', 'rotate', 'fade', 'info', 'animation' );
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
		return 'https://premiumaddons.com/support/';
	}

	/**
	 * Register Hover Box controls.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$draw_icon = $this->check_icon_draw();

		$this->start_controls_section(
			'premium_flip_front_settings',
			array(
				'label' => __( 'Front', 'premium-addons-pro' ),
			)
		);

		$this->start_controls_tabs( 'premium_flip_front_tabs' );

		$this->start_controls_tab(
			'premium_flip_front_content_tab',
			array(
				'label' => __( 'Content', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_flip_icon_fa_switcher',
			array(
				'label'   => __( 'Icon', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'premium_flip_icon_selection',
			array(
				'label'       => __( 'Icon Type', 'premium-addons-pro' ),
				'description' => __( 'Select type for the icon', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'icon',
				'options'     => array(
					'icon'      => __( 'Font Awesome Icon', 'premium-addons-pro' ),
					'image'     => __( 'Custom Image', 'premium-addons-pro' ),
					'animation' => __( 'Lottie Animation', 'premium-addons-pro' ),
					'svg'       => __( 'SVG Code', 'premium-addons-pro' ),
				),
				'label_block' => true,
				'condition'   => array(
					'premium_flip_icon_fa_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_flip_icon_fa_updated',
			array(
				'label'            => __( 'Icon', 'premium-addons-pro' ),
				'description'      => __( 'Choose an Icon for Front Side', 'premium-addons-pro' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'premium_flip_icon_fa',
				'default'          => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'label_block'      => true,
				'condition'        => array(
					'premium_flip_icon_fa_switcher' => 'yes',
					'premium_flip_icon_selection'   => 'icon',
				),
			)
		);

		$this->add_control(
			'front_custom_svg',
			array(
				'label'       => __( 'SVG Code', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => 'You can use these sites to create SVGs: <a href="https://danmarshall.github.io/google-font-to-svg-path/" target="_blank">Google Fonts</a> and <a href="https://boxy-svg.com/" target="_blank">Boxy SVG</a>',
				'condition'   => array(
					'premium_flip_icon_fa_switcher' => 'yes',
					'premium_flip_icon_selection'   => 'svg',
				),
			)
		);

		$this->add_control(
			'premium_flip_icon_image',
			array(
				'label'       => __( 'Image', 'premium-addons-pro' ),
				'type'        => Controls_Manager::MEDIA,
				'dynamic'     => array( 'active' => true ),
				'default'     => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'description' => __( 'Choose the icon image', 'premium-addons-pro' ),
				'label_block' => true,
				'condition'   => array(
					'premium_flip_icon_fa_switcher' => 'yes',
					'premium_flip_icon_selection'   => 'image',
				),
			)
		);

		$this->add_control(
			'front_lottie_url',
			array(
				'label'       => __( 'Animation JSON URL', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'condition'   => array(
					'premium_flip_icon_fa_switcher' => 'yes',
					'premium_flip_icon_selection'   => 'animation',
				),
			)
		);

		$this->add_responsive_control(
			'premium_flip_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 5,
						'max' => 200,
					),
					'em' => array(
						'min' => 5,
						'max' => 30,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-front-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-flip-text-wrapper svg, {{WRAPPER}}.premium-front-lottie-canvas .premium-flip-text-wrapper .premium-lottie-animation' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important',
				),
				'condition'  => array(
					'premium_flip_icon_fa_switcher' => 'yes',
					'premium_flip_icon_selection!'  => array( 'image', 'svg' ),
				),
			)
		);

		$this->add_responsive_control(
			'premium_flip_image_size',
			array(
				'label'      => __( 'Icon Width', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'size' => 150,
				),
				'range'      => array(
					'px' => array(
						'min' => 5,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-front-image, {{WRAPPER}} .premium-flip-text-wrapper svg' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_flip_icon_fa_switcher' => 'yes',
					'premium_flip_icon_selection'   => array( 'image', 'svg' ),
				),
			)
		);

		$this->add_responsive_control(
			'front_icon_height',
			array(
				'label'      => __( 'Icon Height', 'premium-addons-pro' ),
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
					'premium_flip_icon_selection' => 'svg',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-text-wrapper svg' => 'height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'front_draw_svg',
			array(
				'label'     => __( 'Draw Icon', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'classes'   => $draw_icon ? '' : 'editor-pa-control-disabled',
				'condition' => array(
					'premium_flip_icon_fa_switcher' => 'yes',
					'premium_flip_icon_selection'   => array( 'icon', 'svg' ),
					'premium_flip_icon_fa_updated[library]!' => 'svg',
				),
			)
		);

		$front_anim_conditions = array(
			'terms' => array(
				array(
					'name'  => 'premium_flip_icon_fa_switcher',
					'value' => 'yes',
				),
				array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'terms' => array(
								array(
									'name'  => 'premium_flip_icon_selection',
									'value' => 'animation',
								),
								array(
									'name'     => 'front_lottie_url',
									'operator' => '!==',
									'value'    => '',
								),
							),
						),
						array(
							'terms' => array(
								array(
									'relation' => 'or',
									'terms'    => array(
										array(
											'name'  => 'premium_flip_icon_selection',
											'value' => 'icon',
										),
										array(
											'name'  => 'premium_flip_icon_selection',
											'value' => 'svg',
										),
									),
								),
								array(
									'name'  => 'front_draw_svg',
									'value' => 'yes',
								),
							),
						),
					),
				),
			),
		);

		if ( $draw_icon ) {
			$this->add_control(
				'front_stroke_width',
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
					'condition' => array(
						'premium_flip_icon_selection' => array( 'icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-flip-text-wrapper svg:not(.premium-btn-svg) *' => 'stroke-width: {{SIZE}}',
					),
				)
			);

			$this->add_control(
				'front_svg_sync',
				array(
					'label'     => __( 'Draw All Paths Together', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'premium_flip_icon_fa_switcher' => 'yes',
						'premium_flip_icon_selection'   => array( 'icon', 'svg' ),
						'front_draw_svg'                => 'yes',
					),
				)
			);

			$this->add_control(
				'front_frames',
				array(
					'label'       => __( 'Speed', 'premium-addons-pro' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => __( 'Larger value means longer animation duration.', 'premium-addons-pro' ),
					'default'     => 5,
					'min'         => 1,
					'max'         => 100,
					'condition'   => array(
						'premium_flip_icon_fa_switcher' => 'yes',
						'premium_flip_icon_selection'   => array( 'icon', 'svg' ),
						'front_draw_svg'                => 'yes',
					),
				)
			);
		} elseif ( method_exists( 'PremiumAddons\Includes\Helper_Functions', 'get_draw_svg_notice' ) ) {

			Helper_Functions::get_draw_svg_notice(
				$this,
				'3d',
				array(
					'premium_flip_icon_fa_switcher' => 'yes',
					'premium_flip_icon_selection'   => array( 'icon', 'svg' ),
					'premium_flip_icon_fa_updated[library]!' => 'svg',
				)
			);

		}

		$this->add_control(
			'front_lottie_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'conditions'   => $front_anim_conditions,
			)
		);

		$this->add_control(
			'front_lottie_reverse',
			array(
				'label'        => __( 'Reverse', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'conditions'   => $front_anim_conditions,
			)
		);

		if ( $draw_icon ) {
			$this->add_control(
				'front_start_point',
				array(
					'label'       => __( 'Start Point (%)', 'premium-addons-pro' ),
					'type'        => Controls_Manager::SLIDER,
					'description' => __( 'Set the point that the SVG should start from.', 'premium-addons-pro' ),
					'default'     => array(
						'unit' => '%',
						'size' => 0,
					),
					'condition'   => array(
						'premium_flip_icon_fa_switcher' => 'yes',
						'premium_flip_icon_selection'   => array( 'icon', 'svg' ),
						'front_draw_svg'                => 'yes',
						'front_lottie_reverse!'         => 'true',
					),

				)
			);

			$this->add_control(
				'front_end_point',
				array(
					'label'       => __( 'End Point (%)', 'premium-addons-pro' ),
					'type'        => Controls_Manager::SLIDER,
					'description' => __( 'Set the point that the SVG should end at.', 'premium-addons-pro' ),
					'default'     => array(
						'unit' => '%',
						'size' => 0,
					),
					'condition'   => array(
						'premium_flip_icon_fa_switcher' => 'yes',
						'premium_flip_icon_selection'   => array( 'icon', 'svg' ),
						'front_draw_svg'                => 'yes',
						'front_lottie_reverse'          => 'true',
					),

				)
			);

			$this->add_control(
				'front_svg_yoyo',
				array(
					'label'     => __( 'Yoyo Effect', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'premium_flip_icon_fa_switcher' => 'yes',
						'premium_flip_icon_selection'   => array( 'icon', 'svg' ),
						'front_draw_svg'                => 'yes',
						'front_lottie_loop'             => 'true',
					),
				)
			);
		}

		$this->add_control(
			'front_lottie_renderer',
			array(
				'label'        => __( 'Render As', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'svg'    => __( 'SVG', 'premium-addons-pro' ),
					'canvas' => __( 'Canvas', 'premium-addons-pro' ),
				),
				'default'      => 'svg',
				'render_type'  => 'template',
				'prefix_class' => 'premium-front-lottie-',
				'condition'    => array(
					'premium_flip_icon_fa_switcher' => 'yes',
					'premium_flip_icon_selection'   => 'animation',
					'front_lottie_url!'             => '',
				),
			)
		);

		$this->add_control(
			'premium_flip_title_switcher',
			array(
				'label'   => __( 'Title', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'premium_flip_paragraph_header',
			array(
				'label'       => __( 'Title', 'premium-addons-pro' ),
				'description' => __( 'Type a title for the front side', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => __( 'Front Box Title', 'premium-addons-pro' ),
				'label_block' => true,
				'condition'   => array(
					'premium_flip_title_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_flip_paragraph_header_size',
			array(
				'label'       => __( 'HTML Tag', 'premium-addons-pro' ),
				'description' => __( 'Select the front side title tag', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'h3',
				'options'     => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'label_block' => true,
				'condition'   => array(
					'premium_flip_title_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_flip_description_switcher',
			array(
				'label' => __( 'Description', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_flip_paragraph_text',
			array(
				'label'     => __( 'Description', 'premium-addons-pro' ),
				'type'      => Controls_Manager::WYSIWYG,
				'dynamic'   => array( 'active' => true ),
				'default'   => __( 'Your Cool Description', 'premium-addons-pro' ),
				'condition' => array(
					'premium_flip_description_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_flip_vertical_align',
			array(
				'label'     => __( 'Vertical Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-up',
					),
					'center'     => array(
						'title' => __( 'Middle', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-justify',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-down',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-front-content-container' => 'align-items: {{VALUE}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'premium_flip_horizontal_align',
			array(
				'label'     => __( 'Horizontal Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-justify',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-front-content-container' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_flip_text_align',
			array(
				'label'     => __( 'Content Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'    => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => __( 'Justify', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-front' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_flip_front_background_tab',
			array(
				'label' => __( 'Background', 'premium-addons-pro' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_flip_front_background_type',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-flip-front',
			)
		);

		$this->add_control(
			'premium_flip_overlay_selection',
			array(
				'label'     => __( 'Overlay Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-front-overlay'    => 'background: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_flip_back_settings',
			array(
				'label' => __( 'Back', 'premium-addons-pro' ),
			)
		);

		$this->start_controls_tabs( 'premium_flip_back_tabs' );

		$this->start_controls_tab(
			'premium_flip_back_content_tab',
			array(
				'label' => __( 'Content', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_flip_back_icon_fa_switcher',
			array(
				'label'   => __( 'Icon', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'premium_flip_back_icon_selection',
			array(
				'label'       => __( 'Icon Type', 'premium-addons-pro' ),
				'description' => __( 'Select type for the icon', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'icon',
				'options'     => array(
					'icon'      => __( 'Font Awesome Icon', 'premium-addons-pro' ),
					'image'     => __( 'Custom Image', 'premium-addons-pro' ),
					'animation' => __( 'Lottie Animation', 'premium-addons-pro' ),
					'svg'       => __( 'SVG Code', 'premium-addons-pro' ),
				),
				'label_block' => true,
				'condition'   => array(
					'premium_flip_back_icon_fa_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_flip_back_icon_fa_updated',
			array(
				'label'            => __( 'Icon', 'premium-addons-pro' ),
				'description'      => __( 'Choose an Icon for Back Side', 'premium-addons-pro' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'premium_flip_back_icon_fa',
				'default'          => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'label_block'      => true,
				'condition'        => array(
					'premium_flip_back_icon_fa_switcher' => 'yes',
					'premium_flip_back_icon_selection'   => 'icon',
				),
			)
		);

		$this->add_control(
			'back_custom_svg',
			array(
				'label'       => __( 'SVG Code', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => 'You can use these sites to create SVGs: <a href="https://danmarshall.github.io/google-font-to-svg-path/" target="_blank">Google Fonts</a> and <a href="https://boxy-svg.com/" target="_blank">Boxy SVG</a>',
				'condition'   => array(
					'premium_flip_back_icon_fa_switcher' => 'yes',
					'premium_flip_back_icon_selection'   => 'svg',
				),
			)
		);

		$this->add_control(
			'premium_flip_back_icon_image',
			array(
				'label'       => __( 'Image', 'premium-addons-pro' ),
				'type'        => Controls_Manager::MEDIA,
				'dynamic'     => array( 'active' => true ),
				'default'     => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'description' => __( 'Choose the icon image', 'premium-addons-pro' ),
				'label_block' => true,
				'condition'   => array(
					'premium_flip_back_icon_fa_switcher' => 'yes',
					'premium_flip_back_icon_selection'   => 'image',
				),
			)
		);

		$this->add_control(
			'back_lottie_url',
			array(
				'label'       => __( 'Animation JSON URL', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'condition'   => array(
					'premium_flip_back_icon_fa_switcher' => 'yes',
					'premium_flip_back_icon_selection'   => 'animation',
				),
			)
		);

		$this->add_control(
			'premium_flip_back_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 5,
						'max' => 200,
					),
					'em' => array(
						'min' => 5,
						'max' => 30,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-back-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-flip-back-text-wrapper svg, {{WRAPPER}}.premium-back-lottie-canvas .premium-flip-back-text-wrapper .premium-lottie-animation' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important',
				),
				'condition'  => array(
					'premium_flip_back_icon_fa_switcher' => 'yes',
					'premium_flip_back_icon_selection!'  => array( 'image', 'svg' ),
				),
			)
		);

		$this->add_control(
			'premium_flip_back_image_size',
			array(
				'label'      => __( 'Icon Width', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'size' => 40,
				),
				'range'      => array(
					'px' => array(
						'min' => 5,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-back-image, {{WRAPPER}} .premium-flip-back-text-wrapper svg' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_flip_back_icon_fa_switcher' => 'yes',
					'premium_flip_back_icon_selection'   => array( 'image', 'svg' ),
				),
			)
		);

		$this->add_responsive_control(
			'back_icon_height',
			array(
				'label'      => __( 'Icon Height', 'premium-addons-pro' ),
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
					'premium_flip_back_icon_fa_switcher' => 'yes',
					'premium_flip_back_icon_selection'   => 'svg',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-back-text-wrapper svg' => 'height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'back_draw_svg',
			array(
				'label'     => __( 'Draw Icon', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'classes'   => $draw_icon ? '' : 'editor-pa-control-disabled',
				'condition' => array(
					'premium_flip_back_icon_fa_switcher' => 'yes',
					'premium_flip_back_icon_selection'   => array( 'icon', 'svg' ),
					'premium_flip_back_icon_fa_updated[library]!' => 'svg',
				),
			)
		);

		$back_anim_conditions = array(
			'terms' => array(
				array(
					'name'  => 'premium_flip_back_icon_fa_switcher',
					'value' => 'yes',
				),
				array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'terms' => array(
								array(
									'name'  => 'premium_flip_back_icon_selection',
									'value' => 'animation',
								),
								array(
									'name'     => 'back_lottie_url',
									'operator' => '!==',
									'value'    => '',
								),
							),
						),
						array(
							'terms' => array(
								array(
									'relation' => 'or',
									'terms'    => array(
										array(
											'name'  => 'premium_flip_back_icon_selection',
											'value' => 'icon',
										),
										array(
											'name'  => 'premium_flip_back_icon_selection',
											'value' => 'svg',
										),
									),
								),
								array(
									'name'  => 'back_draw_svg',
									'value' => 'yes',
								),
							),
						),
					),
				),
			),
		);

		if ( $draw_icon ) {
			$this->add_control(
				'back_stroke_width',
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
					'condition' => array(
						'premium_flip_icon_selection' => array( 'icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-flip-back-text-wrapper svg:not(.premium-btn-svg) *' => 'stroke-width: {{SIZE}}',
					),
				)
			);

			$this->add_control(
				'back_svg_sync',
				array(
					'label'     => __( 'Draw All Paths Together', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'premium_flip_back_icon_fa_switcher' => 'yes',
						'premium_flip_back_icon_selection' => array( 'icon', 'svg' ),
						'back_draw_svg'                    => 'yes',
					),
				)
			);

			$this->add_control(
				'back_frames',
				array(
					'label'       => __( 'Speed', 'premium-addons-pro' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => __( 'Larger value means longer animation duration.', 'premium-addons-pro' ),
					'default'     => 5,
					'min'         => 1,
					'max'         => 100,
					'condition'   => array(
						'premium_flip_back_icon_fa_switcher' => 'yes',
						'premium_flip_back_icon_selection' => array( 'icon', 'svg' ),
						'back_draw_svg'                    => 'yes',
					),
				)
			);
		} elseif ( method_exists( 'PremiumAddons\Includes\Helper_Functions', 'get_draw_svg_notice' ) ) {

			Helper_Functions::get_draw_svg_notice(
				$this,
				'3d',
				array(
					'premium_flip_back_icon_fa_switcher' => 'yes',
					'premium_flip_back_icon_selection'   => array( 'icon', 'svg' ),
					'premium_flip_back_icon_fa_updated[library]!' => 'svg',
				),
				1
			);

		}

		$this->add_control(
			'back_lottie_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'conditions'   => $back_anim_conditions,
			)
		);

		$this->add_control(
			'back_lottie_reverse',
			array(
				'label'        => __( 'Reverse', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'conditions'   => $back_anim_conditions,
			)
		);

		if ( $draw_icon ) {
			$this->add_control(
				'back_start_point',
				array(
					'label'       => __( 'Start Point (%)', 'premium-addons-pro' ),
					'type'        => Controls_Manager::SLIDER,
					'description' => __( 'Set the point that the SVG should start from.', 'premium-addons-pro' ),
					'default'     => array(
						'unit' => '%',
						'size' => 0,
					),
					'condition'   => array(
						'premium_flip_back_icon_fa_switcher' => 'yes',
						'premium_flip_back_icon_selection' => array( 'icon', 'svg' ),
						'front_draw_svg'                   => 'yes',
						'front_lottie_reverse!'            => 'true',
					),

				)
			);

			$this->add_control(
				'back_end_point',
				array(
					'label'       => __( 'End Point (%)', 'premium-addons-pro' ),
					'type'        => Controls_Manager::SLIDER,
					'description' => __( 'Set the point that the SVG should end at.', 'premium-addons-pro' ),
					'default'     => array(
						'unit' => '%',
						'size' => 0,
					),
					'condition'   => array(
						'premium_flip_back_icon_fa_switcher' => 'yes',
						'premium_flip_back_icon_selection' => array( 'icon', 'svg' ),
						'back_draw_svg'                    => 'yes',
						'back_lottie_reverse'              => 'true',
					),

				)
			);

			$this->add_control(
				'back_svg_yoyo',
				array(
					'label'     => __( 'Yoyo Effect', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'premium_flip_back_icon_fa_switcher' => 'yes',
						'premium_flip_back_icon_selection' => array( 'icon', 'svg' ),
						'back_draw_svg'                    => 'yes',
						'back_lottie_loop'                 => 'true',
					),
				)
			);
		}

		$this->add_control(
			'back_lottie_renderer',
			array(
				'label'        => __( 'Render As', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'svg'    => __( 'SVG', 'premium-addons-pro' ),
					'canvas' => __( 'Canvas', 'premium-addons-pro' ),
				),
				'default'      => 'svg',
				'render_type'  => 'template',
				'prefix_class' => 'premium-back-lottie-',
				'condition'    => array(
					'premium_flip_back_icon_fa_switcher' => 'yes',
					'premium_flip_back_icon_selection'   => 'animation',
					'back_lottie_url!'                   => '',
				),
			)
		);

		$this->add_control(
			'premium_flip_back_title_switcher',
			array(
				'label'   => __( 'Title', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'premium_flip_back_paragraph_header',
			array(
				'label'       => __( 'Title', 'premium-addons-pro' ),
				'description' => __( 'Type a title for the back side', 'premium-addons-pro' ),
				'dynamic'     => array( 'active' => true ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Back Box Title', 'premium-addons-pro' ),
				'label_block' => true,
				'condition'   => array(
					'premium_flip_back_title_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_flip_back_paragraph_header_size',
			array(
				'label'       => __( 'HTML Tag', 'premium-addons-pro' ),
				'description' => __( 'Select the tag for the title', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'h3',
				'options'     => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'label_block' => true,
				'condition'   => array(
					'premium_flip_back_title_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_flip_back_description_switcher',
			array(
				'label' => __( 'Description', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_flip_back_paragraph_text',
			array(
				'label'       => __( 'Description', 'premium-addons-pro' ),
				'type'        => Controls_Manager::WYSIWYG,
				'dynamic'     => array( 'active' => true ),
				'default'     => __( 'Your Cool Description', 'premium-addons-pro' ),
				'label_block' => true,
				'condition'   => array(
					'premium_flip_back_description_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_flip_back_vertical_align',
			array(
				'label'     => __( 'Vertical Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-up',
					),
					'center'     => array(
						'title' => __( 'Middle', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-justify',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-down',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-back-content-container' => 'align-items: {{VALUE}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'premium_flip_back_horizontal_align',
			array(
				'label'     => __( 'Horizontal Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-justify',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-back-content-container' => 'justify-content: {{VALUE}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'premium_flip_back_text_align',
			array(
				'label'     => __( 'Content Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'    => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => __( 'Justify', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-back' => 'text-align: {{VALUE}};',
				),
			)
		);

        $this->end_controls_tab();

		$this->start_controls_tab(
			'premium_flip_back_background_tab',
			array(
				'label' => __( 'Background', 'premium-addons-pro' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_flip_back_background_type',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-flip-back',
			)
		);

		$this->add_control(
			'premium_flip_back_overlay_selection',
			array(
				'label'     => __( 'Overlay Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-back-overlay'    => 'background: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

        $this->start_controls_section(
			'backside_link_section',
			array(
				'label' => __( 'Back Side Link', 'premium-addons-pro' ),
			)
		);

        $this->add_control(
			'premium_flip_back_link_switcher',
			array(
				'label' => __( 'Link', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_flip_back_link_trigger',
			array(
				'label'       => __( 'Apply on', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'text' => __( 'Button Only', 'premium-addons-pro' ),
					'full' => __( 'Whole Box', 'premium-addons-pro' ),
				),
				'default'     => 'text',
				'label_block' => true,
				'condition'   => array(
					'premium_flip_back_link_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_flip_back_link_text',
			array(
				'label'       => __( 'Text', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => __( 'Click Me', 'premium-addons-pro' ),
				'label_block' => true,
				'condition'   => array(
					'premium_flip_back_link_trigger'  => 'text',
					'premium_flip_back_link_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_flip_back_link_selection',
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
					'premium_flip_back_link_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_flip_back_link',
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
					'premium_flip_back_link_switcher'  => 'yes',
					'premium_flip_back_link_selection' => 'url',
				),
			)
		);

		$this->add_control(
			'premium_flip_back_existing_link',
			array(
				'label'       => __( 'Existing Page', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_all_posts(),
				'condition'   => array(
					'premium_flip_back_link_switcher'  => 'yes',
					'premium_flip_back_link_selection' => 'link',
				),
				'multiple'    => false,
				'separator'   => 'after',
				'label_block' => true,
			)
		);

        if ( version_compare( PREMIUM_ADDONS_VERSION, '4.10.17', '>' ) ) {
            Helper_Functions::add_btn_hover_controls( $this, array(
                'premium_flip_back_link_switcher'  => 'yes',
                'premium_flip_back_link_trigger'  => 'text',
                )
            );
        }

        $this->end_controls_section();

		$this->start_controls_section(
			'premium_flip_control_settings',
			array(
				'label' => __( 'Additional Settings', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_flip_style',
			array(
				'label'        => __( 'Effect', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'fade'     => __( 'Fade', 'premium-addons-pro' ),
					'flip'     => __( 'Flip', 'premium-addons-pro' ),
					'slide'    => __( 'Slide', 'premium-addons-pro' ),
					'push'     => __( 'Push', 'premium-addons-pro' ),
					'cube'     => __( 'Cube', 'premium-addons-pro' ),
					'zoom'     => __( 'Zoom', 'premium-addons-pro' ),
					'zoom-in'  => __( 'Faded Zoom In', 'premium-addons-pro' ),
					'zoom-out' => __( 'Faded Zoom Out', 'premium-addons-pro' ),
				),
				'prefix_class' => 'premium-flip-style-',
				'render_type'  => 'template',
				'default'      => 'flip',
			)
		);

		$this->add_control(
			'premium_flip_direction',
			array(
				'label'       => __( 'Direction', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'rl' => __( 'Right to Left', 'premium-addons-pro' ),
					'lr' => __( 'Left to Right', 'premium-addons-pro' ),
					'tb' => __( 'Top to Bottom', 'premium-addons-pro' ),
					'bt' => __( 'Bottom to Top', 'premium-addons-pro' ),
				),
				'render_type' => 'template',
				'default'     => 'rl',
				'condition'   => array(
					'premium_flip_style!' => array( 'fade', 'zoom', 'zoom-out', 'zoom-in' ),
				),
			)
		);

		$this->add_responsive_control(
			'premium_flip_box_perspective',
			array(
				'label'       => __( 'Perspective', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'Controls how close the cube to the eye, set higher perspective value for higher height values for better 3D effect.', 'premium-addons-pro' ),
				'default'     => array(
					'size' => 5,
				),
				'range'       => array(
					'px' => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'render_type' => 'template',
				'selectors'   => array(
					'{{WRAPPER}}.premium-flip-style-cube, {{WRAPPER}}.premium-flip-style-cube .premium-flip-main-box'    => 'perspective: calc( 1000 * {{SIZE}}{{UNIT}} ) !important ',
				),
				'condition'   => array(
					'premium_flip_style' => 'cube',
				),
			)
		);

		$this->add_control(
			'premium_flip_text_animation',
			array(
				'label'     => __( 'Hover Text Animation', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'premium_flip_style' => 'flip',
				),
			)
		);

		$this->add_responsive_control(
			'premium_flip_box_height',
			array(
				'label'     => __( 'Height', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 380,
				),
				'range'     => array(
					'px' => array(
						'min' => 155,
						'max' => 1500,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-main-box' => 'height: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'transition_duration',
			array(
				'label'     => __( 'Speed', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0.1,
						'max'  => 5,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-front, {{WRAPPER}} .premium-flip-back'   => 'transition-duration: {{SIZE}}s',
				),
			)
		);

		$this->start_controls_tabs( 'premium_flip_box_border_tabs' );

		$this->start_controls_tab(
			'premium_flip_box_border_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_flip_border_settings_normal',
				'selector' => '{{WRAPPER}} .premium-flip-front',
			)
		);

		$this->add_control(
			'premium_flip_border_radius_normal',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 150,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-front, {{WRAPPER}}.premium-flip-style-flip .premium-flip-front-overlay'  => 'border-radius: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'front_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'front_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-pro' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'front_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-front, {{WRAPPER}}.premium-flip-style-flip .premium-flip-front-overlay' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'front_adv_radius' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_flip_box_border_hover',
			array(
				'label' => __( 'hover', 'premium-addons-pro' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_flip_border_settings_hover',
				'selector' => '{{WRAPPER}} .premium-flip-main-box:hover .premium-flip-back',
			)
		);

		$this->add_control(
			'premium_flip_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 150,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-back, {{WRAPPER}}.premium-flip-style-flip .premium-flip-back-overlay'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'back_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'back_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-pro' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'back_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-back, {{WRAPPER}}.premium-flip-style-flip .premium-flip-back-overlay' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'back_adv_radius' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-pro' ),
			)
		);

		$doc1_url = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/flip-box-widget-tutorial/', 'editor-page', 'wp-editor', 'get-support' );

		$this->add_control(
			'doc_1',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc1_url, __( 'Getting started »', 'premium-addons-pro' ) ),
				'content_classes' => 'editor-pa-doc',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_flip_front_section_title_style',
			array(
				'label' => __( 'Front', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'front_svg_color',
			array(
				'label'     => __( 'After Draw Fill Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => false,
				'condition' => array(
					'premium_flip_icon_fa_switcher' => 'yes',
					'premium_flip_icon_selection'   => array( 'icon', 'svg' ),
					'front_draw_svg'                => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'frontboxshadow',
				'selector' => '{{WRAPPER}} .premium-flip-front',
			)
		);

		$this->start_controls_tabs( 'premium_flip_box_style_tabs' );

		$this->start_controls_tab(
			'premium_flip_box_icon_style',
			array(
				'label'     => __( 'Icon', 'premium-addons-pro' ),
				'condition' => array(
					'premium_flip_icon_fa_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_flip_fa_color_selection',
			array(
				'label'       => __( 'Color', 'premium-addons-pro' ),
				'type'        => Controls_Manager::COLOR,
				'global'      => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-flip-front-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-flip-text-wrapper .premium-drawable-icon' => 'fill: {{VALUE}};',
				),
				'render_type' => 'template',
				'condition'   => array(
					'premium_flip_icon_fa_switcher' => 'yes',
					'premium_flip_icon_selection'   => array( 'icon', 'svg' ),
				),
			)
		);

		if ( $draw_icon ) {
			$this->add_control(
				'front_stroke_color',
				array(
					'label'     => __( 'Stroke Color', 'premium-addons-pro' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_ACCENT,
					),
					'condition' => array(
						'premium_flip_icon_fa_switcher' => 'yes',
						'premium_flip_icon_selection'   => array( 'icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-flip-text-wrapper .premium-drawable-icon *' => 'stroke: {{VALUE}};',
					),
				)
			);
		}

		$this->add_control(
			'premium_flip_fa_color_background_selection',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-front-icon, {{WRAPPER}} .premium-flip-front-image, {{WRAPPER}} .premium-flip-front-lottie'    => 'background: {{VALUE}};',
				),
				'condition' => array(
					'premium_flip_icon_fa_switcher' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'premium_flip_icon_border',
				'selector'  => '{{WRAPPER}} .premium-flip-front-icon, {{WRAPPER}} .premium-flip-front-image, {{WRAPPER}} .premium-flip-front-lottie',
				'condition' => array(
					'premium_flip_icon_fa_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_flip_icon_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-front-icon, {{WRAPPER}} .premium-flip-front-image, {{WRAPPER}} .premium-flip-front-lottie'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_flip_icon_fa_switcher' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'     => __( 'Shadow', 'premium-addons-pro' ),
				'name'      => 'premium_flip_icon_shadow',
				'selector'  => '{{WRAPPER}} .premium-flip-front-icon',
				'condition' => array(
					'premium_flip_icon_fa_switcher' => 'yes',
					'premium_flip_icon_selection'   => 'icon',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'     => __( 'Shadow', 'premium-addons-pro' ),
				'name'      => 'premium_flip_image_shadow',
				'selector'  => '{{WRAPPER}} .premium-flip-front-image',
				'condition' => array(
					'premium_flip_icon_fa_switcher' => 'yes',
					'premium_flip_icon_selection'   => 'image',
				),
			)
		);

		$this->add_responsive_control(
			'premium_flip_icon_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-front-icon , {{WRAPPER}} .premium-flip-front-image, {{WRAPPER}} .premium-flip-front-lottie' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_flip_icon_fa_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_flip_icon_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-front-icon, {{WRAPPER}} .premium-flip-front-image, {{WRAPPER}} .premium-flip-front-lottie' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_flip_icon_fa_switcher' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_flip_box_title_style',
			array(
				'label'     => __( 'Title', 'premium-addons-pro' ),
				'condition' => array(
					'premium_flip_title_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_flip_title_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-front-title' => 'color: {{VALUE}};',
				),
				'separator' => 'before',
				'condition' => array(
					'premium_flip_title_switcher' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'premium_flip_front_title_typo',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector'  => '{{WRAPPER}} .premium-flip-front-title',
				'condition' => array(
					'premium_flip_title_switcher' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'premium_flip_title_shadow',
				'selector'  => '{{WRAPPER}} .premium-flip-front-title',
				'condition' => array(
					'premium_flip_title_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_flip_title_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-front-title'    => 'background: {{VALUE}};',
				),
				'condition' => array(
					'premium_flip_title_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_flip_title_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-front-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_flip_title_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_flip_title_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-front-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_flip_title_switcher' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_flip_box_description_style',
			array(
				'label'     => __( 'Description', 'premium-addons-pro' ),
				'condition' => array(
					'premium_flip_description_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_flip_desc_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-front-description' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'premium_flip_description_switcher' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'premium_flip_desc_typography',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector'  => '{{WRAPPER}} .premium-flip-front-description',
				'condition' => array(
					'premium_flip_description_switcher' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'premium_flip_description_shadow',
				'selector'  => '{{WRAPPER}} .premium-flip-front-description',
				'condition' => array(
					'premium_flip_description_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_flip_description_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-front-description'    => 'background: {{VALUE}};',
				),
				'condition' => array(
					'premium_flip_description_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_flip_desc_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-front-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_flip_description_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_flip_desc_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-front-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_flip_description_switcher' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'back_section_title_style',
			array(
				'label' => __( 'Back', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'back_svg_color',
			array(
				'label'     => __( 'After Draw Fill Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => false,
				'condition' => array(
					'premium_flip_back_icon_fa_switcher' => 'yes',
					'premium_flip_back_icon_selection'   => array( 'icon', 'svg' ),
					'back_draw_svg'                      => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'backboxshadow',
				'selector' => '{{WRAPPER}} .premium-flip-back',
			)
		);

		$this->start_controls_tabs( 'premium_flip_box_back_style_tabs' );

		$this->start_controls_tab(
			'premium_flip_box_back_icon_style',
			array(
				'label'     => __( 'Icon', 'premium-addons-pro' ),
				'condition' => array(
					'premium_flip_back_icon_fa_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_flip_back_fa_color_selection',
			array(
				'label'       => __( 'Color', 'premium-addons-pro' ),
				'type'        => Controls_Manager::COLOR,
				'global'      => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-flip-back-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .premium-flip-back-text-wrapper .premium-drawable-icon' => 'fill: {{VALUE}}',
				),
				'render_type' => 'template',
				'condition'   => array(
					'premium_flip_back_icon_fa_switcher' => 'yes',
					'premium_flip_back_icon_selection'   => array( 'icon', 'svg' ),
				),
			)
		);

		if ( $draw_icon ) {
			$this->add_control(
				'back_stroke_color',
				array(
					'label'     => __( 'Stroke Color', 'premium-addons-pro' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_ACCENT,
					),
					'condition' => array(
						'premium_flip_back_icon_fa_switcher' => 'yes',
						'premium_flip_icon_selection' => array( 'icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-flip-back-text-wrapper .premium-drawable-icon *' => 'stroke: {{VALUE}};',
					),
				)
			);
		}

		$this->add_control(
			'premium_flip_back_fa_color_background_selection',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-back-icon, {{WRAPPER}} .premium-flip-back-image, {{WRAPPER}} .premium-flip-back-lottie'    => 'background: {{VALUE}};',
				),
				'condition' => array(
					'premium_flip_back_icon_fa_switcher' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'premium_flip_back_icon_border',
				'selector'  => '{{WRAPPER}} .premium-flip-back-icon, {{WRAPPER}} .premium-flip-back-image, {{WRAPPER}} .premium-flip-back-lottie',
				'condition' => array(
					'premium_flip_back_icon_fa_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_flip_back_icon_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-back-icon, {{WRAPPER}} .premium-flip-back-image, {{WRAPPER}} .premium-flip-back-lottie'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_flip_back_icon_fa_switcher' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'     => __( 'Shadow', 'premium-addons-pro' ),
				'name'      => 'premium_flip_back_icon_shadow',
				'selector'  => '{{WRAPPER}} .premium-flip-back-icon',
				'condition' => array(
					'premium_flip_back_icon_fa_switcher' => 'yes',
					'premium_flip_back_icon_selection'   => 'icon',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'     => __( 'Shadow', 'premium-addons-pro' ),
				'name'      => 'premium_flip_back_image_shadow',
				'selector'  => '{{WRAPPER}} .premium-flip-back-image',
				'condition' => array(
					'premium_flip_back_icon_fa_switcher' => 'yes',
					'premium_flip_back_icon_selection'   => 'image',
				),
			)
		);

		$this->add_responsive_control(
			'premium_flip_back_icon_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-back-icon, {{WRAPPER}} .premium-flip-back-image, {{WRAPPER}} .premium-flip-back-lottie' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_flip_back_icon_fa_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_flip_back_icon_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-back-icon, {{WRAPPER}} .premium-flip-back-image, {{WRAPPER}} .premium-flip-back-lottie' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_flip_back_icon_fa_switcher' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_flip_box_back_title_style',
			array(
				'label'     => __( 'Title', 'premium-addons-pro' ),
				'condition' => array(
					'premium_flip_back_title_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_flip_back_title_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-back-title' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'premium_flip_back_title_switcher' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'premium_flip_back_title_typo',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector'  => '{{WRAPPER}} .premium-flip-back-title',
				'condition' => array(
					'premium_flip_back_title_switcher' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'premium_flip_back_title_shadow',
				'selector'  => '{{WRAPPER}} .premium-flip-back-title',
				'condition' => array(
					'premium_flip_back_title_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_flip_back_title_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-back-title' => 'background: {{VALUE}};',
					'condition'                            => array(
						'premium_flip_back_title_switcher' => 'yes',
					),
				),
			)
		);

		$this->add_responsive_control(
			'premium_flip_back_title_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-back-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_flip_back_title_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_flip_back_title_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-back-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_flip_back_title_switcher' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_flip_box_back_description_style',
			array(
				'label'     => __( 'Description', 'premium-addons-pro' ),
				'condition' => array(
					'premium_flip_back_description_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_flip_back_desc_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-back-description' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'premium_flip_back_description_switcher' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'premium_flip_back_desc_typography',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector'  => '{{WRAPPER}} .premium-flip-back-description',
				'condition' => array(
					'premium_flip_back_description_switcher' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'premium_flip_back_description_shadow',
				'selector'  => '{{WRAPPER}} .premium-flip-back-description',
				'condition' => array(
					'premium_flip_back_description_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_flip_back_description_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-back-description'    => 'background: {{VALUE}};',
				),
				'condition' => array(
					'premium_flip_back_description_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_flip_back_desc_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-back-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}}  {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_flip_back_description_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_flip_back_desc_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-back-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}}  {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_flip_back_description_switcher' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_flip_box_link_style',
			array(
				'label'     => __( 'Link', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_flip_back_link_switcher' => 'yes',
					'premium_flip_back_link_trigger'  => 'text',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'premium_flip_box_link_typo',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector'  => '{{WRAPPER}} .premium-flip-box-link',
				'condition' => array(
					'premium_flip_back_link_switcher' => 'yes',
					'premium_flip_back_link_trigger'  => 'text',
				),
			)
		);

		$this->start_controls_tabs( 'premium_flip_box_link_style_tabs' );

		$this->start_controls_tab(
			'premium_flip_box_link_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_flip_box_link_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-box-link' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_flip_box_link_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
                'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-box-link, {{WRAPPER}} .premium-button-style2-shutinhor:before, {{WRAPPER}} .premium-button-style2-shutinver:before, {{WRAPPER}} .premium-button-style5-radialin:before, {{WRAPPER}} .premium-button-style5-rectin:before' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_flip_box_link_border',
				'selector' => '{{WRAPPER}} .premium-flip-box-link',
			)
		);

		$this->add_control(
			'premium_flip_box_link_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-box-link' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

        $this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'premium_flip_box_link_shadow',
				'selector' => '{{WRAPPER}} .premium-flip-box-link',
			)
		);

		$this->add_responsive_control(
			'premium_flip_box_link_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-box-link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_flip_box_link_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-box-link, {{WRAPPER}} .premium-button-line6::after' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_flip_box_link_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_flip_box_link_hover_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-flip-box-link:hover, {{WRAPPER}} .premium-button-line6::after'   => 'color: {{VALUE}};',
				),
			)
		);

        $this->add_control(
			'underline_color',
			array(
				'label'     => __( 'Line Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
                'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-btn-svg'   => 'stroke: {{VALUE}};',
                    '{{WRAPPER}} .premium-button-line2::before, {{WRAPPER}} .premium-button-line4::before, {{WRAPPER}} .premium-button-line5::before, {{WRAPPER}} .premium-button-line5::after, {{WRAPPER}} .premium-button-line6::before, {{WRAPPER}} .premium-button-line7::before'   => 'background-color: {{VALUE}};'
				),
				'condition' => array(
					'premium_button_hover_effect' => 'style8',
				),
			)
		);

        $this->add_control(
			'first_layer_hover',
			array(
				'label'     => __( 'Layer #1 Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-button-style7 .premium-button-text-icon-wrapper:before' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'premium_button_hover_effect' => 'style7',

				),
			)
		);

		$this->add_control(
			'second_layer_hover',
			array(
				'label'     => __( 'Layer #2 Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-button-style7 .premium-button-text-icon-wrapper:after' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'premium_button_hover_effect' => 'style7',
				),
			)
		);

		$this->add_control(
			'premium_flip_box_link_hover_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
                'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-button-none:hover, {{WRAPPER}} .premium-button-style8:hover, {{WRAPPER}} .premium-button-style1:before, {{WRAPPER}} .premium-button-style2-shutouthor:before, {{WRAPPER}} .premium-button-style2-shutoutver:before, {{WRAPPER}} .premium-button-style2-shutinhor, {{WRAPPER}} .premium-button-style2-shutinver, {{WRAPPER}} .premium-button-style2-dshutinhor:before, {{WRAPPER}} .premium-button-style2-dshutinver:before, {{WRAPPER}} .premium-button-style2-scshutouthor:before, {{WRAPPER}} .premium-button-style2-scshutoutver:before, {{WRAPPER}} .premium-button-style5-radialin, {{WRAPPER}} .premium-button-style5-radialout:before, {{WRAPPER}} .premium-button-style5-rectin, {{WRAPPER}} .premium-button-style5-rectout:before, {{WRAPPER}} .premium-button-style6-bg, {{WRAPPER}} .premium-button-style6:before' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_flip_box_link_hover_border',
				'selector' => '{{WRAPPER}} .premium-flip-box-link:hover',
			)
		);

		$this->add_control(
			'premium_flip_box_link_hover_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-box-link:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

        $this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'premium_flip_box_link_hover_shadow',
				'selector' => '{{WRAPPER}} .premium-flip-box-link:hover',
			)
		);

		$this->add_responsive_control(
			'premium_flip_box_link_hover_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-box-link:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_flip_box_link_hover_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-flip-box-link:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render Hover Hover widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$trigger = $settings['premium_flip_back_link_trigger'];

		$front_icon = $settings['premium_flip_icon_selection'];
		$back_icon  = $settings['premium_flip_back_icon_selection'];

		if ( 'url' === $settings['premium_flip_back_link_selection'] ) {

			$this->add_link_attributes( 'link', $settings['premium_flip_back_link'] );

		} else {

			$this->add_render_attribute( 'link', 'href', get_permalink( $settings['premium_flip_back_existing_link'] ) );
		}

		if ( 'full' === $trigger ) {
			$this->add_render_attribute(
				'link',
				array(
					'class'      => 'premium-flip-box-full-link',
					'aria-label' => $settings['premium_flip_back_paragraph_header'],
				)
			);
		} else {

            $effect_class = '';
            if ( version_compare( PREMIUM_ADDONS_VERSION, '4.10.17', '>' ) ) {
                $effect_class = Helper_Functions::get_button_class( $settings );
            }

            $this->add_render_attribute( 'link', array(
                'class' => array(
                    'premium-flip-box-link text',
                    $effect_class
                ),
                'data-text' =>  $settings['premium_flip_back_link_text']
            ));
		}

		if ( 'yes' === $settings['premium_flip_icon_fa_switcher'] ) {

			if ( 'icon' === $front_icon || 'svg' === $front_icon ) {

				$this->add_render_attribute( 'front_icon', 'class', 'premium-drawable-icon' );

				if ( 'icon' === $front_icon ) {

					if ( ! empty( $settings['premium_flip_icon_fa'] ) ) {

						$this->add_render_attribute(
							'front_icon',
							array(
								'class'       => array(
									'premium-flip-front-icon',
									$settings['premium_flip_icon_fa'],
								),
								'aria-hidden' => 'true',
							)
						);

					}

					$front_migrated = isset( $settings['__fa4_migrated']['premium_flip_icon_fa_updated'] );
					$front_new      = empty( $settings['premium_flip_icon_fa'] ) && Icons_Manager::is_migration_allowed();

				}

				if ( ( 'yes' === $settings['front_draw_svg'] && 'icon' === $front_icon ) || 'svg' === $front_icon ) {
					$this->add_render_attribute( 'front_icon', 'class', 'premium-flip-front-icon' );
				}

				if ( 'yes' === $settings['front_draw_svg'] ) {

					if ( 'icon' === $front_icon ) {

						$this->add_render_attribute( 'front_icon', 'class', $settings['premium_flip_icon_fa_updated']['value'] );

					}

					$this->add_render_attribute(
						'front_icon',
						array(
							'class'            => 'premium-svg-drawer',
							'data-svg-reverse' => $settings['front_lottie_reverse'],
							'data-svg-loop'    => $settings['front_lottie_loop'],
							'data-svg-sync'    => $settings['front_svg_sync'],
							'data-svg-fill'    => $settings['front_svg_color'],
							'data-svg-frames'  => $settings['front_frames'],
							'data-svg-yoyo'    => $settings['front_svg_yoyo'],
							'data-svg-point'   => $settings['front_lottie_reverse'] ? $settings['front_end_point']['size'] : $settings['front_start_point']['size'],
						)
					);

				} else {
					$this->add_render_attribute( 'front_icon', 'class', 'premium-svg-nodraw' );
				}
			} elseif ( 'image' === $front_icon ) {

				$this->add_render_attribute(
					'front_image',
					array(
						'class' => 'premium-flip-front-image',
						'src'   => $settings['premium_flip_icon_image']['url'],
						'alt'   => Control_Media::get_image_alt( $settings['premium_flip_icon_image'] ),
					)
				);

			} else {
				$this->add_render_attribute(
					'front_lottie',
					array(
						'class'               => array(
							'premium-flip-front-lottie',
							'premium-lottie-animation',
						),
						'data-lottie-url'     => $settings['front_lottie_url'],
						'data-lottie-loop'    => $settings['front_lottie_loop'],
						'data-lottie-reverse' => $settings['front_lottie_reverse'],
						'data-lottie-render'  => $settings['front_lottie_renderer'],
					)
				);
			}
		}

		if ( 'yes' === $settings['premium_flip_back_icon_fa_switcher'] ) {

			if ( 'icon' === $back_icon || 'svg' === $back_icon ) {

				$this->add_render_attribute( 'back_icon', 'class', 'premium-drawable-icon' );

				if ( ! empty( $settings['premium_flip_back_icon_fa'] ) ) {

					$this->add_render_attribute(
						'back_icon',
						array(
							'class'       => array(
								'premium-flip-back-icon',
								$settings['premium_flip_back_icon_fa'],
							),
							'aria-hidden' => 'true',
						)
					);

				}

				$back_migrated = isset( $settings['__fa4_migrated']['premium_flip_back_icon_fa_updated'] );
				$back_new      = empty( $settings['premium_flip_back_icon_fa'] ) && Icons_Manager::is_migration_allowed();

				if ( ( 'yes' === $settings['back_draw_svg'] && 'icon' === $back_icon ) || 'svg' === $back_icon ) {
					$this->add_render_attribute( 'back_icon', 'class', 'premium-flip-back-icon' );
				}

				if ( 'yes' === $settings['back_draw_svg'] ) {

					$this->add_render_attribute( 'container', 'class', 'premium-drawer-hover' );

					if ( 'icon' === $back_icon ) {

						$this->add_render_attribute( 'back_icon', 'class', $settings['premium_flip_back_icon_fa_updated']['value'] );

					}

					$this->add_render_attribute(
						'back_icon',
						array(
							'class'            => 'premium-svg-drawer',
							'data-svg-reverse' => $settings['back_lottie_reverse'],
							'data-svg-loop'    => $settings['back_lottie_loop'],
							'data-svg-hover'   => true,
							'data-svg-sync'    => $settings['back_svg_sync'],
							'data-svg-fill'    => $settings['back_svg_color'],
							'data-svg-frames'  => $settings['back_frames'],
							'data-svg-yoyo'    => $settings['back_svg_yoyo'],
							'data-svg-point'   => $settings['back_lottie_reverse'] ? $settings['back_end_point']['size'] : $settings['back_start_point']['size'],
						)
					);

				} else {
					$this->add_render_attribute( 'back_icon', 'class', 'premium-svg-nodraw' );
				}
			} elseif ( 'image' === $back_icon ) {

				$this->add_render_attribute(
					'back_image',
					array(
						'class' => 'premium-flip-back-image',
						'src'   => $settings['premium_flip_back_icon_image']['url'],
						'alt'   => Control_Media::get_image_alt( $settings['premium_flip_back_icon_image'] ),
					)
				);

			} else {
				$this->add_render_attribute(
					'back_lottie',
					array(
						'class'               => array(
							'premium-flip-back-lottie',
							'premium-lottie-animation',
						),
						'data-lottie-url'     => $settings['back_lottie_url'],
						'data-lottie-loop'    => $settings['back_lottie_loop'],
						'data-lottie-reverse' => $settings['back_lottie_reverse'],
						'data-lottie-render'  => $settings['back_lottie_renderer'],
					)
				);
			}
		}

		$flip_dir = $settings['premium_flip_direction'];

		$this->add_render_attribute( 'container', 'class', 'premium-flip-main-box' );

		if ( 'flip' === $settings['premium_flip_style'] ) {
			if ( 'yes' === $settings['premium_flip_text_animation'] ) {
				$this->add_render_attribute( 'container', 'data-flip-animation', 'true' );
			}
		}

		$front_title_size = PAPRO_Helper::validate_html_tag( $settings['premium_flip_paragraph_header_size'] );
		$back_title_size  = PAPRO_Helper::validate_html_tag( $settings['premium_flip_back_paragraph_header_size'] );

		?>

	<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'container' ) ); ?>>
		<div class="premium-flip-front premium-flip-front<?php echo esc_attr( $flip_dir ); ?>">
			<div class="premium-flip-front-overlay">
				<div class="premium-flip-front-content-container">
					<div class="premium-flip-text-wrapper">
						<?php if ( 'yes' === $settings['premium_flip_icon_fa_switcher'] ) : ?>
							<?php
							if ( 'icon' === $front_icon ) :
								if ( ( $front_new || $front_migrated ) && 'yes' !== $settings['front_draw_svg'] ) :
									Icons_Manager::render_icon(
										$settings['premium_flip_icon_fa_updated'],
										array(
											'class'       => array( 'premium-flip-front-icon', 'premium-svg-nodraw', 'premium-drawable-icon' ),
											'aria-hidden' => 'true',
										)
									);
								else :
									?>
										<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'front_icon' ) ); ?>></i>
									<?php
								endif;
							elseif ( 'svg' === $front_icon ) :
								?>
								<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'front_icon' ) ); ?>>
									<?php $this->print_unescaped_setting( 'front_custom_svg' ); ?>
								</div>
								<?php
							elseif ( 'image' === $front_icon ) :
								?>
								<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'front_image' ) ); ?>>
							<?php elseif ( 'animation' === $front_icon ) : ?>
								<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'front_lottie' ) ); ?>></div>
							<?php endif; ?>
						<?php endif; ?>

						<?php if ( 'yes' === $settings['premium_flip_title_switcher'] && ! empty( $settings['premium_flip_paragraph_header'] ) ) : ?>
							<<?php echo wp_kses_post( $front_title_size ); ?> class="premium-flip-front-title">
								<?php echo wp_kses_post( $settings['premium_flip_paragraph_header'] ); ?>
							</<?php echo wp_kses_post( $front_title_size ); ?>>
						<?php endif; ?>

						<?php if ( 'yes' === $settings['premium_flip_description_switcher'] ) : ?>
							<div class="premium-flip-front-description">
								<?php echo $this->parse_text_editor( $settings['premium_flip_paragraph_text'] ); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="premium-flip-back premium-flip-back<?php echo esc_attr( $flip_dir ); ?>">
			<div class="premium-flip-back-overlay">
				<div class="premium-flip-back-content-container">
					<?php if ( 'yes' === $settings['premium_flip_back_link_switcher'] && 'full' === $trigger ) : ?>
						<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'link' ) ); ?>></a>
					<?php endif; ?>

					<div class="premium-flip-back-text-wrapper">

						<?php if ( 'yes' === $settings['premium_flip_back_icon_fa_switcher'] ) : ?>
							<?php
							if ( 'icon' === $back_icon ) :
								if ( ( $back_new || $back_migrated ) && 'yes' !== $settings['back_draw_svg'] ) :
									Icons_Manager::render_icon(
										$settings['premium_flip_back_icon_fa_updated'],
										array(
											'class'       => array( 'premium-flip-back-icon', 'premium-svg-nodraw', 'premium-drawable-icon' ),
											'aria-hidden' => 'true',
										)
									);
							else : ?>
                                <i <?php echo wp_kses_post( $this->get_render_attribute_string( 'back_icon' ) ); ?>></i>
                            <?php endif;

                            elseif ( 'svg' === $back_icon ) : ?>
								<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'back_icon' ) ); ?>>
									<?php $this->print_unescaped_setting( 'back_custom_svg' ); ?>
								</div>
                            <?php elseif ( 'image' === $back_icon ) : ?>
								<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'back_image' ) ); ?>>
							<?php else : ?>
								<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'back_lottie' ) ); ?>></div>
							<?php endif; ?>
						<?php endif; ?>

						<?php if ( 'yes' === $settings['premium_flip_back_title_switcher'] && ! empty( $settings['premium_flip_back_paragraph_header'] ) ) : ?>
							<<?php echo wp_kses_post( $back_title_size ); ?> class="premium-flip-back-title">
								<?php echo wp_kses_post( $settings['premium_flip_back_paragraph_header'] ); ?>
							</<?php echo wp_kses_post( $back_title_size ); ?>>
						<?php endif; ?>

						<?php if ( 'yes' === $settings['premium_flip_back_description_switcher'] ) : ?>
							<span class="premium-flip-back-description">
								<?php echo $this->parse_text_editor( $settings['premium_flip_back_paragraph_text'] ); ?>
							</span>
						<?php endif; ?>

						<?php if ( 'yes' === $settings['premium_flip_back_link_switcher'] && 'text' === $trigger ) : ?>

							<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'link' ) ); ?>>

                                <div class="premium-button-text-icon-wrapper">
                                    <span><?php echo wp_kses_post( ( $settings['premium_flip_back_link_text'] ) ); ?></span>
                                </div>

                                <?php if ( 'style6' === $settings['premium_button_hover_effect'] && 'yes' === $settings['mouse_detect'] ) : ?>
                                    <span class="premium-button-style6-bg"></span>
                                <?php endif; ?>

                                <?php if ( 'style8' === $settings['premium_button_hover_effect'] ) : ?>
                                    <?php echo Helper_Functions::get_btn_svgs( $settings['underline_style'] ); ?>
                                <?php endif; ?>

                            </a>

						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

		<?php
	}

	/**
	 * Render Hover Box widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
			var trigger = settings.premium_flip_back_link_trigger,
				buttonUrl = 'url' == settings.premium_flip_back_link_selection ? settings.premium_flip_back_link.url : settings.premium_flip_back_existing_link,
				backTitleTag = elementor.helpers.validateHTMLTag( settings.premium_flip_back_paragraph_header_size ),
				frontTitleTag = elementor.helpers.validateHTMLTag( settings.premium_flip_paragraph_header_size ),
				flipDir = settings.premium_flip_direction,
				frontIcon = settings.premium_flip_icon_selection,
				backIcon = settings.premium_flip_back_icon_selection;

			view.addRenderAttribute( 'back_side_wrap', 'class', [ 'premium-flip-back','premium-flip-back' + flipDir ] );

			view.addRenderAttribute( 'front_side_wrap', 'class', [ 'premium-flip-front','premium-flip-front' + flipDir ] );

			if( 'yes' === settings.premium_flip_icon_fa_switcher ) {

				if ( 'icon' === frontIcon || 'svg' === frontIcon ) {

					view.addRenderAttribute( 'front_icon', 'class', 'premium-drawable-icon' );

					if( 'icon' === frontIcon ) {

						var frontIconHTML = 'yes' !== settings.front_draw_svg ? elementor.helpers.renderIcon( view, settings.premium_flip_icon_fa_updated, { 'class': [ 'premium-flip-front-icon', 'premium-svg-nodraw' , 'premium-drawable-icon' ], 'aria-hidden': true }, 'i' , 'object' ) : false,
							frontMigrated = elementor.helpers.isIconMigrated( settings, 'premium_flip_icon_fa_updated' );
					}

					if( ( 'yes' === settings.front_draw_svg && 'icon' === frontIcon ) || 'svg' === frontIcon ) {
						view.addRenderAttribute( 'front_icon', 'class', 'premium-flip-front-icon' );
					}


					if ( 'yes' === settings.front_draw_svg ) {

						if( 'icon' === frontIcon ) {

							view.addRenderAttribute( 'front_icon', 'class', settings.premium_flip_icon_fa_updated.value );

						}

						view.addRenderAttribute( 'front_icon',
							{
								'class'            : 'premium-svg-drawer',
								'data-svg-reverse' : settings.front_lottie_reverse,
								'data-svg-loop'    : settings.front_lottie_loop,
								'data-svg-sync'    : settings.front_svg_sync,
								'data-svg-fill'    : settings.front_svg_color,
								'data-svg-frames'  : settings.front_frames,
								'data-svg-yoyo'    : settings.front_svg_yoyo,
								'data-svg-point'   : settings.front_lottie_reverse ? settings.front_end_point.size : settings.front_start_point.size,
							}
						);

					} else {
						view.addRenderAttribute( 'front_icon', 'class', 'premium-svg-nodraw' );
					}

				} else if( 'animation' === frontIcon ) {

					view.addRenderAttribute( 'front_lottie', {
						'class': [
							'premium-flip-front-lottie',
							'premium-lottie-animation'
						],
						'data-lottie-url': settings.front_lottie_url,
						'data-lottie-loop': settings.front_lottie_loop,
						'data-lottie-reverse': settings.front_lottie_reverse,
						'data-lottie-render': settings.front_lottie_renderer,
					});

				}

			}

			if( 'yes' === settings.premium_flip_back_icon_fa_switcher ) {

				if ( 'icon' === backIcon || 'svg' === backIcon ) {

					view.addRenderAttribute( 'back_icon', 'class', 'premium-drawable-icon' );

					if( 'icon' === backIcon ) {

						var backIconHTML = 'yes' !== settings.back_draw_svg ? elementor.helpers.renderIcon( view, settings.premium_flip_back_icon_fa_updated, { 'class': [ 'premium-flip-back-icon', 'premium-svg-nodraw' , 'premium-drawable-icon' ], 'aria-hidden': true }, 'i' , 'object' ) : false,
							backMigrated = elementor.helpers.isIconMigrated( settings, 'premium_flip_back_icon_fa_updated' );

					}

					if( ( 'yes' === settings.back_draw_svg && 'icon' === backIcon ) || 'svg' === backIcon ) {
						view.addRenderAttribute( 'back_icon', 'class', 'premium-flip-back-icon' );
					}


					if ( 'yes' === settings.back_draw_svg ) {

						view.addRenderAttribute( 'container', 'class', 'premium-drawer-hover' );

						if( 'icon' === backIcon ) {

							view.addRenderAttribute( 'back_icon', 'class', settings.premium_flip_back_icon_fa_updated.value );

						}

						view.addRenderAttribute( 'back_icon',
							{
								'class'            : 'premium-svg-drawer',
								'data-svg-reverse' : settings.back_lottie_reverse,
								'data-svg-loop'    : settings.back_lottie_loop,
								'data-svg-hover'   : true,
								'data-svg-sync'    : settings.back_svg_sync,
								'data-svg-fill'    : settings.back_svg_color,
								'data-svg-frames'  : settings.back_frames,
								'data-svg-yoyo'    : settings.back_svg_yoyo,
								'data-svg-point'   : settings.back_lottie_reverse ? settings.back_end_point.size : settings.back_start_point.size,
							}
						);

					} else {
						view.addRenderAttribute( 'back_icon', 'class', 'premium-svg-nodraw' );
					}

				} else if (  'animation' === backIcon ) {

					view.addRenderAttribute( 'back_lottie', {
						'class': [
							'premium-flip-back-lottie',
							'premium-lottie-animation'
						],
						'data-lottie-url': settings.back_lottie_url,
						'data-lottie-loop': settings.back_lottie_loop,
						'data-lottie-reverse': settings.back_lottie_reverse,
						'data-lottie-render': settings.back_lottie_renderer,
					});

				}

			}


			view.addRenderAttribute( 'container', 'class', 'premium-flip-main-box' );

			if( 'flip' === settings.premium_flip_style ) {
				if( 'yes' === settings.premium_flip_text_animation ) {
					view.addRenderAttribute( 'container', 'data-flip-animation', 'true' );
				}
			}


		#>

		<div {{{ view.getRenderAttributeString('container') }}}>
			<div {{{ view.getRenderAttributeString('front_side_wrap') }}}>
				<div class="premium-flip-front-overlay">
					<div class="premium-flip-front-content-container">
						<div class="premium-flip-text-wrapper">
							<# if( 'yes' === settings.premium_flip_icon_fa_switcher ) {
								if( 'icon' === frontIcon ) {
									if ( frontIconHTML && frontIconHTML.rendered && ( ! settings.premium_flip_icon_fa || frontMigrated ) ) { #>
											{{{ frontIconHTML.value }}}
									<# } else { #>
										<i {{{ view.getRenderAttributeString('front_icon') }}}></i>
									<# }

								} else if( 'svg' === frontIcon ) { #>
									<div {{{ view.getRenderAttributeString('front_icon') }}}>
										{{{ settings.front_custom_svg }}}
									</div>
								<# } else if( 'image' === frontIcon ) { #>
									<img alt="front side img" class="premium-flip-front-image" src="{{ settings.premium_flip_icon_image.url }}">
								<# } else { #>
									<div {{{ view.getRenderAttributeString('front_lottie') }}}></div>
								<# } #>
							<# } #>

							<# if( 'yes' === settings.premium_flip_title_switcher && '' != settings.premium_flip_paragraph_header ) { #>
								<{{{frontTitleTag}}} class="premium-flip-front-title">{{{ settings.premium_flip_paragraph_header }}}</{{{frontTitleTag}}}>
							<# } #>

							<# if( 'yes' === settings.premium_flip_description_switcher ) { #>
								<div class="premium-flip-front-description">{{{settings.premium_flip_paragraph_text}}}</div>
							<# } #>

						</div>
					</div>
				</div>
			</div>

			<div {{{ view.getRenderAttributeString('back_side_wrap') }}}>
				<div class="premium-flip-back-overlay">
					<div class="premium-flip-back-content-container">
						<# if( 'yes' === settings.premium_flip_back_link_switcher && 'full' === trigger ) { #>
							<a class="premium-flip-box-full-link" href="{{ buttonUrl }}"></a>
						<# } #>

						<div class="premium-flip-back-text-wrapper">

							<# if( 'yes' === settings.premium_flip_back_icon_fa_switcher ) {
								if( 'icon' === backIcon ) {
									if ( backIconHTML && backIconHTML.rendered && ( ! settings.premium_flip_back_icon_fa || backMigrated ) ) { #>
										{{{ backIconHTML.value }}}
									<# } else { #>
										<i {{{ view.getRenderAttributeString('back_icon') }}}></i>
									<# }

								} else if( 'svg' === backIcon ) { #>
									<div {{{ view.getRenderAttributeString('back_icon') }}}>
										{{{ settings.back_custom_svg }}}
									</div>
								<# } else if( 'image' === backIcon ) { #>
									<img alt="back side img" class="premium-flip-back-image" src="{{ settings.premium_flip_back_icon_image.url }}">
								<# } else { #>
									<div {{{ view.getRenderAttributeString('back_lottie') }}}></div>
								<# } #>
							<# } #>

							<# if( 'yes' === settings.premium_flip_back_title_switcher && '' != settings.premium_flip_back_paragraph_header ) { #>
								<{{{backTitleTag}}} class="premium-flip-back-title">{{{ settings.premium_flip_back_paragraph_header }}}</{{{backTitleTag}}}>
							<# } #>

							<# if( 'yes' === settings.premium_flip_back_description_switcher ) { #>

								<div class="premium-flip-back-description">{{{settings.premium_flip_back_paragraph_text}}}</div>

							<# } #>

							<# if( 'yes' === settings.premium_flip_back_link_switcher && 'text' === trigger ) {

                                var btnClass = '';

                                if ( 'none' === settings.premium_button_hover_effect ) {
                                    btnClass = 'premium-button-none';
                                } else if ( 'style1' === settings.premium_button_hover_effect ) {
                                    btnClass = 'premium-button-style1-' + settings.premium_button_style1_dir;
                                } else if ( 'style2' === settings.premium_button_hover_effect ) {
                                    btnClass = 'premium-button-style2-' + settings.premium_button_style2_dir;
                                } else if ( 'style5' === settings.premium_button_hover_effect ) {
                                    btnClass = 'premium-button-style5-' + settings.premium_button_style5_dir;
                                } else if ( 'style6' === settings.premium_button_hover_effect ) {
                                    btnClass = 'premium-button-style6';
                                } else if ( 'style7' === settings.premium_button_hover_effect ) {
                                    btnClass = 'premium-button-style7-' + settings.premium_button_style7_dir;
                                } else if ( 'style8' === settings.premium_button_hover_effect ) {
                                    btnClass = 'premium-button-' + settings.underline_style;

                                    var btnSVG = '';
                                    switch ( settings.underline_style ) {
                                        case 'line1':
                                            btnSVG = '<div class="premium-btn-line-wrap"><svg class="premium-btn-svg" width="100%" height="9" viewBox="0 0 101 9"><path d="M.426 1.973C4.144 1.567 17.77-.514 21.443 1.48 24.296 3.026 24.844 4.627 27.5 7c3.075 2.748 6.642-4.141 10.066-4.688 7.517-1.2 13.237 5.425 17.59 2.745C58.5 3 60.464-1.786 66 2c1.996 1.365 3.174 3.737 5.286 4.41 5.423 1.727 25.34-7.981 29.14-1.294" pathLength="1"></path></svg></div>';
                                            break;

                                        case 'line3':
                                            btnSVG = '<div class="premium-btn-line-wrap"><svg class="premium-btn-svg" width="100%" height="18" viewBox="0 0 59 18"><path d="M.945.149C12.3 16.142 43.573 22.572 58.785 10.842" pathLength="1"></path></svg></div>';
                                            break;

                                        case 'line4':
                                            btnSVG = '<svg class="premium-btn-svg" width="300%" height="100%" viewBox="0 0 1200 60" preserveAspectRatio="none"><path d="M0,56.5c0,0,298.666,0,399.333,0C448.336,56.5,513.994,46,597,46c77.327,0,135,10.5,200.999,10.5c95.996,0,402.001,0,402.001,0"></path></svg>';
                                            break;

                                        default:
                                            break;
                                    }

                                }

                                btnClass = 'premium-button-' + settings.premium_button_hover_effect + ' ' + btnClass;

                            #>

								<a class="premium-flip-box-link text {{ btnClass }}" href="{{ buttonUrl }}" data-text="{{ settings.premium_flip_back_link_text }}">

                                    <div class="premium-button-text-icon-wrapper">
                                        <span>{{{ settings.premium_flip_back_link_text }}}</span>
                                    </div>

                                    <# if ( 'style6' === settings.premium_button_hover_effect && 'yes' === settings.mouse_detect ) { #>
                                        <span class="premium-button-style6-bg"></span>
                                    <# } #>

                                    <# if( 'style8' === settings.premium_button_hover_effect ) { #>
                                        {{{ btnSVG }}}
                                    <# } #>

                                </a>

							<# } #>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php
	}
}
