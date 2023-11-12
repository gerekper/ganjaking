<?php
/**
 * UAEL Core Plugin.
 *
 * @package UAEL
 */

namespace UltimateElementor;

use UltimateElementor\Classes\UAEL_Helper;
use UltimateElementor\Modules\ControlQuery\Types\Uae_Control_Query;
use UltimateElementor\Modules\PresetsSelect\Controls\Presets_Select;
use UltimateElementor\Modules\QueryPost\Controls\Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * UAEL_Core_Plugin.
 *
 * @package UAEL
 */
class UAEL_Core_Plugin {

	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 * Member Variable
	 *
	 * @var Modules Manager
	 */
	public $modules_manager;

	/**
	 * Cross-Site CDN URL.
	 *
	 * @since  1.24.1
	 * @var (String) URL
	 */
	public $cdn_url;

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Branding Widget details
	 *
	 * @var branding
	 */
	private static $branding = null;

	/**
	 * Constructor
	 */
	public function __construct() {

		spl_autoload_register( array( $this, 'autoload' ) );

		$this->includes();

		$this->setup_actions_filters();
	}

	/**
	 * AutoLoad
	 *
	 * @since 0.0.1
	 * @param string $class class.
	 */
	public function autoload( $class ) {

		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		$class_to_load = $class;

		if ( ! class_exists( $class_to_load ) ) {
			$filename = strtolower(
				preg_replace(
					array( '/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ),
					array( '', '$1-$2', '-', DIRECTORY_SEPARATOR ),
					$class_to_load
				)
			);
			$filename = UAEL_DIR . $filename . '.php';

			if ( is_readable( $filename ) ) {
				include $filename;
			}
		}
	}

	/**
	 * Includes.
	 *
	 * @since 0.0.1
	 */
	private function includes() {

		require UAEL_DIR . 'classes/class-uael-admin.php';
		require UAEL_DIR . 'includes/manager/modules-manager.php';

		if ( UAEL_Helper::is_widget_active( 'Image_Gallery' ) ) {
			require UAEL_DIR . 'classes/class-uael-attachment.php';
		}

		require_once UAEL_DIR . 'lib/astra-notices/class-astra-notices.php';
	}

	/**
	 * Setup Actions Filters.
	 *
	 * @since 0.0.1
	 */
	private function setup_actions_filters() {

		add_shortcode( 'uael-template', array( $this, 'uael_template_shortcode' ) );

		add_action( 'elementor/init', array( $this, 'elementor_init' ) );

		add_action( 'elementor/elements/categories_registered', array( $this, 'widget_category' ) );

		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_widget_scripts' ) );

		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'register_widgets_helper_script' ) );

		add_action( 'elementor/controls/register', array( $this, 'register_controls' ) );

