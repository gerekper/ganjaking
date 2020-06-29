<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Main class
 *
 * @class   YITH_Livechat
 * @since   1.0.0
 * @author  Your Inspiration Themes
 * @package Yithemes
 */

if ( ! class_exists( 'YITH_Livechat' ) ) {

	class YITH_Livechat {

		/**
		 * @var string $_options_name The name for the options db entry
		 */
		public $_options_name = 'live_chat';

		/**
		 * Panel object
		 *
		 * @since   1.0.0
		 * @var     /Yit_Plugin_Panel object
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		protected $_panel = null;

		/**
		 * @var $_premium string Premium tab template file name
		 */
		protected $_premium = 'premium.php';

		/**
		 * @var string Premium version landing link
		 */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-live-chat/';

		/**
		 * @var string Plugin official documentation
		 */
		protected $_official_documentation = 'https://docs.yithemes.com/yith-live-chat/';

		/**
		 * @var string Yith Live Chat panel page
		 */
		protected $_panel_page = 'yith_live_chat_panel';

		/**
		 * @var string Yith Live Chat console page
		 */
		protected $_console_page = 'yith_live_chat';

		/**
		 * @var $user YLC_User
		 */
		public $user = null;

		/**
		 * Single instance of the class
		 *
		 * @since 1.1.0
		 * @var \YITH_Livechat
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_Livechat
		 * @since 1.1.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self;

			}

			return self::$instance;

		}

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 12 );
			add_action( 'plugins_loaded', array( $this, 'include_privacy_text' ), 20 );
			add_filter( 'plugin_action_links_' . plugin_basename( YLC_DIR . '/' . basename( YLC_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			// Include required files
			$this->includes();

			add_action( 'admin_menu', array( $this, 'add_custom_fields' ), 10 );

			add_action( 'init', array( $this, 'set_authentication_version' ), 5 );
			add_action( 'admin_enqueue_scripts', array( $this, 'register_styles_scripts' ), 80 );
			add_action( 'wp_enqueue_scripts', array( $this, 'register_styles_scripts' ), 80 );
			add_action( 'admin_menu', array( $this, 'add_menu_page' ), 5 );
			add_action( 'yith_live_chat_premium', array( $this, 'premium_tab' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 100 );

			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			add_action( 'init', array( $this, 'user_init' ), 25 );
			add_action( 'init', array( $this, 'init_chat' ), 30 );

		}

		public function init_chat() {

			if ( ylc_get_option( 'plugin-enable', ylc_get_default( 'plugin-enable' ) ) == 'yes' && ylc_is_setup_complete() && ylc_check_valid_user( $this->user ) ) {

				add_action( 'admin_menu', array( $this, 'add_console_page' ), 5 );
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 100 );
				add_action( 'wp_footer', array( $this, 'show_chat' ) );

				do_action( 'ylc_additional_init_setup' );

			}

		}


		public function admin_frontend_scripts() {

		}

		public function load_fontawesome() {
		}

		/**
		 * Include required core files
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function includes() {

			require_once( 'includes/functions-ylc-commons.php' );
			require_once( 'includes/functions-ylc-ajax.php' );
			require_once( 'includes/class-ylc-token.php' );
			require_once( 'includes/class-ylc-user.php' );
			require_once( 'includes/class-ylc-settings.php' );

			if ( is_admin() ) {
				include_once( 'includes/admin/class-ylc-custom-fields.php' );
			}

		}

		/**
		 * Add custom fields to plugin options
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function add_custom_fields() {
			new YLC_Custom_Fields( $this->_panel );
		}

		/**
		 * If it's a new installation the user will see the new authentication method
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function set_authentication_version() {

			if ( get_option( 'ylc_authentication_method' ) == '' ) {

				if ( ylc_get_option( 'firebase-appsecret' ) == '' || ylc_get_option( 'firebase-appurl' ) == '' ) {
					update_option( 'ylc_authentication_method', '1.4.0' );
				}

				if ( isset( $_GET['settings-updated'] ) && 'true' == $_GET['settings-updated'] ) {

					$api_key     = ylc_get_option( 'firebase-apikey', '' );
					$private_key = ylc_get_option( 'firebase-private-key', '' );

					if ( $api_key != '' && $private_key != '' ) {
						update_option( 'ylc_authentication_method', '1.4.0' );
					}

				}

			}

		}

		/**
		 * Add styles and scripts for Chat Console or Chat Frontend
		 *
		 * @return  void
		 * @since   1.1.0
		 * @author  Alberto Ruggiero
		 */
		public function register_styles_scripts( $frontend = false ) {

			if ( ylc_frontend_manager() && ! $frontend ) {
				return;
			}

			//Google Fonts
			wp_register_style( 'ylc-google-fonts', '//fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,700italic,400,700,600', array(), null );

			//Font Awesome
			wp_register_style( 'ylc-icons', yit_load_css_file( YLC_ASSETS_URL . '/css/ylc-icons.css' ), array(), YLC_VERSION );

			//Options Panel, Chat Log, Offline Messages, Macro sections styles
			wp_register_style( 'ylc-styles', yit_load_css_file( YLC_ASSETS_URL . '/css/ylc-styles.css' ), array( 'ylc-icons' ), YLC_VERSION );

			//Console & Frontend Styles
			wp_register_style( 'ylc-console', yit_load_css_file( YLC_ASSETS_URL . '/css/ylc-console.css' ), array( 'ylc-google-fonts', 'ylc-icons' ), YLC_VERSION );
			wp_register_style( 'ylc-frontend', yit_load_css_file( YLC_ASSETS_URL . '/css/ylc-frontend.css' ), array( 'ylc-google-fonts', 'ylc-icons' ), YLC_VERSION );

			// AutoSize Plug-in
			wp_register_script( 'jquery-autosize', yit_load_js_file( YLC_ASSETS_URL . '/js/jquery-autosize.js' ), array( 'jquery' ), YLC_VERSION, true );

			//Update notices
			wp_register_script( 'ylc-notices', yit_load_js_file( YLC_ASSETS_URL . '/js/ylc-notices.js' ), array( 'jquery' ), YLC_VERSION, true );

			if ( get_option( 'ylc_authentication_method' ) == '1.4.0' ) {

				//Firebase Engine
				wp_register_script( 'ylc-firebase', 'https://www.gstatic.com/firebasejs/7.10.0/firebase-app.js', array(), '7.10.0', true );
				wp_register_script( 'ylc-firebase-auth', 'https://www.gstatic.com/firebasejs/7.10.0/firebase-auth.js', array( 'ylc-firebase' ), '7.10.0', true );
				wp_register_script( 'ylc-firebase-database', 'https://www.gstatic.com/firebasejs/7.10.0/firebase-database.js', array( 'ylc-firebase' ), '7.10.0', true );

				//Console & Frontend Engine
				wp_register_script( 'ylc-engine-console', yit_load_js_file( YLC_ASSETS_URL . '/js/ylc-engine-console.js' ), array( 'jquery', 'ylc-firebase', 'ylc-firebase-auth', 'ylc-firebase-database', 'jquery-autosize' ), YLC_VERSION, true );
				wp_register_script( 'ylc-engine-frontend', yit_load_js_file( YLC_ASSETS_URL . '/js/ylc-engine-frontend.js' ), array( 'jquery', 'ylc-firebase', 'ylc-firebase-auth', 'ylc-firebase-database', 'jquery-autosize' ), YLC_VERSION, true );

			} else {

				//Firebase Engine
				wp_register_script( 'ylc-firebase', YLC_ASSETS_URL . '/js/firebase.min.js', array(), '2.4.2', true );

				//Console & Frontend Engine
				wp_register_script( 'ylc-engine-console', yit_load_js_file( YLC_ASSETS_URL . '/js/ylc-engine-console-old.js' ), array( 'jquery', 'ylc-firebase', 'jquery-autosize' ), false, true );
				wp_register_script( 'ylc-engine-frontend', yit_load_js_file( YLC_ASSETS_URL . '/js/ylc-engine-frontend-old.js' ), array( 'jquery', 'ylc-firebase', 'jquery-autosize' ), false, true );

			}

			if ( ylc_get_option( 'plugin-enable', ylc_get_default( 'plugin-enable' ) ) == 'yes' && ylc_is_setup_complete() && ylc_check_valid_user( $this->user ) ) {

				$js_vars = array(
					'defaults'             => ylc_get_plugin_options(),
					'ajax_url'             => str_replace( array( 'https:', 'http:' ), '', admin_url( 'admin-ajax.php' ) ),
					'plugin_url'           => YLC_ASSETS_URL,
					'is_premium'           => ylc_check_premium(),
					'company_avatar'       => apply_filters( 'ylc_company_avatar', '' ),
					'default_user_avatar'  => apply_filters( 'ylc_default_avatar', '', 'user' ),
					'default_admin_avatar' => apply_filters( 'ylc_default_avatar', '', 'admin' ),
					'yith_wpv_active'      => ( ylc_check_premium() && defined( 'YITH_WPV_PREMIUM' ) ) ? true : false,
					'active_vendor'        => apply_filters( 'ylc_vendor', array(
						'vendor_id'   => 0,
						'vendor_name' => ''
					) ),
					'vendor_only_chat'     => apply_filters( 'ylc_vendor_only', false ),
					'strings'              => ylc_get_strings( 'console' )
				);

				wp_localize_script( 'ylc-engine-console', 'ylc', $js_vars );

			}

			if ( ylc_is_setup_complete() && ! is_admin() && ylc_check_valid_user( $this->user ) ) {

				$js_vars = array(
					'defaults'             => ylc_get_plugin_options(),
					'ajax_url'             => str_replace( array( 'https:', 'http:' ), '', admin_url( 'admin-ajax.php' ) ),
					'plugin_url'           => YLC_ASSETS_URL,
					'frontend_op_access'   => ( current_user_can( 'answer_chat' ) ) ? true : false,
					'is_premium'           => ylc_check_premium(),
					'show_busy_form'       => apply_filters( 'ylc_busy_form', false ),
					'show_delay'           => apply_filters( 'ylc_show_delay', 1000 ),
					'max_guests'           => apply_filters( 'ylc_max_guests', 2 ),
					'company_avatar'       => apply_filters( 'ylc_company_avatar', '' ),
					'default_user_avatar'  => apply_filters( 'ylc_default_avatar', '', 'user' ),
					'default_admin_avatar' => apply_filters( 'ylc_default_avatar', '', 'admin' ),
					'autoplay_opts'        => apply_filters( 'ylc_autoplay_opts', array() ),
					'yith_wpv_active'      => ( ylc_check_premium() && defined( 'YITH_WPV_PREMIUM' ) ) ? true : false,
					'active_vendor'        => apply_filters( 'ylc_vendor', array(
						'vendor_id'   => 0,
						'vendor_name' => ''
					) ),
					'gdpr'                 => apply_filters( 'ylc_gdpr_compliance', false ),
					'chat_gdpr'            => apply_filters( 'ylc_chat_gdpr_compliance', false ),
					'vendor_only_chat'     => apply_filters( 'ylc_vendor_only', false ),
					'button_animation'     => apply_filters( 'ylc_round_btn_animation', true ),
					'strings'              => ylc_get_strings( 'frontend' )
				);

				wp_localize_script( 'ylc-engine-frontend', 'ylc', $js_vars );

			}

		}

		/**
		 * User Init
		 *
		 * @return  void
		 * @since   1.1.0
		 * @author  Alberto Ruggiero
		 */
		public function user_init() {

			if ( current_user_can( 'answer_chat' ) && ( is_admin() || ylc_frontend_manager() ) ) {

				define( 'YLC_OPERATOR', true );

			} else {

				define( 'YLC_GUEST', true );

			}

			$display_name = '';
			$user_email   = '';

			if ( is_user_logged_in() ) {

				$current_user = wp_get_current_user();
				$user_id      = $current_user->ID;
				$display_name = $current_user->display_name;
				$user_email   = $current_user->user_email;

			} else {

				$user_id = isset( $_COOKIE['ylc_user_session'] ) ? $_COOKIE['ylc_user_session'] : '';

				if ( empty( $user_id ) ) {

					$user_id = uniqid( rand(), false );
					@setcookie( 'ylc_user_session', $user_id, time() + ( 3600 * 24 ) );

				}

			}

			$this->user = new YLC_User( $user_id, $display_name, $user_email );

		}

		/**
		 * ADMIN FUNCTIONS
		 */

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 * @use     /Yit_Plugin_Panel class
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		public function add_menu_page() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			if ( ylc_check_premium() ) {
				$admin_tabs = array(
					'general'    => esc_html__( 'General', 'yith-live-chat' ),
					'display'    => esc_html__( 'Display', 'yith-live-chat' ),
					'texts'      => esc_html__( 'Messages', 'yith-live-chat' ),
					'offline'    => esc_html__( 'Offline Messages', 'yith-live-chat' ),
					'transcript' => esc_html__( 'Conversation', 'yith-live-chat' ),
					'style'      => esc_html__( 'Appearance', 'yith-live-chat' ),
					'user'       => esc_html__( 'Users', 'yith-live-chat' ),
					'privacy'    => esc_html__( 'Privacy', 'yith-live-chat' ),
				);
			} else {
				$admin_tabs = array(
					'general' => esc_html__( 'General', 'yith-live-chat' ),
					'texts'   => esc_html__( 'Messages', 'yith-live-chat' ),
					'premium' => esc_html__( 'Premium Version', 'yith-live-chat' ),
				);
			}

			$args = array(
				'create_menu_page' => true,
				'plugin_slug'      => YLC_SLUG,
				'parent_slug'      => '',
				'page_title'       => esc_html__( 'Live Chat', 'yith-live-chat' ),
				'menu_title'       => 'Live Chat',
				'capability'       => 'manage_options',
				'parent'           => $this->_options_name,
				'parent_page'      => 'yit_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'plugin-url'       => YLC_URL,
				'options-path'     => YLC_DIR . 'plugin-options',
				'class'            => yith_set_wrapper_class(),
			);

			$this->_panel = new YIT_Plugin_Panel( $args );

		}

		/**
		 * Add YITH Live Chat console page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function add_console_page() {

			if ( ! ylc_multivendor_check() ) {
				return;
			}

			if ( current_user_can( 'answer_chat' ) ) {

				add_menu_page( 'YITH Live Chat', 'YITH Live Chat', 'answer_chat', $this->_console_page, array( $this, 'get_console_template' ), YLC_ASSETS_URL . '/images/favicon.png', 63 );

			}

		}

		/**
		 * Advise if the plugin cannot be performed
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function admin_notices() {

			$doc_url = 'https://docs.yithemes.com/yith-live-chat/free-version-settings/configure-firebase/';


			if ( ! ylc_is_setup_complete() && ylc_is_private_key_valid() ) {

				?>
				<div class="notice notice-error">
					<p>
						<?php printf( esc_html__( 'Please complete Firebase setup for %s. %sClick here%s know how to do.', 'yith-live-chat' ), 'YITH Live Chat', '<a target="_blank" href="' . $doc_url . '">', '</a>' ); ?>
					</p>
				</div>
				<?php

			} elseif ( ylc_is_setup_complete() && get_option( 'ylc_authentication_method' ) == '' ) {


				if ( ( ! empty( $_COOKIE['hide_ylc_alert'] ) && 'yes' == $_COOKIE['hide_ylc_alert'] ) ) {
					return;
				}

				$show_notice = true;

				if ( true === $show_notice ) {

					wp_enqueue_script( 'ylc-notices' );

					?>
					<div id="ylc-alert" class="notice notice-info is-dismissible" style="position: relative;">
						<p>
							<?php echo sprintf( esc_html__( 'We have updated %s for %s. The plugin will still work with the old configuration until the old API will not be removed. %sClick here%s to know about how to update.', 'yith-live-chat' ), '<b>Firebase API</b>', '<b>YITH Live Chat</b>', '<a href="' . $doc_url . '">', '</a>' ) ?>
						</p>
						<span class="notice-dismiss"></span>

					</div>
					<?php
				}

			} elseif ( ! ylc_is_setup_complete() && ! ylc_is_private_key_valid() ) {
				?>
				<div class="notice notice-error">
					<p>
						<?php printf( esc_html__( 'The Firebase Private Key is not valid. %sClick here%s to know how to get the correct one.', 'yith-live-chat' ), '<a target="_blank" href="' . $doc_url . '">', '</a>' ); ?>
					</p>
				</div>
				<?php

			}

		}

		/**
		 * Add styles and scripts for options panel and chat console
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function admin_scripts() {

			switch ( ylc_get_current_page() ) {

				case $this->_panel_page:

					wp_enqueue_style( 'ylc-styles' );

					break;

				case $this->_console_page:

					//YLC Console Engine
					wp_enqueue_script( 'ylc-engine-console' );

					// Console stylesheet
					wp_enqueue_style( 'ylc-console' );

					break;

			}

		}

		/**
		 * Create / Update Chat Operator Role
		 *
		 * @param   $role
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ylc_operator_role( $role ) {

			remove_role( 'ylc_chat_op' );                                                                    // First clean role
			$op_role = add_role( 'ylc_chat_op', esc_html__( 'YITH Live Chat Operator', 'yith-live-chat' ) ); // Create operator role
			$op_role->add_cap( 'answer_chat' );                                                              // Add common operator capability

			switch ( $role ) {

				/** N/A */
				case 'none':
					$op_role->add_cap( 'read' );
					break;
				/** Other roles */
				default:
					$r = get_role( $role ); // Get editor role

					// Add editor caps to chat operator
					foreach ( $r->capabilities as $custom_role => $v ) {
						$op_role->add_cap( $custom_role );
					}
			}

		}

		/**
		 * Load Console Template
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function get_console_template() {

			ylc_get_template( 'chat-backend/chat-console.php', array() );

		}

		/**
		 * FRONTEND FUNCTIONS
		 */

		/**
		 * Enqueue Scripts
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function enqueue_scripts() {

			if ( apply_filters( 'ylc_can_show_chat', true ) ) {

				wp_enqueue_style( 'ylc-frontend' );
				wp_enqueue_script( 'ylc-engine-frontend' );

			}

		}

		/**
		 * Load Chat Box
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function show_chat() {

			if ( apply_filters( 'ylc_can_show_chat', true ) ) {

				$opts = apply_filters( 'ylc_frontend_opts', array(
					'button_type' => 'classic',
					'button_pos'  => 'bottom',
					'form_width'  => '',
					'chat_width'  => '',
				) );

				ylc_get_template( 'chat-frontend/chat-container.php', $opts );

			}

		}

		/**
		 * Register privacy text
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function include_privacy_text() {
			include_once( 'includes/class-ylc-privacy.php' );
		}

		/**
		 * YITH FRAMEWORK
		 */

		/**
		 * Enqueue css file
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

		/**
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function premium_tab() {
			$premium_tab_template = YLC_TEMPLATE_PATH . '/admin/' . $this->_premium;
			if ( file_exists( $premium_tab_template ) ) {
				include_once( $premium_tab_template );
			}
		}

		/**
		 * Get the premium landing uri
		 *
		 * @return  string The premium landing link
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function get_premium_landing_uri() {
			return $this->_premium_landing;
		}

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param   $links | links plugin array
		 *
		 * @return  mixed
		 * @since   1.0.0
		 *
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {

			$links = yith_add_action_links( $links, $this->_panel_page, false );

			return $links;
		}

		/**
		 * plugin_row_meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param   $new_row_meta_args
		 * @param   $plugin_meta
		 * @param   $plugin_file
		 * @param   $plugin_data
		 * @param   $status
		 * @param   $init_file
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YLC_FREE_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug'] = YLC_SLUG;
			}

			return $new_row_meta_args;
		}

	}

}