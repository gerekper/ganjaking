<?php
/**
 * Main class
 *
 * @author  YITH
 * @package YITH Easy Login & Register Popup For WooCommerce
 * @version 1.0.0
 */

defined( 'YITH_WELRP' ) || exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_Easy_Login_Register' ) ) {
	/**
	 * YITH Easy Login & Register Popup For WooCommerce
	 *
	 * @since 1.0.0
	 */
	final class YITH_Easy_Login_Register {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var YITH_Easy_Login_Register
		 */
		protected static $instance;

		/**
		 * An array of available social
		 *
		 * @since 1.0.0
		 * @var array
		 */
		protected $available_social = [ 'facebook', 'google' ];

		/**
		 * Popup instance
		 *
		 * @since 1.0.0
		 * @var YITH_Easy_Login_Register_Popup
		 */
		public $popup = null;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 * @return YITH_Easy_Login_Register
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Cloning is forbidden.
		 *
		 * @since 1.0.0
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', '1.0.0' );
		} // End __clone()

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since 1.0.0
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', '1.0.0' );
		} // End __wakeup()

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 * @return void
		 */
		private function __construct() {

			// Load Plugin Framework
			add_action( 'after_setup_theme', [ $this, 'plugin_fw_loader' ], 1 );

			if ( $this->is_admin() ) {
				include 'class.yith-easy-login-register-admin.php';
				new YITH_Easy_Login_Register_Admin();
			} else {
				include 'class.yith-easy-login-register-popup-handler.php';
				new YITH_Easy_Login_Register_Popup_Handler();

				// popup and social classes
				add_action( 'init', [ $this, 'init_social' ], 0 );
				add_action( 'template_redirect', [ $this, 'init_popup' ], 0 );
				// enqueue assets
				add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles_scripts' ], 20 );
			}

			// handle third party plugin compatibility
			add_action( 'init', [ $this, 'load_compatibility_classes' ], 10 );

			// Email
			add_filter( 'woocommerce_email_classes', [ $this, 'add_email' ] );
		}

		/**
		 * Load Plugin Framework
		 *
		 * @since  1.0
		 * @access public
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once $plugin_fw_file;
				}
			}
		}

		/**
		 * Filters woocommerce available mails to add plugin email
		 *
		 * @since  1.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param $emails array
		 * @return array
		 */
		public function add_email( $emails ) {
			$emails['YITH_WELRP_Customer_Authentication_Code'] = include 'email/class.yith-welrp-customer-authentication-code.php';
			return $emails;
		}

		/**
		 * Check if context is admin
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return boolean
		 */
		public function is_admin() {
			$is_ajax = ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['context'] ) && $_REQUEST['context'] == 'frontend' );
			return apply_filters( 'yith_welrp_is_admin', is_admin() && ! $is_ajax );
		}

		/**
		 * Get available social
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return array
		 */
		public function get_available_social() {
			return $this->available_social;
		}

		/**
		 * Init social class
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return void
		 */
		public function init_social() {

			if ( is_user_logged_in() ) {
				return;
			}

			// include deps
			include 'class.yith-easy-login-register-social.php';
			foreach ( $this->available_social as $social ) {
				// get option
				$options = get_option( "yith_welrp_social_login_{$social}", [] );
				// if social is enabled and file exists, instance it
				if ( isset( $options['enabled'] ) && $options['enabled'] == 'yes' && ! empty( $options['app_id'] ) &&
					file_exists( YITH_WELRP_PATH . "includes/class.yith-easy-login-register-social-{$social}.php" ) ) {

					// include class
					include "class.yith-easy-login-register-social-{$social}.php";
					// new instance
					$classname = "YITH_Easy_Login_Register_Social_" . ucfirst( $social );
					new $classname( $options );
				}
			}
		}

		/**
		 * Init the popup instance
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return void
		 */
		public function init_popup() {
			// if user is logged in, return
			if ( is_user_logged_in() ) {
				return;
			}

			$additional = get_option( 'yith_welrp_additional_popup_selectors', '' );
			if ( ! empty( $additional ) || ( is_cart() && ! WC()->cart->is_empty() ) || apply_filters( 'yith_welrp_init_popup', false ) ) {
				include 'class.yith-easy-login-register-popup.php';
				$this->popup = new YITH_Easy_Login_Register_Popup();
			}
		}

		/**
		 * Enqueue styles and scripts
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return void
		 */
		public function enqueue_styles_scripts() {
			wp_register_style( 'yith_welrp_css', YITH_WELRP_ASSETS_URL . 'css/popup-style.css', [], YITH_WELRP_VERSION, 'all' );
			wp_register_style( 'yith_welrp_animate', YITH_WELRP_ASSETS_URL . 'css/animate.min.css', [], YITH_WELRP_VERSION, 'all' );
			wp_register_script( 'yith_welrp_js', YITH_WELRP_ASSETS_URL . 'js/' . yit_load_js_file( 'popup-handler.js' ),
				[ 'jquery', 'wp-util', 'jquery-blockui' ], YITH_WELRP_VERSION, true );

			if ( $this->popup ) {
				// add defer to improve performance
				add_filter( 'script_loader_tag', [ $this, 'add_defer_attribute' ], 10, 2 );

				if ( ! defined( 'YITH_PROTEO_VERSION' ) ) {
					wp_enqueue_style( 'yith_welrp_css' );
					wp_add_inline_style( 'yith_welrp_css', $this->get_custom_css() );
				} else {
					wp_add_inline_style( 'yith-proteo-style', $this->get_custom_css() );
				}

				// password strength meter
				if ( get_option( 'yith_welrp_popup_register_password_strength', 'yes' ) == 'yes' ) {
					wp_enqueue_script( 'wc-password-strength-meter' );
				} else {
					wp_dequeue_script( 'wc-password-strength-meter' );
				}

				wp_enqueue_style( 'yith_welrp_animate' );
				wp_enqueue_script( 'yith_welrp_js' );

				$main_selectors = apply_filters( 'yith_welrp_script_main_selectors', [ '.wc-proceed-to-checkout a' ] );

				// add script data
				wp_localize_script( 'yith_welrp_js', 'yith_welrp', apply_filters( 'yith_welrp_script_data', [
					'popupWidth'         => get_option( 'yith_welrp_popup_width', '590' ),
					'ajaxUrl'            => WC_AJAX::get_endpoint( "%%endpoint%%" ),
					'errorMsg'           => yith_welrp_get_std_error_message(),
					'loader'             => YITH_WELRP_ASSETS_URL . 'images/loader.gif',
					'mainSelector'       => implode(',', $main_selectors ),
					'fsTitle'            => get_option( 'yith_welrp_popup_title', __( 'But first... login or register!', 'yith-easy-login-register-popup-for-woocommerce' ) ),
					'fsAdditionalTitle'  => get_option( 'yith_welrp_additional_popup_title', __( 'Login or Register', 'yith-easy-login-register-popup-for-woocommerce' ) ),
					'additionalSelector' => get_option( 'yith_welrp_additional_popup_selectors', '' ),
					'emailSuggestions'   => [ 'gmail.com', 'hotmail.com', 'outlook.com', 'yahoo.com', 'icloud.com' ],
				] ) );
			}
		}

		/**
		 * Add defer to plugin script to improve performance
		 *
		 * @param string $tag
		 * @param string $handle
		 * @return string
		 */
		public function add_defer_attribute( $tag, $handle ) {
			if ( 'yith_welrp_js' !== $handle ) {
				return $tag;
			}
			return str_replace( ' src', ' defer="defer" src', $tag );
		}

		/**
		 * Get plugin custom css
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return string
		 */
		public function get_custom_css() {

			$custom_css = '';
			// popup background
			$popup_background = get_option( 'yith_welrp_popup_bg', '#ffffff' );
			$popup_color      = get_option( 'yith_welrp_popup_text_color', self::get_proteo_default( 'yith_welrp_popup_text_color', '#1a1a1a' ) );
			// link color
			$link_color = get_option( 'yith_welrp_popup_link_color', self::get_proteo_default( 'yith_welrp_popup_link_color', [ 'normal' => "#007acc", 'hover' => "#686868" ] ) );
			// overlay
			$overlay_bg = get_option( 'yith_welrp_overlay_color', 'rgba(0,0,0,0.5)' );
			// header
			$header_bg_color = get_option( 'yith_welrp_popup_header_bg', self::get_proteo_default( 'yith_welrp_popup_header_bg', '#ffffff' ) );
			// button
			$button_bg_color = get_option( 'yith_welrp_button_bg_color', self::get_proteo_default( 'yith_welrp_button_bg_color', [ 'normal' => "#a46497", 'hover' => "#96588a" ] ) );
			$button_br_color = get_option( 'yith_welrp_button_br_color', self::get_proteo_default( 'yith_welrp_button_br_color', [ 'normal' => "#a46497", 'hover' => "#96588a" ] ) );
			$button_lb_color = get_option( 'yith_welrp_button_lb_color', self::get_proteo_default( 'yith_welrp_button_lb_color', [ 'normal' => "#ffffff", 'hover' => "#ffffff" ] ) );
			// images
			$arrow_eye        = YITH_WELRP_ASSETS_URL . 'images/arrow_eye.svg';
			$arrow_eye_closed = YITH_WELRP_ASSETS_URL . 'images/arrow_eye_closed.svg';
			$close_icon       = YITH_WELRP_ASSETS_URL . 'images/close.png';
			// blur
			if ( get_option( 'yith_welrp_popup_blur_overlay', 'yes' ) == 'yes' ) {
				$custom_css = ".yith_welrp_opened #page{filter:blur(2px);}";
			}

			$custom_css .= "#yith-welrp .yith-welrp-popup-inner,#yith-welrp .yith-welrp-social-sep span{background:{$popup_background};color:{$popup_color}}
            #yith-welrp .yith-welrp-popup-header{background:{$header_bg_color};}#yith-welrp .yith-welrp-popup-inner a{color:{$link_color['normal']}}
		    #yith-welrp .yith-welrp-popup-inner a:hover{color:{$link_color['hover']}}#yith-welrp .yith-welrp-overlay{background:{$overlay_bg};}
		    #yith-welrp .yith-welrp-submit-button,#yith-welrp .yith-welrp-continue-as-guest .button{color:{$button_lb_color['normal']};background:{$button_bg_color['normal']};border-color:{$button_br_color['normal']};}
		    #yith-welrp .yith-welrp-submit-button:hover,#yith-welrp .yith-welrp-continue-as-guest .button:hover{color:{$button_lb_color['hover']};background:{$button_bg_color['hover']};border-color:{$button_br_color['hover']};}
		    #yith-welrp span.yith-welrp-password-eye.opened{background-image:url($arrow_eye);}#yith-welrp span.yith-welrp-password-eye{background-image:url($arrow_eye_closed);}
		    #yith-welrp .yith-welrp-popup-close:not(.custom){background-image:url({$close_icon});}#yith-welrp .yith-welrp-popup-close:not(.custom):hover{background-image:url({$close_icon});}";

			return apply_filters( 'yith_welrp_custom_css', $custom_css );
		}

		/**
		 * Get Proteo default style
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param string $option_id
		 * @param mixed  $default
		 * @return mixed
		 */
		public static function get_proteo_default( $option_id, $default ) {

			if ( ! defined( 'YITH_PROTEO_VERSION' ) ) {
				return $default;
			}

			switch ( $option_id ) {
				case 'yith_welrp_popup_header_bg':
					$default = '#f4f4f4';
					break;
				case 'yith_welrp_popup_text_color':
					$default = get_theme_mod( 'yith_proteo_base_font_color', '#404040' );
					break;
				case 'yith_welrp_popup_link_color[normal]':
					$default = '#448a85';
					break;
				case 'yith_welrp_popup_link_color[hover]':
					$default = '#1a4e43';
					break;
				case 'yith_welrp_popup_link_color':
					$default = [
						'normal' => self::get_proteo_default( 'yith_welrp_popup_link_color[normal]', isset( $default['normal'] ) ? $default['normal'] : '' ),
						'hover'  => self::get_proteo_default( 'yith_welrp_popup_link_color[hover]', isset( $default['hover'] ) ? $default['hover'] : '' ),
					];
					break;
				case 'yith_welrp_button_bg_color[normal]':
					$default = get_theme_mod( 'yith_proteo_button_style_1_bg_color', '#448a85' );
					break;
				case 'yith_welrp_button_bg_color[hover]':
					$default = get_theme_mod( 'yith_proteo_button_style_1_bg_color_hover', '#4ac4aa' );
					break;
				case 'yith_welrp_button_bg_color':
					$default = [
						'normal' => self::get_proteo_default( 'yith_welrp_button_bg_color[normal]', isset( $default['normal'] ) ? $default['normal'] : '' ),
						'hover'  => self::get_proteo_default( 'yith_welrp_button_bg_color[hover]', isset( $default['hover'] ) ? $default['hover'] : '' ),
					];
					break;
				case 'yith_welrp_button_br_color[normal]':
					$default = get_theme_mod( 'yith_proteo_button_style_1_border_color', '#448a85' );
					break;
				case 'yith_welrp_button_br_color[hover]':
					$default = get_theme_mod( 'yith_proteo_button_style_1_border_color_hover', '#4ac4aa' );
					break;
				case 'yith_welrp_button_br_color':
					$default = [
						'normal' => self::get_proteo_default( 'yith_welrp_button_br_color[normal]', isset( $default['normal'] ) ? $default['normal'] : '' ),
						'hover'  => self::get_proteo_default( 'yith_welrp_button_br_color[hover]', isset( $default['hover'] ) ? $default['hover'] : '' ),
					];
					break;
				case 'yith_welrp_button_lb_color[normal]':
					$default = get_theme_mod( 'yith_proteo_button_style_1_text_color', '#ffffff' );
					break;
				case 'yith_welrp_button_lb_color[hover]':
					$default = get_theme_mod( 'yith_proteo_button_style_1_text_color_hover', '#ffffff' );
					break;
				case 'yith_welrp_button_lb_color':
					$default = [
						'normal' => self::get_proteo_default( 'yith_welrp_button_lb_color[normal]', isset( $default['normal'] ) ? $default['normal'] : '' ),
						'hover'  => self::get_proteo_default( 'yith_welrp_button_lb_color[hover]', isset( $default['hover'] ) ? $default['hover'] : '' ),
					];
					break;
				case 'facebook_background_color':
				case 'google_background_color':
					$default = '#ffffff';
					break;
				case 'facebook_background_color_hover':
				case 'google_background_color_hover':
					$default = '#f7f7f7';
					break;
				case 'facebook_border_color':
				case 'facebook_border_color_hover':
				case 'google_border_color':
				case 'google_border_color_hover':
					$default = '#707070';
					break;
				case 'facebook_text_color':
				case 'google_text_color':
					$default = '#4b4b4b';
					break;
				case 'facebook_text_color_hover':
				case 'google_text_color_hover':
					$default = '#000000';
					break;
			}

			return $default;
		}

		/**
		 * Load compatibility classes for third party plugins
		 *
		 * @since 1.5.0
		 * @author Francesco Licandro
		 * @return void
		 */
		public function load_compatibility_classes() {
			$compatibility = [
				'XT_Woo_Floating_Cart' => 'class.yith-easy-login-xt-floating-cart.php'
			];

			foreach ( $compatibility as $class => $file ) {
				if( class_exists( $class ) && file_exists( YITH_WELRP_PATH . "includes/compatibility/{$file}" ) ) {
					include_once YITH_WELRP_PATH . "includes/compatibility/{$file}";
				}
			}
		}
	}
}

/**
 * Unique access to instance of YITH_Easy_Login_Register class
 *
 * @since 1.0.0
 * @return YITH_Easy_Login_Register
 */
function YITH_Easy_Login_Register() {
	return YITH_Easy_Login_Register::get_instance();
}