		add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'enqueue_widget_styles' ) );

		// Active widgets data to analytics.
		add_filter( 'bsf_core_stats', array( $this, 'uae_specific_stats' ) );

		if ( UAEL_Helper::is_widget_active( 'Cross_Domain' ) ) {

			add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'enqueue_copy_paste_scripts' ), 11, 0 );
			require_once UAEL_DIR . 'classes/class-uael-cross-domain-copy-paste.php';
			add_action( 'elementor/preview/enqueue_scripts', array( $this, 'enqueue_fpcp_preview' ) );
		}

		if ( ! isset( self::$branding ) ) {
			self::$branding = UAEL_Helper::get_white_labels();
		}

		// Hide analytics notice if UAE is white labeled.
		if ( isset( self::$branding['plugin']['name'] ) && '' !== self::$branding['plugin']['name'] ) {

			add_filter( 'bsf_white_label_options', array( $this, 'uae_bsf_analytics_white_label' ) );
		}
	}

	/**
	 * Register custom controls.
	 *
	 * @return void
	 */
	public function register_controls() {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;

		$controls_manager->register( new Query() );
		$controls_manager->register( new Presets_Select() );
		$controls_manager->register( new Uae_Control_Query() );
	}

	/**
	 * Elementor Template Shortcode.
	 *
	 * @param array $atts Shortcode Attributes.
	 * @since 0.0.1
	 */
	public function uael_template_shortcode( $atts ) {

		$atts = shortcode_atts(
			array(
				'id' => '',
			),
			$atts,
			'uael-template'
		);

		if ( '' !== $atts['id'] ) {
			return \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $atts['id'] );
		}
	}

	/**
	 * Elementor Init.
	 *
	 * @since 0.0.1
	 */
	public function elementor_init() {

		$this->modules_manager = new Module_Manager();

		$this->init_category();

		do_action( 'ultimate_elementor/init' );

		/* Wpml Compatibility */
		require UAEL_DIR . 'compatibility/class-uael-wpml.php';
	}

	/**
	 * Sections init
	 *
	 * @since 1.24.0
	 * @param object $branding white label.
	 */
	public function plugin_branding( $branding ) {

		$plugin_short_name = $branding['plugin']['short_name'];
		return ( isset( $plugin_short_name ) && '' !== $plugin_short_name ) ? $plugin_short_name . ' Elements' : UAEL_CATEGORY;
	}

	/**
	 * Sections init
	 *
	 * @since 0.0.1
	 * @param object $this_cat class.
	 */
	public function widget_category( $this_cat ) {

		if ( ! isset( self::$branding ) ) {
			self::$branding = UAEL_Helper::get_white_labels();
		}

		$category = $this->plugin_branding( self::$branding );

		$this_cat->add_category(
			'ultimate-elements',
			array(
				'title' => $category,
				'icon'  => 'eicon-font',
			)
		);

		return $this_cat;
	}


	/**
	 * Sections init
	 *
	 * @since 0.0.1
	 *
	 * @access private
	 */
	private function init_category() {

		if ( version_compare( ELEMENTOR_VERSION, '2.0.0' ) < 0 ) {

			if ( ! isset( self::$branding ) ) {
				self::$branding = UAEL_Helper::get_white_labels();
			}

			$category = $this->plugin_branding( self::$branding );

			\Elementor\Plugin::instance()->elements_manager->add_category(
				'ultimate-elements',
				array(
					'title' => $category,
				),
				1
			);
		}
	}

	/**
	 * Register module required js on elementor's action.
	 *
	 * @since 0.0.1
	 */
	public function register_widget_scripts() {

		$js_files            = UAEL_Helper::get_widget_script();
		$map_options         = UAEL_Helper::get_integrations_options();
		$language            = '';
		$api_url             = 'https://maps.googleapis.com';
		$validation_messages = array(
			'ajax_url'           => admin_url( 'admin-ajax.php' ),
			'invalid_mail'       => __( 'Enter valid Email!', 'uael' ),
			'pass_unmatch'       => __( 'The specified password do not match!', 'uael' ),
			'required'           => __( 'This Field is required!', 'uael' ),
			'incorrect_password' => __( 'Error: The Password you have entered is incorrect.', 'uael' ),
			'invalid_username'   => __( 'Unknown username. Check again or try your email address.', 'uael' ),
			'invalid_email'      => __( 'Unknown email address. Check again or try your username.', 'uael' ),
			'logged_in_message'  => __( 'Thanks for logging in, ', 'uael' ),
			'wp_version'         => version_compare( get_bloginfo( 'version' ), '5.4.99', '>=' ),
		);
		if ( isset( $map_options['language'] ) && '' !== $map_options['language'] ) {
			$language = 'language=' . $map_options['language'];

			// This checks for Chinese language.
			// The Maps JavaScript API is served within China from http://maps.google.cn.
			if ( 'zh-CN' === $map_options['language'] ||
				'zh-TW' === $map_options['language']
			) {
				$api_url = 'http://maps.googleapis.cn';
			}
		}

		if ( isset( $map_options['google_api'] ) && '' !== $map_options['google_api'] ) {
			$language = '&' . $language;
			$url      = $api_url . '/maps/api/js?key=' . $map_options['google_api'] . $language;
		} else {
			$url = $api_url . '/maps/api/js?' . $language;
		}

		if ( UAEL_Helper::is_widget_active( 'Particles' ) ) {

			wp_localize_script(
				'elementor-frontend',
				'uael_particles_script',
				array(
					'uael_particles_url' => UAEL_URL . 'assets/min-js/uael-particles.min.js',
					'particles_url'      => UAEL_URL . 'assets/lib/particles/particles.min.js',
					'snowflakes_image'   => UAEL_URL . 'assets/img/snowflake.svg',
					'gift'               => UAEL_URL . 'assets/img/gift.png',
					'tree'               => UAEL_URL . 'assets/img/tree.png',
					'skull'              => UAEL_URL . 'assets/img/skull.png',
					'ghost'              => UAEL_URL . 'assets/img/ghost.png',
					'moon'               => UAEL_URL . 'assets/img/moon.png',
					'bat'                => UAEL_URL . 'assets/img/bat.png',
					'pumpkin'            => UAEL_URL . 'assets/img/pumpkin.png',
				)
			);
		}

		wp_register_script( 'uael-google-maps-api', $url, array( 'jquery' ), UAEL_VER, true );

		wp_register_script( 'uael-google-maps-cluster', 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js', array( 'jquery' ), UAEL_VER, true );

		wp_register_script( 'uael-video-subscribe', 'https://apis.google.com/js/platform.js', array( 'jquery' ), UAEL_VER, true );

		wp_register_script( 'uael-google-recaptcha', 'https://www.google.com/recaptcha/api.js?onload=onLoadUAEReCaptcha&render=explicit', array( 'jquery', 'uael-registration' ), UAEL_VER, true );

		foreach ( $js_files as $handle => $data ) {
			wp_register_script( $handle, UAEL_URL . $data['path'], $data['dep'], UAEL_VER, $data['in_footer'] );
		}

		wp_localize_script(
			'uael-frontend-script',
			'uael_script',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			)
		);

		wp_localize_script(
			'uael-posts',
			'uael_posts_script',
			array(
				'ajax_url'    => admin_url( 'admin-ajax.php' ),
				'posts_nonce' => wp_create_nonce( 'uael-posts-widget-nonce' ),
			)
		);

		$wc_localize = apply_filters(
			'uael_js_localize',
			array(
				'ajax_url'              => admin_url( 'admin-ajax.php' ),
				'get_product_nonce'     => wp_create_nonce( 'uael-product-nonce' ),
				'quick_view_nonce'      => wp_create_nonce( 'uael-qv-nonce' ),
				'add_cart_nonce'        => wp_create_nonce( 'uael-ac-nonce' ),
				'checkout_update_nonce' => wp_create_nonce( 'uael-checkout-nonce' ),
			)
		);

		wp_localize_script( 'uael-woocommerce', 'uael_wc_script', $wc_localize );

		wp_localize_script(
			'uael-woocommerce',
			'uael_woo_chekout',
			array(
				'validation_msg' => __( 'Invalid or data missing in the required(*) fields.', 'uael' ),
			)
		);

		wp_localize_script(
			'uael-table',
			'uael_table_script',
			array(
				'table_not_found_str' => __( 'No matching records found', 'uael' ),
				'table_length_string' => __( 'Show _MENU_ Entries', 'uael' ),
			)
		);

		if ( UAEL_Helper::is_widget_active( 'Timeline' ) ) {
			wp_localize_script(
				'uael-frontend-script',
				'uael_post_loader_script',
				array(
					'post_loader' => UAEL_URL . 'assets/img/post-loader.gif',
				)
			);
		}

		if ( UAEL_Helper::is_widget_active( 'LoginForm' ) ) {
			wp_localize_script(
				'uael-frontend-script',
				'uael_login_form_script',
				apply_filters( 'uael_form_error_string', $validation_messages )
			);
		}

		wp_localize_script(
			'uael-registration',
			'uaelRegistration',
			apply_filters( 'uael_form_error_string', $validation_messages )
		);
	}

	/**
	 * Register module required js on elementor's editor action.
	 *
	 * @since 1.30.0
	 */
	public function register_widgets_helper_script() {

		wp_localize_script(
			'elementor-editor',
			'UAEWidgetsData',
			array(
				'allowed_tags'         => UAEL_Helper::ALLOWED_HTML_WRAPPER_TAGS,
				'extension_tree'       => UAEL_URL . 'assets/img/extension-tree.png',
				'extension_snowman'    => UAEL_URL . 'assets/img/extension-snowman.png',
				'extension_hang_decor' => UAEL_URL . 'assets/img/extension-hang-decor.png',
				'extension_deer'       => UAEL_URL . 'assets/img/extension-deer.png',
				'santa_cap'            => UAEL_URL . 'assets/img/uae-santa-cap.png',
				'santa_claus'          => UAEL_URL . 'assets/img/santa-claus.png',
				'snow'                 => UAEL_URL . 'assets/img/snow.png',
				'hanukkah'             => UAEL_URL . 'assets/img/hanukkah.png',
			)
		);

	}

	/**
	 * Enqueue module required styles.
	 *
	 * @since 0.0.1
	 */
	public function enqueue_widget_styles() {

		$css_files = UAEL_Helper::get_widget_style();

		if ( ! empty( $css_files ) ) {
			foreach ( $css_files as $handle => $data ) {
				wp_register_style( $handle, UAEL_URL . $data['path'], $data['dep'], UAEL_VER );
				wp_enqueue_style( $handle );
			}
		}

		if ( class_exists( 'Caldera_Forms_Render_Assets' ) && class_exists( 'Caldera_Forms' ) && UAEL_Helper::is_widget_active( 'CafStyler' ) ) {
			\Caldera_Forms_Render_Assets::maybe_register();
			\Caldera_Forms_Render_Assets::optional_style_includes();
			\Caldera_Forms_Render_Assets::enqueue_style( 'front' );
			\Caldera_Forms_Render_Assets::enqueue_style( 'fields' );

			foreach ( \Caldera_Forms_Fields::get_all() as $field_type ) {
				if ( ! empty( $field_type['styles'] ) ) {
					foreach ( $field_type['styles'] as $style ) {
						\Caldera_Forms_Render_Assets::enqueue_style( $style );
					}
				}
			}
		}

		if ( function_exists( 'wpforms' ) && UAEL_Helper::is_widget_active( 'WpfStyler' ) ) {
			wpforms()->frontend->assets_css();
		}

		$gravity_js_load_frontend_filter = apply_filters( 'uael_gravity_js_load_frontend', false );
		$gravity_js_frontend             = class_exists( 'GFCommon' ) && UAEL_Helper::is_widget_active( 'GfStyler' );
		$gravity_js_backend              = $gravity_js_frontend && \Elementor\Plugin::$instance->preview->is_preview_mode();

		$gravity_load_condition = ! $gravity_js_load_frontend_filter ? $gravity_js_backend : $gravity_js_frontend;

		if ( $gravity_load_condition ) {

			$gf_forms = \RGFormsModel::get_forms( null, 'title' );

			foreach ( $gf_forms as $form ) {

				if ( '0' !== $form->id ) {
					wp_enqueue_script( 'gform_gravityforms' );
					gravity_form_enqueue_scripts( $form->id );
				}
			}
		}

		if ( function_exists( 'wpFluentForm' ) && UAEL_Helper::is_widget_active( 'FfStyler' ) && \Elementor\Plugin::$instance->preview->is_preview_mode() ) {

			wp_enqueue_style(
				'uael-fluent-forms-default-css',
				plugins_url( '/fluentform/public/css/fluentform-public-default.css', 'fluentform' ),
				null,
				FLUENTFORM_VERSION
			);

			wp_enqueue_style(
				'uael-fluent-forms-public-css',
				plugins_url( '/fluentform/public/css/fluent-forms-public.css', 'fluentform' ),
				null,
				FLUENTFORM_VERSION
			);
		}
	}

	/**
	 * Load required js on before enqueue widget JS.
	 *
	 * @since 1.24.0
	 */
	public function enqueue_copy_paste_scripts() {

		if ( ! isset( self::$branding ) ) {
			self::$branding = UAEL_Helper::get_white_labels();
		}

		$plugin_branding   = self::$branding['plugin'];
		$category          = ( isset( $plugin_branding['short_name'] ) && '' !== $plugin_branding['short_name'] ) ? $plugin_branding['short_name'] : UAEL_PLUGIN_SHORT_NAME;
		$cross_domain_icon = ( isset( $plugin_branding['short_name'] ) && '' !== $plugin_branding['short_name'] ) ? '' : 'uael-icon-uae';

		$folder = UAEL_Helper::get_js_folder();
		$suffix = UAEL_Helper::get_js_suffix();

		$this->cdn_url = apply_filters( 'uael_cross_domain_cdn', 'https://brainstormforce.github.io/uae-cdcp/updated-index.html' );

		// Check for required Compatible Elementor version.
		if ( ! version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
			$elementor_old_compatibility = true;
		} else {
			$elementor_old_compatibility = false;
		}

		wp_enqueue_script(
			'uael-cross-site-cp-helper',
			UAEL_URL . 'assets/' . $folder . '/uael-cross-site-cp-helper' . $suffix . '.js',
			null,
			UAEL_VER,
			true
		);

		wp_enqueue_script(
			'uael-cross-domain',
			UAEL_URL . 'assets/' . $folder . '/uael-cross-domain-copy-paste' . $suffix . '.js',
			array( 'jquery', 'elementor-editor', 'uael-cross-site-cp-helper' ),
			UAEL_VER,
			true
		);

		wp_localize_script(
			'uael-cross-domain',
			'uael_cross_domain',
			array(
				'ajaxURL'             => admin_url( 'admin-ajax.php' ),
				'nonce'               => wp_create_nonce( 'uael_process_import' ),
				'widget_not_found'    => __( 'The widget type you are trying to paste is not available on this site.', 'uael' ),
				/* translators: %s: html tags */
				'uae_copy'            => sprintf( __( '%1s Copy', 'uael' ), $category ),
				/* translators: %s: html tags */
				'uae_paste'           => sprintf( __( '%1s Paste', 'uael' ), $category ),
				/* translators: %s: html tags */
				'uae_copy_all'        => sprintf( __( '%1s Copy All', 'uael' ), $category ),
				/* translators: %s: html tags */
				'uae_paste_all'       => sprintf( __( '%1s Paste All', 'uael' ), $category ),
				'cross_domain_icon'   => $cross_domain_icon,
				'cross_domain_cdn'    => $this->cdn_url,
				'elementorCompatible' => $elementor_old_compatibility,
			)
		);

	}

	/**
	 * Pass UAE specific stats to BSF analytics.
	 *
	 * @since 1.25.0
	 * @param array $default_stats Default stats array.
	 * @return array $default_stats Default stats with UAE specific stats array.
	 */
	public function uae_specific_stats( $default_stats ) {

		$widgets        = array();
		$active_widgets = array();
		$widgets        = UAEL_Helper::get_widget_list();

		foreach ( $widgets as $widget => $name ) {
			if ( UAEL_Helper::is_widget_active( $widget ) ) {
				array_push( $active_widgets, $widget );
			}
		}

		$default_stats['uae_settings'] = array(
			'elementor_version'  => ELEMENTOR_VERSION,
			'uae_active_widgets' => $active_widgets,
		);

		return $default_stats;
	}

	/**
	 * Return White Label status to BSF Analytics.
	 * Return true if the White Label is enabled from UAE to the BSF Analytics library.
	 *
	 * @since 1.25.0
	 * @param array $bsf_analytics_wl_arr array of white labeled products.
	 * @return array product name with white label status.
	 */
	public function uae_bsf_analytics_white_label( $bsf_analytics_wl_arr ) {
		if ( ! isset( $bsf_analytics_wl_arr['uael'] ) ) {
			$bsf_analytics_wl_arr['uael'] = true;
		}

		return $bsf_analytics_wl_arr;
	}

	/**
	 * Load FPCP helper css in elementor editor preview screen.
	 *
	 * @since 1.28.0
	 * @access public
	 */
	public function enqueue_fpcp_preview() {
		wp_register_style(
			'uael-fpcp-style',
			UAEL_URL . 'editor-assets/css/fpcp-preview.css',
			array(),
			UAEL_VER
		);
		wp_enqueue_style( 'uael-fpcp-style' );
	}
}

/**
 *  Prepare if class 'UAEL_Core_Plugin' exist.
 *  Kicking this off by calling 'get_instance()' method
 */
UAEL_Core_Plugin::get_instance();
