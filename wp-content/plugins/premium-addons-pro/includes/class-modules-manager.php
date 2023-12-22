<?php
/**
 * PAPRO Manager.
 */

namespace PremiumAddonsPro\Includes;

use PremiumAddons\Includes\Premium_Template_Tags;

use PremiumAddonsPro\Base\Module_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Manager.
 */
final class Manager {

	/**
	 * Template Instance
	 *
	 * @var template_instance
	 */
	protected $template_instance;

	/**
	 * Modules
	 *
	 * @var modules
	 */
	private $modules = array();

	/**
	 * Require Files.
	 *
	 * @since 1.6.1
	 * @access public
	 *
	 * @return void
	 */
	public function require_files() {
		require PREMIUM_PRO_ADDONS_PATH . 'base/module-base.php';
	}

	/**
	 * Register Modules.
	 *
	 * @since 1.6.1
	 * @access public
	 *
	 * @return void
	 */
	public function register_modules() {

		$modules = array(
			'premium-section-parallax',
			'premium-section-particles',
			'premium-section-gradient',
			'premium-section-kenburns',
			'premium-section-lottie',
			'premium-section-blob',
			'premium-global-cursor',
			'premium-global-badge',
			'premium-global-mscroll',
		);

		foreach ( $modules as $module_name ) {
			$class_name = str_replace( '-', ' ', $module_name );

			$class_name = str_replace( ' ', '', ucwords( $class_name ) );

			$class_name = 'PremiumAddonsPro\\Modules\\' . $class_name . '\Module';

			if ( $class_name::is_active() ) {
				$this->modules[ $module_name ] = $class_name::instance();
			}
		}

	}

	/**
	 * Run Modules Extender
	 *
	 * Extendes the free modules with extra options
	 *
	 * @since 2.6.0
	 * @access public
	 */
	public function run_modules_extender() {

		add_filter( 'papro_activated', '__return_true' );

		$this->template_instance = Premium_Template_Tags::getInstance();

		add_action( 'pa_floating_opacity_controls', array( $this, 'add_opacity_controls' ) );
		add_action( 'pa_floating_bg_controls', array( $this, 'add_bg_controls' ) );

		add_action( 'pa_floating_blur_controls', array( $this, 'add_blur_controls' ) );
		add_action( 'pa_floating_contrast_controls', array( $this, 'add_contrast_controls' ) );
		add_action( 'pa_floating_gs_controls', array( $this, 'add_gs_controls' ) );
		add_action( 'pa_floating_hue_controls', array( $this, 'add_hue_controls' ) );
		add_action( 'pa_floating_brightness_controls', array( $this, 'add_brightness_controls' ) );
		add_action( 'pa_floating_saturation_controls', array( $this, 'add_saturation_controls' ) );

		add_action( 'pa_custom_menu_controls', array( $this, 'add_custom_menu_controls' ), 10, 2 );

		// Extend Display Conditions Module.
		add_filter( 'pa_display_conditions', array( $this, 'extend_display_conditions_options' ) );
		add_filter( 'pa_display_conditions_keys', array( $this, 'extend_display_conditions_keys' ) );
		add_filter( 'pa_pro_display_conditions', array( $this, 'extend_pro_display_conditions' ) );

		// Extend Woo Product Listings Skins.
		add_filter( 'pa_pro_label', array( $this, 'extend_woo_skins' ) );

		// Extend Mega Menu - Random Badges.
		add_action( 'pa_rn_badges_controls', array( $this, 'add_random_badges_controls' ), 10, 2 );
		add_filter( 'pa_get_random_badges_settings', array( $this, 'get_random_badges_settings' ), 10 );

		// Extend Google Maps - Advanced Marker.
		add_action( 'pa_maps_marker_controls', array( $this, 'add_maps_marker_controls' ) );

		// Extend Terms Cloud widget controls.
		add_filter( 'pa_tcloud_layouts', array( $this, 'pa_tcloud_layouts' ) );
		add_action( 'pa_tcloud_shape_controls', array( $this, 'add_tcloud_shape_controls' ) );
		add_action( 'pa_tcloud_sphere_controls', array( $this, 'add_tcloud_sphere_controls' ) );

		// Extend Recent Posts Notification widget controls.
		add_filter( 'pa_notification_options', array( $this, 'pa_notification_options' ) );
		add_action( 'pa_notification_cats_controls', array( $this, 'add_notification_cats_controls' ) );

		// Extend World Clock widget controls.
		add_filter( 'pa_clock_options', array( $this, 'pa_clock_options' ) );

		// Extend News Ticker widget controls.
		add_filter( 'pa_ticker_options', array( $this, 'pa_ticker_options' ) );
		add_action( 'pa_ticker_stock_query', array( $this, 'add_ticker_stock_query' ) );
		add_action( 'pa_ticker_stock_controls', array( $this, 'add_ticker_stock_controls' ) );
		add_action( 'pa_ticker_stock_style', array( $this, 'add_ticker_stock_style' ) );

		// Extend Weather widget controls.
		add_filter( 'pa_weather_options', array( $this, 'pa_weather_options' ) );
		add_action( 'pa_weather_source_controls', array( $this, 'add_weather_source_controls' ) );
		add_action( 'pa_weather_daily_forecast_controls', array( $this, 'add_weather_dailyf_controls' ) );
		add_action( 'pa_weather_custom_icons_controls', array( $this, 'add_weather_custom_icons_controls' ) );

		// Extend Pinterest controls.
		add_filter( 'pa_pinterest_layouts', array( $this, 'pa_pinterest_layouts' ) );

		add_action( 'pa_pinterest_slide_align', array( $this, 'add_pinterest_slide_align' ) );
		add_action( 'pa_pinterest_render_dots', array( $this, 'add_pinterest_slide_dots' ) );
		add_action( 'pa_pinterest_dots_style', array( $this, 'add_pinterest_dots_style' ) );
		add_action( 'pa_pinterest_board_controls', array( $this, 'add_pinterest_board_controls' ) );
		add_action( 'pa_image_hover_effects', array( $this, 'add_image_hover_effects' ) );
		add_action( 'pa_pinterest_board_style', array( $this, 'add_pinterest_board_style' ) );
		add_action( 'pa_pinterest_profile_controls', array( $this, 'add_pinterest_profile_controls' ) );
		add_action( 'pa_pinterest_profile_style', array( $this, 'add_pinterest_profile_style' ) );

		// Extend TikTok controls.
		add_filter( 'pa_tiktok_layouts', array( $this, 'pa_tiktok_layouts' ) );

		add_action( 'pa_tiktok_load_more_options', array( $this, 'add_tiktok_loadmore_options' ) );
		add_action( 'pa_tiktok_loadmore_style', array( $this, 'add_tiktok_loadmore_style' ) );
		add_action( 'pa_tiktok_profile_controls', array( $this, 'add_tiktok_profile_controls' ) );
		add_action( 'pa_tiktok_profile_style', array( $this, 'add_tiktok_profile_style' ) );

		// Extend Global Tooltip controls.
		add_action( 'pa_tooltips_type_controls', array( $this, 'add_tooltips_type_controls' ) );

		// Extend CF7 controls.
		add_action( 'pa_cf_presets_options', array( $this, 'add_cf_presets_controls' ) );
		add_action( 'pa_cf_effects_options', array( $this, 'add_cf_effects_controls' ) );

		// Extend Media Carousel controls.
		add_action( 'pa_adv_carousel_options', array( $this, 'add_adv_carousel_controls' ) );
		add_action( 'pa_adv_carousel_navigation', array( $this, 'add_adv_carousel_effects_navigation' ) );

        // Extend Testimonials controls.
        add_action( 'pa_testimonials_skins_options', array( $this, 'add_testimonials_skins_controls' ) );

        // Extend Animated Shape Divider controls.
        add_action( 'pa_divider_custom_svg', array( $this, 'add_shape_divider_svg' ) );
        add_action( 'pa_divider_fill_controls', array( $this, 'add_shape_divider_fill' ) );

        // Extend Textual Showcase controls.
        add_action( 'pa_showcase_svg', array( $this, 'add_showcase_svg' ) );
        add_action( 'pa_showcase_svg_hover', array( $this, 'add_showcase_svg_hover' ) );
        add_filter(  'pa_showcase_highlights' , array( $this, 'add_showcase_highlights' ) );
	}

	/**
	 * Get random badges settings.
	 *
	 * @since 2.8.10
	 * @access public
	 *
	 * @param array $settings widget settings.
	 *
	 * @return array $badges_settings settings.
	 */
	public function get_random_badges_settings( $settings ) {

		$badges = $settings['rn_badges'];

		$badges_settings = array();

		foreach ( $badges as $index => $badge ) {

			$options = array(
				'id'       => $badge['_id'],
				'text'     => $badge['rn_badge_text'],
				'max'      => $badge['rn_badge_max'],
				'selector' => $badge['rn_badge_target'],
			);

			array_push( $badges_settings, $options );
		}

		return $badges_settings;

	}

