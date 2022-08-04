<?php
/**
 * This file belongs to the YIT Plugin Framework.
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @author YITH
 * @package YITH License & Upgrade Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_Theme_Licence' ) ) {
	/**
	 * YIT Plugin Licence Panel
	 * Setting Page to Manage Plugins
	 *
	 * @class      YITH_Theme_Licence
	 * @since      1.0
	 * @author     Andrea Grillo      <andrea.grillo@yithemes.com>
	 * @package    YITH
	 */
	class YITH_Theme_Licence extends YITH_Licence {

		/**
		 * The settings required to add the submenu page "Activation"
		 *
		 * @since 1.0
		 * @var array
		 */
		protected $settings = array();

		/**
		 * The single instance of the class
		 *
		 * @since 1.0
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * The option name
		 *
		 * @since 1.0
		 * @var string
		 */
		protected $licence_option = 'yit_theme_licence_activation';

		/**
		 * The product type
		 *
		 * @since 1.0
		 * @var string product type
		 */
		protected $product_type = 'theme';

		/**
		 * Old theme licence works until 28 January 2016
		 *
		 * @since 1.0
		 * @var string
		 */
		protected $old_licence_expires = 1453939200; // 28 January 2016.

		/**
		 * Constructor
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function __construct() {
			parent::__construct();

			$this->settings = array(
				'parent_page' => 'yit_product_panel',
				'page_title'  => __( 'License Activation', 'yith-plugin-upgrade-fw' ),
				'menu_title'  => __( 'License Activation', 'yith-plugin-upgrade-fw' ),
				'capability'  => 'manage_options',
				'page'        => 'yith_plugins_activation',
			);

			add_action( 'admin_menu', array( $this, 'add_submenu_page' ), 99 );
			add_action( 'wp_ajax_yith_activate-' . $this->product_type, array( $this, 'activate' ) );
			add_action( 'wp_ajax_yith_deactivate-' . $this->product_type, array( $this, 'deactivate' ) );
			add_action( 'wp_ajax_yith_update_licence_information-' . $this->product_type, array( $this, 'update_licence_information' ) );
		}

		/**
		 * Main plugin Instance
		 *
		 * @static
		 * @since  1.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return object Main instance
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Add "Activation" submenu page under YITH Plugins
		 *
		 * @since  1.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function add_submenu_page() {

			$admin_tree = array(
				'parent_slug' => apply_filters( 'yit_licence_parent_slug', 'yit_panel' ),
				'page_title'  => __( 'License Activation', 'yith-plugin-upgrade-fw' ),
				'menu_title'  => __( 'License Activation', 'yith-plugin-upgrade-fw' ),
				'capability'  => 'manage_options',
				'menu_slug'   => 'yit_panel_license',
				'function'    => 'show_activation_panel',
			);

			add_submenu_page(
				$admin_tree['parent_slug'],
				sprintf( '%s', $admin_tree['page_title'] ),
				sprintf( '%s', $admin_tree['menu_title'] ),
				$admin_tree['capability'],
				$admin_tree['menu_slug'],
				array( $this, $admin_tree['function'] )
			);
		}

		/**
		 * Premium product registration
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @param string $product_init The product init file.
		 * @param string $secret_key The product secret key.
		 * @param string $product_id The product slug (product_id).
		 * @return void
		 */
		public function register( $product_init, $secret_key, $product_id ) {
			$theme                                   = wp_get_theme();
			$products[ $product_init ]['Name']       = $theme->Name; // phpcs:ignore
			$products[ $product_init ]['secret_key'] = $secret_key;
			$products[ $product_init ]['product_id'] = $product_id;
			$this->products[ $product_init ]         = $products[ $product_init ];
		}

		/**
		 * Check for old licence
		 *
		 * @since  2.2
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return bool True for old licence period, false otherwise
		 */
		public function show_old_licence_message() {
			return time() < $this->old_licence_expires;
		}

		/**
		 * Get old licence message
		 *
		 * @since  2.2
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function get_old_licence_message() {
			ob_start(); ?>
			<div class="activation-faq">
				<h3><?php esc_html_e( 'I cannot find the license key for activating the theme I have bought some time ago. Where can I find it?', 'yith-plugin-upgrade-fw' ); ?></h3>

				<p>
					<?php
					esc_html_e(
						'If you have purchased one of our products before 27 January 2015, you can benefit from support and updates (the services offered with the license)
                    until 27 January 2016 and you do not have to purchase it again to get a new license key, because, before this date, your license used to be activated automatically by our system.
                    After 27 January 2016, instead, if you want to benefit from support and updates you have to buy a new license and activate it through the license key you will be
                    provided with and that you can find in your YITH account, in section "My licenses".',
						'yith-plugin-upgrade-fw'
					);
					?>
				</p>
			</div>
			<?php
			echo ob_get_clean(); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Get the product type
		 *
		 * @author Francesco Licandro
		 * @return string
		 */
		public function get_product_type() {
			return $this->product_type;
		}

		/**
		 * Get the activation licence url
		 *
		 * @author Francesco Licandro
		 * @return bool|string
		 */
		public function get_license_url() {
			return add_query_arg( array( 'page' => 'yit_panel_license' ), admin_url( 'admin.php' ) );
		}
	}
}

if ( ! function_exists( 'YITH_Theme_Licence' ) ) {
	/**
	 * Get the main instance of class
	 *
	 * @since  1.0
	 * @author Francesco Licandro
	 * @return YITH_Theme_Licence
	 */
	function YITH_Theme_Licence() { // phpcs:ignore
		return YITH_Theme_Licence::instance();
	}
}
