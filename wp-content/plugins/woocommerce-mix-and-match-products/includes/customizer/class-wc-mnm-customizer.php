<?php
/**
 * Customizer Functions and Filters
 *
 * @package  WooCommerce Mix and Match Products/Customizer
 * @since    2.0.0
 * @version  2.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_Customizer Class.
 *
 * Functions and filters for Mix and Match customizer options.
 */
class WC_MNM_Customizer {

	/**
	 * The single instance of the class.
	 * @var WC_MNM_Customizer
	 */
	protected static $_instance = null;

	/**
	 * Main class instance. Ensures only one instance of class is loaded or can be loaded.
	 *
	 * @static
	 * @return WC_MNM_Customizer
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * __construct function.
	 *
	 * @return object
	 */
	public function __construct() {
		add_action( 'customize_preview_init', array( $this, 'customize_preview' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_controls' ) );
		add_action( 'customize_register', array( $this, 'add_section' ) );
		add_action( 'admin_menu', array( $this, 'add_fse_customize_link' ) );
		add_filter( 'woocommerce_product_get_layout_override', array( $this, 'disable_override' ) );
	}

	/**
	 * Enqueues scripts for the live preview.
	 */
	public function customize_preview() {
		$suffix      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$script_path = 'assets/js/admin/customizer-preview' . $suffix . '.js';

		wp_enqueue_script( 'wc-mnm-customizer-preview', WC_Mix_and_Match()->plugin_url() . '/' . $script_path, array( 'customize-preview' ), WC_Mix_and_Match()->get_file_version( WC_MNM_ABSPATH . $script_path ), true );

		// Localization.
		$localize = array(
			'product_page'   => get_permalink( $this->get_preview_page_id() ),
		);

		wp_localize_script( 'wc-mnm-customizer-preview', 'WC_MNM_CONTROLS', $localize );
	}

	/**
	 * Enqueue scripts.
	 */
	public function customize_controls() {
		$suffix      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$script_path = 'assets/js/admin/customizer-controls' . $suffix . '.js';

		wp_enqueue_script( 'wc-mnm-customizer-controls', WC_Mix_and_Match()->plugin_url() . '/' . $script_path, array( 'customize-controls' ), WC_Mix_and_Match()->get_file_version( WC_MNM_ABSPATH . $script_path ) );

		// Localization.
		$localize = array(
			'product_page'   => get_permalink( $this->get_preview_page_id() ),
		);

		wp_localize_script( 'wc-mnm-customizer-controls', 'WC_MNM_CONTROLS', $localize );

	}

	/**
	 * Add Mix and Match section to the WooCommerce panel.
	 *
	 * @param  bool     $wp_customize
	 */
	public function add_section( $wp_customize ) {

		if ( ! class_exists( 'KIA_Customizer_Toggle_Control' ) ) {
			require_once 'controls/kia-customizer-toggle-control/class-kia-customizer-toggle-control.php';
		}
		if ( ! class_exists( 'KIA_Customizer_Radio_Image_Control' ) ) {
			require_once 'controls/kia-customizer-radio-image-control/class-kia-customizer-radio-image-control.php';
		}
		if ( ! class_exists( 'KIA_Customizer_Range_Control' ) ) {
			require_once 'controls/kia-customizer-range-control/class-kia-customizer-range-control.php';
		}

		 // Register the control types that we're using as JavaScript controls.
		 $wp_customize->register_control_type( 'KIA_Customizer_Toggle_Control' );
		 $wp_customize->register_control_type( 'KIA_Customizer_Radio_Image_Control' );
		 $wp_customize->register_control_type( 'KIA_Customizer_Range_Control' );

		/**
		 * Custom section
		 */
		$wp_customize->add_section(
			'wc_mnm',
			array(
				'title' => __( 'Mix and Match Products', 'woocommerce-mix-and-match-products' ),
				'priority' => 21,
				'panel' => 'woocommerce',
			)
		);

		/**
		 * Layout
		 */
		$wp_customize->add_setting(
			'wc_mnm_layout',
			array(
				'default'              => 'tabular',
				'type'                 => 'option',
				'capability'           => 'manage_woocommerce',
				'sanitize_callback'    => array( 'KIA_Customizer_Radio_Image_Control', 'sanitize' ),
			)
		);

		$wp_customize->add_control(
			new KIA_Customizer_Radio_Image_Control(
				$wp_customize,
				'wc_mnm_layout',
				array(
					'label'       => __( 'Contents layout', 'woocommerce-mix-and-match-products' ),
					'section'     => 'wc_mnm',
					'settings'    => 'wc_mnm_layout',
					'choices'     => WC_Product_Mix_and_Match::get_layout_options(),
				)
			)
		);

		// The following settings should be hidden if the theme is declaring the values.
		$wp_customize->add_setting(
			'wc_mnm_number_columns',
			array(
				'default'              => 3,
				'type'                 => 'option',
				'capability'           => 'manage_woocommerce',
				'transport'   => 'postMessage',
				'sanitize_callback'    => 'absint',
				'sanitize_js_callback' => 'absint',
			)
		);

		$wp_customize->add_control(
			new KIA_Customizer_Range_Control(
				$wp_customize,
				'wc_mnm_number_columns',
				array(
					'type'        => 'kia-range',
					'label'    => __( 'Number of columns', 'woocommerce-mix-and-match-products' ),
					'description' => __( 'How many products should be shown per row?', 'woocommerce-mix-and-match-products' ),
					'section'  => 'wc_mnm',
					'settings' => 'wc_mnm_number_columns',
					'input_attrs' => array(
						'min'  => wc_get_theme_support( 'product_grid::min_columns', 1 ),
						'max'  => wc_get_theme_support( 'product_grid::max_columns', 6 ),
						'step' => 1,
					),
				)
			)
		);

		/**
		 * Form location
		 */
		$wp_customize->add_setting(
			'wc_mnm_add_to_cart_form_location',
			array(
				'default'    => 'default',
				'type'       => 'option',
				'capability' => 'manage_woocommerce',
				'sanitize_callback'    => array( 'KIA_Customizer_Radio_Image_Control', 'sanitize' ),
			)
		);

		$wp_customize->add_control(
			new KIA_Customizer_Radio_Image_Control(
				$wp_customize,
				'wc_mnm_add_to_cart_form_location',
				array(
					'label'    => __( 'Add to cart location', 'woocommerce-mix-and-match-products' ),
					'section'  => 'wc_mnm',
					'settings' => 'wc_mnm_add_to_cart_form_location',
					'choices'     => WC_Product_Mix_and_Match::get_add_to_cart_form_location_options(),
				)
			)
		);

		/**
		 * Display thumbnails
		 */
		$wp_customize->add_setting(
			'wc_mnm_display_thumbnail',
			array(
				'default'              => 'yes',
				'type'                 => 'option',
				'capability'           => 'manage_woocommerce',
				'sanitize_callback'    => 'wc_bool_to_string',
				'sanitize_js_callback' => 'wc_string_to_bool',
			)
		);

		$wp_customize->add_control(
			new KIA_Customizer_Toggle_Control(
				$wp_customize,
				'wc_mnm_display_thumbnail',
				array(
					'label'    => __( 'Display thumbnail', 'woocommerce-mix-and-match-products' ),
					'section'  => 'wc_mnm',
					'type'     => 'kia-toggle',
					'settings' => 'wc_mnm_display_thumbnail',
				)
			)
		);

		/**
		 * Display descriptions
		 */
		$wp_customize->add_setting(
			'wc_mnm_display_short_description',
			array(
				'default'              => 'no',
				'type'                 => 'option',
				'capability'           => 'manage_woocommerce',
				'sanitize_callback'    => 'wc_bool_to_string',
				'sanitize_js_callback' => 'wc_string_to_bool',
			)
		);

		$wp_customize->add_control(
			new KIA_Customizer_Toggle_Control(
				$wp_customize,
				'wc_mnm_display_short_description',
				array(
					'label'    => __( 'Display short description', 'woocommerce-mix-and-match-products' ),
					'section'  => 'wc_mnm',
					'type'     => 'kia-toggle',
					'settings' => 'wc_mnm_display_short_description',
				)
			)
		);

	}

	/**
	 * For FSE themes add a "Customize Mix and Match" link to the Appearance menu.
	 *
	 * FSE themes hide the "Customize" link in the Appearance menu. In Mix and Match we have several options that can currently
	 * only be edited via the Customizer. For now, we are thus adding a new link for Mix and Match specific Customizer options.
	 *
	 * @since 2.0.0
	 */
	public function add_fse_customize_link() {

		// Exit early if the FSE theme feature isn't present or the current theme is not a FSE theme.
		if ( ! function_exists( 'gutenberg_is_fse_theme' ) || function_exists( 'gutenberg_is_fse_theme' ) && ! gutenberg_is_fse_theme() ) {
			return;
		}

		// Add a link to the Mix and Match panel in the Customizer.
		add_submenu_page(
			'themes.php',
			__( 'Customize Mix and Match', 'woocommerce-mix-and-match-products' ),
			__( 'Customize Mix and Match', 'woocommerce-mix-and-match-products' ),
			'edit_theme_options',
			admin_url( 'customize.php?autofocus[section]=wc_mnm' )
		);
	}


	/**
	 * No per-product overrides allowed when viewing customizer
	 *
	 * @param bool
	 *
	 * @since 2.3.0
	 */
	public function disable_override( $override ) {

		if ( is_customize_preview() ) {
			$override = false;
		}

		return $override;
	}
 

	/**
	 * Get recent MNM product ID.
	 *
	 * @return int
	 */
	private function get_preview_page_id() {

		$products = wc_get_products(
            array(
			'type' => 'mix-and-match',
			'limit' => 1,
			'return' => 'ids',
            ) 
        );

		return ! empty( $products ) ? current( $products ) : 0;

	}

} // End class.
