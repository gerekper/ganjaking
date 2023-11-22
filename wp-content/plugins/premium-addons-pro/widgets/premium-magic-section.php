<?php
/**
 * Class: Premium_Magic_Section
 * Name: Magic Section
 * Slug: premium-addon-magic-section
 */

namespace PremiumAddonsPro\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Icons_Manager;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Core\Responsive\Responsive;

// PremiumAddons Classes.
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Premium_Magic_Section
 */
class Premium_Magic_Section extends Widget_Base {

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

		$is_enabled = Admin_Helper::check_svg_draw( 'premium-magic-section' );
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
	 * Widget rtl check.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function check_rtl() {
		return is_rtl();
	}

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-addon-magic-section';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Magic Section', 'premium-addons-pro' );
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
		return 'pa-pro-magic-section';
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
	 * @return array CSS style handles.
	 */
	public function get_style_depends() {
		return array(
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
			'pa-tweenmax',
			'pa-motionpath',
		) : array();

		return array_merge(
			$draw_scripts,
			array(
				'premium-pro',
				'jquery-ui',
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
		return array( 'pa', 'premium', 'off canvas', 'slide' );
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
	 * Register Magic Section controls.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$draw_icon = $this->check_icon_draw();

		$this->start_controls_section(
			'premium_magic_section',
			array(
				'label' => __( 'Content', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_magic_section_content_type',
			array(
				'label'       => __( 'Content to Show', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'editor'   => __( 'Text Editor', 'premium-addons-pro' ),
					'template' => __( 'Elementor Template', 'premium-addons-pro' ),
				),
				'default'     => 'editor',
				'label_block' => true,
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
					'premium_magic_section_content_type' => 'template',
				),
			)
		);

		$this->add_control(
			'premium_magic_section_content_temp_live',
			array(
				'type'        => Controls_Manager::BUTTON,
				'label_block' => true,
				'button_type' => 'default papro-btn-block',
				'text'        => __( 'Create / Edit Template', 'premium-addons-pro' ),
				'event'       => 'createLiveTemp',
				'condition'   => array(
					'premium_magic_section_content_type' => 'template',
				),
			)
		);

		$this->add_control(
			'premium_magic_section_content_temp',
			array(
				'label'       => __( 'OR Select Existing Template', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'classes'     => 'premium-live-temp-label',
				'options'     => $this->getTemplateInstance()->get_elementor_page_list(),
				'condition'   => array(
					'premium_magic_section_content_type' => 'template',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_magic_section_content',
			array(
				'type'       => Controls_Manager::WYSIWYG,
				'dynamic'    => array( 'active' => true ),
				'default'    => 'Premium Magic Section Content',
				'condition'  => array(
					'premium_magic_section_content_type' => 'editor',
				),
				'show_label' => false,
			)
		);

		$this->add_control(
			'premium_magic_section_close',
			array(
				'label' => __( 'Close Button', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'close_on_outside',
			array(
				'label'   => esc_html__( 'Close On Click Outside', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'premium_magic_section_close_pos_hor',
			array(
				'label'     => __( 'Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom',
				'options'   => array(
					'top'    => __( 'Top', 'premium-addons-pro' ),
					'bottom' => __( 'Bottom', 'premium-addons-pro' ),
				),
				'condition' => array(
					'premium_magic_section_close' => 'yes',
					'premium_magic_section_pos'   => array( 'left', 'right' ),
				),
			)
		);

		$this->add_control(
			'premium_magic_section_close_pos',
			array(
				'label'     => __( 'Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'right',
				'options'   => array(
					'left'  => __( 'Left', 'premium-addons-pro' ),
					'right' => __( 'Right', 'premium-addons-pro' ),
				),
				'condition' => array(
					'premium_magic_section_close' => 'yes',
					'premium_magic_section_pos'   => array( 'top', 'bottom' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_magic_trig_icon',
			array(
				'label' => __( 'Trigger', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_magic_section_trig_selector',
			array(
				'label'   => __( 'Trigger', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'button',
				'options' => array(
					'button' => __( 'Button', 'premium-addons-pro' ),
					'icon'   => __( 'Icon', 'premium-addons-pro' ),
				),
			)
		);

		$this->add_control(
			'premium_magic_section_trig_float',
			array(
				'label' => __( 'Float', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_magic_section_icon_selector',
			array(
				'label'     => __( 'Icon Type', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'font-awesome-icon',
				'options'   => array(
					'font-awesome-icon' => __( 'Font Awesome Icon', 'premium-addons-pro' ),
					'custom-image'      => __( 'Custom Image', 'premium-addons-pro' ),
				),
				'condition' => array(
					'premium_magic_section_trig_selector' => 'icon',
				),
			)
		);

		$this->start_controls_tabs( 'premium_magic_section_icon_font' );

		/*Button Color*/
		$this->start_controls_tab(
			'premium_magic_section_icon_font_in_tab',
			array(
				'label'     => __( 'In', 'premium-addons-pro' ),
				'condition' => array(
					'premium_magic_section_trig_selector' => 'icon',
					'premium_magic_section_icon_selector' => 'font-awesome-icon',
					'premium_magic_section_trig_float'    => 'yes',
				),
			)
		);

		$this->add_control(
			'new_icon_font_in',
			array(
				'label'            => __( 'Icon', 'premium-addons-pro' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'premium_magic_section_icon_font_in',
				'default'          => array(
					'value'   => 'eicon-arrow-down',
					'library' => 'solid',
				),
				'condition'        => array(
					'premium_magic_section_trig_selector' => 'icon',
					'premium_magic_section_icon_selector' => 'font-awesome-icon',
				),
				'separator'        => 'after',

			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_magic_section_icon_font_out_tab',
			array(
				'label'     => __( 'Out', 'premium-addons-pro' ),
				'condition' => array(
					'premium_magic_section_trig_selector' => 'icon',
					'premium_magic_section_icon_selector' => 'font-awesome-icon',
					'premium_magic_section_trig_float'    => 'yes',
				),
			)
		);

		$this->add_control(
			'new_icon_font_out',
			array(
				'label'            => __( 'Icon', 'premium-addons-pro' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'premium_magic_section_icon_font_out',
				'default'          => array(
					'value'   => 'eicon-arrow-up',
					'library' => 'solid',
				),
				'condition'        => array(
					'premium_magic_section_trig_selector' => 'icon',
					'premium_magic_section_icon_selector' => 'font-awesome-icon',
					'premium_magic_section_trig_float'    => 'yes',
				),
				'separator'        => 'after',

			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'premium_magic_section_custom_image',
			array(
				'label'     => __( 'Custom Image', 'premium-addons-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => array( 'active' => true ),
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'premium_magic_section_trig_selector' => 'icon',
					'premium_magic_section_icon_selector' => 'custom-image',
				),
			)
		);

		$this->add_responsive_control(
			'prmium_magic_section_float_icon_size',
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
				'condition'  => array(
					'premium_magic_section_trig_float'     => 'yes',
					'premium_magic_section_trig_selector!' => 'button',
					'premium_magic_section_icon_selector'  => 'font-awesome-icon',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-magic-section-icon' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-magic-section-icon-wrap svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'prmium_magic_section_float_img_size',
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
				'condition'  => array(
					'premium_magic_section_trig_float'     => 'yes',
					'premium_magic_section_trig_selector!' => 'button',
					'premium_magic_section_icon_selector'  => 'custom-image',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-magic-section-icon-wrap' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'prmium_magic_section_icon_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-magic-section-btn' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-magic-section-button-trig svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'premium_magic_section_trig_float!'   => 'yes',
					'premium_magic_section_trig_selector' => 'icon',
					'premium_magic_section_icon_selector' => 'font-awesome-icon',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'prmium_magic_section_trig_image_size',
				'default'   => 'full',
				'condition' => array(
					'premium_magic_section_trig_selector' => 'icon',
					'premium_magic_section_icon_selector' => 'custom-image',
					'premium_magic_section_trig_float!'   => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_magic_section_button_text',
			array(
				'label'       => __( 'Button Text', 'premium-addons-pro' ),
				'default'     => __( 'Premium Magic Section', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'premium_magic_section_trig_selector' => 'button',
				),
			)
		);

		$common_conditions = array(
			'premium_magic_section_icon_switcher' => 'yes',
			'premium_magic_section_trig_selector' => 'button',
		);

		$this->add_control(
			'premium_magic_section_icon_switcher',
			array(
				'label'       => __( 'Icon', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable or disable button icon', 'premium-addons-pro' ),
				'condition'   => array(
					'premium_magic_section_trig_selector' => 'button',
				),
			)
		);

		$this->add_control(
			'icon_type',
			array(
				'label'     => __( 'Icon Type', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'icon' => __( 'Icon', 'premium-addons-pro' ),
					'svg'  => __( 'SVG Code', 'premium-addons-pro' ),
				),
				'default'   => 'icon',
				'condition' => $common_conditions,
			)
		);

		$this->add_control(
			'new_button_icon_selection',
			array(
				'label'            => __( 'Icon', 'premium-addons-pro' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'premium_magic_section_button_icon_selection',
				'default'          => array(
					'value'   => 'fa fa-star',
					'library' => 'solid',
				),
				'condition'        => array_merge(
					$common_conditions,
					array(
						'icon_type' => 'icon',
					)
				),
			)
		);

		$this->add_control(
			'custom_svg',
			array(
				'label'       => __( 'SVG Code', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => 'You can use these sites to create SVGs: <a href="https://danmarshall.github.io/google-font-to-svg-path/" target="_blank">Google Fonts</a> and <a href="https://boxy-svg.com/" target="_blank">Boxy SVG</a>',
				'condition'   => array_merge(
					$common_conditions,
					array(
						'icon_type' => 'svg',
					)
				),
			)
		);

		$this->add_control(
			'draw_svg',
			array(
				'label'     => __( 'Draw Icon', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'classes'   => $draw_icon ? '' : 'editor-pa-control-disabled',
				'condition' => array_merge(
					$common_conditions,
					array(
						'icon_type' => array( 'icon', 'svg' ),
						'new_button_icon_selection[library]!' => 'svg',
					)
				),
			)
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
					'condition' => array_merge(
						$common_conditions,
						array(
							'icon_type' => array( 'icon', 'svg' ),
						)
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-magic-section-btn svg *' => 'stroke-width: {{SIZE}}',
					),
				)
			);

			$this->add_control(
				'svg_sync',
				array(
					'label'     => __( 'Draw All Paths Together', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array_merge(
						$common_conditions,
						array(
							'icon_type' => array( 'icon', 'svg' ),
							'draw_svg'  => 'yes',
						)
					),
				)
			);

			$this->add_control(
				'svg_loop',
				array(
					'label'        => __( 'Loop', 'premium-addons-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'default'      => 'true',
					'condition'    => array_merge(
						$common_conditions,
						array(
							'icon_type' => array( 'icon', 'svg' ),
							'draw_svg'  => 'yes',
						)
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
					'condition'   => array_merge(
						$common_conditions,
						array(
							'icon_type' => array( 'icon', 'svg' ),
							'draw_svg'  => 'yes',
						)
					),
				)
			);

			$this->add_control(
				'svg_reverse',
				array(
					'label'        => __( 'Reverse', 'premium-addons-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'condition'    => array_merge(
						$common_conditions,
						array(
							'icon_type' => array( 'icon', 'svg' ),
							'draw_svg'  => 'yes',
						)
					),
				)
			);

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
					'condition'   => array_merge(
						$common_conditions,
						array(
							'icon_type'    => array( 'icon', 'svg' ),
							'draw_svg'     => 'yes',
							'svg_reverse!' => 'true',
						)
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
					'condition'   => array_merge(
						$common_conditions,
						array(
							'icon_type'   => array( 'icon', 'svg' ),
							'draw_svg'    => 'yes',
							'svg_reverse' => 'true',
						)
					),
				)
			);

			$this->add_control(
				'svg_hover',
				array(
					'label'        => __( 'Only Play on Hover', 'premium-addons-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'condition'    => array_merge(
						$common_conditions,
						array(
							'icon_type' => array( 'icon', 'svg' ),
							'draw_svg'  => 'yes',
						)
					),
				)
			);

			$this->add_control(
				'svg_yoyo',
				array(
					'label'     => __( 'Yoyo Effect', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array_merge(
						$common_conditions,
						array(
							'icon_type' => array( 'icon', 'svg' ),
							'draw_svg'  => 'yes',
							'svg_loop'  => 'true',
						)
					),
				)
			);
		} elseif ( method_exists( 'PremiumAddons\Includes\Helper_Functions', 'get_draw_svg_notice' ) ) {

			Helper_Functions::get_draw_svg_notice(
				$this,
				'magic',
				array_merge(
					$common_conditions,
					array(
						'icon_type' => array( 'icon', 'svg' ),
						'new_button_icon_selection[library]!' => 'svg',
					)
				)
			);
		}

		$this->add_control(
			'premium_magic_section_icon_position',
			array(
				'label'       => __( 'Icon Position', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'before',
				'options'     => array(
					'before' => __( 'Before', 'premium-addons-pro' ),
					'after'  => __( 'After', 'premium-addons-pro' ),
				),
				'condition'   => $common_conditions,
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_magic_section_icon_before_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'condition'  => array_merge(
					$common_conditions,
					array(
						'icon_type' => 'icon',
					)
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-magic-section-btn i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-magic-section-btn svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'svg_icon_width',
			array(
				'label'      => __( 'Icon Width', 'premium-addons-pro' ),
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
				'condition'  => array_merge(
					$common_conditions,
					array(
						'icon_type' => 'svg',
					)
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-magic-section-btn svg' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'svg_icon_height',
			array(
				'label'      => __( 'Icon Height', 'premium-addons-pro' ),
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
				'condition'  => array_merge(
					$common_conditions,
					array(
						'icon_type' => 'svg',
					)
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-magic-section-btn svg' => 'height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		if ( ! $this->check_rtl() ) {
			$this->add_control(
				'premium_magic_section_icon_before_spacing',
				array(
					'label'     => __( 'Icon Spacing', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SLIDER,
					'condition' => array(
						'premium_magic_section_trig_selector' => 'button',
						'premium_magic_section_icon_switcher' => 'yes',
						'premium_magic_section_icon_position' => 'before',
					),
					'default'   => array(
						'size' => 15,
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-magic-section-btn .premium-magic-btn-icon' => 'margin-right: {{SIZE}}px',
					),
					'separator' => 'after',
				)
			);
		}

		if ( ! $this->check_rtl() ) {
			$this->add_control(
				'premium_magic_box_icon_after_spacing',
				array(
					'label'     => __( 'Icon Spacing', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SLIDER,
					'condition' => array(
						'premium_magic_section_trig_selector' => 'button',
						'premium_magic_section_icon_switcher' => 'yes',
						'premium_magic_section_icon_position' => 'after',
					),
					'default'   => array(
						'size' => 15,
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-magic-section-btn .premium-magic-btn-icon' => 'margin-left: {{SIZE}}px',
					),
					'separator' => 'after',
				)
			);
		}

		if ( $this->check_rtl() ) {
			$this->add_control(
				'premium_magic_box_icon_rtl_before_spacing',
				array(
					'label'     => __( 'Icon Spacing', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SLIDER,
					'condition' => array(
						'premium_magic_section_trig_selector' => 'button',
						'premium_magic_section_icon_switcher' => 'yes',
						'premium_magic_section_icon_position' => 'before',
					),
					'default'   => array(
						'size' => 15,
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-magic-section-btn .premium-magic-btn-icon' => 'margin-left: {{SIZE}}px',
					),
					'separator' => 'after',
				)
			);
		}

		if ( $this->check_rtl() ) {
			$this->add_control(
				'premium_magic_box_icon_rtl_after_spacing',
				array(
					'label'     => __( 'Icon Spacing', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SLIDER,
					'condition' => array(
						'premium_magic_section_trig_selector' => 'button',
						'premium_magic_section_icon_switcher' => 'yes',
						'premium_magic_section_icon_position' => 'after',
					),
					'default'   => array(
						'size' => 15,
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-magic-section-btn .premium-magic-btn-icon' => 'margin-right: {{SIZE}}px',
					),
					'separator' => 'after',
				)
			);
		}

		/*Button Size*/
		$this->add_control(
			'premium_magic_section_button_size',
			array(
				'label'       => __( 'Button Size', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'premium-btn-sm'    => __( 'Small', 'premium-addons-pro' ),
					'premium-btn-md'    => __( 'Medium', 'premium-addons-pro' ),
					'premium-btn-lg'    => __( 'Large', 'premium-addons-pro' ),
					'premium-btn-block' => __( 'Block', 'premium-addons-pro' ),
				),
				'label_block' => true,
				'default'     => 'premium-btn-lg',
				'condition'   => array(
					'premium_magic_section_trig_selector' => 'button',
					'premium_magic_section_trig_float!'   => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_magic_section_button_align',
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
				'selectors' => array(
					'{{WRAPPER}} .premium-magic-section-button-trig' => 'text-align: {{VALUE}}',
				),
				'default'   => 'center',
				'toggle'    => false,
				'condition' => array(
					'premium_magic_section_trig_float!'  => 'yes',
					'premium_magic_section_button_size!' => 'premium-btn-block',
				),
			)
		);

		$this->add_control(
			'premium_magic_section_trig_anim',
			array(
				'label'     => __( 'Hover Animation', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HOVER_ANIMATION,
				'condition' => array(
					'premium_magic_section_trig_selector' => 'icon',
				),
			)
		);

		$this->add_responsive_control(
			'premium_magic_section_icon_align_hor',
			array(
				'label'     => __( 'Icon Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-justify',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-magic-section-icon-wrap' => 'text-align: {{VALUE}}',
				),
				'default'   => 'center',
				'condition' => array(
					'premium_magic_section_trig_selector' => 'icon',
					'premium_magic_section_trig_float'    => 'yes',
					'premium_magic_section_pos'           => array( 'top', 'bottom' ),
				),
			)
		);

		$this->add_responsive_control(
			'premium_magic_section_icon_align_ver',
			array(
				'label'     => __( 'Icon Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'top'    => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-up',
					),
					'middle' => array(
						'title' => __( 'Middle', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-down',
					),
				),
				'default'   => 'middle',
				'condition' => array(
					'premium_magic_section_trig_float' => 'yes',
					'premium_magic_section_pos'        => array( 'right', 'left' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_magic_section_display',
			array(
				'label' => __( 'Display Options', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_magic_section_pos',
			array(
				'label'   => __( 'Position', 'premium-addons-pro' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'top'    => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-down',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-left',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-up',
					),
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-right',
					),
				),
				'default' => 'top',
			)
		);

		$this->add_responsive_control(
			'content_height',
			array(
				'label'      => __( 'Maximum Height', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 500,
					),
					'em' => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'condition'  => array(
					'premium_magic_section_pos' => array( 'top', 'bottom' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-magic-section-wrap' => 'max-height: {{SIZE}}{{UNIT}}; overflow-y: scroll',
				),
			)
		);

		$this->add_control(
			'premium_magic_section_gutter',
			array(
				'label'       => __( 'Gutter (%)', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => -100,
				'max'         => 100,
				'description' => __( '0% is default. Increase to push the section outside or decrease to pull the section inside.', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_magic_section_style',
			array(
				'label'       => __( 'Style', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'slide' => __( 'Slide', 'premium-addons-pro' ),
					'push'  => __( 'Push', 'premium-addons-pro' ),
				),
				'default'     => 'slide',
				'label_block' => true,
				'condition'   => array(
					'premium_magic_section_pos!' => 'bottom',
				),
			)
		);

		$this->add_control(
			'premium_magic_section_overlay',
			array(
				'label' => __( 'Overlay', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_responsive_control(
			'premium_magic_section_content_align',
			array(
				'label'     => __( 'Content Alignment', 'premium-addons-pro' ),
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
				'selectors' => array(
					'{{WRAPPER}} .premium-magic-section-content-wrap' => 'text-align: {{VALUE}}',
				),
				'default'   => 'center',
				'condition' => array(
					'premium_magic_section_content_type' => 'editor',
				),
			)
		);

		$this->add_responsive_control(
			'premium_magic_section_content_position',
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
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-down',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-magic-section-wrap.left .premium-magic-section-content-wrap-out, {{WRAPPER}} .premium-magic-section-wrap.right .premium-magic-section-content-wrap-out' => 'align-items: {{VALUE}}',
				),
				'default'   => 'center',
				'condition' => array(
					'premium_magic_section_pos' => array( 'right', 'left' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_magic_responsive',
			array(
				'label' => __( 'Responsive', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_magic_section_responsive_switcher',
			array(
				'label'       => __( 'Responsive Controls', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'This options will hide the trigger and the content below a specific screen size', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_magic_section_hide_tabs',
			array(
				'label'       => __( 'Hide on Tablets', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Hide Magic Section below Elementor\'s Tablet Breakpoint ', 'premium-addons-pro' ),
				'condition'   => array(
					'premium_magic_section_responsive_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_magic_section_hide_mobs',
			array(
				'label'       => __( 'Hide on Mobiles', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Hide Magic Section below Elementor\'s Mobile Breakpoint ', 'premium-addons-pro' ),
				'condition'   => array(
					'premium_magic_section_responsive_switcher' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		/*Selector Style*/
		$this->start_controls_section(
			'premium_magic_section_button_style',
			array(
				'label' => __( 'Trigger', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		/*Selector Text Typography*/
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'premium_magic_section_button_typo',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector'  => '{{WRAPPER}} .premium-magic-section-btn span',
				'condition' => array(
					'premium_magic_section_trig_selector' => 'button',
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
					'premium_magic_section_trig_selector' => 'button',
					'icon_type'                           => array( 'icon', 'svg' ),
					'draw_svg'                            => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'premium_magic_section_button_style_tabs' );

		/*Button Color*/
		$this->start_controls_tab(
			'premium_magic_section_button_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_magic_section_button_text_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'condition' => array(
					'premium_magic_section_trig_selector' => 'button',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-magic-section-btn, {{WRAPPER}} .premium-magic-section-btn .premium-magic-btn-text' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_magic_section_button_icon_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'condition' => array(
					'premium_magic_section_trig_selector' => 'button',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-magic-section-btn .premium-magic-btn-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-magic-section-btn svg, {{WRAPPER}} .premium-magic-section-btn svg *' => 'fill: {{VALUE}}',
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
						'icon_type' => array( 'icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-magic-section-button-trig svg *' => 'stroke: {{VALUE}};',
					),
				)
			);
		}

		$this->add_control(
			'premium_magic_section_icon_color_normal',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'condition' => array(
					'premium_magic_section_trig_selector!' => 'button',
					'premium_magic_section_icon_selector'  => 'font-awesome-icon',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-magic-section-btn' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-magic-section-icon-wrap svg, {{WRAPPER}} .premium-magic-section-button-trig svg' => 'fill: {{VALUE}}',
				),
			)
		);

		/*Button Background Color*/
		$this->add_control(
			'premium_magic_section_button_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-magic-section-btn'   => 'background-color: {{VALUE}};',
				),
			)
		);

		/*Button Border*/
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_magic_section_button_border',
				'selector' => '{{WRAPPER}} .premium-magic-section-btn',
			)
		);

		/*Button Border Radius*/
		$this->add_control(
			'premium_magic_section_button_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-magic-section-btn'     => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		/*Selector Box Shadow*/
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_magic_section_button_box_shadow',
				'selector' => '{{WRAPPER}} .premium-magic-section-btn',
			)
		);

		/*Selector Padding*/
		$this->add_responsive_control(
			'premium_magic_section_button_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-magic-section-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_magic_section_button_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'button_text_hover_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'condition' => array(
					'premium_magic_section_trig_selector' => 'button',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-magic-section-btn:hover .premium-magic-btn-text' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_icon_hover_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'condition' => array(
					'premium_magic_section_trig_selector' => 'button',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-magic-section-btn:hover .premium-magic-btn-icon' => 'color:{{VALUE}};',
					'{{WRAPPER}} .premium-magic-section-btn:hover svg, {{WRAPPER}} .premium-magic-section-btn:hover svg *' => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'premium_magic_section_icon_color_HOVER',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'condition' => array(
					'premium_magic_section_trig_selector!' => 'button',
					'premium_magic_section_icon_selector'  => 'font-awesome-icon',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-magic-section-btn:hover' => 'color:{{VALUE}};',
					'{{WRAPPER}} .premium-magic-section-icon-wrap:hover' => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'premium_magic_section_button_hover_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-magic-section-btn:hover' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_magic_section_button_border_hover',
				'selector' => '{{WRAPPER}} .premium-magic-section-btn:hover',
			)
		);

		/*Button Border Radius*/
		$this->add_control(
			'premium_magic_section_button_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-magic-section-btn:hover' => 'border-radius:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_magic_section_button_box_shadow_hover',
				'selector' => '{{WRAPPER}} .premium-magic-section-btn:hover',
			)
		);

		$this->add_responsive_control(
			'premium_magic_section_button_padding_hover',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-magic-section-btn:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_magic_section_close_style',
			array(
				'label'     => __( 'Close', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_magic_section_close' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_magic_section_close_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-magic-section-wrap .premium-magic-section-close i'   => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_magic_section_close_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-magic-section-wrap .premium-magic-section-close' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_magic_section_close_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-magic-section-wrap .premium-magic-section-close:hover i'   => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_magic_section_close_backcolor',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-magic-section-wrap .premium-magic-section-close'   => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_magic_section_close_border',
				'selector' => '{{WRAPPER}} .premium-magic-section-wrap .premium-magic-section-close',
			)
		);

		$this->add_control(
			'premium_magic_section_close_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-magic-section-wrap .premium-magic-section-close' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_magic_section_close_shadow',
				'selector' => '{{WRAPPER}} .premium-magic-section-wrap .premium-magic-section-close',
			)
		);

		/*Icon Margin*/
		$this->add_responsive_control(
			'premium_magic_section_close_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-magic-section-wrap .premium-magic-section-close' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_magic_section_close_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-magic-section-wrap .premium-magic-section-close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		/*Magic Section Container Style Section*/
		$this->start_controls_section(
			'premium_magic_section_container',
			array(
				'label' => __( 'Container', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_magic_section_overlay_background',
			array(
				'label'     => __( 'Overlay Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-magic-section-overlay' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'premium_magic_section_overlay' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_magic_section_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-magic-section-wrap',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_magic_section_background__border',
				'selector' => '{{WRAPPER}} .premium-magic-section-wrap',
			)
		);

		$this->add_responsive_control(
			'premium_magic_section_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-magic-section-wrap' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-top-right-radius: {{RIGHT}}{{UNIT}}; border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(

				'name'     => 'premium_magic_section_background_box_shadow',
				'selector' => '{{WRAPPER}} .premium-magic-section-wrap',
			)
		);

		$this->add_responsive_control(
			'premium_magic_section_background_margin',
			array(
				'label'      => __( 'Out Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-magic-section-wrap.out' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_magic_section_background_margin_in',
			array(
				'label'      => __( 'In Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-magic-section-wrap.in' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_magic_section_background_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-magic-section-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render Magic Section widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$template = empty( $settings['premium_magic_section_content_temp'] ) ? $settings['live_temp_content'] : $settings['premium_magic_section_content_temp'];

		$in_icon  = '';
		$out_icon = '';

		if ( 'icon' === $settings['premium_magic_section_trig_selector'] ) {

			if ( 'font-awesome-icon' === $settings['premium_magic_section_icon_selector'] ) {

				$migrated_in = isset( $settings['__fa4_migrated']['new_icon_font_in'] );
				$is_new_in   = empty( $settings['premium_magic_section_icon_font_in'] ) && Icons_Manager::is_migration_allowed();
				$in_icon     = ( $is_new_in || $migrated_in ) ? $settings['new_icon_font_in']['value'] : $settings['premium_magic_section_icon_font_in'];
				if ( 'yes' === $settings['premium_magic_section_trig_float'] ) {
					$migrated_out = isset( $settings['__fa4_migrated']['new_icon_font_out'] );
					$is_new_out   = empty( $settings['premium_magic_section_icon_font_out'] ) && Icons_Manager::is_migration_allowed();
					$out_icon     = ( $is_new_out || $migrated_out ) ? $settings['new_icon_font_out']['value'] : $settings['premium_magic_section_icon_font_out'];
				}
			} else {
				$icon_font = $settings['premium_magic_section_custom_image']['url'];
			}
		} else {

			$icon_type = $settings['icon_type'];

			if ( 'icon' === $icon_type ) {

				$migrated = isset( $settings['__fa4_migrated']['new_button_icon_selection'] );
				$is_new   = empty( $settings['premium_magic_section_button_icon_selection'] ) && Icons_Manager::is_migration_allowed();
			}

			if ( ( 'yes' === $settings['draw_svg'] && 'icon' === $icon_type ) || 'svg' === $icon_type ) {
				$this->add_render_attribute( 'icon', 'class', 'premium-magic-btn-icon' );
			}

			if ( 'yes' === $settings['draw_svg'] ) {

				$this->add_render_attribute(
					'container',
					'class',
					array(
						'elementor-invisible',
						'premium-drawer-hover',
					)
				);

				if ( 'icon' === $icon_type ) {

					$this->add_render_attribute( 'icon', 'class', $settings['new_button_icon_selection']['value'] );

				}

				$this->add_render_attribute(
					'icon',
					array(
						'class'            => 'premium-svg-drawer',
						'data-svg-reverse' => $settings['svg_reverse'],
						'data-svg-loop'    => $settings['svg_loop'],
						'data-svg-sync'    => $settings['svg_sync'],
						'data-svg-hover'   => $settings['svg_hover'],
						'data-svg-fill'    => $settings['svg_color'],
						'data-svg-frames'  => $settings['frames'],
						'data-svg-yoyo'    => $settings['svg_yoyo'],
						'data-svg-point'   => $settings['svg_reverse'] ? $settings['end_point']['size'] : $settings['start_point']['size'],
					)
				);

			} else {

				$this->add_render_attribute( 'icon', 'class', 'premium-svg-nodraw' );

			}
		}

		$section_gutter = ! empty( $settings['premium_magic_section_gutter'] ) ? $settings['premium_magic_section_gutter'] : 0;
		$section_pos    = $settings['premium_magic_section_pos'];
		if ( 'right' === $section_pos || 'left' === $section_pos ) {
			$icon_align  = $settings['premium_magic_section_icon_align_ver'];
			$close_align = 'close-' . $settings['premium_magic_section_close_pos_hor'];
		} else {
			$icon_align  = '';
			$close_align = 'close-' . $settings['premium_magic_section_close_pos'];
		}

		$magic_section_settings = array(
			'position'     => $section_pos,
			'gutter'       => $section_gutter,
			'trigger'      => $settings['premium_magic_section_trig_selector'],
			'style'        => $settings['premium_magic_section_style'],
			'inIcon'       => $in_icon,
			'outIcon'      => $out_icon,
			'responsive'   => ( 'yes' === $settings['premium_magic_section_responsive_switcher'] ) ? true : false,
			'hideTabs'     => ( 'yes' === $settings['premium_magic_section_hide_tabs'] ) ? true : false,
			'tabSize'      => ( 'yes' === $settings['premium_magic_section_hide_tabs'] ) ? Responsive::get_breakpoints()['lg'] : Responsive::get_breakpoints()['lg'],
			'hideMobs'     => ( 'yes' === $settings['premium_magic_section_hide_mobs'] ) ? true : false,
			'mobSize'      => ( 'yes' === $settings['premium_magic_section_hide_mobs'] ) ? Responsive::get_breakpoints()['md'] : Responsive::get_breakpoints()['md'],
			'clickOutside' => 'yes' === $settings['close_on_outside'] ? true : false,
		);

		$this->add_render_attribute( 'container', 'class', 'premium-magic-section-container' );

		?>

		<?php if ( 'yes' === $settings['premium_magic_section_overlay'] ) : ?>
			<div class="premium-magic-section-overlay"></div>
		<?php endif; ?>

		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'container' ) ); ?>>
			<div id="premium-magic-section-<?php echo esc_attr( $this->get_id() ); ?>" class="premium-magic-section-wrap magic-section-hide out <?php echo esc_attr( $section_pos ) . ' ' . esc_attr( $close_align ); ?>" data-settings='<?php echo wp_json_encode( $magic_section_settings ); ?>'>
				<?php if ( 'top' === $section_pos || 'left' === $section_pos ) : ?>
				<div class="premium-magic-section-content-wrap-out">
					<div class="premium-magic-section-content-wrap">
						<?php
						if ( 'editor' === $settings['premium_magic_section_content_type'] ) :
							echo $this->parse_text_editor( $settings['premium_magic_section_content'] );
							else :
								echo $this->getTemplateInstance()->get_template_content( $template ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							endif;
							?>
					</div>
				</div>

					<?php if ( $settings['premium_magic_section_close'] ) : ?>
					<div class="premium-magic-section-close-wrap">
						<button type="button" class="premium-magic-section-close"><i class="fa fa-times"></i></button>
					</div>
				<?php endif; ?>

					<?php if ( 'yes' === $settings['premium_magic_section_trig_float'] ) : ?>
					<div class="premium-magic-section-icon-wrap <?php echo esc_attr( $icon_align ); ?>">
						<?php if ( 'icon' === $settings['premium_magic_section_trig_selector'] && 'font-awesome-icon' === $settings['premium_magic_section_icon_selector'] ) : ?>
							<?php
							if ( $is_new_in || $migrated_in ) :
								Icons_Manager::render_icon(
									$settings['new_icon_font_in'],
									array(
										'class'       => array( 'premium-magic-section-btn', 'premium-magic-section-icon', 'elementor-animation-' . $settings['premium_magic_section_trig_anim'] ),
										'aria-hidden' => 'true',
									)
								);
								else :
									?>
							<i class ="premium-magic-section-btn premium-magic-section-icon <?php echo esc_attr( $settings['premium_magic_section_icon_font_in'] ); ?> <?php echo 'elementor-animation-' . esc_attr( $settings['premium_magic_section_trig_anim'] ); ?>"></i>
						<?php endif; ?>

						<?php elseif ( 'icon' === $settings['premium_magic_section_trig_selector'] && 'custom-image' === $settings['premium_magic_section_icon_selector'] ) : ?>

							<img class="premium-magic-section-btn premium-magic-section-icon-image <?php echo 'elementor-animation-' . esc_attr( $settings['premium_magic_section_trig_anim'] ); ?>" alt ="Custom Image" src="<?php echo esc_attr( $icon_font ); ?>">

						<?php elseif ( 'button' === $settings['premium_magic_section_trig_selector'] ) : ?>
							<button class="premium-magic-section-btn btn">
								<?php
								if ( 'yes' === $settings['premium_magic_section_icon_switcher'] && 'before' === $settings['premium_magic_section_icon_position'] ) :
									if ( 'icon' === $icon_type ) :
										if ( ( $is_new || $migrated ) && 'yes' !== $settings['draw_svg'] ) :
											Icons_Manager::render_icon(
												$settings['new_button_icon_selection'],
												array(
													'class' => array( 'premium-magic-btn-icon', 'premium-svg-nodraw' ),
													'aria-hidden' => 'true',
												)
											);
										else :
											?>
										<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>></i>
											<?php
										endif;
									else :
										?>
										<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>>
											<?php $this->print_unescaped_setting( 'custom_svg' ); ?>
										</div>
										<?php
									endif;
								endif;
								?>

								<?php if ( ! empty( $settings['premium_magic_section_button_text'] ) ) : ?>
									<span class="premium-magic-btn-text">
										<?php echo wp_kses_post( $settings['premium_magic_section_button_text'] ); ?>
									</span>
								<?php endif; ?>

								<?php
								if ( 'yes' === $settings['premium_magic_section_icon_switcher'] && 'after' === $settings['premium_magic_section_icon_position'] ) :
									if ( 'icon' === $icon_type ) :
										if ( ( $is_new || $migrated ) && 'yes' !== $settings['draw_svg'] ) :
											Icons_Manager::render_icon(
												$settings['new_button_icon_selection'],
												array(
													'class' => array( 'premium-magic-btn-icon', 'premium-svg-nodraw' ),
													'aria-hidden' => 'true',
												)
											);
										else :
											?>
											<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>></i>
											<?php
										endif;
									else :
										?>
										<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>>
											<?php $this->print_unescaped_setting( 'custom_svg' ); ?>
										</div>
										<?php
									endif;
								endif;
								?>
							</button>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php elseif ( 'bottom' === $section_pos || 'right' === $section_pos ) : ?>
					<?php if ( 'yes' === $settings['premium_magic_section_trig_float'] ) : ?>
						<div class="premium-magic-section-icon-wrap <?php echo esc_attr( $icon_align ); ?>">
							<?php if ( 'icon' === $settings['premium_magic_section_trig_selector'] && 'font-awesome-icon' === $settings['premium_magic_section_icon_selector'] ) : ?>
								<?php
								if ( $is_new_in || $migrated_in ) :
									Icons_Manager::render_icon(
										$settings['new_icon_font_in'],
										array(
											'class'       => array( 'premium-magic-section-btn', 'premium-magic-section-icon', 'elementor-animation-' . $settings['premium_magic_section_trig_anim'] ),
											'aria-hidden' => 'true',
										)
									);
								else :
									?>
								<i class ="premium-magic-section-btn premium-magic-section-icon <?php echo esc_attr( $settings['premium_magic_section_icon_font_in'] ); ?> <?php echo 'elementor-animation-' . esc_attr( $settings['premium_magic_section_trig_anim'] ); ?>"></i>
								<?php endif; ?>
							<?php elseif ( 'icon' === $settings['premium_magic_section_trig_selector'] && 'custom-image' === $settings['premium_magic_section_icon_selector'] ) : ?>
								<img class="premium-magic-section-btn premium-magic-section-icon-image <?php echo 'elementor-animation-' . esc_attr( $settings['premium_magic_section_trig_anim'] ); ?>" alt ="Custom Image" src="<?php echo esc_attr( $icon_font ); ?>" >
							<?php elseif ( 'button' === $settings['premium_magic_section_trig_selector'] ) : ?>
								<button class="premium-magic-section-btn btn">
									<?php
									if ( 'yes' === $settings['premium_magic_section_icon_switcher'] && 'before' === $settings['premium_magic_section_icon_position'] ) :
										if ( 'icon' === $icon_type ) :
											if ( ( $is_new || $migrated ) && 'yes' !== $settings['draw_svg'] ) :
												Icons_Manager::render_icon(
													$settings['new_button_icon_selection'],
													array(
														'class' => array( 'premium-magic-btn-icon', 'premium-svg-nodraw' ),
														'aria-hidden' => 'true',
													)
												);
											else :
												?>
												<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>></i>
												<?php
											endif;
										else :
											?>
											<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>>
												<?php $this->print_unescaped_setting( 'custom_svg' ); ?>
											</div>
											<?php
										endif;
									endif;
									?>

									<?php if ( ! empty( $settings['premium_magic_section_button_text'] ) ) : ?>
										<span class="premium-magic-btn-text">
											<?php echo wp_kses_post( $settings['premium_magic_section_button_text'] ); ?>
										</span>
									<?php endif; ?>

									<?php
									if ( 'yes' === $settings['premium_magic_section_icon_switcher'] && 'after' === $settings['premium_magic_section_icon_position'] ) :
										if ( 'icon' === $icon_type ) :
											if ( ( $is_new || $migrated ) && 'yes' !== $settings['draw_svg'] ) :
												Icons_Manager::render_icon(
													$settings['new_button_icon_selection'],
													array(
														'class' => array( 'premium-magic-btn-icon', 'premium-svg-nodraw' ),
														'aria-hidden' => 'true',
													)
												);
											else :
												?>
												<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>></i>
												<?php
											endif;
										else :
											?>
											<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>>
												<?php $this->print_unescaped_setting( 'custom_svg' ); ?>
											</div>
											<?php
										endif;
									endif;
									?>

								</button>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<div class="premium-magic-section-content-wrap-out">
						<div class="premium-magic-section-content-wrap">
							<?php
							if ( 'editor' === $settings['premium_magic_section_content_type'] ) :
								echo wp_kses_post( $settings['premium_magic_section_content'] );
							else :
								echo $this->getTemplateInstance()->get_template_content( $template ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							endif;
							?>
						</div>
					</div>
					<?php if ( $settings['premium_magic_section_close'] ) : ?>
						<div class="premium-magic-section-close-wrap">
							<button type="button" class="premium-magic-section-close"><i class="fa fa-times"></i></button>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>

			<?php if ( 'yes' !== $settings['premium_magic_section_trig_float'] ) : ?>
				<div class="premium-magic-section-button-trig">
					<?php if ( 'button' === $settings['premium_magic_section_trig_selector'] ) : ?>
						<button class="premium-magic-section-btn btn <?php echo esc_attr( $settings['premium_magic_section_button_size'] ); ?>" >
						<?php
						if ( 'yes' === $settings['premium_magic_section_icon_switcher'] && 'before' === $settings['premium_magic_section_icon_position'] ) :
							if ( 'icon' === $icon_type ) :
								if ( ( $is_new || $migrated ) && 'yes' !== $settings['draw_svg'] ) :
									Icons_Manager::render_icon(
										$settings['new_button_icon_selection'],
										array(
											'class'       => array( 'premium-magic-btn-icon', 'premium-svg-nodraw' ),
											'aria-hidden' => 'true',
										)
									);
								else :
									?>
										<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>></i>
										<?php
									endif;
								else :
									?>
									<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>>
										<?php $this->print_unescaped_setting( 'custom_svg' ); ?>
									</div>
									<?php
								endif;
							endif;
						?>

							<?php if ( ! empty( $settings['premium_magic_section_button_text'] ) ) : ?>
									<span class="premium-magic-btn-text">
									<?php echo wp_kses_post( $settings['premium_magic_section_button_text'] ); ?>
								</span>
							<?php endif; ?>

							<?php
							if ( 'yes' === $settings['premium_magic_section_icon_switcher'] && 'after' === $settings['premium_magic_section_icon_position'] ) :
								if ( 'icon' === $icon_type ) :
									if ( ( $is_new || $migrated ) && 'yes' !== $settings['draw_svg'] ) :
										Icons_Manager::render_icon(
											$settings['new_button_icon_selection'],
											array(
												'class' => array( 'premium-magic-btn-icon', 'premium-svg-nodraw' ),
												'aria-hidden' => 'true',
											)
										);
									else :
										?>
											<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>></i>
										<?php
									endif;
								else :
									?>
									<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>>
										<?php $this->print_unescaped_setting( 'custom_svg' ); ?>
									</div>
									<?php
									endif;
								endif;
							?>

						</button>
					<?php elseif ( 'icon' === $settings['premium_magic_section_trig_selector'] && 'font-awesome-icon' === $settings['premium_magic_section_icon_selector'] ) : ?>
						<?php
						if ( $is_new_in || $migrated_in ) :
							Icons_Manager::render_icon(
								$settings['new_icon_font_in'],
								array(
									'class'       => array( 'premium-magic-section-btn', 'elementor-animation-' . $settings['premium_magic_section_trig_anim'] ),
									'aria-hidden' => 'true',
								)
							);
						else :
							?>
							<i class ="premium-magic-section-btn premium-magic-section-icon <?php echo esc_attr( $settings['premium_magic_section_icon_font_in'] ); ?> <?php echo 'elementor-animation-' . esc_attr( $settings['premium_magic_section_trig_anim'] ); ?>"></i>
						<?php endif; ?>
						<?php
					elseif ( 'icon' === $settings['premium_magic_section_trig_selector'] && 'custom-image' === $settings['premium_magic_section_icon_selector'] ) :
						$image_custom   = $settings['premium_magic_section_custom_image'];
						$image_url_main = Group_Control_Image_Size::get_attachment_image_src( $image_custom['id'], 'prmium_magic_section_trig_image_size', $settings );
						$image_url_main = empty( $image_url_main ) ? $image_custom['url'] : $image_url_main;
						?>
						<img class="premium-magic-section-btn <?php echo 'elementor-animation-' . esc_attr( $settings['premium_magic_section_trig_anim'] ); ?>" src="<?php echo esc_attr( $image_url_main ); ?>">
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

}