	/**
	 * Add Random Badges Controls
	 *
	 * @since 2.8.10
	 * @access public
	 *
	 * @param object $element elementor element.
	 */
	public function add_random_badges_controls( $element ) {

		$element->add_control(
			'rn_badge_enabled',
			array(
				'label'       => __( 'Enable Random Badges', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'This option allows you to add badges that appear randomly on your menu items', 'premium-addons-pro' ),
			)
		);

		$badges = new Repeater();

		$badges->add_control(
			'rn_badge_text',
			array(
				'label'   => __( 'Text', 'premium-addons-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'New', 'premium-addons-pro' ),
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$badges->add_control(
			'rn_badge_target',
			array(
				'label'   => __( 'CSS Selector', 'premium-addons-pro' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$badges->add_control(
			'rn_badge_max',
			array(
				'label'       => __( 'Max Number to Apply This Badge', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Set a maximum number that this badge should show.', 'premium-addons-pro' ),
				'default'     => 3,

			)
		);

		$badges->add_control(
			'rn_badge_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$badges->add_control(
			'rn_badge_bg',
			array(
				'label'     => __( 'Backgroud Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'background: {{VALUE}} !important;',
				),
			)
		);

		$element->add_control(
			'rn_badges',
			array(
				'label'         => __( 'Badges', 'premium-addons-pro' ),
				'type'          => Controls_Manager::REPEATER,
				'show_label'    => true,
				'fields'        => $badges->get_controls(),
				'title_field'   => '{{{ rn_badge_text }}}',
				'separator'     => 'after',
				'prevent_empty' => false,
				'condition'     => array(
					'rn_badge_enabled' => 'yes',
				),
			)
		);
	}

	/**
	 * Add Custom Menu Controls
	 * Adds repeater controls for mega menu widget.
	 *
	 * @access public
	 * @since 2.7.6
	 *
	 * @param object $elem elementor element.
	 * @param object $repeater repeater element.
	 */
	public function add_custom_menu_controls( $elem, $repeater ) {

		$elem->add_control(
			'menu_items',
			array(
				'label'       => __( 'Menu Items', 'premium-addons-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'show_label'  => true,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'item_type' => 'menu',
						'text'      => __( 'Menu Item 1', 'premium-addons-pro' ),
					),
					array(
						'item_type' => 'submenu',
						'text'      => __( 'Sub Menu', 'premium-addons-pro' ),
					),
					array(
						'item_type' => 'menu',
						'text'      => __( 'Menu Item 2', 'premium-addons-pro' ),
					),
					array(
						'item_type' => 'submenu',
						'text'      => __( 'Sub Menu', 'premium-addons-pro' ),
					),
				),
				'title_field' => '{{{ text }}}',
				'separator'   => 'before',
				'condition'   => array(
					'menu_type' => 'custom',
				),
			)
		);

	}

	/**
	 * Extend woo skins.
	 * Removes the ( PRO ) label from woo skins' title.
	 *
	 * @access public
	 * @since 2.6.6
	 *
	 * @param string $skin skin title.
	 *
	 * @return string
	 */
	public function extend_woo_skins( $skin ) {

		return preg_replace( '/\((PRO|Pro)\)/i', '', $skin );
	}

	/**
	 * Extend Terms Cloud layouts.
	 *
	 * @access public
	 * @since 2.6.6
	 *
	 * @return array $layouts widget layouts.
	 */
	public function pa_tcloud_layouts() {

		$options = array(
			'layouts'          => array(
				'default' => __( 'Default', 'premium-addons-pro' ),
				'ribbon'  => __( 'Label', 'premium-addons-pro' ),
				'shape'   => __( 'Shape', 'premium-addons-pro' ),
				'sphere'  => __( 'Sphere', 'premium-addons-pro' ),
			),
			'order_condition'  => '',
			'source_condition' => array(),
		);

		return $options;
	}

	/**
	 * Add Terms Cloud Shape Controls
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_tcloud_shape_controls( $elem ) {

		$elem->add_control(
			'shape',
			array(
				'label'              => __( 'Shape', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'label_block'        => true,
				'options'            => array(
					'circle'           => __( 'Circle', 'premium-addons-pro' ),
					'square'           => __( 'Square', 'premium-addons-pro' ),
					'diamond'          => __( 'Diamond', 'premium-addons-pro' ),
					'triangle'         => __( 'Triangle', 'premium-addons-pro' ),
					'triangle-forward' => __( 'Triangle Forward', 'premium-addons-pro' ),
					'cardioid'         => __( 'Cardioid', 'premium-addons-pro' ),
					'pentagon'         => __( 'Pentagon', 'premium-addons-pro' ),
					'star'             => __( 'Star', 'premium-addons-pro' ),
				),
				'default'            => 'circle',
				'separator'          => 'before',
				'frontend_available' => true,
				'condition'          => array(
					'words_order' => 'shape',
				),
			)
		);

		$elem->add_responsive_control(
			'width',
			array(
				'label'       => __( 'Width (PX)', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min' => 1,
						'max' => 1000,
					),
				),
				'render_type' => 'template',
				'selectors'   => array(
					'{{WRAPPER}} .premium-tcloud-canvas' => 'width: {{SIZE}}px; --pa-tcloud-width: {{SIZE}}',
				),
				'condition'   => array(
					'words_order!' => array( 'default', 'ribbon' ),
				),
			)
		);

		$elem->add_responsive_control(
			'height',
			array(
				'label'       => __( 'Height (PX)', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min' => 1,
						'max' => 1000,
					),
				),
				'render_type' => 'template',
				'selectors'   => array(
					'{{WRAPPER}} .premium-tcloud-canvas' => 'height: {{SIZE}}px; --pa-tcloud-height: {{SIZE}}',
				),
				'condition'   => array(
					'words_order!' => array( 'default', 'ribbon' ),
				),
			)
		);

		$elem->add_control(
			'grid_size',
			array(
				'label'              => __( 'Words Spacing', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SLIDER,
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'default'            => array(
					'size' => 8,
				),
				'frontend_available' => true,
				'condition'          => array(
					'words_order' => 'shape',
				),
			)
		);

		$elem->add_control(
			'weight_scale',
			array(
				'label'              => __( 'Scale', 'premium-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 5,
				'frontend_available' => true,
				'condition'          => array(
					'words_order' => 'shape',
				),
			)
		);

		$elem->add_control(
			'rotation_select',
			array(
				'label'              => __( 'Rotation', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'default'    => __( 'Default', 'premium-addons-pro' ),
					'horizontal' => __( 'Horizontal', 'premium-addons-pro' ),
					'vertical'   => __( 'Vertical', 'premium-addons-pro' ),
					'hv'         => __( 'Horizontal and Vertical', 'premium-addons-pro' ),
					'random'     => __( 'Random', 'premium-addons-pro' ),
					'custom'     => __( 'Custom', 'premium-addons-pro' ),
				),
				'default'            => 'default',
				'frontend_available' => true,
				'condition'          => array(
					'words_order' => 'shape',
				),
			)
		);

		$elem->add_control(
			'rotation',
			array(
				'label'              => __( 'Rotation Ratio', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SLIDER,
				'description'        => __( 'The ratio between rotated words to horizontal words.', 'premium-addons-pro' ),
				'range'              => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					),
				),
				'default'            => array(
					'size' => 0.3,
				),
				'frontend_available' => true,
				'condition'          => array(
					'rotation_select' => 'custom',
					'words_order'     => 'shape',
				),
			)
		);

		$elem->add_control(
			'degrees',
			array(
				'label'              => __( 'Rotation Degrees', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SLIDER,
				'range'              => array(
					'px' => array(
						'min' => -180,
						'max' => 180,
					),
				),
				'frontend_available' => true,
				'condition'          => array(
					'rotation_select' => 'custom',
					'words_order'     => 'shape',
				),
			)
		);

		$elem->add_control(
			'font_family',
			array(
				'label'              => __( 'Font Family', 'premium-addons-pro' ),
				'type'               => Controls_Manager::FONT,
				'frontend_available' => true,
				'render_type'        => 'template',
				'selectors'          => array(
					'{{WRAPPER}} .font-loader' => 'font-family: "{{VALUE}}", Sans-serif',
				),
				'condition'          => array(
					'words_order!' => array( 'default', 'ribbon' ),
				),
			)
		);

		$elem->add_control(
			'font_weight',
			array(
				'label'              => __( 'Font Weight', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'frontend_available' => true,
				'render_type'        => 'template',
				'options'            => array(
					'100'    => '100 ' . esc_html_x( '(Thin)', 'Typography Control', 'premium-addons-pro' ),
					'200'    => '200 ' . esc_html_x( '(Extra Light)', 'Typography Control', 'premium-addons-pro' ),
					'300'    => '300 ' . esc_html_x( '(Light)', 'Typography Control', 'premium-addons-pro' ),
					'400'    => '400 ' . esc_html_x( '(Normal)', 'Typography Control', 'premium-addons-pro' ),
					'500'    => '500 ' . esc_html_x( '(Medium)', 'Typography Control', 'premium-addons-pro' ),
					'600'    => '600 ' . esc_html_x( '(Semi Bold)', 'Typography Control', 'premium-addons-pro' ),
					'700'    => '700 ' . esc_html_x( '(Bold)', 'Typography Control', 'premium-addons-pro' ),
					'800'    => '800 ' . esc_html_x( '(Extra Bold)', 'Typography Control', 'premium-addons-pro' ),
					'900'    => '900 ' . esc_html_x( '(Black)', 'Typography Control', 'premium-addons-pro' ),
					''       => esc_html_x( 'Default', 'Typography Control', 'premium-addons-pro' ),
					'normal' => esc_html_x( 'Normal', 'Typography Control', 'premium-addons-pro' ),
					'bold'   => esc_html_x( 'Bold', 'Typography Control', 'premium-addons-pro' ),
				),
				'condition'          => array(
					'words_order!' => array( 'default', 'ribbon' ),
				),
			)
		);

		$elem->add_control(
			'text_transform',
			array(
				'label'       => __( 'Text Transform', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					''           => __( 'Default', 'premium-addons-pro' ),
					'uppercase'  => __( 'Uppercase', 'premium-addons-pro' ),
					'lowercase'  => __( 'Lowercase', 'premium-addons-pro' ),
					'capitalize' => __( 'Capitalize', 'premium-addons-pro' ),
				),
				'render_type' => 'template',
				'condition'   => array(
					'words_order!' => array( 'default', 'ribbon' ),
				),
			)
		);

		$elem->add_control(
			'text_height',
			array(
				'label'              => __( 'Text Height', 'premium-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'frontend_available' => true,
				'condition'          => array(
					'words_order'    => 'sphere',
					'sphere_weight!' => 'yes',
				),
			)
		);

		$elem->add_control(
			'sphere_weight',
			array(
				'label'              => __( 'Scale Font Size', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'description'        => __( 'This option is used to increase the font size of each term based on the number of posts in it.', 'premium-addons-pro' ),
				'frontend_available' => true,
				'condition'          => array(
					'words_order' => 'sphere',
				),
			)
		);

		$elem->add_control(
			'weight_min',
			array(
				'label'              => __( 'Minimum Font Size (px)', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'unit' => 'px',
					'size' => 10,
				),
				'frontend_available' => true,
				'condition'          => array(
					'words_order'   => 'sphere',
					'sphere_weight' => 'yes',
				),
			)
		);

		$elem->add_control(
			'weight_max',
			array(
				'label'              => __( 'Maximum Font Size (px)', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'unit' => 'px',
					'size' => 20,
				),
				'frontend_available' => true,
				'condition'          => array(
					'words_order'   => 'sphere',
					'sphere_weight' => 'yes',
				),
			)
		);

		$elem->add_control(
			'wheel_zoom',
			array(
				'label'              => __( 'Mouse Wheel Zoom', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition'          => array(
					'words_order' => 'sphere',
				),
			)
		);

		$elem->add_control(
			'reverse',
			array(
				'label'              => __( 'Reverse', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition'          => array(
					'words_order'   => 'sphere',
					'drag_control!' => 'yes',
				),
			)
		);

		$elem->add_control(
			'drag_control',
			array(
				'label'              => __( 'Drag Control', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition'          => array(
					'words_order' => 'sphere',
				),
			)
		);

		$elem->add_control(
			'stop_onDrag',
			array(
				'label'              => __( 'Stop Animation After Drag', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'yes',
				'frontend_available' => true,
				'condition'          => array(
					'words_order'  => 'sphere',
					'drag_control' => 'yes',
				),
			)
		);

		$elem->add_control(
			'start_xspeed',
			array(
				'label'              => __( 'Start Horizontal Rotate Speed', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SLIDER,
				'description'        => __( 'Use this option to control the initial horizontal rotation of the sphere.', 'premium-addons-pro' ),
				'default'            => array(
					'unit' => 'px',
					'size' => 0,
				),
				'range'              => array(
					'px' => array(
						'min'  => -5,
						'max'  => 5,
						'step' => 0.1,
					),
				),
				'frontend_available' => true,
				'condition'          => array(
					'words_order' => 'sphere',
				),
			)
		);

		$elem->add_control(
			'start_yspeed',
			array(
				'label'              => __( 'Start Vertical Rotate Speed', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SLIDER,
				'description'        => __( 'Use this option to control the initial vertical rotation of the sphere.', 'premium-addons-pro' ),
				'default'            => array(
					'unit' => 'px',
					'size' => 0,
				),
				'range'              => array(
					'px' => array(
						'min'  => -5,
						'max'  => 5,
						'step' => 0.1,
					),
				),
				'frontend_available' => true,
				'condition'          => array(
					'words_order' => 'sphere',
				),
			)
		);

		$elem->add_control(
			'interval',
			array(
				'label'              => __( 'Time between Rendering Words (sec)', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SLIDER,
				'description'        => __( 'Use this option to set the time before rendering the next word.', 'premium-addons-pro' ),
				'default'            => array(
					'unit' => 'px',
					'size' => 0,
				),
				'range'              => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					),
				),
				'frontend_available' => true,
				'condition'          => array(
					'words_order' => 'shape',
				),
			)
		);

	}

	/**
	 * Add Terms Cloud Sphere Controls
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_tcloud_sphere_controls( $elem ) {

		$elem->add_control(
			'colors_target',
			array(
				'label'              => __( 'Apply Colors On', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'text'       => __( 'Text', 'premium-addons-for-elementor' ),
					'background' => __( 'Background', 'premium-addons-for-elementor' ),
				),
				'default'            => 'text',
				'frontend_available' => true,
				'condition'          => array(
					'words_order' => 'sphere',
				),
			)
		);

		$elem->add_control(
			'sphere_term_color',
			array(
				'label'       => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'{{WRAPPER}} .premium-tcloud-term-link' => 'color: {{VALUE}}',
				),
				'render_type' => 'template',
				'condition'   => array(
					'words_order'   => 'sphere',
					'colors_target' => 'background',
				),
			)
		);

		$elem->add_control(
			'sphere_term_padding',
			array(
				'label'              => __( 'Padding (px)', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SLIDER,
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'default'            => array(
					'size' => 20,
				),
				'frontend_available' => true,
				'condition'          => array(
					'words_order'   => 'sphere',
					'colors_target' => 'background',
				),
			)
		);

		$elem->add_control(
			'sphere_term_radius',
			array(
				'label'              => __( 'Radius (px)', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SLIDER,
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'default'            => array(
					'size' => 5,
				),
				'frontend_available' => true,
				'condition'          => array(
					'words_order'   => 'sphere',
					'colors_target' => 'background',
				),
			)
		);

	}

	/**
	 * Extend Recent Posts Notification Options.
	 *
	 * @access public
	 * @since 2.6.6
	 *
	 * @return array $layouts widget layouts.
	 */
	public function pa_notification_options() {

		$options = array(
			'skins'            => array(
				'classic' => __( 'Classic', 'premium-addons-pro' ),
				'modern'  => __( 'Modern', 'premium-addons-pro' ),
				'cards'   => __( 'Cards', 'premium-addons-pro' ),
				'banner'  => __( 'Banner', 'premium-addons-pro' ),
			),
			'skin_condition'   => '',
			'source_condition' => array(),
		);

		return $options;
	}

	/**
	 * Add Recent Notification Categories Controls
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_notification_cats_controls( $elem ) {

		$elem->start_controls_section(
			'post_categories_style_section',
			array(
				'label'     => __( 'Categories', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_blog_skin'            => 'banner',
					'premium_blog_categories_meta' => 'yes',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'category_typography',
				'selector' => '{{WRAPPER}} .premium-blog-cats-container a',
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'category_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}}',
				),
			)
		);

		$repeater->add_control(
			'category_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$repeater->add_control(
			'category_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'background-color: {{VALUE}}',
				),
			)
		);

		$repeater->add_control(
			'category_hover_background_color',
			array(
				'label'     => __( 'Hover Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}:hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$repeater->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'category_border',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}',
			)
		);

		$repeater->add_control(
			'category_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$elem->add_control(
			'categories_repeater',
			array(
				'label'       => __( 'Categories', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'category_background_color' => '',
					),
				),
				'render_type' => 'ui',
				'condition'   => array(
					'premium_blog_skin'            => 'banner',
					'premium_blog_categories_meta' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'categories_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-cats-container a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$elem->add_responsive_control(
			'categories_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-cats-container a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$elem->end_controls_section();

	}

	/**
	 * Extend World Clock Options.
	 *
	 * @access public
	 * @since 2.6.6
	 *
	 * @return array $layouts widget layouts.
	 */
	public function pa_clock_options() {

		$options = array(
			'skins'          => array(
				'digital' => array(
					'label'   => __( 'Digital', 'premium-addons-pro' ),
					'options' => array(
						'skin-2' => __( 'Layout 1', 'premium-addons-pro' ),
						'skin-3' => __( 'Layout 2', 'premium-addons-pro' ),
						'skin-4' => __( 'Layout 3', 'premium-addons-pro' ),
					),
				),
				'analog'  => array(
					'label'   => __( 'Analog', 'premium-addons-pro' ),
					'options' => array(
						'skin-1' => __( 'Style 1', 'premium-addons-pro' ),
						'skin-5' => __( 'Style 2', 'premium-addons-pro' ),
						'skin-6' => __( 'Style 3', 'premium-addons-pro' ),
						'skin-7' => __( 'Style 4', 'premium-addons-pro' ),
					),
				),
			),
			'skin_condition' => '',
		);

		return $options;
	}

	/**
	 * Extend News Ticker Options.
	 *
	 * @access public
	 * @since 2.6.6
	 *
	 * @return array $layouts widget layouts.
	 */
	public function pa_ticker_options() {

		$options = array(
			'layouts'          => array(
				'layout-1' => __( 'Layout 1', 'premium-addons-for-elementor' ),
				'layout-2' => __( 'Layout 2', 'premium-addons-for-elementor' ),
				'layout-3' => __( 'Layout 3', 'premium-addons-for-elementor' ),
				'layout-4' => __( 'Layout 4', 'premium-addons-for-elementor' ),
			),
			'layout_condition' => array(),
		);

		return $options;
	}

	/**
	 * Add Ticker Stock Query
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_ticker_stock_query( $elem ) {

		$elem->add_control(
			'req_function',
			array(
				'label'     => __( 'Type', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'CURRENCY_EXCHANGE_RATE' => __( 'Currencies', 'premium-addons-for-elementor' ),
					'GLOBAL_QUOTE'           => __( 'Equities', 'premium-addons-for-elementor' ),
				),
				'default'   => 'GLOBAL_QUOTE',
				'condition' => array(
					'post_type_filter' => 'stock',
				),
			)
		);

		$elem->add_control(
			'currency_ex_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Returns the realtime exchange rate for any pair of digital currency (e.g., Bitcoin) or physical currency (e.g., USD). <b>Cryptocurrecies must start with "/", e.g (/BTC)', 'premium-addons-for-elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'post_type_filter' => 'stock',
					'req_function'     => 'CURRENCY_EXCHANGE_RATE',
				),
			)
		);

		$elem->add_control(
			'equity_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Returns the stock price details for a token/symbol of your choice.', 'premium-addons-for-elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'post_type_filter' => 'stock',
					'req_function'     => 'GLOBAL_QUOTE',
				),
			)
		);

		$elem->add_control(
			'api_key',
			array(
				'label'       => __( 'API Key', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => 'Get your Alpha Vintage <b>Free API Key</b> from <a href="https://www.alphavantage.co/support/#api-key" target="_blank">here</a>',
				'condition'   => array(
					'post_type_filter' => 'stock',
				),
			)
		);

		$elem->add_control(
			'symbol',
			array(
				'label'       => __( 'Symbol', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Enter the stock tokens/symbols you want to query up to 5 symbols separated by ",". Example: AAPL,MSFT,INTC', 'premium-addons-for-elementor' ),
				'label_block' => true,
				'default'     => 'AAPL,MSFT,INTC',
				'condition'   => array(
					'post_type_filter' => 'stock',
					'req_function'     => 'GLOBAL_QUOTE',
				),
			)
		);

		$elem->add_control(
			'from_currency',
			array(
				'label'       => __( 'Exchange From', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Enter the currency you want exchange from up to 5 symbols separated by ",". Example: USD,EUR,GBP', 'premium-addons-for-elementor' ),
				'label_block' => true,
				'default'     => 'USD,EUR,GBP',
				'condition'   => array(
					'post_type_filter' => 'stock',
					'req_function'     => 'CURRENCY_EXCHANGE_RATE',
				),
			)
		);

		$elem->add_control(
			'to_currency',
			array(
				'label'       => __( 'Exchange To', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'EUR,JPY,CAD',
				'description' => __( 'Enter the currencies you want to exchange to separated by ",". Example: EUR,JPY,CAD', 'premium-addons-for-elementor' ),
				'label_block' => true,
				'condition'   => array(
					'post_type_filter' => 'stock',
					'req_function'     => 'CURRENCY_EXCHANGE_RATE',
				),
			)
		);

		$elem->add_control(
			'curr_change',
			array(
				'label'       => __( 'Show Change Details', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Display the change details depending on the previously cached data.', 'premium-addons-for-elementor' ),
				// 'render_type'  => 'template',
				'condition'   => array(
					'post_type_filter' => 'stock',
					'req_function'     => 'CURRENCY_EXCHANGE_RATE',
				),
			)
		);

		$elem->add_control(
			'reload',
			array(
				'label'     => __( 'Reload Data Once Every', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'separator' => 'before',
				'options'   => array(
					1  => __( 'Hour', 'premium-addons-for-elementor' ),
					3  => __( '3 Hours', 'premium-addons-for-elementor' ),
					6  => __( '6 Hours', 'premium-addons-for-elementor' ),
					12 => __( '12 Hours', 'premium-addons-for-elementor' ),
					24 => __( 'Day', 'premium-addons-for-elementor' ),
					48 => __( '2 Days', 'premium-addons-for-elementor' ),
				),
				'default'   => 3,
				'condition' => array(
					'post_type_filter' => 'stock',
				),
			)
		);

		$elem->add_control(
			'gold_api_key',
			array(
				'label'       => __( 'API Key', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => 'Get your <b>Free Gold API Key</b> from <a href="https://www.goldapi.io" target="_blank">here</a> or <a href="https://metalpriceapi.com/" target="_blank">here</a>',
				'condition'   => array(
					'post_type_filter' => 'gold',
				),
			)
		);

		$elem->add_control(
			'alter_api_key',
			array(
				'label'       => __( 'Alternative API Key', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => 'This API key will be used in case the quota for the default API key was exceeded. Get your <b>Free Gold API Key</b> from <a href="https://www.goldapi.io" target="_blank">here</a> or <a href="https://metalpriceapi.com/" target="_blank">here</a>',
				'condition'   => array(
					'post_type_filter' => 'gold',
				),
			)
		);

		$elem->add_control(
			'currencies',
			array(
				'label'       => __( 'Currencies', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Enter the Currencies symbols you want to query up to 5 symbols separated by ",". Example: USD,EUR,JPY, You can check the available currencies <a href="https://www.goldapi.io/dashboard" target="_blank">here -> Currency Code.</a>', 'premium-addons-for-elementor' ),
				'label_block' => true,
				'default'     => 'USD,EUR,JPY',
				'condition'   => array(
					'post_type_filter' => 'gold',
				),
			)
		);

		$elem->add_control(
			'gold_reload',
			array(
				'label'     => __( 'Reload Data Once Every', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'separator' => 'before',
				'options'   => array(
					12 => __( '12 Hours', 'premium-addons-for-elementor' ),
					24 => __( 'Day', 'premium-addons-for-elementor' ),
					48 => __( '2 Days', 'premium-addons-for-elementor' ),
				),
				'default'   => 24,
				'condition' => array(
					'post_type_filter' => 'gold',
				),
			)
		);

	}

	/**
	 * Add Ticker Stock Controls
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_ticker_stock_controls( $elem ) {
		$common_cond = array(
			'relation' => 'or',
			'terms'    => array(
				array(
					'name'  => 'post_type_filter',
					'value' => 'gold',
				),
				array(
					'terms' => array(
						array(
							'name'  => 'post_type_filter',
							'value' => 'stock',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'req_function',
									'value' => 'GLOBAL_QUOTE',
								),
								array(
									'terms' => array(
										array(
											'name'  => 'req_function',
											'value' => 'CURRENCY_EXCHANGE_RATE',
										),
										array(
											'name'  => 'curr_change',
											'value' => 'yes',
										),
									),
								),
							),
						),
					),
				),
			),
		);

		$elem->start_controls_section(
			'pa_ticker_Stock_section',
			array(
				'label'     => __( 'Stock Options', 'premium-addons-for-elementor' ),
				'condition' => array(
					'post_type_filter' => array( 'stock', 'gold' ),
				),
			)
		);

		$elem->add_control(
			'show_symbol_icon',
			array(
				'label'     => __( 'Show Symbol Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'post_type_filter' => 'stock',
				),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'symbol_name',
			array(
				'label'       => __( 'Symbol', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Use this to add the currency/company symbol that the image below will be linked to. For example, USD or AAPL.', 'premium-addons-for-elementor' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'symbol_img',
			array(
				'label' => __( 'Symbol Image', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$elem->add_control(
			'symbol_icons_repeater',
			array(
				'label'       => __( 'Custom Symbols Icons', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ symbol_name }}}',
				'condition'   => array(
					'post_type_filter' => 'stock',
					'show_symbol_icon' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'symbol_icon_size',
			array(
				'label'     => __( 'Icon Size (px)', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 30,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-post-ticker__symbol-icon'  => 'width: {{SIZE}}px',
				),
				'condition' => array(
					'post_type_filter' => 'stock',
					'show_symbol_icon' => 'yes',
				),
			)
		);

		$elem->add_control(
			'symbol_names_sw',
			array(
				'label'     => __( 'Show Symbols Names', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'req_function'     => 'GLOBAL_QUOTE',
					'post_type_filter' => 'stock',
				),
			)
		);

		$elem->add_control(
			'symbol_name',
			array(
				'label'       => __( 'Symbols Names', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Enter the stock tokens\' names you want to display corresponding to the above symbols and separated by ",". Example: Apple Inc,Microsoft Corp,Intel Corporation', 'premium-addons-for-elementor' ),
				'label_block' => true,
				'default'     => 'Apple Inc,Microsoft Corp,Intel Corporation',
				'condition'   => array(
					'req_function'     => 'GLOBAL_QUOTE',
					'symbol_names_sw'  => 'yes',
					'post_type_filter' => 'stock',
				),
			)
		);

		$elem->add_control(
			'show_symbol',
			array(
				'label'     => __( 'Show Symbol', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'req_function'     => 'GLOBAL_QUOTE',
					'post_type_filter' => 'stock',
				),
			)
		);

		$elem->add_control(
			'show_price',
			array(
				'label'   => __( 'Show Price', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$elem->add_control(
			'show_change',
			array(
				'label'      => __( 'Show Change', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SWITCHER,
				'default'    => 'yes',
				'conditions' => $common_cond,
			)
		);

		$elem->add_control(
			'show_change_per',
			array(
				'label'      => __( 'Show Change Percent', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SWITCHER,
				'default'    => 'yes',
				'conditions' => $common_cond,
			)
		);

		$elem->add_control(
			'change_indicator',
			array(
				'label'      => __( 'Change Indicator', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SELECT,
				'options'    => array(
					'sign'  => __( '+/- Sign', 'premium-addons-for-elementor' ),
					'arrow' => __( 'Arrow', 'premium-addons-for-elementor' ),
				),
				'default'    => 'arrow',
				'conditions' => $common_cond,
			)
		);

		$elem->add_control(
			'arrow_style',
			array(
				'label'      => __( 'Arrow Style', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SELECT,
				'options'    => array(
					'fas fa-caret'          => __( 'Style 1', 'premium-addons-for-elementor' ),
					'fas fa-long-arrow-alt' => __( 'Style 2', 'premium-addons-for-elementor' ),
					'fas fa-arrow'          => __( 'Style 3', 'premium-addons-for-elementor' ),
					'fas fa-chevron'        => __( 'Style 4', 'premium-addons-for-elementor' ),
				),
				'default'    => 'fas fa-long-arrow-alt',
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'change_indicator',
							'value' => 'arrow',
						),
						$common_cond,
					),
				),
			)
		);

		$elem->add_control(
			'decimal_places',
			array(
				'label'   => __( 'Decimal Places', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'default' => 2,
			)
		);

		$elem->add_responsive_control(
			'stock_ele_min_width',
			array(
				'label'       => __( 'Minimum Width (px)', 'premium-addons-for-elementor' ),
				'description' => __( 'Use this option to add equal spacing to the element data.', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-post-ticker__post-wrapper span'  => 'min-width: {{SIZE}}px',
				),
				'condition'   => array(
					'layout' => 'layout-4',
				),
			)
		);

		$elem->end_controls_section();

	}

	/**
	 * Add Ticker Stock Style
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_ticker_stock_style( $elem ) {

		$stock_gold_cond = array(
			'terms' => array(
				array(
					'name'  => 'post_type_filter',
					'value' => 'stock',
				),
				array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'req_function',
							'value' => 'CURRENCY_EXCHANGE_RATE',
						),
						array(
							'terms' => array(
								array(
									'name'  => 'req_function',
									'value' => 'GLOBAL_QUOTE',
								),
								array(
									'name'  => 'show_symbol',
									'value' => 'yes',
								),
							),
						),
					),
				),
			),
		);

		$stock_gold_cond = array(
			'terms' => array(
				array(
					'name'  => 'post_type_filter',
					'value' => 'stock',
				),
				array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'req_function',
							'value' => 'CURRENCY_EXCHANGE_RATE',
						),
						array(
							'terms' => array(
								array(
									'name'  => 'req_function',
									'value' => 'GLOBAL_QUOTE',
								),
								array(
									'name'  => 'show_symbol',
									'value' => 'yes',
								),
							),
						),
					),
				),
			),
		);

		$elem->add_control(
			'pa_stock_name_heading',
			array(
				'label'     => __( 'Name', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'post_type_filter' => 'stock',
					'req_function'     => 'GLOBAL_QUOTE',
					'symbol_names_sw'  => 'yes',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'pa_symbol_name_typo',
				'selector'  => '{{WRAPPER}} .premium-post-ticker__symbol-name',
				'condition' => array(
					'post_type_filter' => 'stock',
					'req_function'     => 'GLOBAL_QUOTE',
					'symbol_names_sw'  => 'yes',
				),
			)
		);

		$elem->add_control(
			'pa_symbol_name_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-post-ticker__symbol-name'  => 'color: {{VALUE}};',
				),
				'separator' => 'after',
				'condition' => array(
					'symbol_names_sw'  => 'yes',
					'post_type_filter' => 'stock',
					'req_function'     => 'GLOBAL_QUOTE',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'symbol_icon_shadow',
				'label'     => __( 'Symbol Icon Shadow', 'premium-addons-for-elementor' ),
				'selector'  => '{{WRAPPER}} .premium-post-ticker__symbol-icon',
				'condition' => array(
					'post_type_filter' => 'stock',
					'show_symbol_icon' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'symbol_icon_margin',
			array(
				'label'      => __( 'Symbol Icon Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__symbol-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'post_type_filter' => 'stock',
					'show_symbol_icon' => 'yes',
				),
			)
		);

		$elem->add_control(
			'pa_symbol_heading',
			array(
				'label'     => __( 'Symbol', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'post_type_filter' => array( 'stock', 'gold' ),
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'       => 'pa_symbol_typo',
				'selector'   => '{{WRAPPER}} .premium-post-ticker__symbol',
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'post_type_filter',
							'value' => 'gold',
						),
						$stock_gold_cond,
					),
				),
			)
		);

		$elem->add_control(
			'pa_symbol_color',
			array(
				'label'      => __( 'Color', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__symbol'  => 'color: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'post_type_filter',
							'value' => 'gold',
						),
						$stock_gold_cond,
					),
				),
			)
		);

		$elem->add_responsive_control(
			'pa_symbol_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__symbol' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'post_type_filter',
							'value' => 'gold',
						),
						$stock_gold_cond,
					),
				),
			)
		);

		$elem->add_control(
			'pa_price_details_heading',
			array(
				'label'     => __( 'Price Details', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'post_type_filter' => array( 'stock', 'gold' ),
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'pa_price_details_typo',
				'selector'  => '{{WRAPPER}} .premium-post-ticker__change-wrapper > *, {{WRAPPER}} .premium-post-ticker__price.exhange-rate',
				'condition' => array(
					'post_type_filter' => array( 'stock', 'gold' ),
				),
			)
		);

		$elem->add_control(
			'pa_price_details_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-post-ticker__change-wrapper > *, {{WRAPPER}} .premium-post-ticker__price.exhange-rate'  => 'color: {{VALUE}};',
				),
				'condition' => array(
					'post_type_filter' => array( 'stock', 'gold' ),
				),
			)
		);

		$elem->add_control(
			'pa_neg_change_color',
			array(
				'label'     => __( 'Negative Change Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-post-ticker__change-wrapper .down'  => 'color: {{VALUE}};',
				),
				'condition' => array(
					'post_type_filter' => array( 'stock', 'gold' ),
				),
			)
		);

		$elem->add_control(
			'pa_pos_change_color',
			array(
				'label'     => __( 'Positive Change Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-post-ticker__change-wrapper .up'  => 'color: {{VALUE}};',
				),
				'condition' => array(
					'post_type_filter' => array( 'stock', 'gold' ),
				),
			)
		);

		$elem->add_responsive_control(
			'price_details_spacing',
			array(
				'label'      => __( 'Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__change-wrapper'  => 'column-gap: {{SIZE}}px;',
				),
				'condition'  => array(
					'post_type_filter' => array( 'stock', 'gold' ),
				),
			)
		);

		$elem->add_responsive_control(
			'price_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__change-wrapper i'  => 'font-size: {{SIZE}}px;',
				),
				'condition'  => array(
					'change_indicator' => 'arrow',
					'post_type_filter' => array( 'stock', 'gold' ),
				),
			)
		);

		$elem->add_responsive_control(
			'pa_price_icon_margin',
			array(
				'label'      => __( 'Icon Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__change-wrapper i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'change_indicator' => 'arrow',
					'post_type_filter' => array( 'stock', 'gold' ),
				),
			)
		);

		$elem->add_responsive_control(
			'pa_price_details_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__change-wrapper, {{WRAPPER}} .premium-post-ticker__price.exhange-rate' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'post_type_filter' => array( 'stock', 'gold' ),
				),
			)
		);

	}

	/**
	 * Extend Weather Options.
	 *
	 * @access public
	 * @since 2.6.6
	 *
	 * @return array $options widget options.
	 */
	public function pa_weather_options() {

		$options = array(
			'source'                 => array(
				'name'   => __( 'City Name', 'premium-addons-for-elementor' ),
				'coords' => __( 'City Coordinates', 'premium-addons-for-elementor' ),
			),
			'layouts'                => array(
				'layout-1' => __( 'Layout 1', 'premium-addons-for-elementor' ),
				'layout-2' => __( 'Layout 2', 'premium-addons-for-elementor' ),
				'layout-3' => __( 'Layout 3', 'premium-addons-for-elementor' ),
			),
			'source_condition'       => array(),
			'dailyf_condition'       => array(),
			'custom_icons_condition' => array(),
		);

		return $options;
	}

	/**
	 * Add Weather Source Controls
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_weather_source_controls( $elem ) {

		$elem->add_control(
			'lat_coord',
			array(
				'label'       => __( 'Latitude', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'location_type'        => 'custom',
					'custom_location_type' => 'coords',
				),
			)
		);

		$elem->add_control(
			'long_coord',
			array(
				'label'       => __( 'Longitude', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'location_type'        => 'custom',
					'custom_location_type' => 'coords',
				),
			)
		);

	}

	/**
	 * Add Weather Daily Forecast Controls
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_weather_dailyf_controls( $elem ) {

		$elem->add_control(
			'forecast_layouts',
			array(
				'label'        => __( 'Choose Style', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'premium-daily-forecast__',
				'render_type'  => 'template',
				'label_block'  => true,
				'options'      => array(
					'style-1' => __( 'Style 1', 'premium-addons-for-elementor' ),
					'style-2' => __( 'Style 2', 'premium-addons-for-elementor' ),
					'style-3' => __( 'Style 3', 'premium-addons-for-elementor' ),
					'style-4' => __( 'Style 4', 'premium-addons-for-elementor' ),
				),
				'default'      => 'style-1',
				'conditions'   => array(
					'terms' => array(
						array(
							'name'  => 'enable_forecast',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'     => 'forecast_tabs',
									'operator' => '!==',
									'value'    => 'yes',
								),
								array(
									'terms' => array(
										array(
											'name'  => 'forecast_tabs',
											'value' => 'yes',
										),
										array(
											'name'     => 'forecast_days',
											'operator' => 'in',
											'value'    => array( '1', '6', '7', '8' ),
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$elem->add_control(
			'show_forecast_icon',
			array(
				'label'     => __( 'Weather state Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
				'condition' => array(
					'enable_forecast' => 'yes',
					'forecast_tabs!'  => 'yes',
				),
			)
		);

		$elem->add_control(
			'forecast_days',
			array(
				'label'       => __( 'Number Of Days', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'render_type' => 'template',
				'label_block' => true,
				'options'     => array(
					'1' => __( '1 Day', 'premium-addons-for-elementor' ),
					'2' => __( '2 Days', 'premium-addons-for-elementor' ),
					'3' => __( '3 Days', 'premium-addons-for-elementor' ),
					'4' => __( '4 Days', 'premium-addons-for-elementor' ),
					'5' => __( '5 Days', 'premium-addons-for-elementor' ),
					'6' => __( '6 Days', 'premium-addons-for-elementor' ),
					'7' => __( '7 Days', 'premium-addons-for-elementor' ),
					'8' => __( '8 Days', 'premium-addons-for-elementor' ),
				),
				'default'     => 5,
				'condition'   => array(
					'enable_forecast' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'forecast_alignment',
			array(
				'label'        => __( 'Block Alignment', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::CHOOSE,
				'prefix_class' => 'premium-weather-',
				'options'      => array(
					'flex-start'    => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'        => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'      => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					),
					'space-between' => array(
						'title' => __( 'Strech', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-stretch',
					),
				),
				'default'      => 'space-between',
				'toggle'       => false,
				'selectors'    => array(
					'{{WRAPPER}} .premium-weather__forecast, {{WRAPPER}} .premium-weather__tabs-headers' => 'justify-content: {{VALUE}}',
				),
				'conditions'   => array(
					'terms' => array(
						array(
							'name'  => 'enable_forecast',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'forecast_tabs',
									'value' => 'yes',
								),
								array(
									'terms' => array(
										array(
											'name'     => 'forecast_tabs',
											'operator' => '!==',
											'value'    => 'yes',
										),
										array(
											'name'     => 'forecast_layouts',
											'operator' => '!==',
											'value'    => 'style-4',
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$elem->add_responsive_control(
			'forecast_item_gap',
			array(
				'label'      => __( 'Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-weather__forecast-item-data'  => 'gap: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'enable_forecast'  => 'yes',
					'forecast_layouts' => 'style-4',
				),
			)
		);

		$elem->add_control(
			'height',
			array(
				'label'       => __( 'Height', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'render_type' => 'template',
				'size_units'  => array( 'px' ),
				'selectors'   => array(
					'{{WRAPPER}} .premium-weather__forecast'  => 'height: {{SIZE}}px; overflow-y: auto;',
				),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'condition'   => array(
					'enable_forecast'  => 'yes',
					'forecast_layouts' => 'style-4',
				),
			)
		);

		$elem->add_responsive_control(
			'weather_ele_min_width',
			array(
				'label'      => __( 'Element Minimum Width', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}}:not(.premium-forecast-tabs-yes) .premium-weather__forecast-item-data > *:not(.premium-weather__icon-wrapper)'  => 'min-width: {{SIZE}}px;',
					'{{WRAPPER}}.premium-forecast-tabs-yes .premium-weather__hourly-item > *,
					{{WRAPPER}}.premium-forecast-tabs-yes .premium-weather__weather-indicators > *'  => 'min-width: {{SIZE}}px;',
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'terms' => array(
								array(
									'name'  => 'enable_forecast',
									'value' => 'yes',
								),
								array(
									'name'  => 'forecast_layouts',
									'value' => 'style-4',
								),
							),
						),
						array(
							'terms' => array(
								array(
									'name'  => 'enable_forecast',
									'value' => 'yes',
								),
								array(
									'name'  => 'forecast_tabs',
									'value' => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$elem->add_control(
			'forecast_tabs',
			array(
				'label'        => __( 'Activate Tabs', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => __( 'Display 5 days weather forecast data with 3-hour step', 'premium-addons-for-elementor' ),
				'render_type'  => 'template',
				'prefix_class' => 'premium-forecast-tabs-',
				'separator'    => 'before',
				'condition'    => array(
					'enable_forecast' => 'yes',
					'forecast_days!'  => array( '1', '6', '7', '8' ),
				),
			)
		);

		$elem->add_control(
			'forecast_dates',
			array(
				'label'          => __( 'Date', 'premium-addons-for-elementor' ),
				'description'    => __( 'Use this to display specific dates', 'premium-addons-for-elementor' ),
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => array(
					'format'     => 'y-m-d',
					'enableTime' => false,
					'mode'       => 'multiple',
				),
				'dynamic'        => array(
					'active' => true,
				),
				'condition'      => array(
					'enable_forecast' => 'yes',
					'forecast_days!'  => array( '1', '6', '7', '8' ),
					'forecast_tabs'   => 'yes',
				),
			)
		);

		$elem->add_control(
			'date_format',
			array(
				'label'       => __( 'Date Format', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => __( 'Know more abour date format from ', 'premium-addons-for-elementor' ) . '<a href="https://wordpress.org/documentation/article/customize-date-and-time-format/" target="_blank">here</a>',
				'default'     => 'F j',
				'condition'   => array(
					'enable_forecast' => 'yes',
					'forecast_days!'  => array( '1', '6', '7', '8' ),
					'forecast_tabs'   => 'yes',
				),
			)
		);

		$elem->add_control(
			'tabs_weather_data',
			array(
				'label'       => __( 'Weather Data', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'options'     => array(
					'temp'       => __( 'Temperature', 'premium-addons-for-elementor' ),
					'desc'       => __( 'Description', 'premium-addons-for-elementor' ),
					'desc_icon'  => __( 'Weather Icon', 'premium-addons-for-elementor' ),
					'wind'       => __( 'Wind Speed', 'premium-addons-for-elementor' ),
					'pressure'   => __( 'Pressure', 'premium-addons-for-elementor' ),
					'humidity'   => __( 'Humidity', 'premium-addons-for-elementor' ),
					'wind_dir'   => __( 'Wind Direction', 'premium-addons-for-elementor' ),
					'feels_like' => __( 'Feels Like', 'premium-addons-for-elementor' ),
				),
				'default'     => array( 'desc_icon', 'temp', 'pressure', 'humidity', 'wind' ),
				'multiple'    => true,
				'condition'   => array(
					'enable_forecast' => 'yes',
					'forecast_days!'  => array( '1', '6', '7', '8' ),
					'forecast_tabs'   => 'yes',
				),
			)
		);

		$elem->add_control(
			'tabs_hourly_max',
			array(
				'label'       => __( 'Max Number of Hours', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Set a maximum number of hours to display up to 8 hours/day', 'premium-addons-for-elementor' ),
				'default'     => 8,
				'max'         => 8,
				'min'         => 1,
				'condition'   => array(
					'enable_forecast' => 'yes',
					'forecast_days!'  => array( '1', '6', '7', '8' ),
					'forecast_tabs'   => 'yes',
				),
			)
		);

		$elem->add_control(
			'date_notice',
			array(
				'label'       => __( 'Expire Message', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => __( 'A message to show when no forecast data available for a chosen day.', 'premium-addons-for-elementor' ),
				'default'     => 'No Data Available',
				'condition'   => array(
					'enable_forecast' => 'yes',
					'forecast_days!'  => array( '1', '6', '7', '8' ),
					'forecast_tabs'   => 'yes',
					'forecast_dates!' => '',
				),
			)
		);

		$elem->add_control(
			'forecast_carousel_sw',
			array(
				'label'        => __( 'Activate Carousel', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'render_type'  => 'template',
				'prefix_class' => 'premium-forecast-carousel-',
				'separator'    => 'before',
				'condition'    => array(
					'enable_forecast'   => 'yes',
					'forecast_tabs!'    => 'yes',
					'forecast_layouts!' => 'style-4',
				),
			)
		);

		$elem->add_responsive_control(
			'daily_slides_to_show',
			array(
				'label'     => __( 'Slides To Show', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 4,
				'max'       => 8,
				'min'       => 1,
				'condition' => array(
					'enable_forecast'      => 'yes',
					'forecast_tabs!'       => 'yes',
					'forecast_layouts!'    => 'style-4',
					'forecast_carousel_sw' => 'yes',
				),
			)
		);

		$elem->add_control(
			'show_daily_arrows_on_hover',
			array(
				'label'        => __( 'Show Arrows On Hover', 'premium-addons-for-elementor' ),
				'prefix_class' => 'premium-daily-hidden-arrows-',
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'condition'    => array(
					'enable_forecast'      => 'yes',
					'forecast_tabs!'       => 'yes',
					'forecast_layouts!'    => 'style-4',
					'forecast_carousel_sw' => 'yes',
				),
			)
		);

	}

	/**
	 * Add Weather Custom Icons Controls
	 *
	 * @since 2.9.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_weather_custom_icons_controls( $elem ) {

		$elem->add_control(
			'icons_source',
			array(
				'label'     => __( 'Icons Type', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'default' => __( 'Lottie Icons', 'premium-addons-for-elementor' ),
					'custom'  => __( 'Upload Your Own', 'premium-addons-for-elementor' ),
				),
				'default'   => 'default',
				'condition' => array(
					'enable_custom_icon' => 'yes',
				),
			)
		);

		$elem->add_control(
			'lottie_type',
			array(
				'label'       => __( 'Type', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'render_type' => 'template',
				'options'     => array(
					'filled'   => __( 'Filled', 'premium-addons-for-elementor' ),
					'outlined' => __( 'Outlined', 'premium-addons-for-elementor' ),
				),
				'default'     => 'filled',
				'condition'   => array(
					'enable_custom_icon' => 'yes',
					'icons_source'       => 'default',
				),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'weather_desc',
			array(
				'label'   => __( 'Weather Condition', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'Clear Sky'        => __( 'Clear Sky', 'premium-addons-for-elementor' ),
					'Few Clouds'       => __( 'Few Clouds', 'premium-addons-for-elementor' ),
					'Scattered Clouds' => __( 'Scattered Clouds', 'premium-addons-for-elementor' ),
					'Broken Clouds'    => __( 'Broken Clouds', 'premium-addons-for-elementor' ),
					'Shower Rain'      => __( 'Shower Rain', 'premium-addons-for-elementor' ),
					'Rain'             => __( 'Rain', 'premium-addons-for-elementor' ),
					'Thunderstorm'     => __( 'Thunderstorm', 'premium-addons-for-elementor' ),
					'Snow'             => __( 'Snow', 'premium-addons-for-elementor' ),
					'Mist'             => __( 'Mist', 'premium-addons-for-elementor' ),
				),
				'default' => 'Clear Sky',
			)
		);

		$repeater->add_control(
			'pa_icon_type',
			array(
				'label'       => __( 'Icon Type', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'render_type' => 'template',
				'options'     => array(
					'icon'   => __( 'Icon', 'premium-addons-for-elementor' ),
					'image'  => __( 'Image', 'premium-addons-for-elementor' ),
					'lottie' => __( 'Lottie', 'premium-addons-for-elementor' ),
				),
				'default'     => 'icon',
			)
		);

		$repeater->add_control(
			'pa_custom_icon',
			array(
				'label'     => __( 'Day Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-cloud',
					'library' => 'solid',
				),
				'skin'      => 'inline',
				'condition' => array(
					'pa_icon_type' => 'icon',
				),
			)
		);

		$repeater->add_control(
			'pa_custom_icon_night',
			array(
				'label'     => __( 'Night Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-cloud',
					'library' => 'solid',
				),
				'skin'      => 'inline',
				'condition' => array(
					'pa_icon_type' => 'icon',
					'weather_desc' => array( 'Clear Sky', 'Few Clouds', 'Rain' ),
				),
			)
		);

		$repeater->add_control(
			'pa_weather_img',
			array(
				'label'     => __( 'Day Image', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array(
					'pa_icon_type' => 'image',
				),
			)
		);

		$repeater->add_control(
			'pa_weather_img_night',
			array(
				'label'     => __( 'Night Image', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array(
					'pa_icon_type' => 'image',
					'weather_desc' => array( 'Clear Sky', 'Few Clouds', 'Rain' ),
				),
			)
		);

		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'image',
				'default'   => 'thumbnail',
				'condition' => array(
					'pa_icon_type' => 'image',
				),
			)
		);

		$repeater->add_control(
			'pa_lottie_url',
			array(
				'label'       => __( 'Day Icon JSON URL', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'condition'   => array(
					'pa_icon_type' => 'lottie',
				),
			)
		);

		$repeater->add_control(
			'pa_lottie_url_night',
			array(
				'label'       => __( 'Night Icon JSON URL', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => 'Leave empty to use the same icon for both day and night time.',
				'label_block' => true,
				'condition'   => array(
					'pa_icon_type' => 'lottie',
					'weather_desc' => array( 'Clear Sky', 'Few Clouds', 'Rain' ),
				),
			)
		);

		$repeater->add_control(
			'pa_lottie_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'condition'    => array(
					'pa_icon_type' => 'lottie',
				),
			)
		);

		$repeater->add_control(
			'pa_lottie_reverse',
			array(
				'label'        => __( 'Reverse', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array(
					'pa_icon_type' => 'lottie',
				),
			)
		);

		$icons = $repeater->get_controls();

		$elem->add_control(
			'custom_icons',
			array(
				'label'       => esc_html__( 'Icons List', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $icons,
				'title_field' => '{{{ weather_desc }}}',
				'condition'   => array(
					'enable_custom_icon' => 'yes',
					'icons_source'       => 'custom',
				),
			)
		);

	}

	/**
	 * Extend Pinterest layouts.
	 *
	 * @access public
	 * @since 2.6.6
	 *
	 * @return array $layouts widget layouts.
	 */
	public function pa_pinterest_layouts() {

		$options = array(
			'layouts' => array(
				'layout-1' => __( 'Card', 'premium-addons-for-elementor' ),
				'layout-2' => __( 'Banner', 'premium-addons-for-elementor' ),
				'layout-3' => __( 'On Side', 'premium-addons-for-elementor' ),
				'layout-4' => __( 'Slide', 'premium-addons-for-elementor' ),
			),
		);

		return $options;
	}

	/**
	 * Add Pinterest Board Controls
	 *
	 * @since 2.9.2
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_pinterest_board_controls( $elem ) {

		$elem->start_controls_section(
			'pa_pinterest_board_sec',
			array(
				'label'     => __( 'Board Settings', 'premium-addons-for-elementor' ),
				'condition' => array(
					'show_feed' => 'yes',
					'endpoint'  => 'boards/',
				),
			)
		);

		$elem->add_control(
			'board_layout',
			array(
				'label'        => __( 'Skin', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'premium-pinterest-feed__board-',
				'render_type'  => 'template',
				'default'      => 'layout-cover',
				'options'      => array(
					'layout-cover' => __( 'Cover Image', 'premium-addons-for-elementor' ),
					'layout-2'     => __( 'Collage 1', 'premium-addons-for-elementor' ),
					'layout-3'     => __( 'Collage 2', 'premium-addons-for-elementor' ),
				),
			)
		);

		$elem->add_control(
			'board_pinterest_icon',
			array(
				'label'     => __( 'Pinterest Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
			)
		);

		$elem->add_responsive_control(
			'board_pinterest_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'size' => 15,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-icon-board svg'  => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				),
				'condition'  => array(
					'board_pinterest_icon' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'board_pinterest_icon_h',
			array(
				'label'      => __( 'Horizontal Position', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'size' => 15,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-icon-board'  => 'left: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'board_pinterest_icon' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'board_pinterest_icon_v',
			array(
				'label'      => __( 'Vertical Position', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'size' => 15,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-icon-board'  => 'top: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'board_pinterest_icon' => 'yes',
				),
			)
		);

		$elem->add_control(
			'board_pin_count',
			array(
				'label'     => __( 'Pin Count', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'default'   => 'yes',
			)
		);

		$elem->add_control(
			'board_desc',
			array(
				'label'     => __( 'Description', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
			)
		);

		$elem->add_control(
			'board_desc_len',
			array(
				'label'     => __( 'Description Length (Word)', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'condition' => array(
					'pin_desc' => 'yes',
				),
				'default'   => 10,
			)
		);

		$elem->add_control(
			'board_desc_postfix',
			array(
				'label'       => __( 'Postfix', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'dots' => __( 'Dots', 'premium-addons-for-elementor' ),
					'link' => __( 'Link', 'premium-addons-for-elementor' ),
				),
				'default'     => 'dots',
				'label_block' => true,
				'condition'   => array(
					'board_desc'      => 'yes',
					'board_desc_len!' => '',
				),
			)
		);

		$elem->add_control(
			'board_desc_postfix_txt',
			array(
				'label'     => __( 'Read More Text', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Read More »', 'premium-addons-for-elementor' ),
				'condition' => array(
					'board_desc'         => 'yes',
					'board_desc_len!'    => '',
					'board_desc_postfix' => 'link',
				),
			)
		);

		$elem->add_responsive_control(
			'board_desc_order',
			array(
				'label'     => __( 'Description Order', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'toggle'    => false,
				'options'   => array(
					'1' => array(
						'title' => __( 'Before Pin Count', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-start',
					),
					'2' => array(
						'title' => __( 'After Pin Count', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-end',
					),
				),
				'default'   => '1',
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-desc' => 'order: {{VALUE}}',
				),
				'condition' => array(
					'board_desc'      => 'yes',
					'board_pin_count' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'board_width',
			array(
				'label'      => __( 'Board Width', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'custom' ),
				'separator'  => 'before',
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-wrapper' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$elem->add_responsive_control(
			'board_cover_height',
			array(
				'label'      => __( 'Image Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-cover' => 'height: {{SIZE}}px;',
				),
			)
		);

		$elem->add_responsive_control(
			'board_object_fit',
			array(
				'label'     => __( 'Object Fit', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''        => __( 'Default', 'premium-addons-pro' ),
					'fill'    => __( 'Fill', 'premium-addons-pro' ),
					'cover'   => __( 'Cover', 'premium-addons-pro' ),
					'contain' => __( 'Contain', 'premium-addons-pro' ),
				),
				'default'   => 'cover',
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-cover img' => 'object-fit: {{VALUE}};',
				),
				'condition' => array(
					'board_layout' => 'layout-cover',
				),
			)
		);

		$elem->add_responsive_control(
			'boards_container_align',
			array(
				'label'     => __( 'Container Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-feed__boards-wrapper' => 'justify-content: {{VALUE}}',
				),
			)
		);

		$elem->end_controls_section();
	}

	/**
	 * Add Image Hover Effects
	 *
	 * @since 2.9.2
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_image_hover_effects( $elem ) {

		$elem->add_control(
			'image_hover_effect',
			array(
				'label'       => __( 'Hover Effect', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'description' => __( 'Choose a hover effect for the image', 'premium-addons-for-elementor' ),
				'options'     => array(
					'none'    => __( 'None', 'premium-addons-for-elementor' ),
					'zoomin'  => __( 'Zoom In', 'premium-addons-for-elementor' ),
					'zoomout' => __( 'Zoom Out', 'premium-addons-for-elementor' ),
					'scale'   => __( 'Scale', 'premium-addons-for-elementor' ),
					'gray'    => __( 'Grayscale', 'premium-addons-for-elementor' ),
					'blur'    => __( 'Blur', 'premium-addons-for-elementor' ),
					'bright'  => __( 'Bright', 'premium-addons-for-elementor' ),
					'sepia'   => __( 'Sepia', 'premium-addons-for-elementor' ),
					'trans'   => __( 'Translate', 'premium-addons-for-elementor' ),
				),
				'default'     => 'zoomin',
				'label_block' => true,
			)
		);

	}

	/**
	 * Add Pinterest Board Style
	 *
	 * @since 2.9.2
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_pinterest_board_style( $elem ) {

		$elem->start_controls_section(
			'pa_board_style_section',
			array(
				'label'     => __( 'Board', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_feed' => 'yes',
					'endpoint'  => 'boards/',
				),
			)
		);

		$elem->add_control(
			'board_media_heading',
			array(
				'label'     => __( 'Cover', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$elem->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'board_cover_border',
				'selector' => '{{WRAPPER}} .premium-pinterest-feed__board-cover',
			)
		);

		$elem->add_control(
			'board_cover_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-cover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$elem->add_responsive_control(
			'board_cover_margin',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-cover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$elem->add_control(
			'board_title_heading',
			array(
				'label'     => __( 'Title', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$elem->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'board_title_typo',
				'selector' => '{{WRAPPER}} .premium-pinterest-feed__board-title',
			)
		);

		$elem->add_control(
			'board_title_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-title' => 'color: {{VALUE}};',
				),
			)
		);

		$elem->add_responsive_control(
			'board_title_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => 5,
					'right'  => 5,
					'bottom' => 5,
					'left'   => 5,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$elem->add_control(
			'board_desc_heading',
			array(
				'label'     => __( 'Description', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'board_desc' => 'yes',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'board_desc_typo',
				'selector'  => '{{WRAPPER}} .premium-pinterest-feed__board-desc',
				'condition' => array(
					'board_desc' => 'yes',
				),
			)
		);

		$elem->add_control(
			'board_desc_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-desc' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'board_desc' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'board_desc_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'default'    => array(
					'top'    => 5,
					'right'  => 5,
					'bottom' => 5,
					'left'   => 5,
					'unit'   => 'px',
				),
				'condition'  => array(
					'board_desc' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'board_desc_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'board_desc' => 'yes',
				),
			)
		);

		$elem->add_control(
			'board_read_more_heading',
			array(
				'label'     => __( 'Read More', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'board_desc'         => 'yes',
					'board_desc_len!'    => '',
					'board_desc_postfix' => 'link',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'board_read_more_typo',
				'selector'  => '{{WRAPPER}} .premium-pinterest-feed__board-desc .premium-read-more',
				'condition' => array(
					'board_desc'         => 'yes',
					'board_desc_len!'    => '',
					'board_desc_postfix' => 'link',
				),
			)
		);

		$elem->add_control(
			'board_read_more_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-desc .premium-read-more' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'board_desc'         => 'yes',
					'board_desc_len!'    => '',
					'board_desc_postfix' => 'link',
				),
			)
		);

		$elem->add_control(
			'board_read_more_color_hov',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-desc .premium-read-more:hover' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'board_desc'         => 'yes',
					'board_desc_len!'    => '',
					'board_desc_postfix' => 'link',
				),
			)
		);

		$elem->add_responsive_control(
			'board_read_more_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-desc .premium-read-more' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'board_desc'         => 'yes',
					'board_desc_len!'    => '',
					'board_desc_postfix' => 'link',
				),
			)
		);

		$elem->add_control(
			'board_pins_heading',
			array(
				'label'     => __( 'Pin Count', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'view_count' => 'yes',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'board_pins_typo',
				'selector'  => '{{WRAPPER}} .premium-pinterest-feed__board-pins-number',
				'condition' => array(
					'view_count' => 'yes',
				),
			)
		);

		$elem->add_control(
			'board_pins_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-pins-number' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'view_count' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'board_pins_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => 5,
					'right'  => 5,
					'bottom' => 5,
					'left'   => 5,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-pins-number' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'view_count' => 'yes',
				),
			)
		);

		$elem->add_control(
			'board_pinterest_icon_heading',
			array(
				'label'     => __( 'Pinterest Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'board_pinterest_icon' => 'yes',
				),
			)
		);

		$elem->add_control(
			'board_pinterest_icon_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-icon-board svg *'  => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'board_pinterest_icon' => 'yes',
				),
			)
		);

		$elem->add_control(
			'board_pinterest_icon_back',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-icon-board'  => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'board_pinterest_icon' => 'yes',
				),
			)
		);

		$elem->add_control(
			'board_pinterest_icon_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-icon-board' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'board_pinterest_icon' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'board_pinterest_icon_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-icon-board' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'board_pinterest_icon' => 'yes',
				),
			)
		);

		$elem->add_control(
			'board_cont_heading',
			array(
				'label' => __( 'Container', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$elem->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'borad_cont_shadow',
				'selector' => '{{WRAPPER}} .premium-pinterest-feed__board-wrapper',
			)
		);

		$elem->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'borad_cont_background',
				'types'          => array( 'classic', 'gradient' ),
				'fields_options' => array(
					'background' => array(
						'default' => 'classic',
					),
					'color'      => array(
						'default' => '#eee',
					),
				),
				'selector'       => '{{WRAPPER}} .premium-pinterest-feed__board-wrapper',
			)
		);

		$elem->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'borad_cont_border',
				'selector' => '{{WRAPPER}} .premium-pinterest-feed__board-wrapper',
			)
		);

		$elem->add_control(
			'borad_cont_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'size' => 15,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-wrapper, {{WRAPPER}}.premium-pinterest-feed__pin-layout-2 .premium-pinterest-feed__pin-media img' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$elem->add_responsive_control(
			'borad_cont_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$elem->add_responsive_control(
			'borad_cont_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$elem->end_controls_section();
	}

	/**
	 * Add Pinterest Profile Controls
	 *
	 * @since 2.9.2
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_pinterest_profile_controls( $elem ) {

		$elem->add_control(
			'avatar_url',
			array(
				'label'     => __( 'Profile Picture', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
				'condition' => array(
					'profile_header' => 'yes',
				),
			)
		);

		$elem->add_control(
			'follow_button',
			array(
				'label'     => __( 'Follow Button', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
				'condition' => array(
					'profile_header' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'follow_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'size' => 25,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-icon-follow svg'  => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				),
				'condition'  => array(
					'follow_button'  => 'yes',
					'profile_header' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'follow_icon_spacing',
			array(
				'label'     => __( 'Spacing (px)', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} a.premium-pinterest-feed__follow-button' => 'column-gap: {{SIZE}}px',
				),
				'condition' => array(
					'follow_button'  => 'yes',
					'profile_header' => 'yes',
				),
			)
		);

		$elem->add_control(
			'bio_description',
			array(
				'label'     => __( 'Biography', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'default'   => 'yes',
				'condition' => array(
					'profile_header' => 'yes',
				),
			)
		);

		$elem->add_control(
			'following_count',
			array(
				'label'     => __( 'Following Count', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
				'separator' => 'before',
				'condition' => array(
					'profile_header' => 'yes',
				),
			)
		);

		$elem->add_control(
			'follower_count',
			array(
				'label'     => __( 'Follower Count', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
				'condition' => array(
					'profile_header' => 'yes',
				),
			)
		);

		$elem->add_control(
			'view_count',
			array(
				'label'     => __( 'Monthly Views Count', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
				'condition' => array(
					'profile_header' => 'yes',
				),
			)
		);

		$counters_cond = array(
			'relation' => 'or',
			'terms'    => array(
				array(
					'name'  => 'following_count',
					'value' => 'yes',
				),
				array(
					'name'  => 'follower_count',
					'value' => 'yes',
				),
				array(
					'name'  => 'view_count',
					'value' => 'yes',
				),
			),
		);

		$elem->add_control(
			'profile_layout_heading',
			array(
				'label'     => __( 'Display Options', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'profile_header' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'profile_basic_display',
			array(
				'label'       => __( 'Display', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::CHOOSE,
				'toggle'      => false,
				'render_type' => 'template',
				'options'     => array(
					'row'    => array(
						'title' => __( 'Inline', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-navigation-horizontal',
					),
					'column' => array(
						'title' => __( 'Block', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-navigation-vertical',
					),
				),
				'default'     => 'row',
				'selectors'   => array(
					'{{WRAPPER}} .premium-pinterest-feed__user-info' => 'flex-direction: {{VALUE}}',
				),
				'condition'   => array(
					'profile_header' => 'yes',
				),
			)
		);

		$elem->add_control(
			'profile_alignment',
			array(
				'label'     => __( 'Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Start', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-start',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'End', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-end',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-feed__profile-header' => 'align-items: {{VALUE}};',
				),
				'condition' => array(
					'profile_basic_display' => 'column',
				),
			)
		);

		$elem->add_responsive_control(
			'bio_align',
			array(
				'label'     => __( 'Biography Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'toggle'    => false,
				'default'   => 'left',
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-feed__profile-desc' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'profile_header'  => 'yes',
					'bio_description' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'profile_bio_order',
			array(
				'label'     => __( 'Biography Order', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'toggle'    => false,
				'options'   => array(
					'2' => array(
						'title' => __( 'Before Counts', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-start',
					),
					'1' => array(
						'title' => __( 'After Counts', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-end',
					),
				),
				'default'   => '1',
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-feed__profile-counts' => 'order: {{VALUE}};',
				),
				'condition' => array(
					'profile_header'  => 'yes',
					'bio_description' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'counts_align',
			array(
				'label'      => __( 'Counts Alignment', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => array(
					'flex-start'    => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'        => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'      => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					),
					'space-between' => array(
						'title' => __( 'Strech', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-stretch',
					),
				),
				'default'    => 'space-between',
				'toggle'     => false,
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__profile-counts' => 'justify-content: {{VALUE}}',
				),
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'profile_header',
							'value' => 'yes',
						),
						array(
							'name'  => 'profile_basic_display',
							'value' => 'yes',
						),
						$counters_cond,
					),
				),
			)
		);

	}

	/**
	 * Add Pinterest Profile Style
	 *
	 * @since 2.9.2
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_pinterest_profile_style( $elem ) {

		$counters_cond = array(
			'relation' => 'or',
			'terms'    => array(
				array(
					'name'  => 'following_count',
					'value' => 'yes',
				),
				array(
					'name'  => 'follower_count',
					'value' => 'yes',
				),
				array(
					'name'  => 'view_count',
					'value' => 'yes',
				),
			),
		);

		$elem->start_controls_section(
			'pa_profile_style_sec',
			array(
				'label'     => __( 'Profile Header', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'profile_header' => 'yes',
				),
			)
		);

		$elem->add_control(
			'pa_profile_name_heading',
			array(
				'label'     => __( 'Username', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'username' => 'yes',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'username_typo',
				'selector'  => '{{WRAPPER}} .premium-pinterest-feed__username a',
				'condition' => array(
					'username' => 'yes',
				),
			)
		);

		$elem->add_control(
			'username_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-feed__username a' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'username' => 'yes',
				),
			)
		);

		$elem->add_control(
			'pa_avatar_heading',
			array(
				'label'     => __( 'Profile Picture', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'avatar_url' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'avatar_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__avatar'  => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				),
				'condition'  => array(
					'avatar_url' => 'yes',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'avatar_shadow',
				'selector'  => '{{WRAPPER}} .premium-pinterest-feed__avatar',
				'condition' => array(
					'avatar_url' => 'yes',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'avatar_background',
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}} .premium-pinterest-feed__avatar',
				'condition' => array(
					'avatar_url' => 'yes',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'avatar_border',
				'selector'  => '{{WRAPPER}} .premium-pinterest-feed__avatar',
				'condition' => array(
					'avatar_url' => 'yes',
				),
			)
		);

		$elem->add_control(
			'avatar_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__avatar, {{WRAPPER}} .premium-pinterest-feed__avatar img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'avatar_url' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'avatar_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__avatar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'avatar_url' => 'yes',
				),
			)
		);

		$elem->add_control(
			'pa_f_btn_heading',
			array(
				'label'     => __( 'Follow Button', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'follow_button' => 'yes',
				),
			)
		);

		$elem->add_control(
			'f_btn_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-feed__follow-button' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'follow_button' => 'yes',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'f_btn_typo',
				'selector'  => '{{WRAPPER}} .premium-pinterest-feed__follow-button',
				'condition' => array(
					'follow_button' => 'yes',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'f_btn_shadow',
				'selector'  => '{{WRAPPER}} .premium-pinterest-feed__follow-button',
				'condition' => array(
					'follow_button' => 'yes',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'f_btn_background',
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}} .premium-pinterest-feed__follow-button',
				'condition' => array(
					'follow_button' => 'yes',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'f_btn_border',
				'selector'  => '{{WRAPPER}} .premium-pinterest-feed__follow-button',
				'condition' => array(
					'follow_button' => 'yes',
				),
			)
		);

		$elem->add_control(
			'f_btn_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__follow-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'follow_button' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'f_btn_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__follow-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'follow_button' => 'yes',
				),
			)
		);

		$elem->add_control(
			'pa_bio_heading',
			array(
				'label'     => __( 'Biography', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'bio_description' => 'yes',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'bio_typo',
				'selector'  => '{{WRAPPER}} .premium-pinterest-feed__profile-desc',
				'condition' => array(
					'bio_description' => 'yes',
				),
			)
		);

		$elem->add_control(
			'bio_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-feed__profile-desc' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'bio_description' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'bio_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__profile-desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'bio_description' => 'yes',
				),
			)
		);

		$elem->add_control(
			'pa_counters_heading',
			array(
				'label'      => __( 'Counters', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::HEADING,
				'separator'  => 'before',
				'conditions' => $counters_cond,
			)
		);

		$elem->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'       => 'counts_typo',
				'selector'   => '{{WRAPPER}} .premium-pinterest-feed__profile-counts',
				'conditions' => $counters_cond,
			)
		);

		$elem->add_control(
			'counts_color',
			array(
				'label'      => __( 'Color', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__profile-counts' => 'color: {{VALUE}};',
				),
				'conditions' => $counters_cond,
			)
		);

		$elem->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'       => 'counts_background',
				'types'      => array( 'classic', 'gradient' ),
				'selector'   => '{{WRAPPER}} .premium-pinterest-feed__profile-counts > span',
				'conditions' => $counters_cond,
			)
		);

		$elem->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'       => 'counts_border',
				'selector'   => '{{WRAPPER}} .premium-pinterest-feed__profile-counts > span',
				'conditions' => $counters_cond,
			)
		);

		$elem->add_control(
			'counts_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__profile-counts > span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => $counters_cond,
			)
		);

		$elem->add_responsive_control(
			'counts_spacing',
			array(
				'label'      => __( 'Inner Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__profile-counts > span'  => 'column-gap: {{SIZE}}px;',
				),
				'conditions' => $counters_cond,
			)
		);

		$elem->add_responsive_control(
			'counts_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__profile-counts > span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => $counters_cond,
			)
		);

		$elem->add_responsive_control(
			'counts_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__profile-counts > span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => $counters_cond,
			)
		);

		$elem->add_control(
			'pa_profile_con_heading',
			array(
				'label'     => __( 'Container', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$elem->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'profile_cont_shadow',
				'selector' => '{{WRAPPER}} .premium-pinterest-feed__profile-header',
			)
		);

		$elem->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'profile_cont_background',
				'types'          => array( 'classic', 'gradient' ),
				'fields_options' => array(
					'background' => array(
						'default' => 'classic',
					),
					'color'      => array(
						'default' => '#eee',
					),
				),
				'selector'       => '{{WRAPPER}} .premium-pinterest-feed__profile-header',
			)
		);

		$elem->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'profile_cont_border',
				'selector' => '{{WRAPPER}} .premium-pinterest-feed__profile-header',
			)
		);

		$elem->add_control(
			'profile_cont_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__profile-header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$elem->add_responsive_control(
			'profile_cont_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__profile-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$elem->add_responsive_control(
			'profile_cont_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => 15,
					'right'  => 15,
					'bottom' => 15,
					'left'   => 15,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__profile-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$elem->end_controls_section();
	}

	/**
	 * Add Pinterest Slide Align
	 *
	 * @since 2.9.2
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_pinterest_slide_align( $elem ) {

		$elem->add_responsive_control(
			'pinterest_slide_align',
			array(
				'label'     => __( 'Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-feed__pin-meta-wrapper' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'pin_layout' => 'layout-4',
				),
			)
		);

		$elem->add_responsive_control(
			'pinterest_slide_offset',
			array(
				'label'       => __( 'Bottom Offset', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%', 'custom' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-pinterest-feed__pin-meta-wrapper' => 'bottom: {{SIZE}}{{UNIT}}',
				),
				'condition'   => array(
					'pin_layout' => 'layout-4',
				),
			)
		);

	}

	/**
	 * Add Pinterest Dots Style
	 *
	 * @since 2.9.2
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_pinterest_dots_style( $elem ) {

		$elem->add_responsive_control(
			'pinterest_dots_size',
			array(
				'label'      => __( 'Dots Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__dot'  => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				),
				'condition'  => array(
					'pin_layout' => 'layout-4',
				),
			)
		);

		$elem->add_control(
			'pinterest_dots_color',
			array(
				'label'     => __( 'Dots Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-feed__dot' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'pin_layout' => 'layout-4',
				),
			)
		);

		$elem->add_responsive_control(
			'pinterest_dots_spacing',
			array(
				'label'      => __( 'Dots Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'separator'  => 'after',
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__dot'  => 'margin: 0 {{SIZE}}px;',
				),
				'condition'  => array(
					'pin_layout' => 'layout-4',
				),
			)
		);

	}

	/**
	 * Add Pinterest Slide Dots
	 *
	 * @since 2.9.2
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_pinterest_slide_dots() {

		echo '<div class="premium-pinterest-feed__dots">';
		for ( $i = 0; $i < 3; $i++ ) {
			echo '<span class="premium-pinterest-feed__dot"></span>';
		}
		echo '</div>';

	}


	/**
	 * Extend World Clock Options.
	 *
	 * @access public
	 * @since 2.9.4
	 *
	 * @return array $layouts widget layouts.
	 */
	public function pa_tiktok_options() {

		$options = array(
			'layouts' => array(
				'layout-1' => __( 'Card', 'premium-addons-for-elementor' ),
				'layout-2' => __( 'Banner', 'premium-addons-for-elementor' ),
				'layout-3' => __( 'On Side', 'premium-addons-for-elementor' ),
			),
		);

		return $options;

	}

	/**
	 * Add TikTok Profile Controls
	 *
	 * @since 2.9.4
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_tiktok_profile_controls( $elem ) {

		$elem->add_control(
			'avatar_url',
			array(
				'label'     => __( 'Avatar', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
				'condition' => array(
					'profile_header' => 'yes',
				),
			)
		);

		$elem->add_control(
			'username',
			array(
				'label'     => __( 'Username', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
				'condition' => array(
					'profile_header' => 'yes',
				),
			)
		);

		$elem->add_control(
			'is_verified',
			array(
				'label'       => __( 'Verify Icon', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Shows the Verified Icon only if the account is verified.', 'premium-addons-for-elementor' ),
				'label_on'    => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off'   => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'     => 'yes',
				'condition'   => array(
					'profile_header' => 'yes',
					'username'       => 'yes',
				),
			)
		);

		$elem->add_control(
			'follow_button',
			array(
				'label'     => __( 'Follow Button', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
				'condition' => array(
					'profile_header' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'follow_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'size' => 15,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-icon-follow'  => 'width: {{SIZE}}px; height: {{SIZE}}px; line-height: {{SIZE}}px;',
				),
				'condition'  => array(
					'follow_button'  => 'yes',
					'profile_header' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'follow_icon_spacing',
			array(
				'label'     => __( 'Spacing (px)', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} a.premium-tiktok-feed__follow-button' => 'column-gap: {{SIZE}}px',
				),
				'condition' => array(
					'follow_button'  => 'yes',
					'profile_header' => 'yes',
				),
			)
		);

		$elem->add_control(
			'bio_description',
			array(
				'label'     => __( 'Biography', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
				'condition' => array(
					'profile_header' => 'yes',
				),
			)
		);

		$elem->add_control(
			'following_count',
			array(
				'label'     => __( 'Following Count', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
				'condition' => array(
					'profile_header' => 'yes',
				),
			)
		);

		$elem->add_control(
			'follower_count',
			array(
				'label'     => __( 'Follower Count', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
				'condition' => array(
					'profile_header' => 'yes',
				),
			)
		);

		$elem->add_control(
			'likes_count',
			array(
				'label'     => __( 'Likes Count', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
				'condition' => array(
					'profile_header' => 'yes',
				),
			)
		);

		$counters_cond = array(
			'relation' => 'or',
			'terms'    => array(
				array(
					'name'  => 'following_count',
					'value' => 'yes',
				),
				array(
					'name'  => 'follower_count',
					'value' => 'yes',
				),
				array(
					'name'  => 'likes_count',
					'value' => 'yes',
				),
			),
		);

		$elem->add_control(
			'profile_layout_heading',
			array(
				'label'     => __( 'Display Options', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'profile_header' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'profile_basic_display',
			array(
				'label'       => __( 'Display', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::CHOOSE,
				'toggle'      => false,
				'render_type' => 'template',
				'options'     => array(
					'row'    => array(
						'title' => __( 'Inline', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-navigation-horizontal',
					),
					'column' => array(
						'title' => __( 'Block', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-navigation-vertical',
					),
				),
				'default'     => 'row',
				'selectors'   => array(
					'{{WRAPPER}} .premium-tiktok-feed__user-info' => 'flex-direction: {{VALUE}}',
				),
				'condition'   => array(
					'profile_header' => 'yes',
				),
			)
		);

		$elem->add_control(
			'profile_alignment',
			array(
				'label'     => __( 'Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Start', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-start',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'End', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-end',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-feed__profile-header' => 'align-items: {{VALUE}};',
				),
				'condition' => array(
					'profile_basic_display' => 'column',
				),
			)
		);

		$elem->add_responsive_control(
			'bio_align',
			array(
				'label'     => __( 'Biography Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'toggle'    => false,
				'default'   => 'left',
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-feed__profile-desc' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'profile_header'  => 'yes',
					'bio_description' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'profile_bio_order',
			array(
				'label'     => __( 'Biography Order', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'toggle'    => false,
				'options'   => array(
					'2' => array(
						'title' => __( 'Before Counts', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-start',
					),
					'1' => array(
						'title' => __( 'After Counts', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-end',
					),
				),
				'default'   => '1',
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-feed__profile-counts' => 'order: {{VALUE}};',
				),
				'condition' => array(
					'profile_header'  => 'yes',
					'bio_description' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'counts_align',
			array(
				'label'      => __( 'Counts Alignment', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => array(
					'flex-start'    => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'        => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'      => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					),
					'space-between' => array(
						'title' => __( 'Strech', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-stretch',
					),
				),
				'default'    => 'space-between',
				'toggle'     => false,
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__profile-counts' => 'justify-content: {{VALUE}}',
				),
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'profile_header',
							'value' => 'yes',
						),
						array(
							'name'  => 'profile_basic_display',
							'value' => 'yes',
						),
						$counters_cond,
					),
				),
			)
		);

	}

	/**
	 * Add TikTok Profile Style
	 *
	 * @since 2.9.4
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_tiktok_profile_style( $elem ) {

		$counters_cond = array(
			'relation' => 'or',
			'terms'    => array(
				array(
					'name'  => 'following_count',
					'value' => 'yes',
				),
				array(
					'name'  => 'follower_count',
					'value' => 'yes',
				),
				array(
					'name'  => 'likes_count',
					'value' => 'yes',
				),
			),
		);

		$elem->start_controls_section(
			'pa_profile_style_sec',
			array(
				'label'     => __( 'Profile Header', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'profile_header' => 'yes',
				),
			)
		);

		$elem->add_control(
			'pa_profile_name_heading',
			array(
				'label'     => __( 'Username', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'username' => 'yes',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'username_typo',
				'selector'  => '{{WRAPPER}} .premium-tiktok-feed__username a',
				'condition' => array(
					'username' => 'yes',
				),
			)
		);

		$elem->add_control(
			'username_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-feed__username a' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'username' => 'yes',
				),
			)
		);

		$elem->add_control(
			'vid_username_icon_color',
			array(
				'label'     => __( 'Verified Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4DD0E1',
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-feed__vid-creator i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-tiktok-feed__vid-creator svg, {{WRAPPER}} .premium-tiktok-feed__vid-creator svg *' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'vid_username' => 'yes',
				),
			)
		);

		$elem->add_control(
			'pa_avatar_heading',
			array(
				'label'     => __( 'Profile Picture', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'avatar_url' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'avatar_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__avatar'  => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				),
				'condition'  => array(
					'avatar_url' => 'yes',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'avatar_shadow',
				'selector'  => '{{WRAPPER}} .premium-tiktok-feed__avatar',
				'condition' => array(
					'avatar_url' => 'yes',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'avatar_background',
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}} .premium-tiktok-feed__avatar',
				'condition' => array(
					'avatar_url' => 'yes',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'avatar_border',
				'selector'  => '{{WRAPPER}} .premium-tiktok-feed__avatar',
				'condition' => array(
					'avatar_url' => 'yes',
				),
			)
		);

		$elem->add_control(
			'avatar_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__avatar, {{WRAPPER}} .premium-tiktok-feed__avatar img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'avatar_url' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'avatar_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__avatar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'avatar_url' => 'yes',
				),
			)
		);

		$elem->add_control(
			'pa_f_btn_heading',
			array(
				'label'     => __( 'Follow Button', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'follow_button' => 'yes',
				),
			)
		);

		$elem->add_control(
			'f_btn_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-feed__follow-button' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'follow_button' => 'yes',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'f_btn_typo',
				'selector'  => '{{WRAPPER}} .premium-tiktok-feed__follow-button',
				'condition' => array(
					'follow_button' => 'yes',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'f_btn_shadow',
				'selector'  => '{{WRAPPER}} .premium-tiktok-feed__follow-button',
				'condition' => array(
					'follow_button' => 'yes',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'f_btn_background',
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}} .premium-tiktok-feed__follow-button',
				'condition' => array(
					'follow_button' => 'yes',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'f_btn_border',
				'selector'  => '{{WRAPPER}} .premium-tiktok-feed__follow-button',
				'condition' => array(
					'follow_button' => 'yes',
				),
			)
		);

		$elem->add_control(
			'f_btn_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__follow-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'follow_button' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'f_btn_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__follow-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'follow_button' => 'yes',
				),
			)
		);

		$elem->add_control(
			'pa_bio_heading',
			array(
				'label'     => __( 'Biography', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'bio_description' => 'yes',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'bio_typo',
				'selector'  => '{{WRAPPER}} .premium-tiktok-feed__profile-desc',
				'condition' => array(
					'bio_description' => 'yes',
				),
			)
		);

		$elem->add_control(
			'bio_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-feed__profile-desc' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'bio_description' => 'yes',
				),
			)
		);

		$elem->add_responsive_control(
			'bio_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__profile-desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'bio_description' => 'yes',
				),
			)
		);

		$elem->add_control(
			'pa_counters_heading',
			array(
				'label'      => __( 'Counters', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::HEADING,
				'separator'  => 'before',
				'conditions' => $counters_cond,
			)
		);

		$elem->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'       => 'counts_typo',
				'selector'   => '{{WRAPPER}} .premium-tiktok-feed__profile-counts',
				'conditions' => $counters_cond,
			)
		);

		$elem->add_control(
			'counts_color',
			array(
				'label'      => __( 'Color', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__profile-counts' => 'color: {{VALUE}};',
				),
				'conditions' => $counters_cond,
			)
		);

		$elem->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'       => 'counts_background',
				'types'      => array( 'classic', 'gradient' ),
				'selector'   => '{{WRAPPER}} .premium-tiktok-feed__profile-counts > span',
				'conditions' => $counters_cond,
			)
		);

		$elem->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'       => 'counts_border',
				'selector'   => '{{WRAPPER}} .premium-tiktok-feed__profile-counts > span',
				'conditions' => $counters_cond,
			)
		);

		$elem->add_control(
			'counts_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__profile-counts > span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => $counters_cond,
			)
		);

		$elem->add_responsive_control(
			'counts_spacing',
			array(
				'label'      => __( 'Inner Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__profile-counts > span'  => 'column-gap: {{SIZE}}px;',
				),
				'conditions' => $counters_cond,
			)
		);

		$elem->add_responsive_control(
			'counts_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__profile-counts > span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => $counters_cond,
			)
		);

		$elem->add_responsive_control(
			'counts_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__profile-counts > span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => $counters_cond,
			)
		);

		$elem->add_control(
			'pa_profile_con_heading',
			array(
				'label'     => __( 'Container', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$elem->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'profile_cont_shadow',
				'selector' => '{{WRAPPER}} .premium-tiktok-feed__profile-header',
			)
		);

		$elem->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'profile_cont_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-tiktok-feed__profile-header',
			)
		);

		$elem->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'profile_cont_border',
				'selector' => '{{WRAPPER}} .premium-tiktok-feed__profile-header',
			)
		);

		$elem->add_control(
			'profile_cont_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__profile-header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$elem->add_responsive_control(
			'profile_cont_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => 15,
					'right'  => 15,
					'bottom' => 15,
					'left'   => 15,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__profile-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$elem->add_responsive_control(
			'profile_cont_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__profile-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$elem->end_controls_section();

	}

	/**
	 * Add TikTok Load More Controls
	 *
	 * @since 2.9.4
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_tiktok_loadmore_options( $elem ) {

		$elem->add_control(
			'no_per_load',
			array(
				'label'       => __( 'videos On Load More', 'premium-addons-for-elementor' ),
				'description' => __( 'Number of videos to load', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 3,
				'condition'   => array(
					'carousel!'     => 'yes',
					'load_more_btn' => 'yes',
				),
			)
		);

		$elem->add_control(
			'more_btn_txt',
			array(
				'label'     => __( 'Load More Text', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Load More', 'premium-addons-for-elementor' ),
				'condition' => array(
					'carousel!'     => 'yes',
					'load_more_btn' => 'yes',
				),
			)
		);

	}

	public function add_tiktok_loadmore_style( $elem ) {

		$elem->start_controls_section(
			'cta_style_section',
			array(
				'label'     => __( 'CTA Button', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_feed'     => 'yes',
					'load_more_btn' => 'yes',
					'carousel!'     => 'yes',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cta_typography',
				'selector' => '{{WRAPPER}} .premium-tiktok-feed__load-more-btn',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
			)
		);

		$elem->add_responsive_control(
			'cta_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__load-more-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$elem->add_responsive_control(
			'cta_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__load-more-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$elem->start_controls_tabs( 'cta_style_tabs' );

		$elem->start_controls_tab(
			'cta_style_tab_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$elem->add_control(
			'cta_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-feed__load-more-btn' => 'color: {{VALUE}};',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'cta_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-tiktok-feed__load-more-btn',
			)
		);

		$elem->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'cta_shadow',
				'selector' => '{{WRAPPER}} .premium-tiktok-feed__load-more-btn',
			)
		);

		$elem->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'cta_border',
				'selector' => '{{WRAPPER}} .premium-tiktok-feed__load-more-btn',
			)
		);

		$elem->add_control(
			'cta_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__load-more-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$elem->end_controls_tab();

		$elem->start_controls_tab(
			'cta_style_tab_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$elem->add_control(
			'cta_color_hover',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-feed__load-more-btn:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'cta_background_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-tiktok-feed__load-more-btn:hover',
			)
		);

		$elem->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'cta_shadow_hover',
				'selector' => '{{WRAPPER}} .premium-tiktok-feed__load-more-btn:hover',
			)
		);

		$elem->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'cta_border_hover',
				'selector' => '{{WRAPPER}} .premium-tiktok-feed__load-more-btn:hover',
			)
		);

		$elem->add_control(
			'cta_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__load-more-btn:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$elem->end_controls_tab();

		$elem->end_controls_tabs();

		$elem->end_controls_section();

	}

	/**
	 * Add TikTok Load More Controls
	 *
	 * @since 2.9.4
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_tooltips_type_controls( $elem ) {

		$elem->add_control(
			'premium_tooltip_gallery',
			array(
				'label'       => __( 'Gallery', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::GALLERY,
				'description' => __( 'Set multiple images for random image selection.', 'premium-addons-for-elementor' ),
				'render_type' => 'template',
				'condition'   => array(
					'premium_tooltip_switcher' => 'yes',
					'premium_tooltip_type'     => 'gallery',
				),
			)
		);

		$elem->add_control(
			'live_temp_content',
			array(
				'label'       => __( 'Template Title', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'classes'     => 'premium-live-temp-title control-hidden',
				'label_block' => true,
				'condition'   => array(
					'premium_tooltip_switcher' => 'yes',
					'premium_tooltip_type'     => 'template',
				),
			)
		);

		$elem->add_control(
			'section_template_live',
			array(
				'type'        => Controls_Manager::BUTTON,
				'label_block' => true,
				'button_type' => 'default papro-btn-block',
				'text'        => __( 'Create / Edit Template', 'premium-addons-for-elementor' ),
				'event'       => 'createLiveTemp',
				'condition'   => array(
					'premium_tooltip_switcher' => 'yes',
					'premium_tooltip_type'     => 'template',
				),
			)
		);

		$elem->add_control(
			'premium_tooltip_template',
			array(
				'label'       => __( 'Elementor Template', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->template_instance->get_elementor_page_list(),
				'label_block' => true,
				'condition'   => array(
					'premium_tooltip_switcher' => 'yes',
					'premium_tooltip_type'     => 'template',
				),
			)
		);

		$elem->add_control(
			'pa_tooltip_temp_notice',
			array(
				'raw'             => __( '<strong>Please Note!: In the Editor/backend only</strong> Elementor Templates are loaded via AJAX. So, it might take a bit to show up.</li></ul>', 'premium-addons-for-elementor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_tooltip_switcher' => 'yes',
					'premium_tooltip_type'     => 'template',
				),
			)
		);

		$elem->add_control(
			'pa_tooltip_class',
			array(
				'label'       => __( 'CSS Navigation Selector', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => __( 'This can be used to navigate to this tooltip by adding the same class from widget settings -> Advanced tab -> CSS classes. <b>Important: the class must start with "tooltip-"</b>', 'premium-addons-for-elementor' ),
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'premium_tooltip_switcher' => 'yes',
					'premium_tooltip_type'     => 'template',
				),
			)
		);

		$elem->add_control(
			'is_tour_starter',
			array(
				'label'       => __( 'First Step in The Tour?', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable if this tooltip is the first step in the user\'s tour|guide you\'re creating.', 'premium-addons-for-elementor' ),
				'render_type' => 'template',
				'condition'   => array(
					'premium_tooltip_switcher' => 'yes',
					'pa_tooltip_class!'        => '',
					'premium_tooltip_type'     => 'template',
				),
			)
		);

	}

	/**
	 * Add Contact Form Presets Controls
	 *
	 * @since 2.9.6
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_cf_presets_controls( $elem ) {

		$elem->add_control(
			'form_insert',
			array(
				'type'        => Controls_Manager::RAW_HTML,
				'raw'         => '<form onsubmit="createCfForm(this);" action="javascript:void(0);"><input type="submit" value="Create & Insert Form" class="elementor-button" style="background-color: #D8D8D8; color: #fff;"></form>',
				'label_block' => true,
				'condition'   => array(
					'source' => 'presets',
				),
			)
		);

		$elem->add_control(
			'form_id',
			array(
				'label'     => __( 'Form ID', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'classes'   => 'premium-cf-form-id',
				'condition' => array(
					'source' => 'presets',
				),
			)
		);

	}

	/**
	 * Add Contact Form Effects Controls
	 *
	 * @since 2.9.6
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_cf_effects_controls( $elem ) {

		$elem->add_responsive_control(
			'labels_pos',
			array(
				'label'     => __( 'Label Position (PX)', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} .cf7-text-input-label' => 'transform: translateX(10px) translateY({{SIZE}}{{UNIT}})',
				),
				'condition' => array(
					'fields_effects' => array( 'label', 'label-pos-back' ),
				),
			)
		);

		$elem->add_responsive_control(
			'labels_focus_pos',
			array(
				'label'     => __( 'Label Focus Position (PX)', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} .input-focused .cf7-text-input-label' => 'transform: translateX(10px) translateY({{SIZE}}{{UNIT}}) !important',
				),
				'condition' => array(
					'fields_effects' => 'label-pos-back',
				),
			)
		);

		$elem->add_control(
			'labels_focus_back_color',
			array(
				'label'     => __( 'Focus Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .input-focused .cf7-text-input-label' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'fields_effects' => 'label-pos-back',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'      => 'labels_css_filters',
				'selector'  => '{{WRAPPER}} .cf7-text-input-label',
				'condition' => array(
					'fields_effects' => 'css-filters',
				),
			)
		);

		$elem->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'      => 'labels_focus_css_filters',
				'label'     => __( 'Focus CSS Filters', 'premium-addons-for-elementor' ),
				'selector'  => '{{WRAPPER}} .input-focused .cf7-text-input-label',
				'condition' => array(
					'fields_effects' => 'css-filters',
				),
			)
		);

		$elem->add_control(
			'fields_focus_line',
			array(
				'label'        => __( 'Focus Line Animation', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-cf-anim-',
			)
		);

		$elem->add_control(
			'focus_line_middle',
			array(
				'label'        => __( 'Animate From Middle', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-cf-anim-middle-',
				'condition'    => array(
					'fields_focus_line' => 'yes',
				),
			)
		);

		$elem->add_control(
			'focus_line_color',
			array(
				'label'     => __( 'Focus Line Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}.premium-cf-anim-yes .wpcf7-span.is-focused::after' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'fields_focus_line' => 'yes',
				),
			)
		);

	}

	/**
	 * Add Advanced Carousel Controls
	 *
	 * @since 2.9.6
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_adv_carousel_controls( $elem ) {

		$elem->add_control(
			'gradual_scale_effect',
			array(
				'label'        => __( 'Gradual Scale Effect', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-adv-carousel__gradual-',
				'default'      => 'yes',
				'condition'    => array(
					'media_wheel_animation' => 'flat',
				),
			)
		);

		$elem->add_control(
			'media_whee_start',
			array(
				'label'       => __( 'Start', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'description' => __( 'Zero based index of the starting item, or leave empty to start in the middle', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'media_wheel_animation!' => 'infinite',
				),
			)
		);

		$elem->add_responsive_control(
			'media_wheel_spacing',
			array(
				'label'       => __( 'Spacing', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'Space between items relative to each item\'s width. 0 for no spacing, negative values to overlap', 'premium-addons-for-elementor' ),
				'size_units'  => array( 'px' ),
				'range'       => array(
					'px' => array(
						'min'  => -5,
						'max'  => 5,
						'step' => 0.1,
					),
				),
				'default'     => array(
					'unit' => 'px',
					'size' => -0.6,
				),
				'condition'   => array(
					'media_wheel_animation!' => 'infinite',
				),
			)
		);

		$elem->add_control(
			'media_wheel_loop',
			array(
				'label'     => __( 'Loop', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'media_wheel_animation!' => 'infinite',
				),
			)
		);

		$elem->add_control(
			'media_wheel_autoplay',
			array(
				'label'     => __( 'Auto Play', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'media_wheel_animation!' => 'infinite',
				),
			)
		);

		$elem->add_control(
			'media_wheel_autoplay_speed',
			array(
				'label'     => __( 'Auto Play Speed (ms)', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3000,
				'condition' => array(
					'media_wheel_animation!' => 'infinite',
					'media_wheel_autoplay'   => 'yes',
				),
			)
		);

		$elem->add_control(
			'nav_on_click',
			array(
				'label'       => __( 'Scroll on Click', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Clicking an item switches to that item', 'premium-addons-for-elementor' ),
				'default'     => 'yes',
				'condition'   => array(
					'media_wheel_animation!' => 'infinite',
				),
			)
		);

		$elem->add_control(
			'nav_by_keyboard',
			array(
				'label'       => __( 'Scroll By Keyboard', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable left/right arrow navigation', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'media_wheel_animation!' => 'infinite',
				),
			)
		);

		$elem->add_control(
			'nav_by_touch',
			array(
				'label'       => __( 'Scoll By Touch on Small Devices', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable swipe navigation for touch devices', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'media_wheel_animation!' => 'infinite',
				),
			)
		);

	}

	/**
	 * Add Advanced Carousel Navigation
	 *
	 * @since 2.9.6
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_adv_carousel_effects_navigation( $elem ) {

		$elem->add_control(
			'arrows',
			array(
				'label'     => __( 'Navigation Arrows', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'media_wheel_animation!' => 'infinite',
				),
			)
		);

		$elem->add_control(
			'prev_arrow',
			array(
				'label'       => __( 'Previous Icon', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'skin'        => 'inline',
				'default'     => array(
					'value'   => 'fas fa-chevron-left',
					'library' => 'fa-solid',
				),
				'condition'   => array(
					'media_wheel_animation!' => 'infinite',
					'arrows'                 => 'yes',
				),
			)
		);

		$elem->add_control(
			'next_arrow',
			array(
				'label'       => __( 'Next Icon', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'skin'        => 'inline',
				'default'     => array(
					'value'   => 'fas fa-chevron-right',
					'library' => 'fa-solid',
				),
				'condition'   => array(
					'media_wheel_animation!' => 'infinite',
					'arrows'                 => 'yes',
				),
			)
		);

	}

    /**
	 * Add Testimonials Skins Controls
	 *
	 * @since 2.9.8
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_testimonials_skins_controls( $elem ) {

		$elem->add_control(
			'img_order',
			array(
				'label'       => __( 'Image Position', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => array(
					'row'         => array(
						'title' => __( 'Before Info', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-flex eicon-align-end-v premium-rotate-icon',
					),
					'row-reverse' => array(
						'title' => __( 'After Info', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-flex eicon-align-start-v premium-rotate-icon',
					),
				),
				'default'     => 'row',
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-testimonial__img-info' => 'flex-direction: {{VALUE}}',
				),
				'condition'   => array(
					'show_image' => 'yes',
					'skin'       => array( 'skin2', 'skin3' ),
				),
			)
		);

		$elem->add_control(
			'stars_place',
			array(
				'label'                => __( 'Stars Position', 'premium-addons-for-elementor' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'top'    => array(
						'title' => __( 'Above Text', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-flex eicon-justify-start-h premium-rotate-icon',
					),
					'bottom' => array(
						'title' => __( 'Below Text', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-flex eicon-justify-end-h premium-rotate-icon',
					),
				),
				'default'              => 'top',
				'selectors_dictionary' => array(
					'top'    => 'order: 1; margin-bottom: 20px;',
					'bottom' => 'order: 2; margin-top: 20px;',
				),
				'selectors'            => array(
					'{{WRAPPER}} .premium-testimonial__rating-wrapper' => '{{VALUE}}',
				),
				'toggle'               => false,
				'label_block'          => true,
				'condition'            => array(
					'skin' => array( 'skin2', 'skin3' ),
				),
			)
		);

	}

    /**
	 * Add Shape Divider SVG
	 *
	 * @since 2.9.8
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_shape_divider_svg( $element ) {

		$element->add_control(
			'premium_gdivider_custom',
			array(
				'label'       => __( 'SVG Code', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => __( 'Enter you SVG code here. You can get Free Shape Dividers ', 'premium-addons-for-elementor' ) . '<a href="https://www.dezign4fun.com/free-shape-dividers/" target="_blank">here</a>' . __( ', Or get cool waves through ', 'premium-addons-for-elementor' ) . '<a href="https://svgwave.in/" target="_blank">SVG Waves, </a><a href="https://getwaves.io/" target="_blank">Waves.io, </a><a href="https://wavelry.vercel.app/" target="_blank">Waverly</a>',
				'label_block' => true,
				'render_type' => 'template',
				'condition'   => array(
					'premium_global_divider_sw' => 'yes',
					'premium_gdivider_source'   => 'custom',
				),
			)
		);

	}

    /**
	 * Add Shape Divider Fill
	 *
	 * @since 2.9.8
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_shape_divider_fill( $element ) {

        // Image
		$element->add_control(
			'premium_gdivider_image',
			array(
				'label'     => __( 'Choose Image', 'premium-addons-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => array(
					'active' => true,
				),
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'premium_global_divider_sw' => 'yes',
					'premium_gdivider_bg_type'  => 'image',
				),
			)
		);

		// Gradient
		$element->add_control(
			'premium_gdivider_grad_firstcolor',
			array(
				'label'     => __( 'Color 1', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FF5F6D',
				'condition' => array(
					'premium_global_divider_sw' => 'yes',
					'premium_gdivider_bg_type'  => 'gradient',
				),
			)
		);

		$element->add_control(
			'premium_gdivider_grad_firstloc',
			array(
				'label'      => __( 'Location', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'default'    => array(
					'unit' => '%',
					'size' => 0,
				),
				'condition'  => array(
					'premium_gdivider_bg_type'  => 'gradient',
					'premium_global_divider_sw' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_gdivider_grad_secondcolor',
			array(
				'label'     => __( 'Color 2', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFC371',
				'condition' => array(
					'premium_gdivider_bg_type'  => 'gradient',
					'premium_global_divider_sw' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_gdivider_grad_secondloc',
			array(
				'label'      => __( 'Location', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'default'    => array(
					'unit' => '%',
					'size' => 100,
				),
				'condition'  => array(
					'premium_gdivider_bg_type'  => 'gradient',
					'premium_global_divider_sw' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_gdivider_grad_type',
			array(
				'label'     => __( 'Type', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'linear',
				'options'   => array(
					'linear' => __( 'Linear', 'premium-addons-pro' ),
					'radial' => __( 'Radial', 'premium-addons-pro' ),
				),
				'condition' => array(
					'premium_gdivider_bg_type'  => 'gradient',
					'premium_global_divider_sw' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_gdivider_grad_angle',
			array(
				'label'      => __( 'Angle (deg)', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'range'      => array(
					'%' => array(
						'min'  => 0,
						'max'  => 360,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'deg',
					'size' => 0,
				),
				'condition'  => array(
					'premium_gdivider_bg_type'   => 'gradient',
					'premium_gdivider_grad_type' => 'linear',
					'premium_global_divider_sw'  => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_gdivider_grad_xpos',
			array(
				'label'      => __( 'X-Postion', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'default'    => array(
					'unit' => '%',
					'size' => 50,
				),
				'condition'  => array(
					'premium_gdivider_bg_type'   => 'gradient',
					'premium_gdivider_grad_type' => 'radial',
					'premium_global_divider_sw'  => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_gdivider_grad_ypos',
			array(
				'label'      => __( 'Y-Position', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'default'    => array(
					'unit' => '%',
					'size' => 50,
				),
				'condition'  => array(
					'premium_gdivider_bg_type'   => 'gradient',
					'premium_gdivider_grad_type' => 'radial',
					'premium_global_divider_sw'  => 'yes',
				),
			)
		);

    }

    /**
	 * Add Showcase SVG
	 *
	 * @since 2.9.10
	 * @access public
	 *
	 * @param object $repeater repeater control.
	 */
    public function add_showcase_svg( $repeater ) {

        $repeater->add_control(
			'custom_svg',
			array(
				'label'       => __( 'SVG Code', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => 'You can use these sites to create SVGs: <a href="https://danmarshall.github.io/google-font-to-svg-path/" target="_blank">Google Fonts</a> and <a href="https://boxy-svg.com/" target="_blank">Boxy SVG</a>',
				'condition'   => array(
					'item_type' => 'svg',
				),
			)
		);

    }

    /**
	 * Add Showcase SVG Hover
	 *
	 * @since 2.9.10
	 * @access public
	 *
	 * @param object $repeater repeater control.
	 */
    public function add_showcase_svg_hover( $repeater ) {

        $repeater->add_control(
			'custom_svg_hov',
			array(
				'label'       => __( 'SVG Code', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => 'You can use these sites to create SVGs: <a href="https://danmarshall.github.io/google-font-to-svg-path/" target="_blank">Google Fonts</a> and <a href="https://boxy-svg.com/" target="_blank">Boxy SVG</a>',
				'condition'   => array(
					'item_type_hov' => 'svg',
				),
			)
		);

    }

    /**
	 * Add Showcase Highlights
	 *
	 * @since 2.9.10
	 * @access public
	 *
	 * @param object $repeater repeater control.
	 */
    public function add_showcase_highlights(  ) {

        return array(
			'outline'          => '<svg class="outline-svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="789.5px" height="238.9px" fill="none" viewBox="0 0 789.5 238.9" style="enable-background:new 0 0 789.5 238.9;" xml:space="preserve" preserveAspectRatio="none"><path d="M1.6,160.2c0,0,17.2-27.5,26.4-45.8s26.9-58.2,26.9-58.2s7.7-7.3,8.8-12.9S63.3,31.8,69,25.9  C74.7,20,86,8.1,93.9,5.7s11-3.1,18.5-2.8s23.3-1.6,37.1-1.9c13.8-0.3,70.6,8.7,78.4,8.6c7.8-0.1,23.6,3.7,29.6,3.9  c6,0.2,32.5,1.9,42.8,2.4c10.3,0.5,19.7,3.2,32.5,6.8s20.6,4,34.2,5.9c13.6,1.9,17.2,2.9,22.7,5.4s18.4,10,33.9,10.6  c15.5,0.6,53.8,2.8,74.2,4.7c20.4,1.9,31.8,4.5,40.8,4.2c8.9-0.3,32.3-5.1,42.5-5c10.2,0.1,44.6,3.5,53.5,2.8  c8.9-0.7,54.3-9.3,63.1-10.2s18.7-2.5,20.8-3.5c2.1-1,8.6-9.7,10.2-8.8s2.9,4.7,4.2,9.8s4.7,9.4,5,16.6s2.4,39.1,2.4,39.1  s1.1,23.2,3,31.7c1.9,8.5,7.2,23.5,7.4,26.6c0.2,3.1,0,7,0,7s2.2-0.2,1.9,1.2c-0.3,1.4-2.8,9.7-2.8,9.7s-5.7-4.3-13.1-2.5  c-7.4,1.8-9,4.4-12.3,8.8c-3.3,4.4-4.1,9.2-29.3,9.3c-25.2,0.1-36.5-0.6-65.6,2.1c-29.1,2.6-37.1,2.2-53.4,8.1  c-16.3,5.9-27.5,12.6-50.6,10.3c-23.2-2.3-18.9-4.3-37-2.8c-18.1,1.5-57.7,5.9-78.8,7s-54.6,3.3-67.8,1.1s-46-3.6-59.5-3.4  s-45.2-6.4-66.6-10s-31.3-4.1-36.2-5.1c-4.9-0.9-22.1-8.6-38.6-13.5c-16.5-4.9-48.7-5.3-56.6-6.7s-13.2-5.5-18.2-5.8  c-5-0.3-21.5,0.4-34.6-1.5C18.4,163.9,1.6,160.2,1.6,160.2z"></path><path d="M37.1,65l2.6-9.1c0,0,10.8,5.3,16.5,2.8s9.8-12.6,18.2-14.5c8.5-1.9,18.5-0.4,50-2c31.6-1.6,47.8,1.5,68.1-4.3  S230.2,24,245,23.1c14.9-0.9,42.8,5.9,58.5,4.7c15.7-1.2,29-5.2,61.4-5.5s63.2-4.2,76.6-0.5s58.4,3.2,73.4,4.7  c15,1.5,46.8,7.1,59.8,12.1c13,5,29.7,3.1,40.4,6.7C625.8,49,647,62.7,672.3,65c25.3,2.3,30.4,2.1,40,4.9s21.5,0,37,2.7  s31,5,36.4,6.5s1.3,3.7,1.3,3.7s-35,60.2-40.9,77.9c-5.9,17.7-18.5,23.9-19.5,31.1c-0.9,7.2-0.5,11.1-4.9,16.4  c-4.5,5.3-16.6,24.1-35.4,26.6c-18.8,2.5-52.2,3.9-63.2,2.5c-11-1.4-45.2-7.7-59.7-8.8c-14.5-1-29.4-6.1-55.4-6.4  s-64.9-16.6-83-17.5c-18.1-0.9-29.9-6.6-40.5-12.6c-10.5-6-29.6-3.2-45.1-4.5c-15.5-1.2-57.3-4.7-77.1-8.1s-36.6,0-51,0  c-14.4,0-27.7-4.4-51.4-3.2c-23.7,1.2-50.5,9.3-65.9,11c-15.4,1.7-27.8-0.2-31.5,5.6c-3.8,5.8-4.8,9.7-7.3-0.2  c-2.6-9.9-8.9-40.8-7.5-57.4s0.7-25.5-3.3-39.9S37.1,65,37.1,65z"></path></svg>',
			'circle'           => '<svg class="outline-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M325,18C228.7-8.3,118.5,8.3,78,21C22.4,38.4,4.6,54.6,5.6,77.6c1.4,32.4,52.2,54,142.6,63.7 c66.2,7.1,212.2,7.5,273.5-8.3c64.4-16.6,104.3-57.6,33.8-98.2C386.7-4.9,179.4-1.4,126.3,20.7"></path></svg>',
			'curly'            => '<svg class="outline-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M3,146.1c17.1-8.8,33.5-17.8,51.4-17.8c15.6,0,17.1,18.1,30.2,18.1c22.9,0,36-18.6,53.9-18.6 c17.1,0,21.3,18.5,37.5,18.5c21.3,0,31.8-18.6,49-18.6c22.1,0,18.8,18.8,36.8,18.8c18.8,0,37.5-18.6,49-18.6c20.4,0,17.1,19,36.8,19 c22.9,0,36.8-20.6,54.7-18.6c17.7,1.4,7.1,19.5,33.5,18.8c17.1,0,47.2-6.5,61.1-15.6"></path></svg>',
			'h-underline'      => '<svg class="outline-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M7.7,145.6C109,125,299.9,116.2,401,121.3c42.1,2.2,87.6,11.8,87.3,25.7"></path></svg>',
			'double-underline' => '<svg class="outline-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M5,125.4c30.5-3.8,137.9-7.6,177.3-7.6c117.2,0,252.2,4.7,312.7,7.6"></path><path d="M26.9,143.8c55.1-6.1,126-6.3,162.2-6.1c46.5,0.2,203.9,3.2,268.9,6.4"></path></svg>',
			'underline-zigzag' => '<svg class="outline-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M9.3,127.3c49.3-3,150.7-7.6,199.7-7.4c121.9,0.4,189.9,0.4,282.3,7.2C380.1,129.6,181.2,130.6,70,139 c82.6-2.9,254.2-1,335.9,1.3c-56,1.4-137.2-0.3-197.1,9"></path></svg>',
			'diagonal'         => '<svg class="outline-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M13.5,15.5c131,13.7,289.3,55.5,475,125.5"></path></svg>',
			'strikethrough'    => '<svg class="outline-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M3,75h493.5"></path></svg>',
			'x'                => '<svg class="outline-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none"><path d="M497.4,23.9C301.6,40,155.9,80.6,4,144.4"></path><path d="M14.1,27.6c204.5,20.3,393.8,74,467.3,111.7"></path></svg>',
		);

    }

	/**
	 * Run Modules Extender
	 *
	 * Extendes the free modules with extra options
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param array $data conditions data.
	 */
	public function extend_display_conditions_options( $data ) {

		$conditions = $data;

		$conditions['urlparams']['label'] = __( 'URL', 'premium-addons-pro' );
		$conditions['misc']['label']      = __( 'Misc', 'premium-addons-pro' );

		if ( class_exists( 'woocommerce' ) ) {
			$conditions['woocommerce']['label'] = __( 'WooCommerce', 'premium-addons-pro' );
		}

		if ( class_exists( 'ACF' ) ) {
			$conditions['acf']['label'] = __( 'ACF', 'premium-addons-pro' );
		}

		$data = $conditions;

		return $data;

	}

	/**
	 * Extend Display Conditions Keys
	 *
	 * Extends display conditions modules keys used to register controls
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param array $keys conditions keys.
	 */
	public function extend_display_conditions_keys( $keys ) {

		$keys = array_merge(
			array(
				'url_string',
				'url_referer',
				'shortcode',
			),
			$keys
		);

		if ( class_exists( 'ACF' ) ) {

			$keys = array_merge(
				array(
					'acf_text',
					'acf_boolean',
					'acf_choice',
				),
				$keys
			);

		}

		if ( class_exists( 'woocommerce' ) ) {

			$keys = array_merge(
				array(
					'woo_cat_page',
					'woo_product_cat',
					'woo_product_price',
					'woo_product_stock',
					'woo_orders',
					'woo_category',
					'woo_last_purchase',
					'woo_total_price',
					'woo_cart_products',
					'woo_purchase_products',
				),
				$keys
			);

		}

		return $keys;
	}

	/**
	 * Changes the conditions for display conditions options
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param array $conditions controls conditions.
	 */
	public function extend_pro_display_conditions( $conditions ) {

		$options_conditions = array( '' );

		return $options_conditions;

	}

	/**
	 * Add Opacity Controls
	 *
	 * Extends Floating Effects Opacity controls.
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_opacity_controls( $elem ) {

		$elem->add_control(
			'premium_fe_opacity',
			array(
				'label'     => __( 'Value', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'from' => 0,
						'to'   => 50,
					),
					'unit'  => '%',
				),
				'labels'    => array(
					__( 'From', 'premium-addons-pro' ),
					__( 'To', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'premium_fe_switcher'         => 'yes',
					'premium_fe_opacity_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_opacity_duration',
			array(
				'label'     => __( 'Duration', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 1000,
				),
				'condition' => array(
					'premium_fe_switcher'         => 'yes',
					'premium_fe_opacity_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_opacity_delay',
			array(
				'label'     => __( 'Delay', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'condition' => array(
					'premium_fe_switcher'         => 'yes',
					'premium_fe_opacity_switcher' => 'yes',
				),

			)
		);

	}

	/**
	 * Add Background Controls
	 *
	 * Extends Floating Effects Background controls.
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_bg_controls( $elem ) {

		$elem->add_control(
			'premium_fe_bg_color_from',
			array(
				'label'     => __( 'From', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'condition' => array(
					'premium_fe_switcher'          => 'yes',
					'premium_fe_bg_color_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_bg_color_to',
			array(
				'label'     => __( 'To', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'condition' => array(
					'premium_fe_switcher'          => 'yes',
					'premium_fe_bg_color_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_bg_color_duration',
			array(
				'label'     => __( 'Duration', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 1000,
				),
				'condition' => array(
					'premium_fe_switcher'          => 'yes',
					'premium_fe_bg_color_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_bg_color_delay',
			array(
				'label'     => __( 'Delay', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'condition' => array(
					'premium_fe_switcher'          => 'yes',
					'premium_fe_bg_color_switcher' => 'yes',
				),

			)
		);

	}

	/**
	 * Add Blur Controls
	 *
	 * Extends Floating Effects Blur controls.
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_blur_controls( $elem ) {

		$elem->add_control(
			'premium_fe_blur_val',
			array(
				'label'     => __( 'Value', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'from' => 0,
						'to'   => 1,
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
				'condition' => array(
					'premium_fe_switcher'      => 'yes',
					'premium_fe_blur_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_blur_duration',
			array(
				'label'     => __( 'Duration', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 1000,
				),
				'condition' => array(
					'premium_fe_switcher'      => 'yes',
					'premium_fe_blur_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_blur_delay',
			array(
				'label'     => __( 'Delay', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'condition' => array(
					'premium_fe_switcher'      => 'yes',
					'premium_fe_blur_switcher' => 'yes',
				),

			)
		);

	}

	/**
	 * Add Contrast Controls
	 *
	 * Extends Floating Effects Contrast controls.
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_contrast_controls( $elem ) {

		$elem->add_control(
			'premium_fe_contrast_val',
			array(
				'label'     => __( 'Value', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'from' => 0,
						'to'   => 50,
					),
					'unit'  => '%',
				),
				'range'     => array(
					'%' => array(
						'min'  => 0,
						'max'  => 200,
						'step' => 10,
					),
				),
				'labels'    => array(
					__( 'From', 'premium-addons-pro' ),
					__( 'To', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'premium_fe_switcher'          => 'yes',
					'premium_fe_contrast_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_contrast_duration',
			array(
				'label'     => __( 'Duration', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 1000,
				),
				'condition' => array(
					'premium_fe_switcher'          => 'yes',
					'premium_fe_contrast_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_contrast_delay',
			array(
				'label'     => __( 'Delay', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'condition' => array(
					'premium_fe_switcher'          => 'yes',
					'premium_fe_contrast_switcher' => 'yes',
				),

			)
		);

	}

	/**
	 * Add Grayscale Controls
	 *
	 * Extends Floating Effects Grayscale controls.
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_gs_controls( $elem ) {

		$elem->add_control(
			'premium_fe_gScale_val',
			array(
				'label'     => __( 'Value', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'from' => 0,
						'to'   => 50,
					),
					'unit'  => '%',
				),
				'labels'    => array(
					__( 'From', 'premium-addons-pro' ),
					__( 'To', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'premium_fe_switcher'        => 'yes',
					'premium_fe_gScale_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_gScale_duration',
			array(
				'label'     => __( 'Duration', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 1000,
				),
				'condition' => array(
					'premium_fe_switcher'        => 'yes',
					'premium_fe_gScale_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_gScale_delay',
			array(
				'label'     => __( 'Delay', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'condition' => array(
					'premium_fe_switcher'        => 'yes',
					'premium_fe_gScale_switcher' => 'yes',
				),

			)
		);

	}

	/**
	 * Add Hue Controls
	 *
	 * Extends Floating Effects Hue controls.
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_hue_controls( $elem ) {

		$elem->add_control(
			'premium_fe_hue_val',
			array(
				'label'     => __( 'Value', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'from' => 0,
						'to'   => 90,
					),
					'unit'  => 'deg',
				),
				'range'     => array(
					'deg' => array(
						'min'  => 0,
						'max'  => 360,
						'step' => 10,
					),
				),
				'labels'    => array(
					__( 'From', 'premium-addons-pro' ),
					__( 'To', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'premium_fe_switcher'     => 'yes',
					'premium_fe_hue_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_hue_duration',
			array(
				'label'     => __( 'Duration', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 1000,
				),
				'condition' => array(
					'premium_fe_switcher'     => 'yes',
					'premium_fe_hue_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_hue_delay',
			array(
				'label'     => __( 'Delay', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'condition' => array(
					'premium_fe_switcher'     => 'yes',
					'premium_fe_hue_switcher' => 'yes',
				),

			)
		);

	}

	/**
	 * Add Brightness Controls
	 *
	 * Extends Floating Effects Brightness controls.
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_brightness_controls( $elem ) {

		$elem->add_control(
			'premium_fe_brightness_val',
			array(
				'label'     => __( 'Value', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'from' => 0,
						'to'   => 50,
					),
					'unit'  => '%',
				),
				'range'     => array(
					'%' => array(
						'min'  => 0,
						'max'  => 200,
						'step' => 10,
					),
				),
				'labels'    => array(
					__( 'From', 'premium-addons-pro' ),
					__( 'To', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'premium_fe_switcher'            => 'yes',
					'premium_fe_brightness_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_brightness_duration',
			array(
				'label'     => __( 'Duration', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 1000,
				),
				'condition' => array(
					'premium_fe_switcher'            => 'yes',
					'premium_fe_brightness_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_brightness_delay',
			array(
				'label'     => __( 'Delay', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'condition' => array(
					'premium_fe_switcher'            => 'yes',
					'premium_fe_brightness_switcher' => 'yes',
				),

			)
		);

	}

	/**
	 * Add Saturation Controls
	 *
	 * Extends Floating Effects Saturation controls.
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_saturation_controls( $elem ) {

		$elem->add_control(
			'premium_fe_saturate_val',
			array(
				'label'     => __( 'Value', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'from' => 0,
						'to'   => 50,
					),
					'unit'  => '%',
				),
				'range'     => array(
					'%' => array(
						'min'  => 0,
						'max'  => 200,
						'step' => 10,
					),
				),
				'labels'    => array(
					__( 'From', 'premium-addons-pro' ),
					__( 'To', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'premium_fe_switcher'          => 'yes',
					'premium_fe_saturate_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_saturate_duration',
			array(
				'label'     => __( 'Duration', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 1000,
				),
				'condition' => array(
					'premium_fe_switcher'          => 'yes',
					'premium_fe_saturate_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_saturate_delay',
			array(
				'label'     => __( 'Delay', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'condition' => array(
					'premium_fe_switcher'          => 'yes',
					'premium_fe_saturate_switcher' => 'yes',
				),

			)
		);

	}

	/**
	 * Add Maps Marker Controls
	 *
	 * @since 2.8.20
	 * @access public
	 *
	 * @param object $element elementor element.
	 */
	public function add_maps_marker_controls( $element ) {

		$element->add_control(
			'marker_skin',
			array(
				'label'     => __( 'Skin', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'skin1' => __( 'Google Default', 'premium-addons-pro' ),
					'skin3' => __( 'Inline Skin', 'premium-addons-pro' ),
					'skin2' => __( 'Block Skin', 'premium-addons-pro' ),
				),
				'default'   => 'skin1',
				'condition' => array(
					'advanced_view' => 'yes',
				),
			)
		);

		$element->add_control(
			'pin_img',
			array(
				'label'     => __( 'Image', 'premium-addons-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'advanced_view' => 'yes',
				),
			)
		);

		$element->add_control(
			'pin_address',
			array(
				'label'       => __( 'Address', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '20 W 34th St., New York, NY, USA',
				'condition'   => array(
					'advanced_view' => 'yes',
				),
			)
		);

		$element->add_control(
			'pin_website',
			array(
				'label'       => __( 'Website', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'www.premiumaddons.com',
				'condition'   => array(
					'advanced_view' => 'yes',
				),
			)
		);

		$element->add_control(
			'pin_phone',
			array(
				'label'       => __( 'Phone Number', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '+12127363100',
				'condition'   => array(
					'advanced_view' => 'yes',
				),
			)
		);

		$element->add_control(
			'pin_hours',
			array(
				'label'       => __( 'Working Hours', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '10AM-10PM',
				'condition'   => array(
					'advanced_view' => 'yes',
				),
			)
		);

	}


	/**
	 * Class Constructor
	 */
	public function __construct() {

		$this->require_files();
		$this->register_modules();

		$this->run_modules_extender();

	}

}
