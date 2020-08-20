<?php
/**
 * Extra Product Options Check class
 *
 * Checks if the plugin can be run according to
 * its requirements.
 *
 * @package Extra Product Options/Classes
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_CHECK_base {

	/**
	 * The single instance of the class
	 *
	 * @since 1.0
	 */
	protected static $_instance = NULL;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;

	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'check_version' ) );

		if ( ! self::compatible_version() ) {
			return;
		}

		add_action( 'plugins_loaded', array( $this, 'eco_check' ), 0 );

	}

	/**
	 * Stop the plugin if requirements are not met
	 *
	 * @since 1.0
	 * @static
	 */
	public function stop_plugin() {

		if ( apply_filters( 'wc_epo_stop_plugin', false ) ) {
			return TRUE;
		}

		if ( ! self::themecomplete_woocommerce_check() ) {
			return TRUE;
		}

		if ( ! self::compatible_version() ) {
			return TRUE;
		}

		if ( self::old_version() ) {
			return TRUE;
		}

		if ( ! self::woocommerce_check() ) {
			return TRUE;
		}

		return FALSE;

	}

	/**
	 * Check for compatible WordPress version
	 *
	 * @since 1.0
	 * @static
	 */
	public static function activation_check() {

		if ( ! self::compatible_version() ) {
			deactivate_plugins( plugin_basename( THEMECOMPLETE_EPO_PLUGIN_FILE ) );
			wp_die( sprintf( esc_html__( 'WooCommerce TM Extra Product Options requires WordPress %s or later.', 'woocommerce-tm-extra-product-options' ), THEMECOMPLETE_EPO_WP_VERSION ) );
		}

	}

	/**
	 * Check for compatible Extra Checkout Options version
	 *
	 * @since 4.8.5
	 * @static
	 */
	public function eco_check() {

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if ( defined( 'THEMECOMPLETE_ECO_VERSION' ) && function_exists( 'themecomplete_eco_plugin_init_admin' ) && function_exists( 'themecomplete_eco_plugin_init' ) ) {

			if ( version_compare( THEMECOMPLETE_ECO_VERSION, THEMECOMPLETE_SUPPORTED_ECO_VERSION, '<' ) ) {
				add_action( 'admin_notices', array( $this, 'themecomplete_addons_check_eco' ) );
				add_action( 'admin_notices', array( $this, 'eco_notice' ) );
				remove_action( 'plugins_loaded', 'themecomplete_eco_plugin_init_admin' );
				remove_action( 'plugins_loaded', 'themecomplete_eco_plugin_init' );
				if ( is_plugin_active( plugin_basename( THEMECOMPLETE_ECO_PLUGIN_PATH . '/tm-woo-extra-checkout-options.php' ) ) ) {
					deactivate_plugins( plugin_basename( THEMECOMPLETE_ECO_PLUGIN_PATH . '/tm-woo-extra-checkout-options.php' ) );
					add_action( 'admin_notices', array( $this, 'eco_notice_deactivate' ) );
				}
			}

		}

		if ( defined( 'TM_ECO_VERSION' ) && function_exists( 'tc_eco_plugin_init_admin' ) && function_exists( 'tc_eco_plugin_init' ) ) {

			if ( version_compare( TM_ECO_VERSION, THEMECOMPLETE_SUPPORTED_ECO_VERSION, '<' ) ) {
				add_action( 'admin_notices', array( $this, 'themecomplete_addons_check_eco' ) );
				add_action( 'admin_notices', array( $this, 'eco_notice' ) );
				remove_action( 'plugins_loaded', 'tc_eco_plugin_init_admin' );
				remove_action( 'plugins_loaded', 'tc_eco_plugin_init' );
				if ( is_plugin_active( plugin_basename( TM_ECO_PLUGIN_PATH . '/tm-woo-extra-checkout-options.php' ) ) ) {
					deactivate_plugins( plugin_basename( TM_ECO_PLUGIN_PATH . '/tm-woo-extra-checkout-options.php' ) );
					add_action( 'admin_notices', array( $this, 'eco_notice_deactivate' ) );
				}
			}

		}

	}

	/**
	 * Deprecation notice for Extra Checkout Options
	 *
	 * @since 4.9.4
	 */
	public function themecomplete_addons_check_eco() {

		echo '<div class="error fade"><p>';
		echo sprintf( esc_html__( 'WooCommerce Extra Product Options requires WooCommerce Extra Checkout Options %s or later!.', 'woocommerce-tm-extra-product-options' ), THEMECOMPLETE_SUPPORTED_ECO_VERSION );

		if ( defined( 'THEMECOMPLETE_ECO_VERSION' ) && isset( $all_plugins['woocommerce-tm-extra-checkout-options-addon/tm-woo-extra-checkout-options.php'] ) ) {
			echo ' ' . esc_html__( 'The installed version is', 'woocommerce-tm-extra-product-options' ) . ' ' . THEMECOMPLETE_ECO_VERSION;
		} else if ( defined( 'TM_ECO_VERSION' ) && isset( $all_plugins['woocommerce-tm-extra-checkout-options-addon/tm-woo-extra-checkout-options.php'] ) ) {
			echo ' ' . esc_html__( 'The installed version is', 'woocommerce-tm-extra-product-options' ) . ' ' . TM_ECO_VERSION;
		}

		echo '</p></div>' . "\n";

	}

	public function eco_notice() {

		echo '<div class="error fade"><h4>WooCommerce TM Extra Product Options</h4><p>';
		echo sprintf( esc_html__( '%sImportant:%s Your version of WooCommerce Extra Checkout Options is not supported. Please update to the latest version.', 'woocommerce-tm-extra-product-options' ),
			'<strong>', '</strong>' );
		echo '</p></div>' . "\n";

	}

	public function eco_notice_deactivate() {

		echo '<div class="error fade"><h4>WooCommerce TM Extra Product Options</h4><p>';
		echo sprintf( esc_html__( '%sImportant:%s WooCommerce Extra Checkout Options has been deactivated because it is not compatible with the current version of WooCommerce Extra product Options.', 'woocommerce-tm-extra-product-options' ),
			'<strong>', '</strong>' );
		echo '</p></div>' . "\n";

	}

	/**
	 * Check for compatible WordPress and WooCommerce version
	 *
	 * The function also checks if there is a previous old version of the
	 * plugin install  that could potentially conflict.
	 *
	 * @since 1.0
	 * @static
	 */
	public function check_version() {

		if ( ! self::compatible_version() ) {
			if ( is_plugin_active( plugin_basename( THEMECOMPLETE_EPO_PLUGIN_FILE ) ) ) {
				deactivate_plugins( plugin_basename( THEMECOMPLETE_EPO_PLUGIN_FILE ) );
				add_action( 'admin_notices', array( $this, 'disabled_notice' ) );
				if ( isset( $_GET['activate'] ) ) {
					unset( $_GET['activate'] );
				}
			}
		}

		if ( self::old_version() ) {
			deactivate_plugins( 'woocommerce-tm-custom-price-fields/tm-woo-custom-prices.php' );
			add_action( 'admin_notices', array( $this, 'deprecated_notice' ) );
		}

		if ( ! self::woocommerce_check() ) {
			add_action( 'admin_notices', array( $this, 'disabled_notice_woocommerce_check' ) );
		}

	}

	/**
	 * Add notice for WooCommerce version
	 *
	 * @since 1.0
	 * @static
	 */
	public function disabled_notice_woocommerce_check() {

		echo '<div class="woocommerce-message error fade"><p>';
		if ( self::themecomplete_woocommerce_check_only() ) {
			echo sprintf( esc_html__( '%sImportant:%s Please run WooCommerce updater before using WooCommerce TM Extra Product Options.',
				'woocommerce-tm-extra-product-options' ),
				'<strong>', '</strong>' );
		} else {
			echo sprintf( esc_html__( '%sImportant:%s WooCommerce TM Extra Product Options requires %sWooCommerce%s %s or later.', 'woocommerce-tm-extra-product-options' ),
				'<strong>', '</strong>', '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', THEMECOMPLETE_EPO_WC_VERSION );
		}
		echo '</p>';

		if ( self::themecomplete_woocommerce_check_only() && get_option( 'woocommerce_db_version' ) ) {

			$update_url = wp_nonce_url(
				add_query_arg( 'do_update_woocommerce', 'true', admin_url( 'admin.php?page=wc-settings' ) ),
				'wc_db_update',
				'wc_db_update_nonce'
			);

			echo '<p class="submit"><a href="' . esc_url( $update_url ) . '" class="wc-update-now button-primary">' . esc_attr__( 'Run the updater', 'woocommerce' ) . '</a></p>';
		}

		echo '</div>';

	}

	/**
	 * Add deprecations notices
	 *
	 * @since 1.0
	 * @static
	 */
	public function deprecated_notice() {

		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );

		if ( in_array( 'woocommerce-tm-custom-price-fields/tm-woo-custom-prices.php', $active_plugins ) ) {
			$deactivate_url = 'plugins.php?action=deactivate&plugin=' . urlencode( 'woocommerce-tm-custom-price-fields/tm-woo-custom-prices.php' ) . '&plugin_status=all&paged=1&s&_wpnonce=' . urlencode( wp_create_nonce( 'deactivate-plugin_woocommerce-tm-custom-price-fields/tm-woo-custom-prices.php' ) );
			echo '<div class="error fade"><p>';
			echo '<strong>Important:</strong> It is highly recommended that you <a href="' . esc_url( admin_url( $deactivate_url ) ) . '"> deactivate the old Custom Price Fields</a> plugin.';
			echo '</p></div>' . "\n";
		} else {
			$delete_url = 'plugins.php?action=delete-selected&checked%5B0%5D=' . urlencode( 'woocommerce-tm-custom-price-fields/tm-woo-custom-prices.php' ) . '&plugin_status=all&paged=1&s&_wpnonce=' . urlencode( wp_create_nonce( 'bulk-plugins' ) );
			echo '<div class="error fade"><p>';
			echo '<strong>Important:</strong> It is highly recommended that you <a href="' . esc_url( admin_url( $delete_url ) ) . '"> delete the old Custom Price Fields</a> plugin.';
			echo '</p></div>' . "\n";
		}

	}

	/**
	 * Add notice for WordPress version
	 *
	 * @since 1.0
	 * @static
	 */
	public function disabled_notice() {

		echo '<div class="error fade"><p>';
		echo sprintf( esc_html__( '%sImportant:%s WooCommerce TM Extra Product Options requires WordPress %s or later.', 'woocommerce-tm-extra-product-options' ),
			'<strong>', '</strong>', THEMECOMPLETE_EPO_WP_VERSION );
		echo '</p></div>' . "\n";

	}

	/**
	 * Check for comaptible WordPress version
	 *
	 * @since 1.0
	 * @static
	 */
	public static function compatible_version() {

		if ( version_compare( $GLOBALS['wp_version'], THEMECOMPLETE_EPO_WP_VERSION, '<' ) ) {
			return FALSE;
		}

		return TRUE;

	}

	/**
	 * Check for an older plugin version
	 *
	 * @since 1.0
	 * @static
	 */
	public static function old_version() {

		if ( class_exists( 'TM_Custom_Prices' ) ) {
			return TRUE;
		}

		return FALSE;

	}

	/**
	 * Check if WooCommerce database needs update
	 *
	 * @since 1.0
	 * @static
	 */
	public static function tc_needs_wc_db_update() {
		$_tm_current_woo_version = get_option( 'woocommerce_db_version' );
		$_tc_needs_wc_db_update  = FALSE;
		if ( get_option( 'woocommerce_db_version' ) !== FALSE ) {
			if ( version_compare( get_option( 'woocommerce_db_version' ), '2.3', '<' ) && version_compare( get_option( 'woocommerce_version' ), '2.4', '<' ) ) {
				$_tm_notice_check       = '_wc_needs_update';
				$_tc_needs_wc_db_update = get_option( $_tm_notice_check );
				// no check after 2.6 update
			} elseif ( version_compare( get_option( 'woocommerce_db_version' ), '2.5', '>=' ) ) {
				$_tc_needs_wc_db_update = FALSE;
			} else {
				$_tm_notice_check       = 'woocommerce_admin_notices';
				$_tc_needs_wc_db_update = in_array( 'update', get_option( $_tm_notice_check, array() ) );
			}
		}

		return $_tc_needs_wc_db_update;
	}

	/**
	 * Check if WooCommerce is active
	 *
	 * @since 1.0
	 * @static
	 */
	public static function themecomplete_woocommerce_check() {
		return ! self::tc_needs_wc_db_update() && self::themecomplete_woocommerce_check_only();
	}

	/**
	 * Check if WooCommerce is active
	 *
	 * @since 1.0
	 * @static
	 */
	public static function themecomplete_woocommerce_check_only() {
		$active_plugins = (array) get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
	}

	/**
	 * Check for comaptible WooCommerce version
	 *
	 * @since 1.0
	 * @static
	 */
	public static function woocommerce_check() {

		if (get_option( 'woocommerce_db_version' ) === FALSE && class_exists('WC_Install')){
			WC_Install::update_db_version();
		}

		if ( self::themecomplete_woocommerce_check() && ! version_compare( get_option( 'woocommerce_db_version' ), THEMECOMPLETE_EPO_WC_VERSION, '<' ) ) {
			return TRUE;
		}

		return FALSE;

	}

}
