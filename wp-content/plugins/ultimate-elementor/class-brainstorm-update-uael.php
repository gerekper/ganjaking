<?php
/**
 * Brainstorm_Update_UAEL initial setup
 *
 * @package UAEL
 * @since 1.0.0
 */

use UltimateElementor\Classes\UAEL_Helper;

// Ignore the PHPCS warning about constant declaration.
// @codingStandardsIgnoreStart
define( 'BSF_REMOVE_uael_FROM_REGISTRATION_LISTING', true );
// @codingStandardsIgnoreEnd

if ( ! class_exists( 'Brainstorm_Update_UAEL' ) ) :

	/**
	 * Brainstorm Update
	 */
	class Brainstorm_Update_UAEL {

		/**
		 * Instance
		 *
		 * @var object Class object.
		 * @access private
		 */
		private static $instance;

		/**
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {

			self::version_check();

			add_filter( 'bsf_get_license_message_uael', array( $this, 'license_message_uael' ), 10, 2 );
			add_filter( 'bsf_skip_braisntorm_menu', array( $this, 'skip_menu' ) );
			add_filter( 'bsf_skip_author_registration', array( $this, 'skip_menu' ) );
			add_filter( 'bsf_allow_beta_updates_uael', array( $this, 'beta_updates_check' ) );
			// Register Licence Link.
			add_filter( 'bsf_registration_page_url_uael', array( $this, 'get_registration_page_url' ) );
			add_filter( 'agency_updater_productname_uael', array( $this, 'product_name' ) );

			// Add popup license form on plugin list page.
			add_filter( 'plugin_action_links_' . UAEL_BASE, array( $this, 'plugin_slug_license_form_and_links' ) );
			add_filter( 'network_admin_plugin_action_links_' . UAEL_BASE, array( $this, 'plugin_slug_license_form_and_links' ) );
			add_filter( 'bsf_is_product_bundled', array( $this, 'remove_uae_pro_bundled_products' ), 20, 3 );
		}

		/**
		 * Remove bundled products.
		 * License Validation and product updates are managed separately for all the products.
		 *
		 * @since 1.35.3
		 *
		 * @param  array  $product_parent  Array of parent product ids.
		 * @param  String $bsf_product    Product ID or  Product init or Product name based on $search_by.
		 * @param  String $search_by      Reference to search by id | init | name of the product.
		 *
		 * @return array                 Array of parent product ids.
		 */
		public function remove_uae_pro_bundled_products( $product_parent, $bsf_product, $search_by ) {

			// Bundled plugins are installed when the demo is imported on Ajax request and bundled products should be unchanged in the ajax.
			if ( ! defined( 'DOING_AJAX' ) && ! defined( 'WP_CLI' ) ) {

				$key = array_search( 'astra-pro-sites', $product_parent, true );

				if ( false !== $key ) {
					unset( $product_parent[ $key ] );
				}
			}

			return $product_parent;
		}

		/**
		 * Show action links on the plugin screen.
		 *
		 * @param   mixed $links Plugin Action links.
		 * @return  array        Filtered plugin action links.
		 */
		public function plugin_slug_license_form_and_links( $links = array() ) {

			if ( function_exists( 'get_bsf_inline_license_form' ) ) {
				$args = array(
					'product_id'              => 'uael',
					'popup_license_form'      => true,
					'bsf_license_allow_email' => false,
				);
				return get_bsf_inline_license_form( $links, $args, 'edd' );
			}

			return $links;
		}

		/**
		 * Get registration page url for addon.
		 *
		 * @since  1.0.0
		 * @return String URL of the licnense registration page.
		 */
		public function get_registration_page_url() {
			$url = admin_url( 'plugins.php?bsf-inline-license-form=uael' );

			return $url;
		}

		/**
		 * Skip Menu.
		 *
		 * @param array $products products.
		 * @return array $products updated products.
		 */
		public function skip_menu( $products ) {
			$products[] = 'uael';

			return $products;
		}

		/**
		 * Update brainstorm product version and product path.
		 *
		 * @return void
		 */
		public static function version_check() {

			$bsf_core_version_file = realpath( dirname( __FILE__ ) . '/admin/bsf-core/version.yml' );

			// Is file 'version.yml' exist?
			if ( is_file( $bsf_core_version_file ) ) {
				global $bsf_core_version, $bsf_core_path;
				$bsf_core_dir = realpath( dirname( __FILE__ ) . '/admin/bsf-core/' );
				$version      = file_get_contents( realpath( plugin_dir_path( __FILE__ ) . '/admin/bsf-core/version.yml' ) );
				// Compare versions.
				if ( version_compare( $version, $bsf_core_version, '>' ) ) {
					$bsf_core_version = $version;
					$bsf_core_path    = $bsf_core_dir;
				}
			}
		}

		/**
		 * Add Message for license.
		 *
		 * @param  string $content       get the link content.
		 * @param  string $purchase_url  purchase_url.
		 * @return string                output message.
		 */
		public function license_message_uael( $content, $purchase_url ) {

			$purchase_url = apply_filters( 'uael_licence_url', $purchase_url );

			$message = "<p><a target='_blank' href='" . esc_url( $purchase_url ) . "'>" . esc_html__( 'Get the license >>', 'uael' ) . '</a></p>';

			$branding = UAEL_Helper::get_white_labels();

			if ( isset( $branding['plugin']['name'] ) && '' !== $branding['plugin']['name'] ) {
				$message = '';
			}

			return $message;
		}

		/**
		 * Check if beta update is enabled or disabled.
		 *
		 * @return bool true / false beta enable arg.
		 */
		public function beta_updates_check() {

			$allow_beta = UAEL_Helper::get_admin_settings_option( '_uael_beta', 'disable' );

			if ( 'enable' === $allow_beta ) {
				return true;
			}

			return false;
		}

		/**
		 * Product Name.
		 *
		 * @param  string $name  Product Name.
		 * @return string product name.
		 */
		public function product_name( $name ) {

			$branding = UAEL_Helper::get_white_labels();

			if ( isset( $branding['plugin']['name'] ) && '' !== $branding['plugin']['name'] ) {
				$name = $branding['plugin']['name'];
			}

			return $name;
		}

	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Brainstorm_Update_UAEL::get_instance();

endif;
