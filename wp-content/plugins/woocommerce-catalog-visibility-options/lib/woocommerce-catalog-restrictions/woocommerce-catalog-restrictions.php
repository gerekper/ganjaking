<?php

/**
 * woocommerce_product_addons class
 * */
if ( !class_exists( 'WC_Catalog_Restrictions' ) ) {

	class WC_Catalog_Restrictions {

		private static $_instance;

		public static function instance() {
			if ( self::$_instance == null ) {
				self::$_instance = new WC_Catalog_Restrictions();
			}

			return self::$_instance;
		}


		public $template_url;
		public $version = '1.0.5';
		public $use_db_filter_cache = false;

		public function __construct() {
			define( 'WOOCOMMERCE_CATALOG_RESTRICTIONS_VERSION', $this->version );

			$this->template_url = apply_filters( 'woocommerce_catalog_restrictions_template_url', 'woocommerce-catalog-visibility-options/' );

			require untrailingslashit( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'woocommerce-catalog-restrictions-functions.php';
			require untrailingslashit( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'shortcodes/class-wc-catalog-restrictions-location-picker-shortcode.php';
			require untrailingslashit( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'widgets/class-wc-catalog-restrictions-location-picker-widget.php';

			WC_Catalog_Restrictions_Location_Picker_ShortCode::instance();

			WC_Catalog_Restrictions_Location_Picker_Widget::register();

			if ( is_admin() && !defined( 'DOING_AJAX' ) ) {
				require untrailingslashit( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'includes/class-wc-catalog-restrictions-product-admin.php';
				require untrailingslashit( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'includes/class-wc-catalog-restrictions-category-admin.php';


				WC_Catalog_Restrictions_Product_Admin::instance();
				WC_Catalog_Restrictions_Category_Admin::instance();

				if ( $this->get_setting( '_wc_restrictions_locations_enabled', 'no' ) == 'yes' ) {
					require untrailingslashit( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'includes/class-wc-catalog-restrictions-user-admin.php';
					WC_Catalog_Restrictions_User_Admin::instance();
				}
			} else {
				if ( !defined( 'DOING_CRON' ) ) {
					add_action( 'woocommerce_init', array( $this, 'on_init' ), 0 );
				}
			}

			if ( is_admin() && !defined( 'DOING_AJAX' ) && !defined( 'DOING_CRON' ) ) {
				require 'woocommerce-catalog-restrictions-installer.php';
				$this->install();

				add_action( 'admin_init', array( $this, 'on_admin_init' ) );
			}

			//Setup Hooks to clear transients when a post is saved, a category is saved, a user changes their location, a user is updated, a user logs on / out. 
			add_action( 'save_post', array( $this, 'clear_transients' ) );
			add_action( 'created_term', array( $this, 'clear_transients' ) );
			add_action( 'edit_term', array( $this, 'clear_transients' ) );
			add_action( 'edit_user_profile_update', array( $this, 'clear_transients' ) );

			add_action( 'user_register', array( $this, 'clear_session_transients' ) );
			add_action( 'wp_login', array( $this, 'clear_session_transients' ) );
			add_action( 'wp_logout', array( $this, 'clear_session_transients' ) );
			add_action( 'wc_restrictions_location_updated', array( $this, 'clear_session_transients' ) );
			add_action( 'wc_restrictions_location_updated', array( $this, 'maybe_clear_cart' ) );

			add_action( 'init', array( $this, 'on_global_init' ), 9999 );
		}

		public function on_global_init() {
			$this->use_db_filter_cache = apply_filters( 'woocommerce_catalog_restrictions_use_db_filter_cache', false );
		}

		public function install() {
			register_activation_hook( __FILE__, 'activate_woocommerce_catalog_restrictions' );
			register_deactivation_hook( __FILE__, 'deactivate_woocommerce_catalog_restrictions' );
			if ( get_option( 'woocommerce_catalog_restrictions_db_version' ) != $this->version ) {
				add_action( 'admin_init', 'install_woocommerce_catalog_restrictions', 1 );
			}
		}

		public function clear_transients() {
			global $wpdb;
			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_wc_related%'" );
			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_wc_loop%'" );
			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_wc_product_loop%'" );
			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_product_query%'" );

			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_twccr%'" );
			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_twccr%'" );

			if ( $this->use_db_filter_cache ) {
				$table = $wpdb->prefix . 'wc_cvo_cache';
				$wpdb->query( "DELETE FROM $table" );
			}

			wp_cache_flush();
		}

		public function clear_session_transients() {
			global $wpdb;

			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_wc_loop%'" );
			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_wc_product_loop%'" );
			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_product_query%'" );
			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_twccr%'" );
			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_twccr%'" );

			if ( $this->use_db_filter_cache ) {
				$table = $wpdb->prefix . 'wc_cvo_cache';
				$wpdb->query( "DELETE FROM $table" );
			}

			$session = WC_Catalog_Visibility_Compatibility::WC()->session;
			if ( isset( $session ) ) {
				$session_id = WC_Catalog_Visibility_Compatibility::WC()->session->get_customer_id();
				$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_twccr_" . $session_id . "%'" );
				$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_twccr_" . $session_id . "%'" );

				wp_cache_flush();
			}
		}

		public function maybe_clear_cart() {
			if ( $this->get_setting( '_wc_restrictions_locations_clear', 'no' ) == 'yes' ) {
				WC()->cart->empty_cart( true );
			}
		}

		public function on_init() {
			if ( !WC() ) {
				return;
			}

			if (apply_filters('woocommerce_catalog_restrictions_allow_editors', false) && current_user_can('edit_posts')) {
				return;
			}

			require 'includes/class-wc-catalog-restrictions-query.php';
			require 'includes/class-wc-catalog-restrictions-filters.php';

			WC_Catalog_Restrictions_Query::instance();
			WC_Catalog_Restrictions_Filters::instance();
			//load after woocommerce

			if ( $this->get_setting( '_wc_restrictions_locations_enabled', 'no' ) == 'yes' ) {
				add_filter( 'template_redirect', array( $this, 'template_redirect' ), 999 );
			}

			if ( $_POST && !empty( $_POST ) ) {
				if ( isset( $_POST['save-location'] ) ) {
					$location = $_POST['location'];

					$session = &WC_Catalog_Visibility_Compatibility::WC()->session;

					if ( $session ) {

						$session->wc_location = $location;

						do_action( 'wc_restrictions_location_updated' );

						if ( is_user_logged_in() ) {
							update_user_meta( get_current_user_id(), '_wc_location', $location );
						}

						if ( isset( $session->wc_catalog_restrictions_return_url ) ) {
							$url = $session->wc_catalog_restrictions_return_url;
							unset( $session->wc_catalog_restrictions_return_url );
							wp_safe_redirect( apply_filters( 'woocommerce_catalog_restrictions_redirect_on_location', $url ) );
						} else {
							wp_safe_redirect( apply_filters( 'woocommerce_catalog_restrictions_redirect_on_location', get_site_url() ) );
						}

						die();
					}
				} else {

				}
			}


		}

		public function on_admin_init() {
			global $wpdb;
			if ( is_admin() && isset( $_GET['wc_catalog_visibility_db_cleanup'] ) && current_user_can( 'administrator' ) ) {

				$wc_term_meta_table = $wpdb->prefix . 'woocommerce_termmeta';
				$wpdb->query( "DELETE FROM $wc_term_meta_table WHERE (meta_key = '_wc_restrictions' OR meta_key = '_wc_restrictions_allowed') AND (meta_value = '');" );
				$wpdb->query( "DELETE FROM $wc_term_meta_table WHERE (meta_key = '_wc_restrictions_location' OR meta_key = '_wc_restrictions_locations') AND (meta_value = '');" );
			}
		}

		/*
		 * Template and Location Picker Function 
		 */

		public function get_location_for_current_user() {
			global $woocommerce, $wc_cvo;

			$location = false;

			/*
			if ( is_user_logged_in() ) {
				$location = get_user_meta( get_current_user_id(), '_wc_location', true );
			}

			if ( ! empty( $location ) ) {
				return $location;
			}
			*/

			$session    = &WC_Catalog_Visibility_Compatibility::WC()->session;
			$location   = isset( $session ) && isset( $session->wc_location ) ? $session->wc_location : '';
			$changeable = apply_filters( 'wc_location_changeable', $wc_cvo->setting( '_wc_restrictions_locations_changeable' ) == 'yes' );

			if ( !$changeable || empty( $location ) ) {
				if ( function_exists( 'wc_get_customer_default_location' ) && $this->get_setting( '_wc_restrictions_locations_use_geo', 'yes' ) == 'yes' ) {
					$l = wc_get_customer_default_location();

					if ( isset( $l['country'] ) ) {
						$location = $l['country'];
					}

					if ( apply_filters( 'woocommerce_catalog_restrictions_locations_include_states', get_option( '_wc_restrictions_locations_type' ) == 'states' ) ) {
						if ( $l['state'] ) {
							$location = $l['country'] . $l['state'];
						}

					}
				}
			}

			return apply_filters( 'woocommerce_catalog_restrictions_get_user_location', $location );
		}

		public function template_redirect() {
			global $woocommerce;

			$location   = $this->get_location_for_current_user();
			$can_change = get_user_meta( get_current_user_id(), '_wc_location_user_changeable', true );

			if ( empty( $location ) && $this->get_setting( '_wc_restrictions_locations_required', 'no' ) == 'yes' && ( empty( $can_change ) || $can_change == 'yes' ) ) {
				$location_page_id = wc_get_page_id( 'choose_location' );
				if ( $location_page_id && !is_page( $location_page_id ) ) {
					$woocommerce->session->wc_catalog_restrictions_return_url = esc_url_raw( add_query_arg( array( 'locationset' => '1' ) ) );
					$location_url                                             = get_permalink( $location_page_id );
					if ( $location_url ) {
						wp_safe_redirect( $location_url );
						exit;
					}
				}
			}
		}

		/*
		 * Utility functions
		 */

		public function plugin_url() {
			return plugin_dir_url( __FILE__ );
		}

		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		public function get_setting( $key, $default = null ) {
			return get_option( $key, $default );
		}

	}

}


function wc_cvo_restrictions() {
	return WC_Catalog_Restrictions::instance();
}

$GLOBALS['wc_catalog_restrictions'] = wc_cvo_restrictions();
