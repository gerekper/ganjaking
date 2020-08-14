<?php
/**
 * Frontend class
 *
 * @author YITH
 * @package YITH WooCommerce Popup
 * @version 1.0.0
 */


if ( ! defined( 'YITH_YPOP_INIT' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_Popup_Frontend' ) ) {
	/**
	 * YITH_Popup_Frontend class
	 *
	 * @since 1.0.0
	 */
	class YITH_Popup_Frontend {
		/**
		 * Single instance of the class
		 *
		 * @var \YITH_Popup_Frontend
		 * @since 1.0.0
		 */
		protected static $instance;


		/**
		 * The name for the plugin options
		 *
		 * @access public
		 * @var string
		 * @since 1.0.0
		 */
		public $plugin_options = 'yit_ypop_options';

		/**
		 * @var string $never_show_cookie_name The name of the cookie never_show_again for newsletter popup preferences
		 */
		public $never_show_again_cookie_name = '';

		/**
		 * @var string $show_next_time_cookie_name The name of the cookie show_next_time for newsletter popup preferences
		 */
		public $show_next_time_cookie_name = '';

		private $_current_post = 0;

		private $_current_popup = 0;


		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_Popup_Frontend
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}


		/**
		 * Constructor.
		 *
		 * @return \YITH_Popup_Frontend
		 * @since 1.0.0
		 */
		public function __construct() {

			add_action( 'template_redirect', array( $this, 'init' ) );

			// custom styles and javascripts
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );
		}


		/**
		 * Init function
		 *
		 * @return \YITH_Popup_Frontend
		 * @since 1.0.0
		 */
		public function init() {
			$post_id = $this->get_current_post();
			$this->get_current_popup();
			$post_type = get_post_type( $this->_current_popup );

			$this->never_show_again_cookie_name = 'ypopup-hide-popup-forever-' . YITH_Popup()->get_option( 'ypop_cookie_var' ) . '-' . $this->_current_popup;
			$this->show_next_time_cookie_name   = 'ypopup-hide-popup-' . YITH_Popup()->get_option( 'ypop_cookie_var' ) . '-' . $this->_current_popup;

			$enabled                 = yith_plugin_fw_is_true( YITH_Popup()->get_option( 'ypop_enable' ) );
			$ypop_enabled_everywhere = yith_plugin_fw_is_true( YITH_Popup()->get_option( 'ypop_enabled_everywhere' ) );

			$hide_popup_other_plugin = apply_filters( 'ypop_hide_popup', false, $this->_current_popup );
			global $yit_current_post;

			$yit_current_post = $post_id;
			if ( $enabled && wp_is_mobile() ) {
				$enabled = yith_plugin_fw_is_true( YITH_Popup()->get_option( 'ypop_enable_in_mobile' ) );
			}

			if (
				$enabled && ! $hide_popup_other_plugin &&
				! isset( $_COOKIE[ $this->never_show_again_cookie_name ] ) &&
				(
					YITH_Popup()->get_option( 'ypop_hide_policy' ) == 'always' ||
					! isset( $_COOKIE[ $this->show_next_time_cookie_name ] )
				)
				&&
				( $post_type == YITH_Popup()->post_type_name || $ypop_enabled_everywhere || (
						is_array( YITH_Popup()->get_option( 'ypop_popup_pages' ) ) &&
						in_array( $post_id, YITH_Popup()->get_option( 'ypop_popup_pages' ) )
					) ) ) {

				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_popup_styles_scripts' ), 11 );
				add_action( 'wp_footer', array( $this, 'get_popup_template' ) );

			}

		}


		/**
		 * Enqueue Scripts and Styles
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function enqueue_styles_scripts() {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_script( 'ypop_cookie', YITH_YPOP_ASSETS_URL . '/js/jquery.cookie' . $suffix . '.js', array( 'jquery' ), YITH_YPOP_VERSION, false );
			wp_enqueue_script( 'ypop_popup', YITH_YPOP_ASSETS_URL . '/js/jquery.yitpopup' . $suffix . '.js', array( 'jquery' ), YITH_YPOP_VERSION, false );

			wp_enqueue_style( 'ypop_frontend', YITH_YPOP_ASSETS_URL . '/css/frontend.css' );
		}

		/**
		 * Enqueue Scripts and Styles
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function enqueue_popup_styles_scripts() {

			$popup = $this->_current_popup;

			$enabled = get_post_meta( $popup, '_enable_popup', true );

			$post_type = get_post_type( $popup );

			if ( ! $enabled || $post_type != YITH_Popup()->post_type_name ) {
				return;
			}

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_script( 'ypop_frontend', YITH_YPOP_ASSETS_URL . '/js/frontend' . $suffix . '.js', array( 'jquery' ), YITH_YPOP_VERSION, false );

			$expired            = YITH_Popup()->get_option( 'ypop_hide_days' );
			$when_display       = get_post_meta( $popup, '_when_display', true );
			$delay              = get_post_meta( $popup, '_delay', true );
			$position           = get_post_meta( $popup, '_position', true );
			$leave_page_message = get_post_meta( $popup, '_leave_page_message', true );
			$hide_option        = YITH_Popup()->get_option( 'ypop_hide_policy' );

			wp_localize_script(
				'ypop_frontend',
				'ypop_frontend_var',
				array(
					'never_show_again_cookie_name' => $this->never_show_again_cookie_name,
					'show_next_time_cookie_name'   => $this->show_next_time_cookie_name,
					'expired'                      => $expired,
					'delay'                        => $delay,
					'position'                     => $position,
					'when_display'                 => $when_display,
					'ismobile'                     => wp_is_mobile(),
					'hide_option'                  => $hide_option,
				)
			);

			$css      = get_post_meta( $popup, '_ypop_css', true );
			$js       = get_post_meta( $popup, '_ypop_javascript', true );
			$template = get_post_meta( $popup, '_template_name', true );
			$css_file = $this->get_popup_template_url( $template, 'css/style.css' );

			if ( $css_file ) {
				wp_enqueue_style( "ypop_{$template}", $css_file, false, YITH_YPOP_VERSION  );
				if ( $css != '' ) {
					wp_add_inline_style( "ypop_{$template}", $css );
				}
			}

			if ( $js != '' ) {
				wc_enqueue_js( $js );
			}
		}



		/**
		 * Return the popup template of the current page
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function get_popup_template() {
			$popup_id = apply_filters( 'ypop_use_current_popup', false ) ? $this->_current_popup : $this->get_current_popup();

			$is_enabled = yith_plugin_fw_is_true( get_post_meta( $popup_id, '_enable_popup', true ) );

			if ( ! $is_enabled ) {
				return;
			}

			$popup         = get_post( $popup_id );
			$template      = '/themes/' . get_post_meta( $popup_id, '_template_name', true );
			$template_path = $this->get_popup_template_path( $template, 'markup.php' );

			if ( $template_path ) {
				$hiding_text = YITH_Popup()->get_option( 'ypop_hide_text' );
				include $template_path;
			};

		}


		/**
		 * Returns the url of the template for popup
		 *
		 * @param $folder
		 * @param $file
		 *
		 * @return string
		 * @since  1.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		public function get_popup_template_path( $folder, $file ) {
			$plugin_url   = YITH_YPOP_TEMPLATE_PATH . "{$folder}/{$file}";
			$template_url = ( ( defined( 'YIT' ) ) ? YIT_THEME_TEMPLATES_PATH : get_template_directory() ) . "/ypop{$folder}/{$file}";
			$child_url    = ( ( defined( 'YIT' ) ) ? str_replace( get_template_directory(), get_stylesheet_directory(), YIT_THEME_TEMPLATES_PATH ) : get_stylesheet_directory() ) . "/ypop{$folder}/{$file}";

			foreach ( array( 'child_url', 'template_url', 'plugin_url' ) as $var ) {
				if ( file_exists( ${$var} ) ) {
					return ${$var};
				}
			}

			return false;
		}

		/**
		 * Returns the url of the template for popup
		 *
		 * @param $template
		 * @param $file
		 *
		 * @return string
		 * @since  1.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		private function get_popup_template_url( $template, $file ) {
			$plugin_path   = YITH_YPOP_TEMPLATE_PATH . "/themes/{$template}/{$file}";
			$template_path = ( ( defined( 'YIT' ) ) ? YIT_THEME_TEMPLATES_PATH : get_template_directory() ) . "/ypop/themes/{$template}/{$file}";
			$child_path    = ( ( defined( 'YIT' ) ) ? str_replace( get_template_directory(), get_stylesheet_directory(), YIT_THEME_TEMPLATES_PATH ) : get_stylesheet_directory() ) . "/ypop/themes/{$template}/{$file}";

			$plugin_url   = YITH_YPOP_TEMPLATE_URL . "/themes/{$template}/{$file}";
			$template_url = ( ( defined( 'YIT' ) ) ? YITH_YPOP_TEMPLATE_URL : get_template_directory_uri() ) . "/ypop/themes/{$template}/{$file}";
			$child_url    = ( ( defined( 'YIT' ) ) ? str_replace( get_template_directory_uri(), get_stylesheet_directory_uri(), YIT_THEME_TEMPLATES_PATH ) : get_stylesheet_directory_uri() ) . "/ypop/themes/{$template}/{$file}";

			foreach ( array( 'child_path', 'template_path', 'plugin_path' ) as $var ) {
				if ( file_exists( ${$var} ) ) {
					$url = str_replace( 'path', 'url', $var );
					return ${$url};
				}
			}

			return false;
		}


		public function get_current_popup() {
			$is_enabled_every_where = YITH_Popup()->get_option( 'ypop_enabled_everywhere' );
			$everywhere             = yith_plugin_fw_is_true( $is_enabled_every_where );
			$pages                  = array();
			if ( ! $everywhere ) {
				$pages = YITH_Popup()->get_option( 'ypop_popup_pages' );
				if ( ! is_array( $pages ) && ( $pages == 'no' || $pages == '' ) ) {
					$pages = array();
				}
			}

			$default_popup = YITH_Popup()->get_option( 'ypop_popup_default' );

			if ( $this->_current_post ) {
				$welcome   = get_post_meta( $this->_current_post, '_welcome_popup', true );
				$post_type = get_post_type( $this->_current_post );

				// for preview
				if ( $post_type == YITH_Popup()->post_type_name ) {
					$this->_current_popup = $this->_current_post;
					return $this->_current_popup;
				}

				if ( $everywhere ) {
					if ( $welcome == 'disable' ) {
						$this->_current_popup = 0;
					} elseif ( $welcome == 'default' || $welcome == '' ) {
						$this->_current_popup = $default_popup;
					} else {
						$this->_current_popup = $welcome;
					}
				} else {

					if ( ! empty( $pages ) && in_array( $this->_current_post, $pages ) ) {
						if ( $welcome == 'default' || $welcome == '' ) {
							$this->_current_popup = $default_popup;
						} else {
							$this->_current_popup = $welcome;
						}
					} else {
						if ( $welcome != 'default' && $welcome != '' ) {
							$this->_current_popup = $welcome;
						} else {
							$this->_current_popup = 0;
						}
					}
				}
			} else {
				$this->_current_popup = $default_popup;
			}

			$is_enabled = get_post_meta( $this->_current_popup, '_enable_popup', true );

			if ( 'no' === $is_enabled ) {
				$this->_current_popup = 0;
			}

			$this->_current_popup = apply_filters( 'ypop_alter_popup', $this->_current_popup, $this->_current_post );

			return $this->_current_popup;
		}

		public function get_current_post() {
			global $wp_query;

			$post = $wp_query->get_queried_object();

			if ( function_exists( 'WC' ) && ( is_shop() || is_product_category() || is_product_tag() ) ) {
				$post_id = wc_get_page_id( 'shop' );
			} elseif ( ! empty( $post ) && isset( $post->ID ) ) {
				$post_id = $post->ID;
			} else {
				$post_id = 0;
			}

			$this->_current_post = apply_filters( 'ypop_current_popup', $post_id );

			return $this->_current_post;
		}

		public function show_popup( $idpop ) {

			$this->_current_popup = $idpop;

			add_filter( 'ypop_use_current_popup', '__return_true' );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_popup_styles_scripts' ), 10 );
			add_action( 'wp_footer', array( $this, 'get_popup_template' ) );
		}
	}

	/**
	 * Unique access to instance of YITH_Popup_Frontend class
	 *
	 * @return \YITH_Popup_Frontend
	 */
	function YITH_Popup_Frontend() {
		return YITH_Popup_Frontend::get_instance();
	}
}
