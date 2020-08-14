<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YWQA_Plugin_FW_Loader' ) ) {

	/**
	 *
	 * @class   YWQA_Plugin_FW_Loader
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 */
	class YWQA_Plugin_FW_Loader {

		/**
		 * @var $_panel Panel Object
		 */
		protected $_panel;

		/**
		 * @var $_premium string Premium tab template file name
		 */
		protected $_premium = 'premium.php';

		/**
		 * @var string Premium version landing link
		 */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-questions-and-answers/';

		/**
		 * @var string Plugin official documentation
		 */
		protected $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-questions-and-answers/';

        /**
         * @var string Official plugin landing page
         */
        protected $_premium_live = 'https://plugins.yithemes.com/yith-woocommerce-questions-and-answers/';

        /**
         * @var string Official plugin support page
         */
        protected $_support = 'https://yithemes.com/my-account/support/dashboard/';

		/**
		 * @var string Plugin panel page
		 */
		protected $_panel_page = 'yith_woocommerce_question_answer_panel';

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct() {
			/**
			 * Register actions and filters to be used for creating an entry on YIT Plugin menu
			 */
			add_action( 'admin_init', array( $this, 'register_pointer' ) );

			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

			//  Add stylesheets and scripts files
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

			//  Show plugin premium tab
			add_action( 'yith_question_answer_premium', array( $this, 'premium_tab' ) );

			/**
			 * register plugin to licence/update system
			 */
			$this->licence_activation();
		}


		/**
		 * Load YIT core plugin
		 *
		 * @since  1.0
		 * @access public
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function plugin_fw_loader() {
			if ( !defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( !empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs['general']  = esc_html__( 'General', 'yith-woocommerce-questions-and-answers' );
			$admin_tabs['advanced'] = esc_html__( 'Advanced', 'yith-woocommerce-questions-and-answers' );

			if ( ! defined( 'YITH_YWQA_PREMIUM' ) ) {
				$admin_tabs['premium-landing'] = esc_html__( 'Premium Version', 'yith-woocommerce-questions-and-answers' );
			}

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => 'Questions & Answers',
				'menu_title'       => 'Questions & Answers',
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yit_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
                'class'            => yith_set_wrapper_class(),
				'options-path'     => YITH_YWQA_DIR . '/plugin-options'
			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {

				require_once( 'plugin-fw/lib/yit-plugin-panel-wc.php' );
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function premium_tab() {
			$premium_tab_template = YITH_YWQA_TEMPLATES_DIR . '/admin/' . $this->_premium;
			if ( file_exists( $premium_tab_template ) ) {
				include_once( $premium_tab_template );
			}
		}

		public function register_pointer() {
			if ( ! class_exists( 'YIT_Pointers' ) ) {
				include_once( 'plugin-fw/lib/yit-pointers.php' );
			}

			$premium_message = defined( 'YITH_YWQA_PREMIUM' )
				? ''
				: esc_html__( 'YITH WooCommerce Questions and Answers is available in an outstanding PREMIUM version with many new options, discover it now.', 'yith-woocommerce-questions-and-answers' ) .
				  ' <a href="' . $this->get_premium_landing_uri() . '">' . esc_html__( 'Premium version', 'yith-woocommerce-questions-and-answers' ) . '</a>';

			$args[] = array(
				'screen_id'  => 'plugins',
				'pointer_id' => 'yith_woocommerce_question_answer',
				'target'     => '#toplevel_page_yit_plugin_panel',
				'content'    => sprintf( '<h3> %s </h3> <p> %s </p>',
					esc_html__( 'YITH WooCommerce Questions and Answers', 'yith-woocommerce-questions-and-answers' ),
					esc_html__( 'In YIT Plugins tab you can find YITH WooCommerce Questions and Answers options.<br> From this menu you can access all settings of YITH plugins activated.', 'yith-woocommerce-questions-and-answers' ) . '<br>' . $premium_message
				),
				'position'   => array( 'edge' => 'left', 'align' => 'center' ),
				'init'       => defined( 'YITH_YWQA_PREMIUM' ) ? YITH_YWQA_INIT : YITH_YWQA_FREE_INIT
			);

			YIT_Pointers()->register( $args );
		}

		/**
		 * Get the premium landing uri
		 *
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return  string The premium landing link
		 */
		public function get_premium_landing_uri() {
			return defined( 'YITH_REFER_ID' ) ? $this->_premium_landing . '?refer_id=' . YITH_REFER_ID : $this->_premium_landing . '?refer_id=1030585';
		}

		//region    ****    licence related methods ****

		/**
		 * Add actions to manage licence activation and updates
		 */
		public function licence_activation() {
			if ( ! defined( 'YITH_YWQA_PREMIUM' ) ) {
				return;
			}

			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_YWQA_DIR . '/plugin-fw/licence/lib/yit-licence.php';
				require_once YITH_YWQA_DIR . '/plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YITH_YWQA_INIT, YITH_YWQA_SECRET_KEY, YITH_YWQA_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once 'plugin-fw/lib/yit-upgrade.php';
			}
			YIT_Upgrade()->register( YITH_YWQA_SLUG, YITH_YWQA_INIT );
		}
		//endregion
	}
}

YWQA_Plugin_FW_Loader::get_instance();
