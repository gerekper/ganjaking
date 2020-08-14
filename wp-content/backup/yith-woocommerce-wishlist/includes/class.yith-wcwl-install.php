<?php
/**
 * Install file
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Install' ) ) {
	/**
	 * Install plugin table and create the wishlist page
	 *
	 * @since 1.0.0
	 */
	class YITH_WCWL_Install {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCWL_Install
		 * @since 2.0.0
		 */
		protected static $instance;

		/**
		 * Items table name
		 *
		 * @var string
		 * @access private
		 * @since 1.0.0
		 */
		private $_table_items;

		/**
		 * Items table name
		 *
		 * @var string
		 * @access private
		 * @since 1.0.0
		 */
		private $_table_wishlists;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCWL_Install
		 * @since 2.0.0
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
		 * @since 1.0.0
		 */
		public function __construct() {
			global $wpdb;

			// define local private attribute.
			$this->_table_items = $wpdb->prefix . 'yith_wcwl';
			$this->_table_wishlists = $wpdb->prefix . 'yith_wcwl_lists';

			// add custom field to global $wpdb.
			$wpdb->yith_wcwl_items = $this->_table_items;
			$wpdb->yith_wcwl_wishlists = $this->_table_wishlists;

			// define constant to use allover the application.
			define( 'YITH_WCWL_ITEMS_TABLE', $this->_table_items );
			define( 'YITH_WCWL_WISHLISTS_TABLE', $this->_table_wishlists );

			/**
			 * @deprecated
			 */
			define( 'YITH_WCWL_TABLE', $this->_table_items );
		}

		/**
		 * Init db structure of the plugin
		 *
		 * @since 1.0.0
		 */
		public function init() {
			$this->_add_tables();
			$this->_add_pages();

			$this->register_current_version();
		}

		/**
		 * Update db structure of the plugin
		 *
		 * @param string $current_version Version from which we're updating.
		 *
		 * @ince 3.0.0
		 */
		public function update( $current_version ) {
			if ( version_compare( $current_version, '1.0.0', '<' ) ) {
				$this->_update_100();
			}

			if ( version_compare( $current_version, '2.0.0', '<' ) ) {
				$this->_update_200();
			}

			if ( version_compare( $current_version, '3.0.0', '<' ) ) {
				$this->_update_300();
			}

			// TODO (3.1): _update_310() should call ->_add_tables(), to update db structure and size of external id columns.

			$this->register_current_version();
		}

		/**
		 * Register current version of plugin and database sctructure
		 *
		 * @since 3.0.0
		 */
		public function register_current_version() {
			delete_option( 'yith_wcwl_version' );
			update_option( 'yith_wcwl_version', YITH_WCWL_VERSION );

			delete_option( 'yith_wcwl_db_version' );
			update_option( 'yith_wcwl_db_version', YITH_WCWL_DB_VERSION );
		}

		/**
		 * Check if the table of the plugin already exists.
		 *
		 * @return bool
		 * @since 1.0.0
		 */
		public function is_installed() {
			global $wpdb;
			$number_of_tables = $wpdb->query( $wpdb->prepare( 'SHOW TABLES LIKE %s', "{$this->_table_items}%" ) );

			return (bool) ( 2 == $number_of_tables );
		}

		/**
		 * Update from 0.x to 1.0
		 */
		private function _update_100() {
			flush_rewrite_rules();
		}

		/**
		 * Update from version 1.0 to 2.0
		 *
		 * @since 2.0.0
		 */
		private function _update_200() {
			// update tables.
			$this->_add_tables();
		}

		/**
		 * Update from version 2.0 to 3.0
		 *
		 * @since 3.0.0
		 */
		private function _update_300() {
			// update tables.
			$this->_add_tables();

			// update color options.
			$options = array(
				'color_add_to_wishlist',
				'color_add_to_cart',
				'color_button_style_1',
				'color_button_style_2',
				'color_wishlist_table',
			);

			foreach ( $options as $option ) {
				$base_option_name = "yith_wcwl_{$option}";

				$background = get_option( "{$base_option_name}_background" );
				$color = get_option( "{$base_option_name}_color" );
				$border = get_option( "{$base_option_name}_border_color" );

				if ( 'color_wishlist_table' != $option ) {
					$background_hover = get_option( "{$base_option_name}_hover_background" );
					$color_hover      = get_option( "{$base_option_name}_hover_color" );
					$border_hover     = get_option( "{$base_option_name}_hover_border_color" );
				}

				update_option(
					$base_option_name,
					array_merge(
						! empty( $background ) ? array( 'background' => $background ) : array(),
						! empty( $color ) ? array( 'text' => $color ) : array(),
						! empty( $border ) ? array( 'border' => $border ) : array(),
						! empty( $background_hover ) ? array( 'background_hover' => $background_hover ) : array(),
						! empty( $color_hover ) ? array( 'text_hover' => $color_hover ) : array(),
						! empty( $border_hover ) ? array( 'border_hover' => $border_hover ) : array()
					)
				);
			}

			// duplicate options.
			$options = array(
				'yith_wcwl_color_button_style_1' => array(
					'yith_wcwl_color_ask_an_estimate',
				),
				'yith_wcwl_color_button_style_1_hover' => array(
					'yith_wcwl_color_ask_an_estimate_hover',
				),
				'woocommerce_promotion_mail_settings' => array(
					'woocommerce_yith_wcwl_promotion_mail_settings',
				),
			);

			foreach ( $options as $original_option => $destinations ) {
				$option_value = get_option( $option );

				if ( $option_value ) {
					foreach ( $destinations as $destination ) {
						update_option( $destination, $option_value );
					}
				}
			}

			// button style options.
			$use_buttons = get_option( 'yith_wcwl_use_button' );
			$use_theme_style = get_option( 'yith_wcwl_frontend_css' );

			if ( 'yes' == $use_buttons && 'no' == $use_theme_style ) {
				$destination_value = 'button_custom';
			} elseif ( 'yes' == $use_buttons ) {
				$destination_value = 'button_default';
			} else {
				$destination_value = 'link';
			}

			update_option( 'yith_wcwl_add_to_wishlist_style', $destination_value );
			update_option( 'yith_wcwl_add_to_cart_style', $destination_value );
			update_option( 'yith_wcwl_ask_an_estimate_style', $destination_value );

			// rounded corners options.
			$rounded_corners = get_option( 'yith_wcwl_rounded_corners' );
			$radius_value = 'yes' == $rounded_corners ? 16 : 0;

			update_option( 'yith_wcwl_rounded_corners_radius', $radius_value );
			update_option( 'yith_wcwl_add_to_cart_rounded_corners_radius', $radius_value );
			update_option( 'yith_wcwl_ask_an_estimate_rounded_corners_radius', $radius_value );
		}

		/**
		 * Add tables for a fresh installation
		 *
		 * @return void
		 * @access private
		 * @since 1.0.0
		 */
		private function _add_tables() {
			$this->_add_wishlists_table();
			$this->_add_items_table();
		}

		/**
		 * Add the wishlists table to the database.
		 *
		 * @return void
		 * @access private
		 * @since 1.0.0
		 */
		private function _add_wishlists_table() {
			global $wpdb;

			if ( ! $this->is_installed() || version_compare( get_option( 'yith_wcwl_db_version' ), '3.0.0', '<' ) ) {
				$sql = "CREATE TABLE {$this->_table_wishlists} (
							ID BIGINT( 20 ) NOT NULL AUTO_INCREMENT,
							user_id BIGINT( 20 ) NULL DEFAULT NULL,
							session_id VARCHAR( 255 ) DEFAULT NULL,
							wishlist_slug VARCHAR( 200 ) NOT NULL,
							wishlist_name TEXT,
							wishlist_token VARCHAR( 64 ) NOT NULL UNIQUE,
							wishlist_privacy TINYINT( 1 ) NOT NULL DEFAULT 0,
							is_default TINYINT( 1 ) NOT NULL DEFAULT 0,
							dateadded timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
							expiration timestamp NULL DEFAULT NULL,
							PRIMARY KEY  ( ID ),
							KEY wishlist_slug ( wishlist_slug )
						) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );
			}

			return;
		}

		/**
		 * Add the items table to the database.
		 *
		 * @return void
		 * @access private
		 * @since 1.0.0
		 */
		private function _add_items_table() {
			global $wpdb;

			if ( ! $this->is_installed() || version_compare( get_option( 'yith_wcwl_db_version' ), '3.0.0', '<' ) ) {
				$sql = "CREATE TABLE {$this->_table_items} (
							ID BIGINT( 20 ) NOT NULL AUTO_INCREMENT,
							prod_id BIGINT( 20 ) NOT NULL,
							quantity INT( 11 ) NOT NULL,
							user_id BIGINT( 20 ) NULL DEFAULT NULL,
							wishlist_id BIGINT( 20 ) NULL,
							position INT( 11 ) DEFAULT 0,
							original_price DECIMAL( 9,3 ) NULL DEFAULT NULL,
							original_currency CHAR( 3 ) NULL DEFAULT NULL,
							dateadded timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
							on_sale tinyint NOT NULL DEFAULT 0,
							PRIMARY KEY  ( ID ),
							KEY prod_id ( prod_id )
						) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );
			}

			return;
		}

		/**
		 * Add a page "Wishlist".
		 *
		 * @return void
		 * @since 1.0.0
		 */
		private function _add_pages() {
			wc_create_page(
				sanitize_title_with_dashes( _x( 'wishlist', 'page_slug', 'yith-woocommerce-wishlist' ) ),
				'yith_wcwl_wishlist_page_id',
				__( 'Wishlist', 'yith-woocommerce-wishlist' ),
				'<!-- wp:shortcode -->[yith_wcwl_wishlist]<!-- /wp:shortcode -->'
			);
		}
	}
}

/**
 * Unique access to instance of YITH_WCWL_Install class
 *
 * @return \YITH_WCWL_Install
 * @since 2.0.0
 */
function YITH_WCWL_Install() {
	return YITH_WCWL_Install::get_instance();
}