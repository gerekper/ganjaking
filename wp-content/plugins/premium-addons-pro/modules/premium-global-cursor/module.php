<?php
/**
 * Class: Module
 * Name: Global Cursor
 * Slug: premium-global-cursor
 */

namespace PremiumAddonsPro\Modules\PremiumGlobalCursor;

// Elementor Classes.
use Elementor\Utils;
use Elementor\Plugin;
use Elementor\Control_Media;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Core\Settings\Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

// Premium Addons Classes.
use PremiumAddonsPro\Base\Module_Base;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Admin\Includes\Admin_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Module For Premium Global Cursor Addon.
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

		parent::__construct();

		$modules = Admin_Helper::get_enabled_elements();

		$global_cursor = $modules['premium-global-cursor'];

		if ( ! $global_cursor ) {
			return;
		}

		// Enqueue the required CSS/JS file.
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_styles' ) );

		// Creates Premium Global Cursor tab at the end of Advanced tab.
		add_action( 'elementor/element/section/section_advanced/after_section_end', array( $this, 'register_controls' ), 10 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', array( $this, 'register_controls' ), 10 );
		add_action( 'elementor/element/common/_section_style/after_section_end', array( $this, 'register_controls' ), 10 );

		// Creates Premium Global Cursor tab at the page settings menu.
		add_action( 'elementor/documents/register_controls', array( $this, 'register_controls' ) );

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

		// handles site cursor option.
		add_action( 'elementor/editor/after_save', array( $this, 'save_global_cursor_settings' ), 10, 2 );
		add_action( 'wp_trash_post', array( $this, 'reset_global_cursor_settings' ) );
		add_action( 'wp_footer', array( $this, 'add_site_cursor_settings' ) );
		add_action( 'transition_post_status', array( $this, 'post_unpublished' ), 10, 3 );
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

		if ( ! wp_script_is( 'pa-tweenmax', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-tweenmax' );
		}

		if ( ! wp_script_is( 'pa-scrolltrigger', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-scrolltrigger' );
		}

		if ( ! wp_script_is( 'pa-cursor', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-cursor' );
		}

		if ( ! wp_script_is( 'premium-cursor-handler', 'enqueued' ) ) {
			wp_enqueue_script( 'premium-cursor-handler' );
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
	 * Register Global Cursor controls.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $element for current element.
	 */
	public function register_controls( $element ) {

		$elem_type = $element->get_name();

		$is_document = ! in_array( $elem_type, array( 'section', 'column', 'common', 'container' ), true );

		$tab = $is_document ? Controls_Manager::TAB_SETTINGS : Controls_Manager::TAB_ADVANCED;

		$elem_id = $is_document ? get_the_ID() : '{{ID}}';

		$global_cursor = get_option( 'pa_site_custom_cursor', false );

		$show_notice = $this->show_site_cursor_notice( $global_cursor );

		$element->start_controls_section(
			'section_premium_cursor',
			array(
				'label' => sprintf( '<i class="pa-extension-icon pa-dash-icon"></i> %s', __( 'Custom Mouse Cursor', 'premium-addons-pro' ) ),
				'tab'   => $tab,
			)
		);

		$element->add_control(
			'premium_global_cursor_switcher',
			array(
				'label'              => __( 'Enable Custom Mouse Cursor', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'prefix_class'       => 'premium-gCursor-',
				'render_type'        => 'template',
				'frontend_available' => true,
			)
		);

		$doc_link = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/elementor-custom-mouse-cursor-addon-tutorial/', 'editor-page', 'wp-editor', 'get-support' );

		$element->add_control(
			'pa_custom_cursor_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => '<a href="' . esc_url( $doc_link ) . '" target="_blank">' . __( 'How to use Premium Custom Mouse Cursor for Elementor »', 'premium-addons-pro' ) . '</a>',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_global_cursor_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'global_cursor_notice',
			array(
				'raw'             => __( 'It\'s recommend to use Elementor Navigator to select elements when Global Cursor is enabled.', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'condition'       => array(
					'premium_global_cursor_switcher' => 'yes',
				),
			)
		);

		if ( $is_document ) {

			if ( $show_notice ) {

				$element->add_control(
					'pa_site_cursor_notice',
					array(
						'type'            => Controls_Manager::RAW_HTML,
						'raw'             => __( 'Custom Mouse Cursor is applied on the entire site from', 'premium-addons-pro' ) . '<a href="' . get_bloginfo( 'url' ) . '/wp-admin/post.php?post=' . $global_cursor['page_id'] . '&action=elementor" target="_blank">' . __( ' here ', 'premium-addons-pro' ) . '</a>' . __( '-> Page Settings', 'premium-addons-pro' ),
						'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					)
				);

			} else {

				$element->add_control(
					'pa_apply_on_site',
					array(
						'label'       => __( 'Apply On Entire Site', 'premium-addons-pro' ),
						'type'        => Controls_Manager::SWITCHER,
						'description' => __( 'Enable this option to apply the cursor on the entire site.', 'premium-addons-pro' ),
						'condition'   => array(
							'premium_global_cursor_switcher' => 'yes',
						),
					)
				);

			}
		}

		$element->add_control(
			'pa_cursor_type',
			array(
				'label'              => __( 'Type', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'render_type'        => 'template',
				'frontend_available' => true,
				// 'prefix_class'       => 'premium-cursor-',
				'options'            => array(
					'icon'   => __( 'Icon', 'premium-addons-pro' ),
					'image'  => __( 'Image', 'premium-addons-pro' ),
					'lottie' => __( 'Lottie Animation', 'premium-addons-pro' ),
					'fimage' => __( 'Follow Image', 'premium-addons-pro' ),
					'ftext'  => __( 'Follow Text', 'premium-addons-pro' ),
				),
				'default'            => 'icon',
				'condition'          => array(
					'premium_global_cursor_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'pa_cursor_pulse',
			array(
				'label'              => __( 'Pulse Effect', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'render_type'        => 'template',
				'condition'          => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_cursor_type'                 => array( 'icon', 'image' ),
					'pa_cursor_buzz!'                => 'yes',
				),
			)
		);

		$element->add_control(
			'pa_cursor_buzz',
			array(
				'label'              => __( 'Buzz Effect', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'render_type'        => 'template',
				'condition'          => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_cursor_type'                 => array( 'icon', 'image' ),
					'pa_cursor_pulse!'               => 'yes',
				),
			)
		);

		$element->add_control(
			'pa_cursor_icon',
			array(
				'label'              => __( 'Choose Icon', 'premium-addons-pro' ),
				'type'               => Controls_Manager::ICONS,
				'frontend_available' => true,
				'default'            => array(
					'value'   => 'fas fa-mouse-pointer',
					'library' => 'solid',
				),
				'condition'          => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_cursor_type'                 => 'icon',
				),
			)
		);

		$element->add_control(
			'pa_cursor_img',
			array(
				'label'              => __( 'Choose Image', 'premium-addons-pro' ),
				'type'               => Controls_Manager::MEDIA,
				'frontend_available' => true,
				'condition'          => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_cursor_type'                 => array( 'image', 'fimage' ),
				),
			)
		);

		$element->add_control(
			'pa_cursor_img_fit',
			array(
				'label'     => __( 'Image Fit', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'cover'   => __( 'Cover', 'premium-addons-pro' ),
					'fill'    => __( 'Fill', 'premium-addons-pro' ),
					'contain' => __( 'Contain', 'premium-addons-pro' ),
				),
				'default'   => 'cover',
				'condition' => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_cursor_type'                 => array( 'image', 'fimage' ),
				),
				'selectors' => array(
					'{{WRAPPER}}.premium-cursor-image .premium-global-cursor-' . $elem_id . ' img, {{WRAPPER}}.premium-cursor-fimage .premium-global-cursor-' . $elem_id . ' img' => 'object-fit: {{VALUE}} !important;',
				),

			)
		);

		$element->add_control(
			'pa_cursor_ftext',
			array(
				'label'              => __( 'Follow Text', 'premium-addons-pro' ),
				'type'               => Controls_Manager::TEXT,
				'frontend_available' => true,
				'default'            => __( 'Premium Follow Text', 'premium-addons-pro' ),
				'condition'          => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_cursor_type'                 => array( 'ftext' ),
				),
			)
		);

		$element->add_control(
			'pa_default_cursor',
			array(
				'label'     => __( 'Default Cursor', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $this->get_default_cursors(),
				'default'   => '',
				'condition' => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_cursor_type'                 => array( 'ftext', 'fimage' ),
					'pa_cursor_dot!'                 => 'yes',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}.premium-gCursor-yes, {{WRAPPER}}.premium-gCursor-yes *' => 'cursor: {{VALUE}} !important;',
					'{{WRAPPER}}.premium-gCursor-yes.premium-cursor-not-active *' => 'cursor: none !important;',
				),
			)
		);

		$element->add_control(
			'pa_cursor_dot',
			array(
				'label'              => __( 'Change Cursor to Dot', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'render_type'        => 'template',
				'condition'          => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_cursor_type'                 => array( 'ftext', 'fimage' ),
				),
			)
		);

		$element->add_control(
			'pa_dot_color',
			array(
				'label'     => __( 'Dot Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_cursor_type'                 => array( 'ftext', 'fimage' ),
					'pa_cursor_dot'                  => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-global-cursor-' . $elem_id . ' .eicon-circle'  => 'color: {{VALUE}};',
				),
			)
		);

		$element->add_control(
			'pa_dot_size',
			array(
				'label'      => __( 'Dot Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'condition'  => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_cursor_type'                 => array( 'ftext', 'fimage' ),
					'pa_cursor_dot'                  => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-global-cursor-' . $elem_id . ' .eicon-circle'  => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_responsive_control(
			'pa_cursor_xpos',
			array(
				'label'              => __( 'X Position (%)', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SLIDER,
				'frontend_available' => true,
				'render_type'        => 'template',
				'range'              => array(
					'px' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'separator'          => 'before',
				'condition'          => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_cursor_type'                 => array( 'fimage', 'ftext' ),
				),
			)
		);

		$element->add_responsive_control(
			'pa_cursor_ypos',
			array(
				'label'              => __( 'Y Position (%)', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SLIDER,
				'frontend_available' => true,
				'render_type'        => 'template',
				'range'              => array(
					'px' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition'          => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_cursor_type'                 => array( 'fimage', 'ftext' ),
				),
			)
		);

		$element->add_control(
			'pa_cursor_trans',
			array(
				'label'              => __( 'Follow Delay (s)', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SLIDER,
				'frontend_available' => true,
				'size_units'         => array( 'px' ),
				'range'              => array(
					'px' => array(
						'min'  => 0.3,
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'default'            => array(
					'unit' => 'px',
					'size' => 0.3,
				),
				'description'        => __( 'Default is 0.3s', 'premium-addons-pro' ),
				'condition'          => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_cursor_type'                 => array( 'fimage', 'ftext' ),
					'pa_magnet!'                     => 'yes',
				),
			)
		);

		$element->add_control(
			'pa_cursor_lottie_url',
			array(
				'label'              => __( 'Animation JSON URL', 'premium-addons-pro' ),
				'type'               => Controls_Manager::TEXT,
				'frontend_available' => true,
				'dynamic'            => array( 'active' => true ),
				'description'        => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block'        => true,
				'condition'          => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_cursor_type'                 => 'lottie',
				),
			)
		);

		$element->add_control(
			'pa_cursor_loop',
			array(
				'label'              => __( 'Loop', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'return_value'       => 'true',
				'default'            => 'true',
				'condition'          => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_cursor_type'                 => 'lottie',
				),
			)
		);

		$element->add_control(
			'pa_cursor_reverse',
			array(
				'label'              => __( 'Reverse', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'return_value'       => 'true',
				'condition'          => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_cursor_type'                 => 'lottie',
				),
			)
		);

		$element->add_control(
			'pa_cursor_heading',
			array(
				'label'     => esc_html__( 'Cursor Style', 'premium-addons-pro' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'premium_global_cursor_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'pa_cursor_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'condition' => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_cursor_type'                 => array( 'icon', 'ftext' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-global-cursor-' . $elem_id  => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
			)
		);

		$element->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'pa_cursor_typo',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'condition' => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_cursor_type'                 => 'ftext',
				),
				'selector'  => '{{WRAPPER}}.premium-cursor-ftext .premium-global-cursor-' . $elem_id . ' .premium-cursor-follow-text',
			)
		);

		$element->add_responsive_control(
			'pa_text_cursor_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}.premium-cursor-ftext .premium-global-cursor-' . $elem_id . ' .premium-cursor-follow-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_cursor_type'                 => 'ftext',
				),
			)
		);

		$element->add_responsive_control(
			'pa_cursor_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'size' => 20,
				),
				'range'      => array(
					'px' => array(
						'max' => 500,
						'min' => 0,
					),
				),
				'condition'  => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_cursor_type!'                => 'ftext',
				),
				'selectors'  => array(
					'{{WRAPPER}}.premium-cursor-icon .premium-global-cursor-' . $elem_id . ' i' => 'font-size: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',

					'{{WRAPPER}}.premium-cursor-icon .premium-global-cursor-' . $elem_id . ' i,
					{{WRAPPER}}.premium-cursor-image .premium-global-cursor-' . $elem_id . ',
					{{WRAPPER}}.premium-cursor-fimage .premium-global-cursor-' . $elem_id . ',
					{{WRAPPER}}.premium-cursor-lottie .premium-global-cursor-' . $elem_id . ' .premium-cursor-lottie-icon,
					{{WRAPPER}}.premium-cursor-icon .premium-global-cursor-' . $elem_id . ' .premium-cursor-icon-svg' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',

				),
			)
		);

		$element->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'pa_cursor_shadow',
				'condition' => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_cursor_type'                 => 'ftext',
				),
				'selector'  => '{{WRAPPER}} .premium-global-cursor-' . $elem_id,
			)
		);

		$element->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'pa_cursor_bgColor',
				'types'     => array( 'classic', 'gradient' ),
				'condition' => array(
					'premium_global_cursor_switcher' => 'yes',
				),
				'selector'  => '{{WRAPPER}} .premium-global-cursor-' . $elem_id,
			)
		);

		$element->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'pa_cursor_border',
				'selector'  => '{{WRAPPER}} .premium-global-cursor-' . $elem_id,
				'condition' => array(
					'premium_global_cursor_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'pa_cursor_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'condition'  => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_cursor_adv_radius!'          => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-global-cursor-' . $elem_id . ', {{WRAPPER}} .premium-global-cursor-' . $elem_id . ' img' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_control(
			'pa_cursor_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-pro' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
				'condition'   => array(
					'premium_global_cursor_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'pa_cursor_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-global-cursor-' . $elem_id . ', {{WRAPPER}} .premium-global-cursor-' . $elem_id . ' img' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'pa_cursor_adv_radius'           => 'yes',
					'premium_global_cursor_switcher' => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'pa_cursor_rotate',
			array(
				'label'      => __( 'Rotate (deg)', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'default'    => array(
					'unit' => 'deg',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-global-cursor-' . $elem_id => 'transform: rotate({{SIZE}}deg)',
				),
				'condition'  => array(
					'premium_global_cursor_switcher' => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'pa_cursor_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-global-cursor-' . $elem_id => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_global_cursor_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'pa_disable_cursor',
			array(
				'label'              => __( 'Disable on Touch Devices', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'description'        => __( 'Please note that this option works on page load.', 'premium-addons-pro' ),
				'condition'          => array(
					'premium_global_cursor_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'pa_magnet',
			array(
				'label'              => __( 'Magnet Effect', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition'          => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_cursor_type!'                => 'ftext',
				),
			)
		);

		$element->add_control(
			'pa_magnet_grow',
			array(
				'label'     => __( 'Grow Amount (PX)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 1,
						'max' => 130,
					),
				),
				'default'   => array(
					'size' => 20,
					'unit' => 'px',
				),
				'condition' => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_magnet'                      => 'yes',
					'pa_cursor_type'                 => array( 'image', 'fimage' ),
				),
			)
		);

		$hotspots_demo = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/image-hotspots-widget-for-elementor-page-builder/', 'editor-page', 'wp-editor', 'get-support' );

		$element->add_control(
			'pa_magnet_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => 'Magnet effect is used with <a href="' . esc_url( $hotspots_demo ) . '" target="_blank">' . __( 'Premium Image Hotspots', 'premium-addons-pro' ) . '</a> widget only',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_global_cursor_switcher' => 'yes',
					'pa_magnet'                      => 'yes',
				),
			)
		);

		$element->end_controls_section();
	}

	/**
	 * Render Global Cursor output in the editor.
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
			var isEnabled = 'yes' === settings.premium_global_cursor_switcher ? true : false;

			if ( isEnabled ) {

				var cursorType = settings.pa_cursor_type,
					pulse = ['icon', 'image'].includes(cursorType) && 'yes' === settings.pa_cursor_pulse ? ' premium-pulse-yes ' : '',
					buzz = ['icon', 'image'].includes(cursorType) && 'yes' === settings.pa_cursor_buzz ? ' premium-buzz-yes ' : '',
					delay = ['ftext', 'fimage'].includes(cursorType) && '' !== settings.pa_cursor_trans.size ? settings.pa_cursor_trans.size : 0.01,
					mobileDisabled = 'yes' === settings.pa_disable_cursor ? true : false,
					elementSettings = {},
					cursorSettings = {
						cursorType : cursorType,
						delay: delay,
						pulse: pulse,
						buzz: buzz,
						cursorDot: ['ftext', 'fimage'].includes(cursorType) && 'yes' === settings.pa_cursor_dot ? ' premium-cursor-dot ' : '',
						magnet: 'yes' === settings.pa_magnet
					};

				if( cursorSettings.magnet && [ 'image', 'fimage' ].includes( cursorType ) ) {
					cursorSettings.magnet_grow = settings.pa_magnet_grow.size;
				}

				if ( 'icon' === cursorType ) {
					elementSettings = settings.pa_cursor_icon;

				} else if ( 'image' === cursorType || 'fimage' === cursorType ) {
					elementSettings.url = settings.pa_cursor_img.url;

					if ( 'fimage' === cursorType ) {
						elementSettings.xpos = settings.pa_cursor_xpos.size;
						elementSettings.ypos = settings.pa_cursor_ypos.size;
					}

				} else if ( 'ftext' === cursorType ) {
					elementSettings.text = settings.pa_cursor_ftext;
					elementSettings.xpos = settings.pa_cursor_xpos.size;
					elementSettings.ypos = settings.pa_cursor_ypos.size;

				} else if ( 'lottie' === cursorType ) {
					elementSettings.url     = settings.pa_cursor_lottie_url;
					elementSettings.loop    = settings.pa_cursor_loop;
					elementSettings.reverse = settings.pa_cursor_reverse;
				}

				cursorSettings.elementSettings = elementSettings;

				view.addRenderAttribute( 'cursor_data', {
					'id': 'premium-global-cursor-' + view.getID(),
					'class': 'premium-global-cursor-wrapper',
					'data-gcursor': JSON.stringify( cursorSettings ),
					'data-pa_mobile_disabled' : mobileDisabled
				});
		#>
				<div {{{ view.getRenderAttributeString( 'cursor_data' ) }}}></div>
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
	 * Render Global Cursor output on the frontend.
	 *
	 * Written in PHP and used to collect cursor settings and add it as an element attribute.
	 *
	 * @access public
	 * @param object $element for current element.
	 */
	public function before_render( $element ) {

		$type = $element->get_type();

		$id = $element->get_id();

		$settings = $element->get_settings_for_display();

		$cursor_switcher = $settings['premium_global_cursor_switcher'];

		if ( 'yes' === $cursor_switcher ) {

			$addon_settings = $this->get_addon_settings( $settings );

			$element->add_render_attribute( '_wrapper', 'data-gcursor', wp_json_encode( $addon_settings['cursor_settings'] ) );
			$element->add_render_attribute( '_wrapper', 'data-pa_mobile_disabled', $addon_settings['mobile_disabled'] && wp_is_mobile() );

			if ( 'widget' === $type && \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
				?>
				<div id='premium-global-cursor-temp-<?php echo esc_attr( $id ); ?>' data-gcursor='<?php echo wp_json_encode( $addon_settings['cursor_settings'] ); ?>' data-pa_mobile_disabled='<?php echo esc_attr( $addon_settings['mobile_disabled'] ); ?>'></div>
				<?php
			}
		}
	}

	/**
	 * Reset site cursor option is post unpublished.
	 *
	 * @access public
	 * @since 2.8.1
	 *
	 * @param string $new_status post new status.
	 * @param string $old_status post old status.
	 * @param object $post post object.
	 */
	public function post_unpublished( $new_status, $old_status, $post ) {

		if ( 'publish' != $new_status ) {
			$current_id = $post->ID;

			$site_settings = get_option( 'pa_site_custom_cursor', false );

			if ( ! $site_settings ) {
				return;
			}

			$trigger_id = $site_settings['page_id'];

			if ( $trigger_id == $current_id ) {

				$site_cursor = array(
					'enabled' => false,
					'page_id' => null,
				);

				delete_option( 'pa_site_custom_cursor' );
				update_option( 'pa_site_custom_cursor', $site_cursor );
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

		$pg_cursor_enabled   = $this->check_page_cursor();
		$elem_cursor_enabled = 'yes' === $element->get_settings_for_display( 'premium_global_cursor_switcher' ) ? true : false;

		if ( $pg_cursor_enabled || $elem_cursor_enabled ) {

			$this->enqueue_styles();
			$this->enqueue_scripts();

			$this->load_script = true;

			remove_action( 'elementor/frontend/before_render', array( $this, 'check_script_enqueue' ) );
		}
	}

	/**
	 * Get addon settings.
	 *
	 * @access private
	 * @since 2.8.0
	 *
	 * @param array $settings element's settings.
	 * @param bool  $is_page  is the element a page.
	 *
	 * @return array
	 */
	private function get_addon_settings( $settings, $is_page = false ) {

		$cursor_type = $settings['pa_cursor_type'];

		$pulse           = 'yes' === $settings['pa_cursor_pulse'] ? ' premium-pulse-yes ' : '';
		$buzz            = 'yes' === $settings['pa_cursor_buzz'] ? ' premium-buzz-yes ' : '';
		$mobile_disabled = 'yes' === $settings['pa_disable_cursor'];

		$element_settings = array();

		$cursor_settings = array(
			'cursorType' => $cursor_type,
			'delay'      => isset( $settings['pa_cursor_trans']['size'] ) && in_array( $cursor_type, array( 'fimage', 'ftext' ), true ) ? $settings['pa_cursor_trans']['size'] : 0.01,
			'pulse'      => $pulse,
			'buzz'       => $buzz,
			'cursorDot'  => in_array( $cursor_type, array( 'fimage', 'ftext' ), true ) && 'yes' === $settings['pa_cursor_dot'] ? ' premium-cursor-dot ' : '',
			'magnet'     => 'yes' === $settings['pa_magnet'],
		);


		if ( $cursor_settings['magnet'] && in_array( $cursor_type, array( 'image', 'fimage' ), true ) ) {
			$cursor_settings['magnet_grow'] = $settings['pa_magnet_grow']['size'];
		}

		if ( 'icon' === $cursor_type ) {
			$element_settings = $settings['pa_cursor_icon'];

		} elseif ( 'image' === $cursor_type || 'fimage' === $cursor_type ) {
			$element_settings['url'] = $settings['pa_cursor_img']['url'];
			$element_settings['alt'] = Control_Media::get_image_alt( $settings['pa_cursor_img'] );

			if ( 'fimage' === $cursor_type ) {
				$element_settings['xpos'] = $settings['pa_cursor_xpos']['size'];
				$element_settings['ypos'] = $settings['pa_cursor_ypos']['size'];
			}
		} elseif ( 'ftext' === $cursor_type ) {
			$element_settings['text'] = $settings['pa_cursor_ftext'];
			$element_settings['xpos'] = $settings['pa_cursor_xpos']['size'];
			$element_settings['ypos'] = $settings['pa_cursor_ypos']['size'];

		} elseif ( 'lottie' === $cursor_type ) {
			$element_settings['url']     = esc_url( $settings['pa_cursor_lottie_url'] );
			$element_settings['loop']    = $settings['pa_cursor_loop'];
			$element_settings['reverse'] = $settings['pa_cursor_reverse'];

		}

		$cursor_settings['elementSettings'] = $element_settings;

		if ( $is_page ) {
			$cursor_settings['elemId'] = get_the_ID();
		}

		return array(
			'cursor_settings' => $cursor_settings,
			'mobile_disabled' => $mobile_disabled,
		);
	}

	/**
	 * Checks if custom cursor is enabled for the whole page.
	 *
	 * @since 2.8.0
	 * @access public
	 * @link https://developers.elementor.com/elementor-document-settings/
	 *
	 * @return bool
	 */
	public function check_page_cursor() {

		// Get the current post id.
		$post_id = get_the_ID();

		// Get the page settings manager.
		$page_settings_manager = Manager::get_settings_managers( 'page' );

		// Get the settings model for current post.
		$page_settings_model = $page_settings_manager->get_model( $post_id );

		// Retrieve the option we want.
		$is_cursor_enabled = 'yes' === $page_settings_model->get_settings( 'premium_global_cursor_switcher' ) ? true : false;

		return $is_cursor_enabled;
	}

	/**
	 * Add Site Cursor Settings.
	 * Add a div to the page holding the site cursor settings.
	 *
	 * @access public
	 * @since 2.8.0
	 */
	public function add_site_cursor_settings() {

		$global_cursor = get_option( 'pa_site_custom_cursor', false );

		if ( ! $global_cursor ) {
			return;
		}

		$curr_id = get_the_ID();

		$trigger_id = $global_cursor['page_id'];

		if ( $curr_id == $trigger_id ) {
			return;
		}

		$site_cursor_enabled = isset( $global_cursor['enabled'] ) && $global_cursor['enabled'];

		if ( $site_cursor_enabled ) {

			$site_cursor_settings = $global_cursor['settings'];
			$mobile_disabled      = $site_cursor_settings['mobile_disabled'] && wp_is_mobile();

			$this->add_cusror_style( $global_cursor['style'] );

			?>
				<div class='premium-site-cursor' data-premium_site_cursor='<?php echo wp_json_encode( $site_cursor_settings['cursor_settings'] ); ?>' data-pa_mobile_disabled='<?php echo esc_attr( $mobile_disabled ); ?>'></div>
			<?php
		}
	}

	/**
	 * Determines whether to show/hide site cursor notice.
	 * Shows the notice if >>> the option is enabled && not the same trigger-page
	 * id ( we're in a different page ) && if the page id is published.
	 *
	 * @access private
	 * @since 2.8.0
	 *
	 * @param array $options site cursor options.
	 *
	 * @return bool
	 */
	private function show_site_cursor_notice( $options ) {

		if ( ! $options || ! isset( $options['enabled'] ) ) {
			return false;
		}

		$is_enabled = $options['enabled'];

		$is_published = isset( $options['page_id'] ) && 'publish' === get_post_status( $options['page_id'] ) ? true : false;

		$is_trigger = isset( $options['page_id'] ) && get_the_ID() != $options['page_id'] ? true : false;

		if ( $is_enabled && $is_published && $is_trigger ) {
			return true;

		} else {
			false;
		}
	}

	/**
	 * Saves the addons settings to use globaly.
	 *
	 * @access public
	 * @since 2.8.0
	 *
	 * @param int $post_id post id.
	 */
	public function save_global_cursor_settings( $post_id ) {

		if ( wp_doing_cron() ) {
			return;
		}

		$document = Plugin::$instance->documents->get( $post_id, false );

		$page_settings = $document->get_settings();

		$global_setting = get_option( 'pa_site_custom_cursor', false );

		$site_cursor_enabled = isset( $global_setting['enabled'] ) && $global_setting['enabled'] ? true : false;

		$addon_enabled = isset( $page_settings['premium_global_cursor_switcher'] ) && 'yes' === $page_settings['premium_global_cursor_switcher'] ? true : false;
		$apply_on_site = isset( $page_settings['pa_apply_on_site'] ) && 'yes' === $page_settings['pa_apply_on_site'] ? true : false;

		if ( $site_cursor_enabled ) { // globally enabled from this page or from somewhere else.

			// check if this page is trigger >> if so >> update.
			if ( get_the_ID() == $global_setting['page_id'] ) {

				if ( $addon_enabled && $apply_on_site ) {

					$addon_settings = $this->get_addon_settings( $page_settings, true );
					$addon_style    = $this->get_addon_style( $addon_settings['cursor_settings']['cursorType'], $post_id, $page_settings );

					$site_cursor = array(
						'enabled'  => true,
						'page_id'  => $post_id,
						'settings' => $addon_settings,
						'style'    => $addon_style,
					);

				} else {

					$site_cursor = array(
						'enabled' => false,
						'page_id' => null,
					);
				}

				delete_option( 'pa_site_custom_cursor' );
				update_option( 'pa_site_custom_cursor', $site_cursor );
			}
		} else { // globally disabled, we should check this page if it was enabled here >> then update the option.

			if ( $addon_enabled && $apply_on_site ) {

				$addon_settings = $this->get_addon_settings( $page_settings, true );
				$addon_style    = $this->get_addon_style( $addon_settings['cursor_settings']['cursorType'], $post_id, $page_settings );

				$site_cursor = array(
					'enabled'  => true,
					'page_id'  => $post_id,
					'settings' => $addon_settings,
					'style'    => $addon_style,
				);

				delete_option( 'pa_site_custom_cursor' );
				update_option( 'pa_site_custom_cursor', $site_cursor );
			}
		}
	}

	/**
	 * Resets the global addon settings.
	 *
	 * @access public
	 * @since 2.8.0
	 *
	 * @param int|string $post_id post id.
	 */
	public function reset_global_cursor_settings( $post_id ) {

		if ( wp_doing_cron() ) {
			return;
		}

		// we should check if it's the same page before reseting.
		$cursor_settings = get_option( 'pa_site_custom_cursor', false );

		if ( ! $cursor_settings ) {
			return;
		}

		if ( get_the_ID() == $cursor_settings['page_id'] ) {

			$site_cursor = array(
				'enabled' => false,
				'page_id' => null,
			);

			delete_option( 'pa_site_custom_cursor' );
			update_option( 'pa_site_custom_cursor', $site_cursor );
		}
	}

	/**
	 * Get default cursors.
	 *
	 * @access private
	 * @since 4.9.20
	 *
	 * @return array
	 */
	private function get_default_cursors() {

		return array(
			''             => 'Choose Cursor',
			'auto'         => 'auto',
			'alias'        => 'alias',
			'all-scroll'   => 'all-scroll',
			'cell'         => 'cell',
			'context-menu' => 'context-menu',
			'col-resize'   => 'col-resize',
			'copy'         => 'copy',
			'crosshair'    => 'crosshair',
			'e-resize'     => 'e-resize',
			'ew-resize'    => 'ew-resize',
			'grab'         => 'grab',
			'help'         => 'help',
			'move'         => 'move',
			'n-resize'     => 'n-resize',
			'ne-resize'    => 'ne-resize',
			'nesw-resize'  => 'nesw-resize',
			'ns-resize'    => 'ns-resize',
			'nw-resize'    => 'nw-resize',
			'nwse-resize'  => 'nwse-resize',
			'no-drop'      => 'no-drop',
			'not-allowed'  => 'not-allowed',
			'pointer'      => 'pointer',
			'progress'     => 'progress',
			'row-resize'   => 'row-resize',
			's-resize'     => 's-resize',
			'se-resize'    => 'se-resize',
			'sw-resize'    => 'sw-resize',
			'text'         => 'text',
			'w-resize'     => 'w-resize',
			'wait'         => 'wait',
			'zoom-in'      => 'zoom-in',
			'zoom-out'     => 'zoom-out',
		);
	}

	/**
	 * Add cursor style.
	 *
	 * @access private
	 * @since 2.8.0
	 *
	 * @param string $style cursor style.
	 */
	private function add_cusror_style( $style ) {

		wp_register_style( 'pa-site-cursor', false );
		wp_enqueue_style( 'pa-site-cursor' );

		wp_add_inline_style( 'pa-site-cursor', $style );
	}

	/**
	 * Get addon style.
	 *
	 * @access private
	 * @since 2.8.0
	 *
	 * @param array      $type cursor type settings.
	 * @param int|string $id page id.
	 * @param array      $settings page settings.
	 *
	 * @return string
	 */
	private function get_addon_style( $type, $id, $settings ) {

		$cursor_css = '';

		$cursor_css .= $this->get_cursor_type_style( $type, $id, $settings );
		$cursor_css .= $this->get_cursor_common_style( $id, $settings );

		return $cursor_css;
	}

	/**
	 * Gets type-specific style.
	 *
	 * @access private
	 * @since 2.8.0
	 *
	 * @param string     $type cursor type settings.
	 * @param int|string $id page id.
	 * @param array      $settings page settings.
	 *
	 * @return string
	 */
	private function get_cursor_type_style( $type, $id, $settings ) {

		$cursor_css = '';

		if ( 'ftext' === $type ) {

			if ( isset( $settings['pa_cursor_typo_typography'] ) ) {
				$cursor_css .= $this->get_typo_ctrl_val( $settings, 'pa_cursor_typo', $id );
			}

			if ( isset( $settings['pa_default_cursor'] ) ) {

				$cursor      = $settings['pa_default_cursor'];
				$cursor_css .= ".premium-gCursor-yes, .premium-gCursor-yes * { cursor: {$cursor} !important; }";
				$cursor_css .= '.premium-gCursor-yes.premium-cursor-not-active * { cursor: none !important; }';
			}

			if ( ! empty( $settings['pa_cursor_shadow_text_shadow_type'] ) ) {

				$shadow      = $settings['pa_cursor_shadow_text_shadow'];
				$cursor_css .= ".premium-gCursor-yes .premium-global-cursor-{$id} { text-shadow: {$shadow['horizontal']}px {$shadow['vertical']}px {$shadow['blur']}px {$shadow['color']}; }";
			}
		} else {
			$cursor_css .= $this->get_cursor_size( $id, $settings, '' );

			$cursor_css .= $this->get_responsive_style( 'cursor_size', 'custom-prop', 'pa_cursor_size', $settings, $id, 'custom' );
		}

		if ( in_array( $type, array( 'ftext', 'icon' ), true ) ) {

			if ( ! empty( $settings['pa_cursor_color'] ) ) {
				$color       = $settings['pa_cursor_color'];
				$cursor_css .= ".premium-gCursor-yes .premium-global-cursor-{$id} { color: {$color}; fill: {$color}; }";
			}
		}

		return $cursor_css;
	}

	/**
	 * Gets cursor size.
	 *
	 * @access private
	 * @since 2.8.0
	 *
	 * @param int|string $id page id.
	 * @param array      $settings page settings.
	 * @param string     $br device breakpoint ( tablet, mobile, etc...).
	 *
	 * @return string
	 */
	private function get_cursor_size( $id, $settings, $br ) {

		$size       = $settings[ 'pa_cursor_size' . $br ]['size'];
		$cursor_css = '';

		if ( ! empty( $size ) ) {

			$unit        = $settings[ 'pa_cursor_size' . $br ]['unit'];
			$cursor_css .= ".premium-gCursor-yes.premium-cursor-icon .premium-global-cursor-{$id} i { font-size: {$size}{$unit}; line-height: {$size}{$unit}; }";

			$cursor_css .= ".premium-gCursor-yes.premium-cursor-icon .premium-global-cursor-{$id} i,
						.premium-gCursor-yes.premium-cursor-image .premium-global-cursor-{$id},
						.premium-gCursor-yes.premium-cursor-fimage .premium-global-cursor-{$id},
						.premium-gCursor-yes.premium-cursor-lottie .premium-global-cursor-{$id} .premium-cursor-lottie-icon," .
						'.premium-gCursor-yes.premium-cursor-icon .premium-global-cursor-' . $id . '.premium-cursor-icon-svg { height: ' . $size . $unit . '; width: ' . $size . $unit . '; }';
		}

		return $cursor_css;
	}

	/**
	 * Gets cursor common style.
	 *
	 * @access private
	 * @since 2.8.0
	 *
	 * @param int|string $id page id.
	 * @param array      $settings page settings.
	 *
	 * @return string
	 */
	private function get_cursor_common_style( $id, $settings ) {

		$padding = $this->get_cusror_padding( $id, $settings );

		$rotate = $this->get_cursor_rotate( $id, $settings );

		$border = $this->get_cursor_border( $id, $settings );

		$border_radius = $this->get_cursor_border_rad( $id, $settings );

		$background .= $this->get_cursor_background( $id, $settings );

		$common_css = $padding . $rotate . $border . $border_radius . $background;

		return $common_css;
	}

	/**
	 * Gets cursor { prop }.
	 *
	 * @access private
	 * @since 2.8.0
	 *
	 * @param string $pg_id page id.
	 * @param array  $settings page settings.
	 *
	 * @return string
	 */
	private function get_cusror_padding( $pg_id, $settings ) {

		$padding_css = '';

		$selector = ".premium-gCursor-yes .premium-global-cursor-{$pg_id} { ";
		// default.
		$padding = $this->get_dimension_ctrl_val( 'padding', 'pa_cursor_padding', $settings );

		$padding_css = "{$selector} {$padding} } ";

		// responsive style.
		$responsive_padding = $this->get_responsive_style( 'dimension', 'padding', 'pa_cursor_padding', $settings, $pg_id, '' );

		$padding_css .= $responsive_padding;

		return $padding_css;
	}

	private function get_cursor_rotate( $pg_id, $settings ) {

		$rotate_css = '';

		$rotate = $settings['pa_cursor_rotate']['size'];

		if ( ! empty( $rotate ) ) {
			$selector = ".premium-gCursor-yes .premium-global-cursor-{$pg_id} { ";

			$rotate = $this->get_rotate_css( $rotate );

			$rotate_css .= "{$selector} {$rotate} }";

			$responsive_rotate = $this->get_responsive_style( 'size', 'rotate', 'pa_cursor_rotate', $settings, $pg_id, '' );

			$rotate_css .= $responsive_rotate;

		}

		return $rotate_css;
	}

	private function get_cursor_border( $pg_id, $settings ) {

		$border_css = '';

		$border = $settings['pa_cursor_border_border'];

		if ( ! empty( $border ) ) {

			$selector = ".premium-gCursor-yes .premium-global-cursor-{$pg_id} {";

			$border_style_css = "border-style: {$border};";

			$border_color     = $settings['pa_cursor_border_color'];
			$border_color_css = ! empty( $border_color ) ? "border-color: {$border_color};" : '';

			$border_width_css = $this->get_dimension_ctrl_val( 'border-width', 'pa_cursor_border_width', $settings );

			$border_css .= "{$selector} {$border_style_css} {$border_color_css} {$border_width_css} }";

			// add responsive border width.
			$border_css .= $this->get_responsive_style( 'dimension', 'border-width', 'pa_cursor_border_width', $settings, $pg_id, '' );
		}

		return $border_css;
	}

	private function get_cursor_border_rad( $pg_id, $settings ) {

		$adv_radius = 'yes' === $settings['pa_cursor_adv_radius'] ? true : false;

		$border_radius = $adv_radius ? $settings['pa_cursor_adv_radius_value'] : $settings['pa_cursor_border_rad'];
		$radius_val    = $adv_radius ? $border_radius : "{$border_radius['size']}{$border_radius['unit']}";

		$css .= ".premium-gCursor-yes .premium-global-cursor-{$pg_id}, .premium-gCursor-yes .premium-global-cursor-{$pg_id} img {border-radius: {$radius_val} ;}";

		return $css;
	}

	private function get_cursor_background( $pg_id, $settings ) {

		$bg_css = '';
		$has_bg = isset( $settings['pa_cursor_bgColor_background'] ) && ! empty( $settings['pa_cursor_bgColor_background'] );

		if ( $has_bg ) {

			$bg_type = $settings['pa_cursor_bgColor_background'];

			if ( 'classic' === $bg_type ) {

				$color = $settings['pa_cursor_bgColor_color'];
				$img   = $settings['pa_cursor_bgColor_image'];

				if ( ! empty( $color ) ) {
					$bg_css .= ".premium-gCursor-yes .premium-global-cursor-{$pg_id} { background-color: {$color}; }"; // background-color.
				}

				if ( ! empty( $img['url'] ) ) {
					$bg_css .= $this->get_img_bg_props( $pg_id, $settings ); // background image.
				}
			} else {
				$bg_css .= $this->get_grad_bg_props( $settings );
			}

			return $bg_css;
		}

		return '';
	}

	/**
	 * Gets image background properties.
	 *
	 * @access private
	 * @since 2.8.0
	 *
	 * @param string $pg_id page id.
	 * @param array  $settings page settings.
	 *
	 * @return string
	 */
	private function get_img_bg_props( $pg_id, $settings ) {

		$img    = $settings['pa_cursor_bgColor_image'];
		$repeat = $settings['pa_cursor_bgColor_repeat'];
		$attach = $settings['pa_cursor_bgColor_attachment'];

		$selector = ".premium-gCursor-yes .premium-global-cursor-{$pg_id} {";

		$bg_img    = $this->get_bg_image_css( $img );
		$bg_repeat = ! empty( $repeat ) ? "background-repeat: {$repeat};" : '';
		$bg_attach = ! empty( $attach ) ? "background-attachment: {$attach};" : '';
		$bg_pos    = $this->get_img_pos( $settings );
		$bg_size   = $this->get_img_size( $settings, '' );

		$bg_img_css = "{$selector} {$bg_img} {$bg_repeat} {$bg_attach} {$bg_pos} {$bg_size} }";

		// add responsive bg image css.
		$responsive_img    = $this->get_responsive_style( 'img', 'background-image', 'pa_cursor_bgColor_image', $settings, $pg_id, '' );
		$responsive_repeat = $this->get_responsive_style( '', 'background-repeat', 'pa_cursor_bgColor_repeat', $settings, $pg_id, '' );
		$responsive_pos    = $this->get_responsive_style( '', 'background-position', 'pa_cursor_bgColor_position', $settings, $pg_id, '' );
		$responsive_size   = $this->get_responsive_style( '', 'background-size', 'pa_cursor_bgColor_size', $settings, $pg_id, '' );

		$resposive_bg_css .= $responsive_img . $responsive_repeat . $responsive_pos . $responsive_size;

		$bg_css = $bg_img_css . $resposive_bg_css;

		return $bg_css;
	}

	/**
	 * Gets background image position.
	 *
	 * @access private
	 * @since 2.8.0
	 *
	 * @param array  $settings page settings.
	 * @param string $br device breakpoint.
	 *
	 * @return string
	 */
	private function get_img_pos( $settings, $br = '' ) {

		$ctrl_id = 'pa_cursor_bgColor_position' . $br;

		$pos = $settings[ $ctrl_id ];

		$pos_css = '';

		if ( ! empty( $pos ) ) {
			if ( 'initial' === $pos ) {
				$xpos = $settings[ 'pa_cursor_bgColor_xpos' . $br ];
				$ypos = $settings[ 'pa_cursor_bgColor_ypos' . $br ];

				$pos_css .= "background-position: {$xpos['size']}{$xpos['unit']} {$ypos['size']}{$ypos['unit']};";
			} else {
				$pos_css .= "background-position: {$pos};";
			}
		}

		return $pos_css;
	}

	private function get_img_size( $settings, $br ) {

		$ctrl_id = 'pa_cursor_bgColor_size' . $br;

		$size = $settings[ $ctrl_id ];

		$size_css = '';

		if ( ! empty( $size ) ) {

			if ( 'initial' === $size ) {
				$width = $settings[ 'pa_cursor_bgColor_bg_width' . $br ];

				$size_css .= "background-size: {$width['size']}{$width['unit']} auto;";

			} else {
				$size_css .= "background-size: {$size};";
			}
		}

		return $size_css;
	}

	/**
	 * Gets gradient background properties.
	 *
	 * @access private
	 * @since 2.8.0
	 *
	 * @param array $settings page settings.
	 *
	 * @return string
	 */
	private function get_grad_bg_props( $settings ) {

		$selector = ".premium-gCursor-yes .premium-global-cursor-{$pg_id} { ";

		$grad_type = $settings['pa_cursor_bgColor_gradient_type'];
		$grad_val  = 'linear' === $grad_type ? $settings['pa_cursor_bgColor_gradient_angle']['size'] . $settings['pa_cursor_bgColor_gradient_angle']['unit'] : 'at ' . $settings['pa_cursor_bgColor_gradient_position'];

		$color      = $settings['pa_cursor_bgColor_color'];
		$color_stop = $settings['pa_cursor_bgColor_color_stop'];

		$color_b      = $settings['pa_cursor_bgColor_color_b'];
		$color_b_stop = $settings['pa_cursor_bgColor_color_b_stop'];

		$bg_css = "background-color: transparent; background-image: {$grad_type}-gradient( {$grad_val}, {$color} {$color_stop['size']}{$color_stop['unit']}, {$color_b} {$color_b_stop['size']}{$color_b_stop['unit']});";

		return "{$selector} {$bg_css} }";
	}

	/**
	 * Get background image css.
	 *
	 * @access private
	 * @since 2.8.0
	 *
	 * @param string $img img value.
	 *
	 * @return string
	 */
	private function get_bg_image_css( $img ) {

		return "background-image: url('{$img['url']}');";
	}

	/**
	 * Get rotate css.
	 *
	 * @access private
	 * @since 2.8.0
	 *
	 * @param string $rotate rotate value.
	 *
	 * @return string
	 */
	private function get_rotate_css( $rotate ) {

		return "transform: rotate({$rotate}deg);";
	}

	/**
	 * Get dimension control css.
	 *
	 * @access private
	 * @since 2.8.0
	 *
	 * @param string $prop css property.
	 * @param string $ctrl_id control id.
	 * @param array  $settings page settings.
	 * @param string $breakpoint  device breakpoint.
	 *
	 * @return string
	 */
	private function get_dimension_ctrl_val( $prop, $ctrl_id, $settings, $breakpoint = '' ) {

		$res_ctrl = $ctrl_id . $breakpoint;

		$res_val = $settings[ $res_ctrl ];
		// TODO:: should be checked to only add when values are not empty.
		return "{$prop}: {$res_val['top']}{$res_val['unit']} {$res_val['right']}{$res_val['unit']} {$res_val['bottom']}{$res_val['unit']} {$res_val['left']}{$res_val['unit']};";
	}

	/**
	 * Get size control css.
	 *
	 * @access private
	 * @since 2.8.0
	 *
	 * @param string $prop css property.
	 * @param string $ctrl_id control id.
	 * @param array  $settings page settings.
	 * @param string $breakpoint  device breakpoint.
	 *
	 * @return string
	 */
	private function get_size_ctrl_val( $prop, $ctrl_id, $settings, $breakpoint = '' ) {

		$res_ctrl = $ctrl_id . $breakpoint;

		$res_val = 'background-image' === $prop ? $settings[ $res_ctrl ]['url'] : $settings[ $res_ctrl ]['size'];

		if ( ! empty( $res_val ) ) {

			if ( 'rotate' === $prop ) {
				return $this->get_rotate_css( $res_val );

			} elseif ( 'background-image' === $prop ) {
				return $this->get_bg_image_css( $settings[ $res_ctrl ] );

			} else {
				return "{$prop}: {$res_val['size']}{$res_val['unit']}";
			}
		}

		return '';
	}

	/**
	 * Get responsive css.
	 *
	 * @access private
	 * @since 2.8.0
	 *
	 * @param string $ctrl_type  control type.
	 * @param string $prop css property.
	 * @param string $ctrl_id control id.
	 * @param array  $settings page settings.
	 * @param string $pg_id page_id.
	 * @param string $selector_type  selector type.
	 *
	 * @return string
	 */
	private function get_responsive_style( $ctrl_type, $prop, $ctrl_id, $settings, $pg_id, $selector_type ) {

		$active_breakpoints = array_reverse( \Elementor\Plugin::$instance->breakpoints->get_active_breakpoints() );
		$selector           = 'custom' === $selector_type ? '' : ".premium-gCursor-yes .premium-global-cursor-{$pg_id} { ";
		$closer             = 'custom' === $selector_type ? '}' : '} }';
		$css                = '';

		foreach ( $active_breakpoints as $breakpoint => $value ) {

			$ctrl = $settings[ $ctrl_id . '_' . $breakpoint ];

			if ( isset( $ctrl ) ) {

				$px = $active_breakpoints[ $breakpoint ]->get_value() . 'px';

				$limit = 'widescreen' === $breakpoint ? 'min-width' : 'max-width';

				$css .= " @media ( {$limit}: {$px} ) { {$selector} ";

				switch ( $ctrl_type ) {
					case 'dimension':
						$css .= $this->get_dimension_ctrl_val( $prop, $ctrl_id, $settings, '_' . $breakpoint );
						break;

					case 'size':
					case 'img':
						$css .= $this->get_size_ctrl_val( $prop, $ctrl_id, $settings, '_' . $breakpoint );
						break;

					case 'background-position':
						$css .= $this->get_img_pos( $settings, '_' . $breakpoint );
						break;

					case 'background-size':
						$css .= $this->get_img_size( $settings, '_' . $breakpoint );
						break;

					case 'cursor_size':
						$css .= $this->get_cursor_size( $pg_id, $settings, '_' . $breakpoint );
						break;

					case 'typo':
						$css .= $this->get_cursor_typo_val( $prop, $pg_id, $settings, '_' . $breakpoint );
						break;

					default:
						if ( ! empty( $ctrl ) ) {
							$css .= "{$prop}: {$ctrl};";
						}
				}

				$css .= $closer;
			}
		}

		return $css;
	}

	/**
	 * Gets typography control values.
	 *
	 * @access private
	 * @since 2.8.0
	 *
	 * @param array  $settings page settings.
	 * @param string $id typography control id.
	 * @param string $pg_id page id.
	 *
	 * @return string
	 */
	private function get_typo_ctrl_val( $settings, $id, $pg_id ) {

		$selector = ".premium-gCursor-yes.premium-cursor-ftext .premium-global-cursor-{$pg_id} .premium-cursor-follow-text {";

		$typo_css = '';
		$responsive_typo_css;

		$ctrl_fields = array(
			'font_family',
			'font_size',
			'font_weight',
			'font_style',
			'text_transform',
			'text_decoration',
			'line_height',
			'letter_spacing',
			'word_spacing',
		);

		foreach ( $ctrl_fields as $field ) {

			$ctrl_id = $id . '_' . $field;

			if ( isset( $settings[ $ctrl_id ] ) ) {

				$val = $settings[ $ctrl_id ];

				if ( in_array( $field, array( 'font_size', 'line_height', 'letter_spacing', 'word_spacing' ), true ) ) {

					if ( ! empty( $val['size'] ) ) {
						$typo_css .= str_replace( '_', '-', $field ) . ': ' . $val['size'] . $val['unit'] . ';';
					}
				} else {

					if ( ! empty( $val ) ) {
						$typo_css .= str_replace( '_', '-', $field ) . ': ' . $val . ';';
					}
				}
			}
		}

		$typo_css = "{$selector} {$typo_css} }";

		$resp_font_size      = $this->get_responsive_style( 'typo', 'font-size', 'pa_cursor_typo_font_size', $settings, $pg_id, 'custom' );
		$resp_line_height    = $this->get_responsive_style( 'typo', 'line-height', 'pa_cursor_typo_line_height', $settings, $pg_id, 'custom' );
		$resp_letter_spacing = $this->get_responsive_style( 'typo', 'letter-spacing', 'pa_cursor_typo_letter_spacing', $settings, $pg_id, 'custom' );
		$resp_word_spacing   = $this->get_responsive_style( 'typo', 'word-spacing', 'pa_cursor_typo_word_spacing', $settings, $pg_id, 'custom' );

		$responsive_typo_css = $resp_font_size . $resp_line_height . $resp_letter_spacing . $resp_word_spacing;

		return $typo_css . $responsive_typo_css;
	}

	/**
	 * Get curosr typography value.
	 *
	 * @access private
	 * @since 2.8.0
	 *
	 * @param string $prop css property.
	 * @param string $pg_id page_id.
	 * @param array  $settings page settings.
	 * @param string $br device breakpoint.
	 *
	 * @return string
	 */
	private function get_cursor_typo_val( $prop, $pg_id, $settings, $br ) {

		$selector = ".premium-gCursor-yes.premium-cursor-ftext .premium-global-cursor-{$pg_id} .premium-cursor-follow-text {";

		$ctrl_id = 'pa_cursor_typo_' . str_replace( '-', '_', $prop ) . $br;

		$ctrl_val = $settings[ $ctrl_id ];

		$prop_css = "{$prop}: {$ctrl_val['size']}{$ctrl_val['unit']}";

		$css = "{$selector}  {$prop_css} }";

		return $css;
	}
}
