<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Main class
 *
 * @class   YITH_FAQ_Plugin_for_Wordpress
 * @package Yithemes
 * @since   1.0.0
 * @author  Your Inspiration Themes
 */

if ( ! class_exists( 'YITH_FAQ_Plugin_for_Wordpress' ) ) {

	class YITH_FAQ_Plugin_for_Wordpress {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_FAQ_Plugin_for_Wordpress
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Panel object
		 *
		 * @var     /Yit_Plugin_Panel object
		 * @since   1.0.0
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		protected $_panel = null;

		/**
		 * @var string Premium version landing link
		 */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-faq-plugin-for-wordpress/';

		/**
		 * @var string Plugin official documentation
		 */
		protected $_official_documentation = 'https://docs.yithemes.com/yith-faq-plugin-for-wordpress/';

		/**
		 * @var string YITH FAQ Plugin for WordPress panel page
		 */
		protected $_panel_page = 'yith-faq-plugin-for-wordpress';

		/**
		 * @var $post_type string post type name
		 */
		public $post_type = 'yith_faq';

		/**
		 * @var $taxonomy string taxonomy name
		 */
		public $taxonomy = 'yith_faq_cat';

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_FAQ_Plugin_for_Wordpress
		 * @since 1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			//Load plugin framework
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 12 );
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_FWP_DIR . '/' . basename( YITH_FWP_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			add_action( 'admin_menu', array( $this, 'add_menu_page' ), 5 );

			$this->includes();

			new YITH_FAQ_Post_Type( $this->post_type, $this->taxonomy );
			new YITH_FAQ_Taxonomy( $this->post_type, $this->taxonomy );
			new YITH_FAQ_Shortcode_Panel( $this->post_type, $this->taxonomy );
			new YITH_FAQ_Shortcode( $this->post_type, $this->taxonomy );

		}

		/**
		 * Files inclusion
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		private function includes() {
			include_once( 'includes/class-yith-faq-post-type.php' );
			include_once( 'includes/class-yith-faq-taxonomy.php' );
			include_once( 'includes/class-yith-faq-shortcode.php' );
			include_once( 'includes/class-yith-faq-shortcode-panel.php' );
			include_once( 'includes/class-yith-faq-settings.php' );
			include_once( 'includes/functions-yith-faq.php' );
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 * @use     /Yit_Plugin_Panel class
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		public function add_menu_page() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = array(
				'shortcode' => _x( 'Shortcode', 'shortcode creation tab name', 'yith-faq-plugin-for-wordpress' ),
				'color'     => _x( 'Color', 'color tab name', 'yith-faq-plugin-for-wordpress' ),
			);

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => _x( 'FAQ', 'plugin name in admin page title', 'yith-faq-plugin-for-wordpress' ),
				'menu_title'       => 'FAQ',
				'capability'       => 'manage_options',
				'parent'           => 'faq',
				'parent_page'      => 'yit_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_FWP_DIR . 'plugin-options',
				 'class'            => yith_set_wrapper_class(),

			);

			$this->_panel = new YIT_Plugin_Panel( $args );

		}

		/**
		 * YITH FRAMEWORK
		 */

		/**
		 * Load plugin framework
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
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
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @since   1.0.0
		 *
		 * @param   $links | links plugin array
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 * @use     plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {

			$links = yith_add_action_links( $links, $this->_panel_page, true );
			return $links;

		}

		/**
		 * Plugin row meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @since   1.0.0
		 *
		 * @param   $new_row_meta_args
		 * @param   $plugin_meta
		 * @param   $plugin_file
		 * @param   $plugin_data
		 * @param   $status
		 * @param   $init_file
		 *
		 * @return  array
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_FWP_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug'] = YITH_FWP_SLUG;
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;

		}

		/**
		 * Register plugins for activation tab
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once 'plugin-fw/licence/lib/yit-licence.php';
				require_once 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YITH_FWP_INIT, YITH_FWP_SECRET_KEY, YITH_FWP_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once( 'plugin-fw/lib/yit-upgrade.php' );
			}
			YIT_Upgrade()->register( YITH_FWP_SLUG, YITH_FWP_INIT );
		}

	}

}