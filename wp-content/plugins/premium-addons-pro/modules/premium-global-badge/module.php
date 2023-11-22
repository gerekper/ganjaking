<?php
/**
 * Class: Module
 * Name: Global Badge
 * Slug: premium-global-badge
 *
 * @since 2.7.0
 */

namespace PremiumAddonsPro\Modules\PremiumGlobalBadge;

// Elementor Classes.
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

// Premium Addons Classes.
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddonsPro\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Module For Premium Global Badge Addon.
 */
class Module extends Module_Base {

	/**
	 * Load Script
	 *
	 * @var $load_script
	 */
	private $load_script = null;

	/**
	 * Class Constructor Funcion.
	 */
	public function __construct() {

		$modules = Admin_Helper::get_enabled_elements();

		$global_badge = $modules['premium-global-badge'];

		if ( ! $global_badge ) {
			return;
		}

		// Enqueue the required JS file.
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_styles' ) );

		// Creates Premium Global Badge tab at the end of layout/content tab.
		add_action( 'elementor/element/section/section_layout/after_section_end', array( $this, 'register_controls' ), 10 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', array( $this, 'register_controls' ), 10 );
		add_action( 'elementor/element/common/_section_style/after_section_end', array( $this, 'register_controls' ), 10 );

		// Editor Hooks.
		add_action( 'elementor/section/print_template', array( $this, 'print_template' ), 10, 2 );
		add_action( 'elementor/column/print_template', array( $this, 'print_template' ), 10, 2 );
		add_action( 'elementor/widget/print_template', array( $this, 'print_template' ), 10, 2 );

		// Frontend Hooks.
		add_action( 'elementor/frontend/section/before_render', array( $this, 'before_render' ) );
		add_action( 'elementor/frontend/column/before_render', array( $this, 'before_render' ) );
		add_action( 'elementor/widget/before_render_content', array( $this, 'before_render' ), 10, 1 );

		add_action( 'elementor/frontend/before_render', array( $this, 'check_script_enqueue' ) );

		if ( Helper_Functions::check_elementor_experiment( 'container' ) ) {
			add_action( 'elementor/element/container/section_layout/after_section_end', array( $this, 'register_controls' ), 10 );
			add_action( 'elementor/container/print_template', array( $this, 'print_template' ), 10, 2 );
			add_action( 'elementor/frontend/container/before_render', array( $this, 'before_render' ) );
		}

	}

	/**
	 * Enqueue scripts.
	 *
	 * Registers required dependencies for the extension and enqueues them.
	 *
	 * @since 1.6.5
	 * @access public
	 */
	public function enqueue_scripts() {

		if ( ! wp_script_is( 'elementor-waypoints', 'enqueued' ) ) {
			wp_enqueue_script( 'elementor-waypoints' );
		}

		if ( ! wp_script_is( 'lottie-js', 'enqueued' ) ) {
			wp_enqueue_script( 'lottie-js' );
		}

		if ( ! wp_script_is( 'pa-anime', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-anime' );
		}

		if ( ! wp_script_is( 'pa-badge', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-badge' );
		}
	}

	/**
	 * Enqueue styles.
	 *
	 * Registers required dependencies for the extension and enqueues them.
	 *
	 * @since 2.6.5
	 * @access public
	 */
	public function enqueue_styles() {

		if ( ! wp_style_is( 'pa-global', 'enqueued' ) ) {
			wp_enqueue_style( 'pa-global' );
		}
	}

	/**
	 * Register Global badge controls.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param object $element for current element.
	 */
	public function register_controls( $element ) {

		$tab = 'common' !== $element->get_name() ? Controls_Manager::TAB_LAYOUT : Controls_Manager::TAB_CONTENT;

		$element->start_controls_section(
			'section_premium_badge',
			array(
				'label' => sprintf( '<i class="pa-extension-icon pa-dash-icon"></i> %s', __( 'Global Badge', 'premium-addons-pro' ) ),
				'tab'   => $tab,
			)
		);

		$this->add_content_controls( $element );

		$element->add_control(
			'pa_badge_heading',
			array(
				'label'     => esc_html__( 'Style & Layout', 'premium-addons-pro' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'premium_global_badge_switcher' => 'yes',
				),
			)
		);

		$element->start_controls_tabs( 'pa_style_tabs' );

		// display section.
		$element->start_controls_tab(
			'pa_display_controls',
			array(
				'label'     => __( 'Layout', 'premium-addons-pro' ),
				'condition' => array(
					'premium_global_badge_switcher' => 'yes',
				),
			)
		);

		$this->add_display_controls( $element );

		$element->end_controls_tab();
		// style section.
		$element->start_controls_tab(
			'pa_style_controls',
			array(
				'label'     => __( 'Style', 'premium-addons-pro' ),
				'condition' => array(
					'premium_global_badge_switcher' => 'yes',
				),
			)
		);

		$this->add_style_controls( $element );

		$element->end_controls_tab();
		// icon style section.
		$element->start_controls_tab(
			'pa_icon_styles',
			array(
				'label'     => __( 'Icon', 'premium-addons-pro' ),
				'condition' => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_icon_enable'          => 'yes',
				),
			)
		);

		$this->add_icon_style( $element );

		$element->end_controls_tab();

		// svg layer style section.
		$element->start_controls_tab(
			'pa_svg_layer_style',
			array(
				'label'     => __( 'SVG Layer', 'premium-addons-pro' ),
				'condition' => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_svg_enabled'          => 'yes',
					'pa_badge_type'                 => 'custom',
				),
			)
		);

		$this->add_svg_layer_style( $element );

		$element->end_controls_tab();

		$element->end_controls_tabs();
		$element->end_controls_section();
	}

	/**
	 * Add content controls.
	 *
	 * @access public
	 * @since 2.7.0
	 *
	 * @param object $element elementor element.
	 */
	public function add_content_controls( $element ) {

		$element->add_control(
			'premium_global_badge_switcher',
			array(
				'label'        => __( 'Enable Global Badge', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-gbadge-',
				'render_type'  => 'template',
			)
		);

		$doc_link = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/elementor-badge-global-addon-tutorial/', 'editor-page', 'wp-editor', 'get-support' );

		$element->add_control(
			'pa_badge_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => '<a href="' . esc_url( $doc_link ) . '" target="_blank">' . __( 'How to use Premium Global Badge for Elementor »', 'premium-addons-pro' ) . '</a>',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_global_badge_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'pa_badge_text',
			array(
				'label'       => __( 'Text', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'New',
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'premium_global_badge_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'pa_badge_type',
			array(
				'label'        => __( 'Style', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'premium-gbadge-',
				'options'      => array(
					'stripe'   => __( 'Stripe', 'premium-addons-pro' ),
					'flag'     => __( 'Flag', 'premium-addons-pro' ),
					'tri'      => __( 'Triangle', 'premium-addons-pro' ),
					'circle'   => __( 'Circle', 'premium-addons-pro' ),
					'bookmark' => __( 'Bookmark', 'premium-addons-pro' ),
					'custom'   => __( 'Custom Layout', 'premium-addons-pro' ),
				),
				'default'      => 'stripe',
				'condition'    => array(
					'premium_global_badge_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'pa_badge_icon_enable',
			array(
				'label'       => __( 'Icon', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'render_type' => 'template',
				'condition'   => array(
					'premium_global_badge_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'pa_icon_type',
			array(
				'label'       => __( 'Icon Type', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'render_type' => 'template',
				'options'     => array(
					'icon'   => __( 'Icon', 'premium-addons-pro' ),
					'image'  => __( 'Image', 'premium-addons-pro' ),
					'lottie' => __( 'Lottie', 'premium-addons-pro' ),
				),
				'default'     => 'icon',
				'condition'   => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_icon_enable'          => 'yes',
				),
			)
		);

		$element->add_control(
			'pa_badge_icon',
			array(
				'label'     => __( 'Choose Icon', 'premium-addons-pro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-mouse-pointer',
					'library' => 'solid',
				),
				'condition' => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_icon_type'                  => 'icon',
					'pa_badge_icon_enable'          => 'yes',
				),
			)
		);

		$element->add_control(
			'pa_badge_img',
			array(
				'label'     => __( 'Choose Image', 'premium-addons-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_icon_type'                  => 'image',
					'pa_badge_icon_enable'          => 'yes',

				),
			)
		);

		$element->add_control(
			'pa_badge_lottie_url',
			array(
				'label'       => __( 'Animation JSON URL', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'condition'   => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_icon_type'                  => 'lottie',
					'pa_badge_icon_enable'          => 'yes',

				),
			)
		);

		$element->add_control(
			'pa_badge_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'condition'    => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_icon_type'                  => 'lottie',
					'pa_badge_icon_enable'          => 'yes',
				),
			)
		);

		$element->add_control(
			'pa_badge_reverse',
			array(
				'label'        => __( 'Reverse', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_icon_type'                  => 'lottie',
					'pa_badge_icon_enable'          => 'yes',
				),
			)
		);

		$element->add_control(
			'pa_badge_clip_enabled',
			array(
				'label'       => __( 'Enable Clip Path', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'render_type' => 'template',
				'condition'   => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_type'                 => 'custom',
				),
			)
		);

		$element->add_control(
			'pa_badge_path',
			array(
				'label'       => __( 'Path Value', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => 'Get Clip Path code from <a href="https://bennettfeely.com/clippy/" target="_blank">Clippy</a>.',
				'placeholder' => __( 'Paste your Path code here. EX: polygon(50% 0%, 0% 100%, 100% 100%)', 'premium-addons-pro' ),
				'label_block' => true,
				'condition'   => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_type'                 => 'custom',
					'pa_badge_clip_enabled'         => 'yes',
				),
				'selectors'   => array(
					'{{WRAPPER}}.premium-gbadge-custom > .premium-global-badge-{{ID}}' => 'filter:blur(.25px); clip-path: {{VALUE}}; -webkit-clip-path: {{VALUE}}; -ms-clip-path: {{VALUE}};',
				),
			)
		);

		$element->add_control(
			'pa_badge_svg_enabled',
			array(
				'label'       => __( 'Add SVG Layer', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'render_type' => 'template',
				'condition'   => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_type'                 => 'custom',
				),
			)
		);

		$element->add_control(
			'pa_badge_svg',
			array(
				'label'       => __( 'SVG Code', 'premium-addons-pro' ),
				'type'        => Controls_Manager::CODE,
				'description' => 'Get Blob SVG code from <a href="https://www.blobmaker.app/" target="_blank">Blobmaker</a>, <a href="https://blobs.app/" target="_blank">Blobs</a> or <a href="https://squircley.app/" target="_blank">Squircley</a>.',
				'label_block' => true,
				'condition'   => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_type'                 => 'custom',
					'pa_badge_svg_enabled'          => 'yes',
				),
			)
		);

		$this->add_floating_effects_controls( $element );
	}

	/**
	 * Add display controls.
	 *
	 * @access public
	 * @since 2.7.0
	 *
	 * @param object $element elementor element.
	 */
	public function add_display_controls( $element ) {

		/** Display & Position */
		$element->add_control(
			'pa_badge_display',
			array(
				'label'        => __( 'Display', 'premium-addons-pro' ),
				'type'         => Controls_Manager::CHOOSE,
				'prefix_class' => 'premium-gbadge-',
				'toggle'       => false,
				'options'      => array(
					'row'    => array(
						'title' => __( 'Inline', 'premium-addons-pro' ),
						'icon'  => 'eicon-ellipsis-h',
					),
					'column' => array(
						'title' => __( 'Block', 'premium-addons-pro' ),
						'icon'  => 'eicon-ellipsis-v',
					),
				),
				'default'      => 'row',
				'condition'    => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_icon_enable'          => 'yes',
				),
				'selectors'    => array(
					'{{WRAPPER}}.premium-gbadge-yes .premium-global-badge-{{ID}} .premium-badge-container' => 'flex-direction: {{VALUE}};',
				),
			)
		);

		$element->add_control(
			'pa_badge_hor',
			array(
				'label'        => __( 'Horizontal Position', 'premium-addons-pro' ),
				'type'         => Controls_Manager::CHOOSE,
				'prefix_class' => 'premium-gbadge-',
				'toggle'       => false,
				'options'      => array(
					'left'  => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'      => 'right',
				'condition'    => array(
					'premium_global_badge_switcher' => 'yes',
				),
				'selectors'    => array(
					'{{WRAPPER}}:not(.premium-gbadge-flag):not(.premium-gbadge-bookmark):not(.premium-gbadge-circle):not(.premium-gbadge-custom) .premium-global-badge-{{ID}}, {{WRAPPER}}.premium-gbadge-custom > .premium-gbadge-svg-{{ID}}' => '{{VALUE}}: 0;',
					'{{WRAPPER}}.premium-gbadge-circle .premium-global-badge-{{ID}}, {{WRAPPER}}.premium-gbadge-custom .premium-global-badge-{{ID}}' => '{{VALUE}}: 8px;',
					'{{WRAPPER}}.premium-gbadge-bookmark .premium-global-badge-{{ID}}' => '{{VALUE}}: 20px;',
				),
			)
		);

		$element->add_control(
			'pa_badge_ver',
			array(
				'label'        => __( 'Vertical Position', 'premium-addons-pro' ),
				'type'         => Controls_Manager::CHOOSE,
				'toggle'       => false,
				'prefix_class' => 'premium-gbadge-',
				'options'      => array(
					'top'    => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'eicon-v-align-top',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'default'      => 'top',
				'condition'    => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_type'                 => array( 'custom', 'circle' ),
				),
				'selectors'    => array(
					'{{WRAPPER}}:not(.premium-gbadge-flag):not(.premium-gbadge-circle):not(.premium-gbadge-custom) .premium-global-badge-{{ID}}, {{WRAPPER}}.premium-gbadge-custom > .premium-gbadge-svg-{{ID}}' => '{{VALUE}}: 0;',
					'{{WRAPPER}}.premium-gbadge-circle .premium-global-badge-{{ID}}, {{WRAPPER}}.premium-gbadge-custom .premium-global-badge-{{ID}}' => '{{VALUE}}: 8px;',
				),
			)
		);

		$element->add_responsive_control(
			'pa_badge_hor_offset',
			array(
				'label'      => __( 'Horizontal Offset', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'condition'  => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_type!'                => array( 'flag', 'stripe' ),
				),
				'selectors'  => array(
					'{{WRAPPER}}.premium-gbadge-flag .premium-global-badge-{{ID}}' => 'top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.premium-gbadge-circle .premium-global-badge-{{ID}}' => '{{pa_badge_hor.VALUE}}: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.premium-gbadge-custom .premium-global-badge-{{ID}}' => '{{pa_badge_hor.VALUE}}: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.premium-gbadge-bookmark .premium-global-badge-{{ID}}' => '{{pa_badge_hor.VALUE}}: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.premium-gbadge-tri .premium-global-badge-{{ID}} .premium-badge-container' => 'left: {{SIZE}}px;',
				),
			)
		);

		$element->add_responsive_control(
			'pa_badge_ver_offset',
			array(
				'label'      => __( 'Vertical Offset', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'condition'  => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_type!'                => array( 'bookmark', 'stripe' ),
				),
				'selectors'  => array(
					'{{WRAPPER}}.premium-gbadge-flag .premium-global-badge-{{ID}}' => 'top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.premium-gbadge-circle .premium-global-badge-{{ID}}' => '{{pa_badge_ver.VALUE}}: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.premium-gbadge-custom .premium-global-badge-{{ID}}' => '{{pa_badge_ver.VALUE}}: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.premium-gbadge-tri.premium-gbadge-left .premium-global-badge-{{ID}} .premium-badge-container' => 'bottom: {{SIZE}}px;',
					'{{WRAPPER}}.premium-gbadge-tri.premium-gbadge-right .premium-global-badge-{{ID}} .premium-badge-container' => 'top: {{SIZE}}px;',
				),
			)
		);

		$indent_class = is_rtl() ? '.premium-badge-text' : '.premium-badge-icon';

		$element->add_responsive_control(
			'pa_badge_spacing',
			array(
				'label'      => __( 'Spacing', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'condition'  => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_icon_enable'          => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}}.premium-gbadge-row .premium-global-badge-{{ID}} ' . $indent_class => 'text-indent: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.premium-gbadge-column .premium-global-badge-{{ID}} .premium-badge-icon' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_responsive_control(
			'pa_badge_rotate',
			array(
				'label'      => __( 'Rotate (Degrees)', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'default'    => array(
					'unit' => 'deg',
					'size' => 0,
				),
				'range'      => array(
					'deg' => array(
						'min' => -180,
						'max' => 180,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-global-badge-{{ID}}' => 'transform: rotate({{SIZE}}deg)',
				),
				'condition'  => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_type'                 => 'custom',
					'pa_badge_ftranslate!'          => 'yes',
					'pa_badge_frotate!'             => 'yes',
				),
			)
		);
	}

	/**
	 * Add style controls.
	 *
	 * @access public
	 * @since 2.7.0
	 *
	 * @param object $element elementor element.
	 */
	public function add_style_controls( $element ) {

		/** Style */
		$element->add_responsive_control(
			'pa_badge_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'condition'  => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_type!'                => array( 'bookmark', 'stripe', 'flag', 'circle' ),
				),
				'selectors'  => array(
					'{{WRAPPER}}.premium-gbadge-tri.premium-gbadge-left > .premium-global-badge-{{ID}}' => 'border-top-width: {{SIZE}}{{UNIT}}; border-bottom-width: {{SIZE}}{{UNIT}}; border-right-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.premium-gbadge-tri.premium-gbadge-right > .premium-global-badge-{{ID}}' => 'border-left-width: {{SIZE}}{{UNIT}}; border-bottom-width: {{SIZE}}{{UNIT}}; border-right-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.premium-gbadge-custom > .premium-global-badge-{{ID}}' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_responsive_control(
			'pa_badge_margin',
			array(
				'label'      => __( 'Text Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'condition'  => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_type'                 => 'custom',
				),
				'selectors'  => array(
					'{{WRAPPER}}.premium-gbadge-custom > .premium-global-badge-{{ID}} .premium-badge-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$element->add_control(
			'pa_badge_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'condition' => array(
					'premium_global_badge_switcher' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-global-badge-{{ID}} .premium-badge-text' => 'color: {{VALUE}};',
				),
			)
		);

		$element->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'pa_badge_bg',
				'types'          => array( 'classic', 'gradient' ),
				'fields_options' => array(
					'background' => array(
						'default' => 'classic',
					),
					'color'      => array(
						'global' => array(
							'default' => Global_Colors::COLOR_PRIMARY,
						),
					),
				),
				'condition'      => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_type!'                => array( 'bookmark', 'tri', 'flag' ),
				),
				'selector'       => '{{WRAPPER}} .premium-global-badge-{{ID}} .premium-badge-container',
			)
		);

		$element->add_control(
			'pa_badge_bgcolor',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6EC1E4',
				'condition' => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_type'                 => array( 'bookmark', 'tri', 'flag' ),
				),
				'selectors' => array(
					'{{WRAPPER}}.premium-gbadge-flag .premium-global-badge-{{ID}} .premium-badge-container, {{WRAPPER}}.premium-gbadge-bookmark .premium-global-badge-{{ID}}' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}.premium-gbadge-tri.premium-gbadge-left .premium-global-badge-{{ID}}' => 'border-top-color:{{VALUE}};',
					'{{WRAPPER}}.premium-gbadge-flag.premium-gbadge-right .premium-global-badge-{{ID}}:after' => 'border-left-color: {{VALUE}};',
					'{{WRAPPER}}.premium-gbadge-flag.premium-gbadge-left .premium-global-badge-{{ID}}:after, {{WRAPPER}}.premium-gbadge-tri.premium-gbadge-right .premium-global-badge-{{ID}}' => 'border-right-color:{{VALUE}};',
					'{{WRAPPER}}.premium-gbadge-bookmark > .premium-global-badge-{{ID}}:after' => 'border-right-color:{{VALUE}}; border-left-color:{{VALUE}};',
				),
			)
		);

		$element->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'pa_badge_text_shadow',
				'condition' => array(
					'premium_global_badge_switcher' => 'yes',
				),
				'selector'  => '{{WRAPPER}} .premium-global-badge-{{ID}}',
			)
		);

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'pa_badge_shadow',
				'condition' => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_type!'                => 'tri',
				),
				'selector'  => '{{WRAPPER}}:not(.premium-gbadge-bookmark) .premium-global-badge-{{ID}} .premium-badge-container, {{WRAPPER}}.premium-gbadge-bookmark .premium-global-badge-{{ID}}',
			)
		);

		$element->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'pa_badge_typo',
				'condition' => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_type!'                => 'bookmark',
				),
				'selector'  => '{{WRAPPER}}:not(.premium-gbadge-stripe) .premium-global-badge-{{ID}},
								{{WRAPPER}}.premium-gbadge-stripe > .premium-global-badge-{{ID}} .premium-badge-container,
								{{WRAPPER}}.premium-gbadge-custom > .premium-global-badge-{{ID}} .premium-badge-container ',
			)
		);

		$element->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'pa_bookmark_typo',
				'condition'      => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_type'                 => 'bookmark',
				),
				'fields_options' => array(
					'font_size'   => array(
						'selectors' => array(
							'{{WRAPPER}}.premium-gbadge-bookmark > .premium-global-badge-{{ID}}' => 'width: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}}.premium-gbadge-bookmark > .premium-global-badge-{{ID}} .premium-badge-text' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}}.premium-gbadge-bookmark > .premium-global-badge-{{ID}}:after' => 'border-left-width: calc( {{SIZE}}{{UNIT}} / 2); border-right-width: calc( {{SIZE}}{{UNIT}} / 2);',
						),
					),
					'line_height' => array(
						'default'   => array(
							'size' => '32',
							'unit' => 'px',
						),
						'selectors' => array(
							'{{WRAPPER}}.premium-gbadge-bookmark > .premium-global-badge-{{ID}}' => 'width: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}}.premium-gbadge-bookmark > .premium-global-badge-{{ID}}:after' => 'border-left-width: calc( {{SIZE}}{{UNIT}} / 2); border-right-width: calc( {{SIZE}}{{UNIT}} / 2);',
						),
					),
				),
				'selector'       => '{{WRAPPER}}.premium-gbadge-bookmark > .premium-global-badge-{{ID}}',
			)
		);

		$element->add_control(
			'bookmark_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Use <b>Line Height</b> to control the bookmark size.', 'premium-addons-pro' ),
				'content_classes' => 'papro-upgrade-notice',
				'condition'       => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_type'                 => 'bookmark',
				),
			)
		);

		$element->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'pa_badge_border',
				'selector'  => '{{WRAPPER}} .premium-global-badge-{{ID}} .premium-badge-container',
				'condition' => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_type!'                => array( 'bookmark', 'tri', 'stripe', 'flag' ),
					'pa_badge_clip_enabled!'        => 'yes',
				),
			)
		);

		$element->add_control(
			'pa_badge_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'condition'  => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_adv_radius!'          => 'yes',
					'pa_badge_clip_enabled!'        => 'yes',
					'pa_badge_type!'                => array( 'bookmark', 'tri', 'stripe' ),
				),
				'selectors'  => array(
					'{{WRAPPER}}:not(.premium-gbadge-flag) .premium-global-badge-{{ID}} .premium-badge-container' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.premium-gbadge-flag.premium-gbadge-left .premium-global-badge-{{ID}} .premium-badge-container' => 'border-radius: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0;',
					'{{WRAPPER}}.premium-gbadge-flag.premium-gbadge-right .premium-global-badge-{{ID}} .premium-badge-container' => 'border-radius: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0 {{SIZE}}{{UNIT}} ;',
				),
			)
		);

		$element->add_control(
			'pa_badge_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-pro' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
				'condition'   => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_clip_enabled!'        => 'yes',
					'pa_badge_type!'                => array( 'bookmark', 'tri', 'stripe', 'flag' ),
				),
			)
		);

		$element->add_control(
			'pa_badge_adv_radius_val',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'selectors' => array(
					'{{WRAPPER}} .premium-global-badge-{{ID}} .premium-badge-container' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'pa_badge_adv_radius'    => 'yes',
					'pa_badge_clip_enabled!' => 'yes',
					'pa_badge_type!'         => array( 'bookmark', 'tri', 'stripe', 'flag' ),
				),
			)
		);

		$element->add_control(
			'pa_badge_zindex',
			array(
				'label'       => __( 'Z-Index', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'step'        => 1,
				'description' => __( 'Default is 5', 'premium-addons-pro' ),
				'selectors'   => array(
					'{{WRAPPER}} .premium-global-badge-{{ID}}' => 'z-index: {{VALUE}}',
				),
				'condition'   => array(
					'premium_global_badge_switcher' => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'pa_badge_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'condition'  => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_type!'                => array( 'bookmark', 'tri' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-global-badge-{{ID}} .premium-badge-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$element->add_responsive_control(
			'pa_badge_padding_bookmark',
			array(
				'label'              => __( 'Padding', 'premium-addons-pro' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'allowed_dimensions' => 'vertical',
				'size_units'         => array( 'px', 'em' ),
				'condition'          => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_type'                 => 'bookmark',
				),
				'selectors'          => array(
					'{{WRAPPER}}.premium-gbadge-bookmark .premium-global-badge-{{ID}}' => 'padding: {{TOP}}{{UNIT}} 0 {{BOTTOM}}{{UNIT}} 0;',
				),
			)
		);
	}

	/**
	 * Add icon style controls.
	 *
	 * @access public
	 * @since 2.7.0
	 *
	 * @param object $element elementor element.
	 */
	public function add_icon_style( $element ) {

		$element->add_control(
			'pa_badge_icon_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'condition' => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_icon_enable'          => 'yes',
					'pa_icon_type'                  => 'icon',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-global-badge-{{ID}} .premium-badge-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
			)
		);

		$element->add_responsive_control(
			'pa_badge_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'condition'  => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_icon_enable'          => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}}:not(.premium-gbadge-bookmark) > .premium-global-badge-{{ID}} .premium-badge-icon' => 'font-size: {{SIZE}}{{UNIT}}; line-height:{{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.premium-gbadge-bookmark > .premium-global-badge-{{ID}} .premium-badge-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} > .premium-global-badge-{{ID}} .premium-badge-icon, {{WRAPPER}} > .premium-global-badge-{{ID}} .premium-lottie-animation, {{WRAPPER}}:not(.premium-gbadge-bookmark) > .premium-global-badge-{{ID}} .premium-badge-img ' => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_control(
			'pa_badge_icon_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'condition'  => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_icon_enable'          => 'yes',
					'pa_icon_type!'                 => 'icon',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-global-badge-{{ID}} .premium-badge-img, {{WRAPPER}} .premium-global-badge-{{ID}} .premium-lottie-animation svg' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);
	}

	/**
	 * Add svg layer style controls.
	 *
	 * @access public
	 * @since 2.7.0
	 *
	 * @param object $element elementor element.
	 */
	public function add_svg_layer_style( $element ) {
		$element->add_responsive_control(
			'pa_badge_svg_size',
			array(
				'label'      => __( 'Layer Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'condition'  => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_svg_enabled'          => 'yes',
					'pa_badge_type'                 => 'custom',
				),
				'selectors'  => array(
					'{{WRAPPER}}.premium-gbadge-custom > .premium-gbadge-svg-{{ID}}' => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_responsive_control(
			'pa_badge_svg_hor',
			array(
				'label'      => __( 'SVG Horizontal Offset', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -200,
						'max' => 200,
					),
				),
				'condition'  => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_svg_enabled'          => 'yes',
					'pa_badge_type'                 => 'custom',
				),
				'selectors'  => array(
					'{{WRAPPER}}.premium-gbadge-custom > .premium-gbadge-svg-{{ID}}' => '{{pa_badge_hor.VALUE}}: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_responsive_control(
			'pa_badge_svg_ver',
			array(
				'label'      => __( 'SVG Vertical Offset', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -200,
						'max' => 200,
					),
				),
				'condition'  => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_svg_enabled'          => 'yes',
					'pa_badge_type'                 => 'custom',
				),
				'selectors'  => array(
					'{{WRAPPER}}.premium-gbadge-custom > .premium-gbadge-svg-{{ID}}' => '{{pa_badge_ver.VALUE}}: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_control(
			'pa_badge_svg_zindex',
			array(
				'label'       => __( 'Z-Index', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'step'        => 1,
				'description' => __( 'Default is 2', 'premium-addons-pro' ),
				'selectors'   => array(
					'{{WRAPPER}}.premium-gbadge-custom > .premium-gbadge-svg-{{ID}}' => 'z-index: {{VALUE}}',
				),
				'condition'   => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_svg_enabled'          => 'yes',
					'pa_badge_type'                 => 'custom',
				),
			)
		);
	}

	/**
	 * Add floating effects controls.
	 *
	 * @access public
	 * @since 2.7.0
	 *
	 * @param object $element elementor element.
	 */
	public function add_floating_effects_controls( $element ) {

		$element->add_control(
			'pa_badge_effects',
			array(
				'label'     => __( 'Floating Effects', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_global_badge_switcher' => 'yes',
				),
			)
		);

		$float_conditions = array(
			'pa_badge_effects'              => 'yes',
			'premium_global_badge_switcher' => 'yes',
		);

		$element->add_control(
			'pa_badge_ftranslate',
			array(
				'label'     => __( 'Translate', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array_merge(
					$float_conditions,
					array(
						'pa_badge_type!' => 'stripe',
					)
				),
			)
		);

		$element->add_control(
			'pa_badge_ftranslatex',
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
						'pa_badge_ftranslate' => 'yes',
						'pa_badge_type!'      => 'stripe',
					)
				),
			)
		);

		$element->add_control(
			'pa_badge_ftranslatey',
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
						'pa_badge_ftranslate' => 'yes',
						'pa_badge_type!'      => 'stripe',
					)
				),
			)
		);

		$element->add_control(
			'pa_badge_ftranslate_speed',
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
						'pa_badge_ftranslate' => 'yes',
						'pa_badge_type!'      => 'stripe',
					)
				),
			)
		);

		$element->add_control(
			'pa_badge_frotate',
			array(
				'label'     => __( 'Rotate', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array_merge(
					$float_conditions,
					array(
						'pa_badge_type!' => 'stripe',
					)
				),
			)
		);

		$element->add_control(
			'pa_badge_frotatex',
			array(
				'label'     => __( 'Rotate X', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'start' => 0,
						'end'   => 45,
					),
					'unit'  => 'deg',
				),
				'range'     => array(
					'deg' => array(
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
						'pa_badge_frotate' => 'yes',
						'pa_badge_type!'   => 'stripe',
					)
				),
			)
		);

		$element->add_control(
			'pa_badge_frotatey',
			array(
				'label'     => __( 'Rotate Y', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'start' => 0,
						'end'   => 45,
					),
					'unit'  => 'deg',
				),
				'range'     => array(
					'deg' => array(
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
						'pa_badge_frotate' => 'yes',
						'pa_badge_type!'   => 'stripe',
					)
				),
			)
		);

		$element->add_control(
			'pa_badge_frotatez',
			array(
				'label'     => __( 'Rotate Z', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'start' => 0,
						'end'   => 45,
					),
					'unit'  => 'deg',
				),
				'range'     => array(
					'deg' => array(
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
						'pa_badge_frotate' => 'yes',
						'pa_badge_type!'   => 'stripe',
					)
				),
			)
		);

		$element->add_control(
			'pa_badge_frotate_speed',
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
						'pa_badge_frotate' => 'yes',
						'pa_badge_type!'   => 'stripe',
					)
				),
			)
		);

		$element->add_control(
			'pa_badge_fopacity',
			array(
				'label'     => __( 'Opacity', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => $float_conditions,
			)
		);

		$element->add_control(
			'pa_badge_fopacity_value',
			array(
				'label'     => __( 'Value', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'start' => 0,
						'end'   => 50,
					),
					'unit'  => '%',
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
						'pa_badge_fopacity' => 'yes',
					)
				),
			)
		);

		$element->add_control(
			'pa_badge_fopacity_speed',
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
						'pa_badge_fopacity' => 'yes',
					)
				),
			)
		);

		$element->add_control(
			'pa_badge_fblur',
			array(
				'label'     => __( 'Blur', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => $float_conditions,
			)
		);

		$element->add_control(
			'pa_badge_fblur_value',
			array(
				'label'     => __( 'Value', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'start' => 0,
						'end'   => 1,
					),
					'unit'  => 'px',
				),
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 3,
						'step' => 0.1,
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
						'pa_badge_fblur' => 'yes',
					)
				),
			)
		);

		$element->add_control(
			'pa_badge_fblur_speed',
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
						'pa_badge_fblur' => 'yes',
					)
				),
			)
		);

		$element->add_control(
			'pa_badge_fgrayscale',
			array(
				'label'     => __( 'Grayscale', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => $float_conditions,
			)
		);

		$element->add_control(
			'pa_badge_fgscale_value',
			array(
				'label'     => __( 'Value', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'start' => 0,
						'end'   => 50,
					),
					'unit'  => '%',
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
						'pa_badge_fgrayscale' => 'yes',
					)
				),
			)
		);

		$element->add_control(
			'pa_badge_fgscale_speed',
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
						'pa_badge_fgrayscale' => 'yes',
					)
				),
			)
		);

		$element->add_control(
			'pa_badge_disable_on_safari',
			array(
				'label'        => __( 'Disable Floating Effects On Safari', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'pa-badge-disable-fe-',
				'separator'    => 'before',
				'condition'    => array(
					'premium_global_badge_switcher' => 'yes',
					'pa_badge_effects'              => 'yes',
				),
			)
		);
	}

	/**
	 * Render Global badge output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.2.8
	 * @access public
	 *
	 * @param object $template for current template.
	 * @param object $element for current element.
	 */
	public function print_template( $template, $element ) {

		if ( ! $template && 'widget' === $element->get_type() ) {
			return;
		}

		$old_template = $template;
		ob_start();
		?>
		<#
			var isEnabled = 'yes' === settings.premium_global_badge_switcher ? true : false;

			if ( isEnabled ) {

				var text = settings.pa_badge_text,
					iconEnabled = 'yes' === settings.pa_badge_icon_enable ? true : false,
					svgEnabled = 'yes' === settings.pa_badge_svg_enabled ? true : false,
					floatingEnabled    = 'yes' === settings.pa_badge_effects ? true : false,
					badgeSettings = {
						text : text,
					};

                if ( svgEnabled ) {
					badgeSettings.svgLayer = settings.pa_badge_svg;
				}

				if ( iconEnabled ) {
					var type = settings.pa_icon_type,
						icon = {};

					badgeSettings.iconType = type;

					switch( type ) {
						case 'icon':
							icon = settings.pa_badge_icon;
							break;

						case 'image':
							icon.url = settings.pa_badge_img.url;
							break;

						case 'lottie':
							icon.url     = settings.pa_badge_lottie_url;
							icon.loop    = settings.pa_badge_loop;
							icon.reverse = settings.pa_badge_reverse;
							break;

						default:
						icon = false;
					}
				} else {
					icon = false;
				}

				badgeSettings.icon = icon;

				if ( floatingEnabled ) {
					var floatingSettings = {},
						filtersEnabled = 'yes' === settings.pa_badge_fblur || 'yes' === settings.pa_badge_fgrayscale ? true : false;

					if ( 'yes' === settings.pa_badge_ftranslate ) {

						var translateSettings = {
							x_param_from: settings.pa_badge_ftranslatex.sizes.start,
							x_param_to: settings.pa_badge_ftranslatex.sizes.end,
							y_param_from: settings.pa_badge_ftranslatey.sizes.start,
							y_param_to: settings.pa_badge_ftranslatey.sizes.end,
							speed: settings.pa_badge_ftranslate_speed.size * 1000,
						};

						floatingSettings.translate = translateSettings;
					}

					if ( 'yes' === settings.pa_badge_frotate ) {

						var rotateSettings = {
							x_param_from: settings.pa_badge_frotatex.sizes.start,
							x_param_to: settings.pa_badge_frotatex.sizes.end,
							y_param_from: settings.pa_badge_frotatey.sizes.start,
							y_param_to: settings.pa_badge_frotatey.sizes.end,
							z_param_from: settings.pa_badge_frotatez.sizes.start,
							z_param_to: settings.pa_badge_frotatez.sizes.end,
							speed: settings.pa_badge_frotate_speed.size * 1000,
						};

						floatingSettings.rotate = rotateSettings;
					}

					if ( 'yes' === settings.pa_badge_fopacity ) {

						var opacitySettings = {
							from: settings.pa_badge_fopacity_value.sizes.start / 100,
							to: settings.pa_badge_fopacity_value.sizes.end / 100,
							speed: settings.pa_badge_fopacity_speed.size * 1000,
						};

						floatingSettings.opacity = opacitySettings;
					}

					if ( filtersEnabled ) {
						var filtersSettings = {};

						if ( 'yes' === settings.pa_badge_fblur ) {

							var blurSettings = {
								from: 'blur(' + settings.pa_badge_fblur_value.sizes.start + 'px)',
								to: 'blur(' + settings.pa_badge_fblur_value.sizes.end + 'px)',
								speed: settings.pa_badge_fblur_speed.size * 1000,
							};

							filtersSettings.blur = blurSettings;
						}

						if ( 'yes' === settings.pa_badge_fgrayscale ) {
							var gscaleSettings = {
								from: 'grayscale(' + settings.pa_badge_fgscale_value.sizes.start + '%)',
								to: 'grayscale(' + settings.pa_badge_fgscale_value.sizes.end + '%)',
								speed: settings.pa_badge_fgscale_speed.size,
							};

							filtersSettings.gscale = gscaleSettings;
						}

						floatingSettings.filters = filtersSettings;
					}

					badgeSettings.floating = floatingSettings;
				}

				view.addRenderAttribute( 'badge_data', {
					'id': 'premium-global-badge-' + view.getID(),
					'class': 'premium-global-badge-wrapper',
					'data-gbadge': JSON.stringify( badgeSettings )
				});
		#>
				<div {{{ view.getRenderAttributeString( 'badge_data' ) }}}></div>
		<#
			}
		#>

		<?php

			$slider_content = ob_get_contents();
			ob_end_clean();
			$template = $slider_content . $old_template;
			return $template;
	}

	/**
	 * Render Global badge output on the frontend.
	 *
	 * Written in PHP and used to collect badge settings and add it as an element attribute.
	 *
	 * @access public
	 * @param object $element for current element.
	 */
	public function before_render( $element ) {

		$element_type = $element->get_type();

		$id = $element->get_id();

		$settings = $element->get_settings_for_display();

		$badge_switcher = $settings['premium_global_badge_switcher'];

		if ( 'yes' === $badge_switcher ) {

			$text             = strip_tags( $settings['pa_badge_text'] );
			$icon_enabled     = 'yes' === $settings['pa_badge_icon_enable'] ? true : false;
			$svg_enabled      = 'yes' === $settings['pa_badge_svg_enabled'] ? true : false;
			$floating_enabled = 'yes' === $settings['pa_badge_effects'] ? true : false;
			$badge_settings   = array(
				'text' => $text,
			);

			if ( $svg_enabled ) {
				$badge_settings['svgLayer'] = $settings['pa_badge_svg'];
			}

			if ( $icon_enabled ) {
				$type                       = $settings['pa_icon_type'];
				$badge_settings['iconType'] = $type;

				switch ( $type ) {
					case 'icon':
						$icon = $settings['pa_badge_icon'];
						break;

					case 'image':
						$icon['url'] = $settings['pa_badge_img']['url'];
						$icon['alt'] = Control_Media::get_image_alt( $settings['pa_badge_img'] );
						break;

					case 'lottie':
						$icon['url']     = esc_url( $settings['pa_badge_lottie_url'] );
						$icon['loop']    = $settings['pa_badge_loop'];
						$icon['reverse'] = $settings['pa_badge_reverse'];
						break;

					default:
						$icon = false;
						break;
				}
			} else {
				$icon = false;
			}

			$badge_settings['icon'] = $icon;

			if ( $floating_enabled ) {
				$floating_settings = array();
				$filters_enabled   = 'yes' === $settings['pa_badge_fblur'] || 'yes' === $settings['pa_badge_fgrayscale'] ? true : false;

				if ( 'yes' === $settings['pa_badge_ftranslate'] ) {

					$translate_settings = array(
						'x_param_from' => $settings['pa_badge_ftranslatex']['sizes']['start'],
						'x_param_to'   => $settings['pa_badge_ftranslatex']['sizes']['end'],
						'y_param_from' => $settings['pa_badge_ftranslatey']['sizes']['start'],
						'y_param_to'   => $settings['pa_badge_ftranslatey']['sizes']['end'],
						'speed'        => $settings['pa_badge_ftranslate_speed']['size'] * 1000,
					);

					$floating_settings['translate'] = $translate_settings;
				}

				if ( 'yes' === $settings['pa_badge_frotate'] ) {

					$rotate_settings = array(
						'x_param_from' => $settings['pa_badge_frotatex']['sizes']['start'],
						'x_param_to'   => $settings['pa_badge_frotatex']['sizes']['end'],
						'y_param_from' => $settings['pa_badge_frotatey']['sizes']['start'],
						'y_param_to'   => $settings['pa_badge_frotatey']['sizes']['end'],
						'z_param_from' => $settings['pa_badge_frotatez']['sizes']['start'],
						'z_param_to'   => $settings['pa_badge_frotatez']['sizes']['end'],
						'speed'        => $settings['pa_badge_frotate_speed']['size'] * 1000,
					);

					$floating_settings['rotate'] = $rotate_settings;
				}

				if ( 'yes' === $settings['pa_badge_fopacity'] ) {

					$opacity_settings = array(
						'from'  => $settings['pa_badge_fopacity_value']['sizes']['start'] / 100,
						'to'    => $settings['pa_badge_fopacity_value']['sizes']['end'] / 100,
						'speed' => $settings['pa_badge_fopacity_speed']['size'] * 1000,
					);

					$floating_settings['opacity'] = $opacity_settings;
				}

				if ( $filters_enabled ) {
					$filters_settings = array();
					if ( 'yes' === $settings['pa_badge_fblur'] ) {

						$blur_settings = array(
							'from'  => 'blur(' . $settings['pa_badge_fblur_value']['sizes']['start'] . 'px)',
							'to'    => 'blur(' . $settings['pa_badge_fblur_value']['sizes']['end'] . 'px)',
							'speed' => $settings['pa_badge_fblur_speed']['size'] * 1000,
						);

						$filters_settings['blur'] = $blur_settings;
					}

					if ( 'yes' === $settings['pa_badge_fgrayscale'] ) {
						$gscale_settings = array(
							'from'  => 'grayscale(' . $settings['pa_badge_fgscale_value']['sizes']['start'] . '%)',
							'to'    => 'grayscale(' . $settings['pa_badge_fgscale_value']['sizes']['end'] . '%)',
							'speed' => $settings['pa_badge_fgscale_speed']['size'],
						);

						$filters_settings['gscale'] = $gscale_settings;
					}

					$floating_settings['filters'] = $filters_settings;
				}

				$badge_settings['floating'] = $floating_settings;
			}

			$element->add_render_attribute( '_wrapper', 'data-gbadge', wp_json_encode( $badge_settings ) );

			if ( 'widget' === $element_type && \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
				?>
				<div id='premium-global-badge-temp-<?php echo esc_html( $id ); ?>' data-gbadge='<?php echo wp_json_encode( $badge_settings ); ?>'></div>
				<?php
			}
		}
	}

	/**
	 * Check Script Enqueue
	 *
	 * Check if the script files should be loaded.
	 *
	 * @since 2.6.3
	 * @access public
	 *
	 * @param object $element for current element.
	 */
	public function check_script_enqueue( $element ) {

		if ( $this->load_script ) {
			return;
		}

		if ( 'yes' === $element->get_settings_for_display( 'premium_global_badge_switcher' ) ) {

			$this->enqueue_styles();
			$this->enqueue_scripts();

			$this->load_script = true;

			remove_action( 'elementor/frontend/before_render', array( $this, 'check_script_enqueue' ) );
		}

	}

}
