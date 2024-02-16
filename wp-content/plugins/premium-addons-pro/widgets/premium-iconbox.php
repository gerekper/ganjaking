<?php
/**
 * Class: Premium_Iconbox
 * Name: Icon Box
 * Slug: premium-addon-icon-box
 */

namespace PremiumAddonsPro\Widgets;

// PremiumAddonsPro Classes.
use PremiumAddonsPro\Includes\PAPRO_Helper;

// Elementor Classes.
use Elementor\Icons_Manager;
use Elementor\Control_Media;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;

// PremiumAddons Classes.
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Iconbox
 */
class Premium_Iconbox extends Widget_Base {

	/**
	 * Check Icon Draw Option.
	 *
	 * @since 2.8.4
	 * @access public
	 */
	public function check_icon_draw() {

		$is_enabled = Admin_Helper::check_svg_draw( 'premium-iconbox' );
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
		return 'premium-addon-icon-box';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Icon Box', 'premium-addons-pro' );
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
		return 'pa-pro-icon-box';
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
		return array( 'pa', 'premium', 'cta', 'action', 'link' );
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
				'pa-tilt',
				'lottie-js',
				'premium-pro',
			)
		);
	}

	/**
	 * Widget preview refresh button.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_custom_help_url() {
		return 'https://premiumaddons.com/support/';
	}

	/**
	 * Register Icon Box controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$draw_icon = $this->check_icon_draw();

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'General', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'mouse_tilt',
			array(
				'label'        => __( 'Enable Mouse Tilt', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
			)
		);

		$this->add_control(
			'mouse_tilt_rev',
			array(
				'label'        => __( 'Reverse', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array(
					'mouse_tilt' => 'true',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_icon_box_icon',
			array(
				'label' => __( 'Icon', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_icon_box_selector',
			array(
				'label'   => __( 'Icon Type', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'font-awesome-icon',
				'options' => array(
					'font-awesome-icon' => __( 'Font Awesome Icon', 'premium-addons-pro' ),
					'custom-image'      => __( 'Custom Image', 'premium-addons-pro' ),
					'animation'         => __( 'Lottie Animation', 'premium-addons-pro' ),
					'svg'               => __( 'SVG Code', 'premium-addons-pro' ),
				),
			)
		);

		$this->add_control(
			'premium_icon_box_font_updated',
			array(
				'label'            => __( 'Icon', 'premium-addons-pro' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'premium_icon_box_font',
				'default'          => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'premium_icon_box_selector' => 'font-awesome-icon',
				),
			)
		);

		$this->add_control(
			'premium_icon_box_custom_image',
			array(
				'label'     => __( 'Custom Image', 'premium-addons-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => array( 'active' => true ),
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'premium_icon_box_selector' => 'custom-image',
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
					'premium_icon_box_selector' => 'animation',
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
					'premium_icon_box_selector' => 'svg',
				),
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_font_size',
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
					'{{WRAPPER}} .premium-icon-box-icon-container i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-icon-box-icon-container .premium-icon-box-animation, {{WRAPPER}} div:not(.premium-lottie-animation) svg:not(.premium-icon-box-more-icon)' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'premium_icon_box_selector!' => array( 'custom-image', 'svg' ),
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
					'premium_icon_box_selector' => 'svg',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-icon-box-icon-container svg' => 'width: {{SIZE}}{{UNIT}};',
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
					'premium_icon_box_selector' => 'svg',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-icon-box-icon-container svg' => 'height: {{SIZE}}{{UNIT}}',
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
					'premium_icon_box_selector' => array( 'font-awesome-icon', 'svg' ),
					'premium_icon_box_font_updated[library]!' => 'svg',
				),
			)
		);

		if ( $draw_icon ) {
			$this->add_control(
				'stroke_width',
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
						'premium_icon_box_selector' => array( 'font-awesome-icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-icon-box-icon-container svg *' => 'stroke-width: {{SIZE}}',
					),
				)
			);

			$this->add_control(
				'svg_sync',
				array(
					'label'     => __( 'Draw All Paths Together', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'premium_icon_box_selector' => array( 'font-awesome-icon', 'svg' ),
						'draw_svg'                  => 'yes',
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
						'premium_icon_box_selector' => array( 'font-awesome-icon', 'svg' ),
						'draw_svg'                  => 'yes',
					),
				)
			);
		} elseif ( method_exists( 'PremiumAddons\Includes\Helper_Functions', 'get_draw_svg_notice' ) ) {

			Helper_Functions::get_draw_svg_notice(
				$this,
				'box',
				array(
					'premium_icon_box_selector' => array( 'font-awesome-icon', 'svg' ),
					'premium_icon_box_font_updated[library]!' => 'svg',
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
				'conditions'   => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'premium_icon_box_selector',
							'value' => 'animation',
						),
						array(
							'terms' => array(
								array(
									'relation' => 'or',
									'terms'    => array(
										array(
											'name'  => 'premium_icon_box_selector',
											'value' => 'font-awesome-icon',
										),
										array(
											'name'  => 'premium_icon_box_selector',
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
				),
			)
		);

		$this->add_control(
			'lottie_reverse',
			array(
				'label'        => __( 'Reverse', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'conditions'   => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'premium_icon_box_selector',
							'value' => 'animation',
						),
						array(
							'terms' => array(
								array(
									'relation' => 'or',
									'terms'    => array(
										array(
											'name'  => 'premium_icon_box_selector',
											'value' => 'font-awesome-icon',
										),
										array(
											'name'  => 'premium_icon_box_selector',
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
				),
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
						'premium_icon_box_selector' => array( 'font-awesome-icon', 'svg' ),
						'draw_svg'                  => 'yes',
						'lottie_reverse!'           => 'true',
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
						'premium_icon_box_selector' => array( 'font-awesome-icon', 'svg' ),
						'draw_svg'                  => 'yes',
						'lottie_reverse'            => 'true',
					),

				)
			);

			$this->add_control(
				'svg_hover',
				array(
					'label'        => __( 'Only Animate on Hover', 'premium-addons-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'condition'    => array(
						'premium_icon_box_selector' => array( 'font-awesome-icon', 'svg' ),
						'draw_svg'                  => 'yes',
					),
				)
			);

			$this->add_control(
				'svg_yoyo',
				array(
					'label'     => __( 'Yoyo Effect', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'premium_icon_box_selector' => array( 'font-awesome-icon', 'svg' ),
						'draw_svg'                  => 'yes',
						'lottie_loop'               => 'true',
					),
				)
			);
		}

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'premium_icon_box_image_size',
				'default'   => 'full',
				'condition' => array(
					'premium_icon_box_selector' => 'custom-image',
				),
			)
		);

		$this->add_control(
			'premium_icon_box_hover',
			array(
				'label'   => __( 'Hover Animation', 'premuim_elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'none'                => __( 'None', 'premium-addons-pro' ),
					'hvr-pulse-grow'      => __( 'Pulse', 'premium-addons-pro' ),
					'rotate'              => __( 'Rotate', 'premium-addons-pro' ),
					'hvr-buzz'            => __( 'Buzz', 'premium-addons-pro' ),
					'd-rotate'            => __( '3D Rotate', 'premium-addons-pro' ),
					'hvr-float-shadow'    => __( 'Drop Shadow', 'premium-addons-pro' ),
					'hvr-wobble-vertical' => __( 'Wobble Vertical', 'premium-addons-pro' ),
				),
				'default' => 'none',
			)
		);

		$this->add_control(
			'premium_icon_box_icon_flex_pos',
			array(
				'label'   => __( 'Icon Position', 'premium-addons-pro' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'top',
				'options' => array(
					'left'  => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'top'   => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default' => 'top',
				'toggle'  => false,
			)
		);

		$this->add_control(
			'premium_icon_box_icon_flex_ver_pos',
			array(
				'label'   => __( 'Vertical Alignment', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'top'    => __( 'Top', 'premium-addons-pro' ),
					'middle' => __( 'Middle', 'premium-addons-pro' ),
					'bottom' => __( 'Bottom', 'premium-addons-pro' ),
				),
				'default' => 'top',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_icon_box_title_section',
			array(
				'label' => __( 'Title', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_icon_box_title_switcher',
			array(
				'label'   => __( 'Title', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'premium_icon_box_title',
			array(
				'label'     => __( 'Title', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'default'   => 'Premium Icon Box',
				'condition' => array(
					'premium_icon_box_title_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_icon_box_title_heading',
			array(
				'label'     => __( 'Title Heading', 'premium-addons-pro' ),
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
					'premium_icon_box_title_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_icon_box_label',
			array(
				'label'     => __( 'Label', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'condition' => array(
					'premium_icon_box_title_switcher' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_icon_box_desc',
			array(
				'label' => __( 'Description', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_icon_box_desc_switcher',
			array(
				'label'   => __( 'Description', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'premium_icon_box_content',
			array(
				'label'     => __( 'Content', 'premium-addons-pro' ),
				'type'      => Controls_Manager::WYSIWYG,
				'dynamic'   => array( 'active' => true ),
				'default'   => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
				'condition' => array(
					'premium_icon_box_desc_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_align',
			array(
				'label'        => __( 'Alignment', 'premuim_elementor' ),
				'type'         => Controls_Manager::CHOOSE,
				'prefix_class' => 'premium%s-icon-box-',
				'options'      => array(
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
				'default'      => 'center',
				'selectors'    => array(
					'{{WRAPPER}} .premium-icon-box-container-in' => 'text-align: {{VALUE}};',
				),
				'toggle'       => false,
				'render_type'  => 'template',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_icon_box_link_section',
			array(
				'label' => __( 'Link', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_icon_box_link_switcher',
			array(
				'label'   => __( 'Link', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'box_link',
			array(
				'label'     => __( 'Whole Box Link', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_icon_box_link_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'keep_text_link',
			array(
				'label'        => __( 'Keep Button Link', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-icon-box-whole-text-',
				'condition'    => array(
					'premium_icon_box_link_switcher' => 'yes',
					'box_link'                       => 'yes',
				),
				'render_type'  => 'template',
			)
		);

		$this->add_control(
			'premium_icon_box_link_text_switcher',
			array(
				'label'      => __( 'Text', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SWITCHER,
				'default'    => 'yes',
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'premium_icon_box_link_switcher',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'box_link',
									'value' => '',
								),
								array(
									'name'  => 'keep_text_link',
									'value' => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'premium_icon_box_more_text',
			array(
				'label'      => __( 'Text', 'premium-addons-pro' ),
				'type'       => Controls_Manager::TEXT,
				'dynamic'    => array( 'active' => true ),
				'default'    => 'Click Here',
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'premium_icon_box_link_switcher',
							'value' => 'yes',
						),
						array(
							'name'  => 'premium_icon_box_link_text_switcher',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'box_link',
									'value' => '',
								),
								array(
									'name'  => 'keep_text_link',
									'value' => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'premium_icon_box_icon_text_pos',
			array(
				'label'      => __( 'Position', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SELECT,
				'options'    => array(
					'left'   => __( 'Left', 'premium-addons-pro' ),
					'right'  => __( 'Right', 'premium-addons-pro' ),
					'bottom' => __( 'Bottom', 'premium-addons-pro' ),
				),
				'default'    => 'bottom',
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'premium_icon_box_link_switcher',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'box_link',
									'value' => '',
								),
								array(
									'name'  => 'keep_text_link',
									'value' => 'yes',
								),
							),
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'premium_icon_box_link_text_switcher',
									'value' => 'yes',
								),
								array(
									'name'  => 'premium_icon_box_link_icon_switcher',
									'value' => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'premium_icon_box_icon_text_ver_pos',
			array(
				'label'       => __( 'Vertical Alignment', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'top'    => __( 'Top', 'premium-addons-pro' ),
					'middle' => __( 'Middle', 'premium-addons-pro' ),
					'bottom' => __( 'Bottom', 'premium-addons-pro' ),
				),
				'default'     => 'top',
				'conditions'  => array(
					'terms' => array(
						array(
							'name'  => 'premium_icon_box_link_switcher',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'box_link',
									'value' => '',
								),
								array(
									'name'  => 'keep_text_link',
									'value' => 'yes',
								),
							),
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'premium_icon_box_icon_text_pos',
									'value' => 'right',
								),
								array(
									'name'  => 'premium_icon_box_icon_text_pos',
									'value' => 'left',
								),
							),
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'premium_icon_box_link_text_switcher',
									'value' => 'yes',
								),
								array(
									'name'  => 'premium_icon_box_link_icon_switcher',
									'value' => 'yes',
								),
							),
						),
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-icon-box-more' => 'display:flex !important;',
				),
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'premium_icon_box_link_icon_switcher',
			array(
				'label'      => __( 'Icon', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SWITCHER,
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'premium_icon_box_link_switcher',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'box_link',
									'value' => '',
								),
								array(
									'name'  => 'keep_text_link',
									'value' => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'premium_icon_box_more_icon_updated',
			array(
				'label'            => __( 'Icon', 'premium-addons-pro' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'premium_icon_box_more_icon',
				'default'          => array(
					'value'   => 'fas fa-link',
					'library' => 'fa-solid',
				),
				'conditions'       => array(
					'terms' => array(
						array(
							'name'  => 'premium_icon_box_link_switcher',
							'value' => 'yes',
						),
						array(
							'name'  => 'premium_icon_box_link_icon_switcher',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'box_link',
									'value' => '',
								),
								array(
									'name'  => 'keep_text_link',
									'value' => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'premium_icon_box_icon_position',
			array(
				'label'        => __( 'Position', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'after',
				'options'      => array(
					'before' => __( 'Before', 'premium-addons-pro' ),
					'after'  => __( 'After', 'premium-addons-pro' ),
				),
				'prefix_class' => 'premium-link-icon-',
				'render_type'  => 'template',
				'conditions'   => array(
					'terms' => array(
						array(
							'name'  => 'premium_icon_box_link_switcher',
							'value' => 'yes',
						),
						array(
							'name'  => 'premium_icon_box_link_icon_switcher',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'box_link',
									'value' => '',
								),
								array(
									'name'  => 'keep_text_link',
									'value' => 'yes',
								),
							),
						),
					),
				),
				'label_block'  => true,
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_link_icon_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 100,
					),
					'em' => array(
						'min' => 1,
						'max' => 15,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-icon-box-more i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-icon-box-more svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'premium_icon_box_link_switcher',
							'value' => 'yes',
						),
						array(
							'name'  => 'premium_icon_box_link_icon_switcher',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'box_link',
									'value' => '',
								),
								array(
									'name'  => 'keep_text_link',
									'value' => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$icon_spacing = is_rtl() ? 'left' : 'right';

		$icon_spacing_after = is_rtl() ? 'right' : 'left';

		$this->add_responsive_control(
			'link_icon_spacing',
			array(
				'label'     => __( 'Icon Spacing', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}}.premium-link-icon-after .premium-icon-box-more-icon' => 'margin-' . $icon_spacing_after . ': {{SIZE}}px',
					'{{WRAPPER}}.premium-link-icon-before .premium-icon-box-more-icon' => 'margin-' . $icon_spacing . ': {{SIZE}}px',
				),
				'default'   => array(
					'size' => 10,
					'unit' => 'px',
				),
				'condition' => array(
					'premium_icon_box_link_switcher'      => 'yes',
					'premium_icon_box_link_icon_switcher' => 'yes',
					'premium_icon_box_link_text_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_icon_box_link_selection',
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
					'premium_icon_box_link_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_icon_box_link',
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
					'premium_icon_box_link_selection' => 'url',
					'premium_icon_box_link_switcher'  => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_icon_box_existing_link',
			array(
				'label'       => __( 'Existing Page', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_all_posts(),
				'multiple'    => false,
				'condition'   => array(
					'premium_icon_box_link_selection' => 'link',
					'premium_icon_box_link_switcher'  => 'yes',
				),
				'label_block' => true,
			)
		);

        if ( version_compare( PREMIUM_ADDONS_VERSION, '4.10.17', '>' ) ) {
            Helper_Functions::add_btn_hover_controls( $this, array(
                    'premium_icon_box_link_switcher' => 'yes',
                    'box_link!'  => 'yes',
                )
            );
        }

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_icon_box_back_icon_section',
			array(
				'label'     => __( 'Back Icon', 'premium-addons-pro' ),
				'condition' => array(
					'premium_icon_box_selector!' => 'svg',
				),
			)
		);

		$this->add_control(
			'premium_icon_box_back_icon_switcher',
			array(
				'label'      => __( 'Show Back Icon', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SWITCHER,
				'label_on'   => 'Show',
				'label_hide' => 'Hide',
				'default'    => 'yes',
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_back_icon_hor',
			array(
				'label'      => __( 'Horizontal Offset', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
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
					'{{WRAPPER}} .premium-icon-box-big' => 'right: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_icon_box_back_icon_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_back_icon_ver',
			array(
				'label'      => __( 'Vertical Offset', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
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
					'{{WRAPPER}} .premium-icon-box-big' => 'bottom: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_icon_box_back_icon_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_back_icon_opacity',
			array(
				'label'     => __( 'Opacity', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-icon-box-big' => 'opacity: {{SIZE}};',
				),
				'condition' => array(
					'premium_icon_box_back_icon_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_back_icon_rotate',
			array(
				'label'       => __( 'Rotate', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Set rotation value in degrees', 'premium-addons-pro' ),
				'min'         => -180,
				'max'         => 180,
				'selectors'   => array(
					'{{WRAPPER}} .premium-icon-box-big' => '-webkit-transform: rotate({{VALUE}}deg); -moz-transform: rotate({{VALUE}}deg); -o-transform: rotate({{VALUE}}deg); transform: rotate({{VALUE}}deg);',
				),
				'condition'   => array(
					'premium_icon_box_back_icon_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_icon_box_back_icon_hover',
			array(
				'label'      => __( 'Hover Effect ', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SWITCHER,
				'label_on'   => 'Show',
				'label_hide' => 'Hide',
				'default'    => 'yes',
				'condition'  => array(
					'premium_icon_box_back_icon_switcher' => 'yes',
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

		$doc1_url = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/icon-box-widget-tutorial/', 'editor-page', 'wp-editor', 'get-support' );

		$this->add_control(
			'doc_1',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc1_url, __( 'Getting started »', 'premium-addons-pro' ) ),
				'content_classes' => 'editor-pa-doc',
			)
		);

		$this->end_controls_section();

		/*Start Icon Style*/
		$this->start_controls_section(
			'premium_icon_box_icon_style',
			array(
				'label' => __( 'Icon', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'svg_color',
			array(
				'label'     => __( 'After Draw Fill Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => false,
				'condition' => array(
					'premium_icon_box_selector' => array( 'font-awesome-icon', 'svg' ),
					'draw_svg'                  => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'premium_icon_box_style_tabs' );

		$this->start_controls_tab(
			'premium_icon_box_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_icon_box_icon_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-icon-box-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-icon-box-icon-container svg, {{WRAPPER}} .premium-icon-box-icon-container svg *' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'premium_icon_box_selector' => array( 'font-awesome-icon', 'svg' ),
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
						'premium_icon_box_selector' => array( 'font-awesome-icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-icon-box-icon-container svg *' => 'stroke: {{VALUE}};',
					),
				)
			);
		}

		$this->add_control(
			'premium_icon_box_background_normal',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-icon-wrapper, {{WRAPPER}} .premium-icon-box-big .premium-icon-box-icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_icon_box_border_normal',
				'selector' => '{{WRAPPER}} .premium-icon-wrapper, {{WRAPPER}} .premium-icon-box-big .premium-icon-box-icon',
			)
		);

		$this->add_control(
			'premium_icon_box_border_radius_normal',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-icon-wrapper, {{WRAPPER}} .premium-icon-box-big .premium-icon-box-icon' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'icon_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'icon_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-pro' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'icon_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-icon-wrapper, {{WRAPPER}} .premium-icon-box-big .premium-icon-box-icon' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'icon_adv_radius' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'     => __( 'Shadow', 'premium-addons-pro' ),
				'name'      => 'premium_icon_box_icon_shadow_normal',
				'selector'  => '{{WRAPPER}} .premium-icon-box-icon, {{WRAPPER}} .premium-icon-box-icon-container svg',
				'condition' => array(
					'premium_icon_box_selector' => 'font-awesome-icon',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'     => __( 'Shadow', 'premium-addons-pro' ),
				'name'      => 'premium_icon_box_image_shadow_normal',
				'selector'  => '{{WRAPPER}} .premium-icon-box-icon-container img',
				'condition' => array(
					'premium_icon_box_selector' => 'custom-image',
				),
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_margin_normal',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-icon-wrapper, {{WRAPPER}} .premium-icon-box-big .premium-icon-box-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_padding_normal',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-icon-wrapper, {{WRAPPER}} .premium-icon-box-big .premium-icon-box-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_icon_box_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_icon_box_icon_color_hover',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}:hover .premium-icon-box-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}}:hover .premium-icon-box-icon, {{WRAPPER}}:hover .premium-icon-box-icon *' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'premium_icon_box_selector' => 'font-awesome-icon',
					'svg_color'                 => '',
				),
			)
		);

		if ( $draw_icon ) {
			$this->add_control(
				'stroke_color_hover',
				array(
					'label'     => __( 'Stroke Color', 'premium-addons-pro' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_ACCENT,
					),
					'condition' => array(
						'premium_icon_box_selector' => array( 'font-awesome-icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}}:hover .premium-icon-box-icon-container .premium-icon-box-icon *' => 'stroke: {{VALUE}};',
					),
				)
			);
		}

		$this->add_control(
			'premium_icon_box_background_hover',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}:hover .premium-icon-wrapper, {{WRAPPER}}:hover .premium-icon-box-big .premium-icon-box-icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_icon_box_border_hover',
				'selector' => '{{WRAPPER}}:hover .premium-icon-wrapper, {{WRAPPER}}:hover .premium-icon-box-big .premium-icon-box-icon',
			)
		);

		$this->add_control(
			'premium_icon_box_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}}:hover .premium-icon-wrapper, {{WRAPPER}}:hover .premium-icon-box-big .premium-icon-box-icon' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'icon_hover_adv_radius' => 'yes',
				),
			)
		);

		$this->add_control(
			'icon_hover_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-pro' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'icon_hover_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}}:hover .premium-icon-wrapper, {{WRAPPER}}:hover .premium-icon-box-big .premium-icon-box-icon' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'icon_hover_adv_radius' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'     => __( 'Shadow', 'premium-addons-pro' ),
				'name'      => 'premium_icon_box_icon_shadow_hover',
				'selector'  => '{{WRAPPER}}:hover .premium-icon-box-icon, {{WRAPPER}}:hover svg',
				'condition' => array(
					'premium_icon_box_selector' => 'font-awesome-icon',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'     => __( 'Shadow', 'premium-addons-pro' ),
				'name'      => 'premium_icon_box_image_shadow_hover',
				'selector'  => '{{WRAPPER}}:hover .premium-icon-box-icon-container img',
				'condition' => array(
					'premium_icon_box_selector' => 'custom-image',
				),
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_margin_hover',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}:hover .premium-icon-wrapper, {{WRAPPER}}:hover .premium-icon-box-big .premium-icon-box-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_padding_hover',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}:hover .premium-icon-wrapper, {{WRAPPER}}:hover .premium-icon-box-big .premium-icon-box-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_icon_box_title_style',
			array(
				'label'     => __( 'Title', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_icon_box_title_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_icon_box_title_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-icon-box-title' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_icon_box_title_color_hover',
			array(
				'label'     => __( 'Text Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}}:hover .premium-icon-box-title'  => 'color:{{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-icon-box-title',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'premium_icon_box_title_shadow',
				'selector' => '{{WRAPPER}} .premium-icon-box-title',
			)
		);

		$this->add_control(
			'premium_icon_box_title_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-icon-box-title' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_icon_box_title_border',
				'selector' => '{{WRAPPER}} .premium-icon-box-title',
			)
		);

		$this->add_control(
			'premium_icon_box_title_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-icon-box-title' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_title_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-icon-box-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_title_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-icon-box-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'heading',
			array(
				'label'     => __( 'Label', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'premium_icon_box_label_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-icon-box-label' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-icon-box-label',
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_label_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-icon-box-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_icon_box_content_style',
			array(
				'label'     => __( 'Description', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_icon_box_desc_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_icon_box_content_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-icon-box-content'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_icon_box_content_color_hover',
			array(
				'label'     => __( 'Text Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}}:hover .premium-icon-box-content'  => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'content_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .premium-icon-box-content',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'premium_icon_box_content_shadow',
				'selector' => '{{WRAPPER}} .premium-icon-box-content',
			)
		);

		$this->add_control(
			'premium_icon_box_content_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-icon-box-content'  => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_icon_box_content_border',
				'selector' => '{{WRAPPER}} .premium-icon-box-content',
			)
		);

		$this->add_control(
			'premium_icon_box_content_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-icon-box-content' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_content_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-icon-box-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_content_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-icon-box-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_icon_box_more_style',
			array(
				'label'      => __( 'Link', 'premium-addons-pro' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'premium_icon_box_link_switcher',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'box_link',
									'value' => '',
								),
								array(
									'name'  => 'keep_text_link',
									'value' => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'read_more_typography',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector'  => '{{WRAPPER}} .premium-icon-box-more',
				'condition' => array(
					'premium_icon_box_link_text_switcher' => 'yes',
					'premium_icon_box_link_switcher'      => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'premium_icon_box_link_style_tabs' );

		$this->start_controls_tab(
			'premium_icon_box_link_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_icon_box_link_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-icon-box-more' => 'color:{{VALUE}};',
					'{{WRAPPER}} .premium-icon-box-more svg' => 'fill:{{VALUE}} !important',
				),
			)
		);

		$this->add_control(
			'premium_icon_box_link_background_normal',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}.premium-icon-box-whole-text-yes .premium-icon-box-more, {{WRAPPER}} .premium-icon-box-link, {{WRAPPER}} .premium-button-style2-shutinhor:before, {{WRAPPER}} .premium-button-style2-shutinver:before, {{WRAPPER}} .premium-button-style5-radialin:before, {{WRAPPER}} .premium-button-style5-rectin:before' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_icon_box_link_border_normal',
				'selector' => '{{WRAPPER}}.premium-icon-box-whole-text-yes .premium-icon-box-more, {{WRAPPER}} .premium-icon-box-link',
			)
		);

		$this->add_control(
			'premium_icon_box_link_border_radius_normal',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}}.premium-icon-box-whole-text-yes .premium-icon-box-more, {{WRAPPER}} .premium-icon-box-link' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

        $this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'premium_icon_box_link_shadow_normal',
				'selector' => '{{WRAPPER}}.premium-icon-box-whole-text-yes .premium-icon-box-more, {{WRAPPER}} .premium-icon-box-link',
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_link_margin_normal',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}.premium-icon-box-whole-text-yes .premium-icon-box-more, {{WRAPPER}} .premium-icon-box-link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_link_padding_normal',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}.premium-icon-box-whole-text-yes .premium-icon-box-more, {{WRAPPER}} .premium-icon-box-link, {{WRAPPER}} .premium-button-line6::after' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_icon_box_link_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_icon_box_link_color_hover',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}}.premium-icon-box-whole-text-yes:hover .premium-icon-box-more, {{WRAPPER}} .premium-icon-box-link:hover, {{WRAPPER}} .premium-button-line6::after'   => 'color: {{VALUE}};',
					'{{WRAPPER}}.premium-icon-box-whole-text-yes:hover .premium-icon-box-more svg, {{WRAPPER}} .premium-icon-box-link:hover svg'   => 'fill: {{VALUE}} !important',
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
			'premium_icon_box_link_background_hover',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
                'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}}.premium-icon-box-whole-text-yes:hover .premium-icon-box-more, {{WRAPPER}} .premium-button-none:hover, {{WRAPPER}} .premium-button-style8:hover, {{WRAPPER}} .premium-button-style1:before, {{WRAPPER}} .premium-button-style2-shutouthor:before, {{WRAPPER}} .premium-button-style2-shutoutver:before, {{WRAPPER}} .premium-button-style2-shutinhor, {{WRAPPER}} .premium-button-style2-shutinver, {{WRAPPER}} .premium-button-style2-dshutinhor:before, {{WRAPPER}} .premium-button-style2-dshutinver:before, {{WRAPPER}} .premium-button-style2-scshutouthor:before, {{WRAPPER}} .premium-button-style2-scshutoutver:before, {{WRAPPER}} .premium-button-style5-radialin, {{WRAPPER}} .premium-button-style5-radialout:before, {{WRAPPER}} .premium-button-style5-rectin, {{WRAPPER}} .premium-button-style5-rectout:before, {{WRAPPER}} .premium-button-style6-bg, {{WRAPPER}} .premium-button-style6:before' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_icon_box_link_border_hover',
				'selector' => '{{WRAPPER}}.premium-icon-box-whole-text-yes:hover .premium-icon-box-more, {{WRAPPER}} .premium-icon-box-link:hover',
			)
		);

		$this->add_control(
			'premium_icon_box_link_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}}.premium-icon-box-whole-text-yes:hover .premium-icon-box-more, {{WRAPPER}} .premium-icon-box-link:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

        $this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'premium_icon_box_link_shadow_hover',
				'selector' => '{{WRAPPER}}.premium-icon-box-whole-text-yes:hover .premium-icon-box-more, {{WRAPPER}} .premium-icon-box-link:hover',
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_link_margin_hover',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}.premium-icon-box-whole-text-yes:hover .premium-icon-box-more, {{WRAPPER}} .premium-icon-box-link:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_link_padding_hover',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}.premium-icon-box-whole-text-yes:hover .premium-icon-box-more, {{WRAPPER}} .premium-icon-box-link:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_icon_box_inner_container_style',
			array(
				'label' => __( 'Inner Container', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'premium_icon_box_inner_container_style_tabs' );

		$this->start_controls_tab(
			'premium_icon_box_inner_container_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_icon_box_inner_container_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-icon-box-container-in',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_icon_box_inner_container_box_border',
				'selector' => '{{WRAPPER}} .premium-icon-box-container-in',
			)
		);

		$this->add_control(
			'premium_icon_box_inner_container_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-icon-box-container-in' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Box Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_icon_box_inner_container_box_shadow',
				'selector' => '{{WRAPPER}} .premium-icon-box-container-in',
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_inner_container_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-icon-box-container-in' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_inner_container_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-icon-box-container-in' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_icon_box_inner_container_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_icon_box_inner_container_background_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}}:hover .premium-icon-box-container-in',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_icon_box_inner_container_border_hover',
				'selector' => '{{WRAPPER}}:hover .premium-icon-box-container-in',
			)
		);

		$this->add_control(
			'premium_icon_box_inner_container_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}:hover .premium-icon-box-container-in' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Box Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_icon_box_inner_container_box_shadow_hover',
				'selector' => '{{WRAPPER}}:hover .premium-icon-box-container-in',
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_inner_container_margin_hover',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}:hover .premium-icon-box-container-in' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_inner_container_padding_hover',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}:hover .premium-icon-box-container-in' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_icon_box_outer_container_style',
			array(
				'label' => __( 'Outer Container', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_icon_box_back_icon',
			array(
				'label'     => __( 'Back Icon Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-icon-box-big i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .premium-icon-box-big svg' => 'fill: {{VALUE}}',
				),
				'condition' => array(
					'premium_icon_box_back_icon_switcher' => 'yes',
					'premium_icon_box_selector'           => 'font-awesome-icon',
				),
			)
		);

		$this->start_controls_tabs( 'premium_icon_box_outer_container_style_tabs' );

		$this->start_controls_tab(
			'premium_icon_box_outer_container_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_icon_box_outer_container_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_icon_box_outer_container_box_border',
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'premium_icon_box_outer_container_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}}' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'container_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'container_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-pro' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'container_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}}' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'container_adv_radius' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Box Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_icon_box_outer_container_box_shadow',
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_outer_container_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_outer_container_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_icon_box_outer_container_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_icon_box_outer_container_background_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}}:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_icon_box_outer_container_border_hover',
				'selector' => '{{WRAPPER}}:hover',
			)
		);

		$this->add_control(
			'premium_icon_box_outer_container_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'container_hover_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'container_hover_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-pro' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'container_hover_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}}:hover' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'container_hover_adv_radius' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Box Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_icon_box_outer_container_box_shadow_hover',
				'selector' => '{{WRAPPER}}:hover',
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_outer_container_margin_hover',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_icon_box_outer_container_padding_hover',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render Icon Box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$title = $settings['premium_icon_box_title'];

		$heading = PAPRO_Helper::validate_html_tag( $settings['premium_icon_box_title_heading'] );

		$this->add_render_attribute( 'premium_icon_box_title', 'class', 'premium-icon-box-title' );
		$this->add_inline_editing_attributes( 'premium_icon_box_title', 'basic' );

		$this->add_render_attribute( 'premium_icon_box_content', 'class', 'premium-icon-box-content' );
		$this->add_inline_editing_attributes( 'premium_icon_box_content', 'advanced' );

		$this->add_render_attribute( 'premium_icon_box_label', 'class', 'premium-icon-box-label' );
		$this->add_inline_editing_attributes( 'premium_icon_box_label', 'basic' );

		$icon_position = $settings['premium_icon_box_icon_position'];

		$icon_hover = 'yes' === $settings['premium_icon_box_back_icon_hover'] ? 'premium-icon-box-big-hover' : '';

		$flex_pos = 'premium-icon-box-flex-' . $settings['premium_icon_box_icon_flex_pos'];

		$flex_ver_pos = 'premium-icon-box-flex-ver-' . $settings['premium_icon_box_icon_flex_ver_pos'];

		$icon_box_url = 'url' === $settings['premium_icon_box_link_selection'] ? $settings['premium_icon_box_link'] : get_permalink( $settings['premium_icon_box_existing_link'] );

		if ( 'url' === $settings['premium_icon_box_link_selection'] ) {
			$this->add_link_attributes( 'link', $icon_box_url );
		} else {
			$this->add_render_attribute( 'link', 'href', $icon_box_url );
		}

		if ( 'yes' !== $settings['box_link'] ) {

            $effect_class = '';
            if ( version_compare( PREMIUM_ADDONS_VERSION, '4.10.17', '>' ) ) {
                $effect_class = Helper_Functions::get_button_class( $settings );
            }

            $this->add_render_attribute( 'link', array(
                'class' => array(
                    'premium-icon-box-link',
                    $effect_class
                ),
                'data-text' =>  $settings['premium_icon_box_more_text']
            ));

		} else {

			$this->add_render_attribute( 'link', 'class', 'premium-icon-box-whole-link' );

		}

		$icon_type = $settings['premium_icon_box_selector'];

		$text_ver_pos = 'bottom' !== $settings['premium_icon_box_icon_text_pos'] ? 'premium-icon-box-flex-ver-' . $settings['premium_icon_box_icon_text_ver_pos'] : '';

		if ( 'font-awesome-icon' === $icon_type || 'svg' === $icon_type ) {

			if ( 'font-awesome-icon' === $icon_type ) {

				if ( ! empty( $settings['premium_icon_box_font'] ) ) {
					$this->add_render_attribute(
						'icon',
						'class',
						array(
							'premium-icon-box-icon',
							$settings['premium_icon_box_font'],
						)
					);
					$this->add_render_attribute( 'icon', 'aria-hidden', 'true' );
				}

				$migrated = isset( $settings['__fa4_migrated']['premium_icon_box_font_updated'] );
				$is_new   = empty( $settings['premium_icon_box_font'] ) && Icons_Manager::is_migration_allowed();

			}

			if ( ( 'yes' === $settings['draw_svg'] && 'font-awesome-icon' === $icon_type ) || 'svg' === $icon_type ) {
				$this->add_render_attribute(
					'icon',
					'class',
					array(
						'premium-icon-box-icon',
						$settings['premium_icon_box_hover'],
					)
				);
			}

			if ( 'yes' === $settings['draw_svg'] ) {

				$this->add_render_attribute( 'icon_wrap', 'class', 'elementor-invisible' );

				if ( 'font-awesome-icon' === $icon_type ) {

					$this->add_render_attribute( 'icon', 'class', $settings['premium_icon_box_font_updated']['value'] );

				}

				$this->add_render_attribute(
					'icon',
					array(
						'class'            => 'premium-svg-drawer',
						'data-svg-reverse' => $settings['lottie_reverse'],
						'data-svg-loop'    => $settings['lottie_loop'],
						'data-svg-hover'   => $settings['svg_hover'],
						'data-svg-sync'    => $settings['svg_sync'],
						'data-svg-fill'    => $settings['svg_color'],
						'data-svg-frames'  => $settings['frames'],
						'data-svg-yoyo'    => $settings['svg_yoyo'],
						'data-svg-point'   => $settings['lottie_reverse'] ? $settings['end_point']['size'] : $settings['start_point']['size'],
					)
				);

			}
		} else {
			$this->add_render_attribute(
				'icon_box_lottie',
				array(
					'class'               => array(
						'premium-icon-box-animation',
						'premium-lottie-animation',
						$settings['premium_icon_box_hover'],
					),
					'data-lottie-url'     => $settings['lottie_url'],
					'data-lottie-loop'    => $settings['lottie_loop'],
					'data-lottie-reverse' => $settings['lottie_reverse'],
				)
			);
		}

		if ( 'yes' === $settings['premium_icon_box_link_switcher'] ) {

			if ( ! empty( $settings['premium_icon_box_more_icon'] ) ) {
				$this->add_render_attribute(
					'more_icon',
					'class',
					array(
						'premium-icon-box-more-icon',
						$settings['premium_icon_box_more_icon'],
					)
				);

				$this->add_render_attribute( 'more_icon', 'aria-hidden', 'true' );
			}

			$read_migrated = isset( $settings['__fa4_migrated']['premium_icon_box_more_icon_updated'] );
			$read_new      = empty( $settings['premium_icon_box_more_icon'] ) && Icons_Manager::is_migration_allowed();
		}

		$this->add_render_attribute( 'box', 'class', 'premium-icon-box-container-out' );

		if ( 'true' === $settings['mouse_tilt'] ) {
			$this->add_render_attribute( '_wrapper', 'data-box-tilt', 'true' );
			if ( 'true' === $settings['mouse_tilt_rev'] ) {
				$this->add_render_attribute( '_wrapper', 'data-box-tilt-reverse', 'true' );
			}
		}

		$this->add_render_attribute(
			'outer_wrap',
			'class',
			array(
				'premium-icon-box-content-wrap',
				'premium-icon-box-cta-' . $settings['premium_icon_box_icon_text_pos'],
			)
		);

		$this->add_render_attribute( 'title_wrap', 'class', 'premium-icon-box-title-container' );

		$this->add_render_attribute( 'icon_wrap', 'class', 'premium-icon-wrapper' );

		?>

		<?php if ( 'yes' === $settings['premium_icon_box_back_icon_switcher'] ) : ?>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'box' ) ); ?>>
		<?php endif; ?>
		<div class="premium-icon-box-container-in <?php echo esc_attr( $flex_pos ) . ' ' . esc_attr( $flex_ver_pos ); ?>" >
			<div class="premium-icon-box-icon-container">

				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon_wrap' ) ); ?>>
					<?php if ( 'font-awesome-icon' === $icon_type ) :

						if ( ( $is_new || $migrated ) && 'yes' !== $settings['draw_svg'] ) :
							Icons_Manager::render_icon(
								$settings['premium_icon_box_font_updated'],
								array(
									'class'       => array(
										'premium-icon-box-icon',
										$settings['premium_icon_box_hover'],
									),
									'aria-hidden' => 'true',
								)
							);
						else : ?>
							<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>></i>
						<?php endif; ?>

					<?php elseif ( 'svg' === $icon_type ) : ?>
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>>
							<?php $this->print_unescaped_setting( 'custom_svg' ); ?>
						</div>
					<?php elseif ( 'custom-image' === $icon_type ) : ?>
						<?php PAPRO_Helper::get_attachment_image_html( $settings, 'premium_icon_box_image_size', 'premium_icon_box_custom_image', $settings['premium_icon_box_hover'] ); ?>
					<?php else : ?>
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon_box_lottie' ) ); ?>></div>
					<?php endif; ?>
					</div>
				</div>

			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'outer_wrap' ) ); ?>>

                <?php if ( 'yes' === $settings['premium_icon_box_title_switcher'] || 'yes' === $settings['premium_icon_box_desc_switcher'] ) : ?>
                    <div class="premium-icon-box-text-wrap">
                    <?php if ( 'yes' === $settings['premium_icon_box_title_switcher'] && ! empty( $title ) ) : ?>

                            <<?php echo wp_kses_post( $heading ) . ' ' . wp_kses_post( $this->get_render_attribute_string( 'premium_icon_box_title' ) ); ?>><?php echo wp_kses_post( $settings['premium_icon_box_title'] ); ?>
                            <?php if ( ! empty( $settings['premium_icon_box_label'] ) ) : ?>
                                <span <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_icon_box_label' ) ); ?> ><?php echo wp_kses_post( $settings['premium_icon_box_label'] ); ?></span>
                            <?php endif; ?>
                            </<?php echo wp_kses_post( $heading ); ?>>
                    <?php endif; ?>

                    <?php if ( 'yes' === $settings['premium_icon_box_desc_switcher'] && ! empty( $settings['premium_icon_box_content'] ) ) : ?>
                        <div <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_icon_box_content' ) ); ?>>
                            <?php echo $this->parse_text_editor( $settings['premium_icon_box_content'] ); ?>
                        </div>
                    <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ( 'yes' === $settings['premium_icon_box_link_switcher'] && ( 'yes' !== $settings['box_link'] || 'yes' === $settings['keep_text_link'] ) ) : ?>
                    <?php if ( 'yes' === $settings['premium_icon_box_link_text_switcher'] || 'yes' === $settings['premium_icon_box_link_icon_switcher'] ) : ?>
                        <div class="premium-icon-box-more <?php echo esc_attr( $text_ver_pos ); ?>">

                            <?php if ( 'yes' !== $settings['box_link'] ) : ?>
                                <a <?php echo wp_kses_post( $this->get_render_attribute_string( 'link' ) ); ?>>
                            <?php endif; ?>

                            <?php if ( 'before' === $icon_position && 'yes' === $settings['premium_icon_box_link_icon_switcher'] ) :
                                if ( $read_new || $read_migrated ) :
                                    Icons_Manager::render_icon(
                                        $settings['premium_icon_box_more_icon_updated'],
                                        array(
                                            'class'       => 'premium-icon-box-more-icon',
                                            'aria-hidden' => 'true',
                                        )
                                    );
                                    else :
                                        ?>
                                        <i <?php echo wp_kses_post( $this->get_render_attribute_string( 'more_icon' ) ); ?>></i>
                                        <?php
                                endif;
                            endif;

                            if ( 'yes' === $settings['premium_icon_box_link_text_switcher'] ) : ?>
                                <div class="premium-button-text-icon-wrapper">
                                    <span><?php echo wp_kses_post( $settings['premium_icon_box_more_text'] ); ?></span>
                                </div>
                            <?php endif;

                            if ( 'after' === $icon_position && 'yes' === $settings['premium_icon_box_link_icon_switcher'] ) :
                                if ( $read_new || $read_migrated ) :
                                    Icons_Manager::render_icon(
                                        $settings['premium_icon_box_more_icon_updated'],
                                        array(
                                            'class'       => 'premium-icon-box-more-icon',
                                            'aria-hidden' => 'true',
                                        )
                                    );
                                    else :
                                        ?>
                                    <i <?php echo wp_kses_post( $this->get_render_attribute_string( 'more_icon' ) ); ?>></i>
                                        <?php
                                endif;
                            endif ?>

                                <?php if ( 'style6' === $settings['premium_button_hover_effect'] && 'yes' === $settings['mouse_detect'] ) : ?>
                                    <span class="premium-button-style6-bg"></span>
                                <?php endif; ?>

                                <?php if ( 'style8' === $settings['premium_button_hover_effect'] ) : ?>
                                    <?php echo Helper_Functions::get_btn_svgs( $settings['underline_style'] ); ?>
                                <?php endif; ?>

                            <?php if ( 'yes' !== $settings['box_link'] ) : ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
			</div>
		</div>

		<?php if ( 'yes' === $settings['premium_icon_box_back_icon_switcher'] ) : ?>
			<div class="premium-icon-box-big <?php echo esc_attr( $icon_hover ); ?>">
				<?php
				if ( 'font-awesome-icon' === $icon_type ) :
					if ( $is_new || $migrated ) :
						Icons_Manager::render_icon(
							$settings['premium_icon_box_font_updated'],
							array(
								'aria-hidden' => 'true',
								'class'       => 'premium-icon-box-icon',
							)
						);
					else :
						?>
						<i class="premium-icon-box-icon-big <?php echo esc_attr( $settings['premium_icon_box_font'] ); ?>"></i>
					<?php endif; ?>
				<?php elseif ( 'custom-image' === $icon_type ) : ?>
					<?php Group_Control_Image_Size::print_attachment_image_html( $settings, 'premium_icon_box_image_size', 'premium_icon_box_custom_image' ); ?>
				<?php else : ?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon_box_lottie' ) ); ?>></div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php if ( 'yes' === $settings['box_link'] ) { ?>
			<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'link' ) ); ?>><span><?php echo wp_kses_post( $settings['premium_icon_box_title'] ); ?></span></a>
		<?php } ?>
		<?php if ( 'yes' === $settings['premium_icon_box_back_icon_switcher'] ) : ?>
			</div>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render Icon Box output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#

			var title = settings.premium_icon_box_title,
				titleTag = elementor.helpers.validateHTMLTag( settings.premium_icon_box_title_heading ),
				iconFont = settings.premium_icon_box_font,
				iconPosition = settings.premium_icon_box_icon_position,
				iconHover = 'yes' === settings.premium_icon_box_back_icon_hover ? 'premium-icon-box-big-hover' : '',
				flexPosition = 'premium-icon-box-flex-'  + settings.premium_icon_box_icon_flex_pos,
				flexVerPosition = 'premium-icon-box-flex-ver-' + settings.premium_icon_box_icon_flex_ver_pos,
				boxUrl = 'url' === settings.premium_icon_box_link_selection ? settings.premium_icon_box_link.url : settings.premium_icon_box_existing_link,
				iconType = settings.premium_icon_box_selector;
				textVerPosition = settings.premium_icon_box_icon_text_pos !== "bottom" ? 'premium-icon-box-flex-ver-' + settings.premium_icon_box_icon_text_ver_pos : "";

				if ( 'font-awesome-icon' === iconType || 'svg' === iconType ) {

					if( 'font-awesome-icon' === iconType ) {

						var iconHTML = 'yes' !== settings.draw_svg ? elementor.helpers.renderIcon( view, settings.premium_icon_box_font_updated, {
						'class': [ 'premium-icon-box-icon', settings.premium_icon_box_hover ],
						'aria-hidden': true
						}, 'i' , 'object' ) : false,
						migrated = elementor.helpers.isIconMigrated( settings, 'premium_icon_box_font_updated' );

					var backIconHtml = elementor.helpers.renderIcon( view, settings.premium_icon_box_font_updated, {
						'class': 'premium-icon-box-icon', 'aria-hidden': true }, 'i' , 'object' );

					}

					if( ('yes' === settings.draw_svg && 'font-awesome-icon' === iconType ) || 'svg'=== iconType ) {
						view.addRenderAttribute( 'icon', 'class', [
							'premium-icon-box-icon',
							settings.premium_icon_box_hover,
						] );
					}


					if ( 'yes' === settings.draw_svg ) {

						if( 'font-awesome-icon' === iconType ) {

							view.addRenderAttribute('icon', 'class', settings.premium_icon_box_font_updated.value );

						}

						view.addRenderAttribute( 'icon',
							{
								'class'            : 'premium-svg-drawer',
								'data-svg-reverse' : settings.lottie_reverse,
								'data-svg-loop'    : settings.lottie_loop,
								'data-svg-hover'   : settings.svg_hover,
								'data-svg-sync'    : settings.svg_sync,
								'data-svg-fill'    : settings.svg_color,
								'data-svg-frames'  : settings.frames,
								'data-svg-yoyo'    : settings.svg_yoyo,
								'data-svg-point'   : settings.lottie_reverse ? settings.end_point.size : settings.start_point.size,
							}
						);

					}

				} else if( 'custom-image' === iconType ) {

					if( settings.premium_icon_box_custom_image.url ) {

						var image = {
							id: settings.premium_icon_box_custom_image.id,
							url: settings.premium_icon_box_custom_image.url,
							size: settings.premium_icon_box_image_size_size,
							dimension: settings.premium_icon_box_image_size_custom_dimension,
							model: view.getEditModel()
						};

						var image_url = elementor.imagesManager.getImageUrl( image );

						view.addRenderAttribute( 'icon_box_img', {
							'class': [
								'premium-icon-box-icon-container img',
								settings.premium_icon_box_hover
							],
							'src': image_url,
						});

					}
				} else {

					view.addRenderAttribute( 'icon_box_lottie', {
						'class': [
							'premium-icon-box-animation',
							'premium-lottie-animation',
							settings.premium_icon_box_hover
						],
						'data-lottie-url': settings.lottie_url,
						'data-lottie-loop': settings.lottie_loop,
						'data-lottie-reverse': settings.lottie_reverse
					});
				}

				view.addRenderAttribute('premium_icon_box_title', 'class', 'premium-icon-box-title' );

				view.addInlineEditingAttributes( 'premium_icon_box_title', 'basic' );

				view.addRenderAttribute('premium_icon_box_label','class', 'premium-icon-box-label' );

				view.addInlineEditingAttributes( 'premium_icon_box_label', 'basic' );


				view.addRenderAttribute('premium_icon_box_content', 'class', 'premium-icon-box-content' );

				view.addInlineEditingAttributes('premium_icon_box_content', 'advanced');

				if( 'yes' === settings.premium_icon_box_link_switcher ) {

					var moreIconHTML = elementor.helpers.renderIcon( view, settings.premium_icon_box_more_icon_updated, { 'class': 'premium-icon-box-more-icon', 'aria-hidden': true
						}, 'i' , 'object' ),
						moreMigrated = elementor.helpers.isIconMigrated( settings, 'premium_icon_box_more_icon_updated' );

				}

				view.addRenderAttribute( 'box', 'class', 'premium-icon-box-container-out' );

				if( 'true' === settings.mouse_tilt ) {
					view.addRenderAttribute( '_wrapper', 'data-box-tilt', 'true' );
					if( 'true' === settings.mouse_tilt_rev ) {
						view.addRenderAttribute( '_wrapper', 'data-box-tilt-reverse', 'true' );
					}
				}

				view.addRenderAttribute( 'outer_wrap','class', [
					'premium-icon-box-content-wrap',
					'premium-icon-box-cta-'+settings.premium_icon_box_icon_text_pos
					]
				);

				view.addRenderAttribute( 'title_wrap','class','premium-icon-box-title-container');

		#>

		<# if ( 'yes' === settings.premium_icon_box_back_icon_switcher ) { #>
		<div {{{ view.getRenderAttributeString('box') }}}>
		<# } #>
			<div class="premium-icon-box-container-in {{ flexPosition }} {{ flexVerPosition }} ">
				<div class="premium-icon-box-icon-container">
					<div class="premium-icon-wrapper">
						<# if( 'font-awesome-icon' === iconType ) {
							if ( iconHTML && iconHTML.rendered && ( ! settings.premium_icon_box_font || migrated ) ) { #>
								{{{ iconHTML.value }}}
							<# } else { #>
								<i {{{ view.getRenderAttributeString('icon') }}}></i>
							<# } #>
						<# } else if( 'svg' === iconType ) { #>
							<div {{{ view.getRenderAttributeString('icon') }}}>
								{{{ settings.custom_svg }}}
							</div>
						<# } else if( 'custom-image' === iconType ) { #>
							<img {{{ view.getRenderAttributeString('icon_box_img') }}}>
						<# } else { #>
							<div {{{ view.getRenderAttributeString('icon_box_lottie') }}}></div>
						<# } #>
					</div>
				</div>
				<div {{{ view.getRenderAttributeString('outer_wrap') }}}>
					<# if( 'yes' === settings.premium_icon_box_title_switcher || 'yes' === settings.premium_icon_box_desc_switcher ) { #>
						<div class="premium-icon-box-text-wrap">
						<# if( 'yes' === settings.premium_icon_box_title_switcher && '' != title ) { #>

								<{{{titleTag}}} {{{ view.getRenderAttributeString('premium_icon_box_title') }}}>{{{ settings.premium_icon_box_title }}}
									<# if( '' != settings.premium_icon_box_label ) { #>
										<span {{{ view.getRenderAttributeString('premium_icon_box_label') }}} >{{{ settings.premium_icon_box_label }}}</span>
									<# } #>
								</{{{titleTag}}}>
						<# } #>

						<# if( 'yes' === settings.premium_icon_box_desc_switcher && '' != settings.premium_icon_box_content ) { #>
								<div {{{ view.getRenderAttributeString('premium_icon_box_content') }}}>
									{{{ settings.premium_icon_box_content }}}
								</div>
						<# } #>
						</div>
					<# } #>

					<# if( 'yes' === settings.premium_icon_box_link_switcher && ('yes' !== settings.box_link || 'yes' === settings.keep_text_link ) ) { #>
						<# if( 'yes' === settings.premium_icon_box_link_text_switcher || 'yes' === settings.premium_icon_box_link_icon_switcher ) {
							if( '' != settings.premium_icon_box_more_text || '' != settings.premium_icon_box_more_icon ) { #>
								<div class="premium-icon-box-more {{ textVerPosition }}">
									<# if( 'yes' !== settings.box_link ) {

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

                                        <a class="premium-icon-box-link {{ btnClass }}" href="{{ boxUrl }}" data-text="{{ settings.premium_icon_box_more_text }}">
                                        <# } #>

                                            <# if( 'before' === iconPosition  && 'yes' === settings.premium_icon_box_link_icon_switcher ) {
                                                if ( moreIconHTML && moreIconHTML.rendered && ( ! settings.premium_icon_box_more_icon || moreMigrated ) ) { #>
                                                    {{{ moreIconHTML.value }}}
                                                <# } else { #>
                                                    <i class="premium-icon-box-more-icon {{ settings.premium_icon_box_more_icon }}" aria-hidden="true"></i>
                                                <# }
                                            }

                                            if( 'yes' === settings.premium_icon_box_link_text_switcher ) {

                                                #>
                                                <div class="premium-button-text-icon-wrapper">
                                                    <span>{{{ settings.premium_icon_box_more_text }}}</span>
                                                </div>
                                            <# }

                                            if( 'after' === iconPosition  && 'yes' === settings.premium_icon_box_link_icon_switcher ) {
                                                if ( moreIconHTML && moreIconHTML.rendered && ( ! settings.premium_icon_box_more_icon || moreMigrated ) ) { #>
                                                    {{{ moreIconHTML.value }}}
                                                <# } else { #>
                                                    <i class="premium-icon-box-more-icon {{ settings.premium_icon_box_more_icon }}" aria-hidden="true"></i>
                                                <# }
                                            } #>

                                            <# if ( 'style6' === settings.premium_button_hover_effect && 'yes' === settings.mouse_detect ) { #>
                                                <span class="premium-button-style6-bg"></span>
                                            <# } #>

                                            <# if( 'style8' === settings.premium_button_hover_effect ) { #>
                                                {{{ btnSVG }}}
                                            <# } #>

                                        <# if( 'yes' !== settings.box_link ) { #>
                                        </a>
									<# } #>
								</div>
							<# } #>
						<# } #>
					<# } #>
				</div>
			</div>
			<# if ( 'yes' === settings.premium_icon_box_back_icon_switcher ) { #>
				<div class="premium-icon-box-big {{ iconHover }}">
					<# if( 'font-awesome-icon' === iconType ) {
						if ( backIconHtml && backIconHtml.rendered && ( ! settings.premium_icon_box_font || migrated ) ) { #>
							{{{ backIconHtml.value }}}
						<# } else { #>
							<i class="premium-icon-box-icon-big {{ settings.premium_icon_box_font }}"></i>
						<# } #>
					<# } else if ( 'custom-image' === iconType ) { #>
						<img class="premium-icon-box-icon-big" src="{{ image_url }}">
					<# } else { #>
						<div {{{ view.getRenderAttributeString('icon_box_lottie') }}}></div>
					<# } #>
				</div>
			<# } #>
			<# if( 'yes' === settings.box_link ) { #>
				<a class="premium-icon-box-whole-link" href="{{ boxUrl }}"><span>{{{ settings.premium_icon_box_title }}}</span></a>
			<# } #>

		<# if ( 'yes' === settings.premium_icon_box_back_icon_switcher ) { #>
			</div>
		<# } #>

		<?php
	}
}
