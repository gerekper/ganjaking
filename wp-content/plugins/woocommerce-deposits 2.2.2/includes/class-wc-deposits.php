<?php
/**
 * Main plugin class.
 *
 * @package woocommerce-deposits
 */

/**
 * WC_Deposits class.
 */
class WC_Deposits {

	/**
	 * Class Instance.
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Get the class instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		global $wpdb;

		define( 'WC_DEPOSITS_PLUGIN_URL', untrailingslashit( plugins_url( '', WC_DEPOSITS_FILE ) ) );
		define( 'WC_DEPOSITS_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __DIR__ ) ) . '/templates/' );
		define( 'WC_DEPOSITS_ABSPATH', trailingslashit( plugin_dir_path( __DIR__ ) ) );

		register_deactivation_hook( WC_DEPOSITS_FILE, array( $this, 'deactivate' ) );

		if ( get_option( 'wc_deposits_version' ) !== WC_DEPOSITS_VERSION ) {
			add_action( 'shutdown', array( $this, 'delayed_install' ) );
		}

		$wpdb->wc_deposits_payment_plans          = $wpdb->prefix . 'wc_deposits_payment_plans';
		$wpdb->wc_deposits_payment_plans_schedule = $wpdb->prefix . 'wc_deposits_payment_plans_schedule';

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'plugins_loaded', array( $this, 'includes' ) );
		add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

		/*
		 * Multicurrency Support (https://woocommerce.com/products/multi-currency/).
		 *
		 * This modifies the fixed price deposit amount when WooCommerce Multi-Currency is enabled.
		 * Note: This differs from the WooPayments MultiCurrency options.
		 */
		add_filter( 'woocommerce_multicurrency_get_props_filters', array( $this, 'deposits_multicurrency_get_props_filters' ) );

		/*
		 * WooPayments MultiCurrency Support (https://woocommerce.com/products/woocommerce-payments/).
		 *
		 * This modifies the fixed price deposit amount when WooPayments MultiCurrency is enabled.
		 * Note: This differs from the WooCommerce Multi-Currency extension.
		 */
		add_filter( 'woocommerce_deposits_fixed_deposit_amount', array( $this, 'get_woopayments_multicurrency_price' ) );
	}

	/**
	 * Localisation.
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-deposits' );
		$dir    = trailingslashit( WP_LANG_DIR );
		load_textdomain( 'woocommerce-deposits', $dir . 'woocommerce-deposits/woocommerce-deposits-' . $locale . '.mo' );
		load_plugin_textdomain( 'woocommerce-deposits', false, dirname( plugin_basename( WC_DEPOSITS_FILE ) ) . '/languages/' );
	}

	/**
	 * Includes.
	 */
	public function includes() {
		if ( is_admin() ) {
			require_once __DIR__ . '/class-wc-deposits-settings.php';
			require_once __DIR__ . '/class-wc-deposits-plans-admin.php';
			require_once __DIR__ . '/class-wc-deposits-product-admin.php';
		}
		require_once __DIR__ . '/class-wc-deposits-product-meta.php';
		require_once __DIR__ . '/class-wc-deposits-plans-manager.php';
		require_once __DIR__ . '/class-wc-deposits-cart-manager.php';
		require_once __DIR__ . '/class-wc-deposits-order-manager.php';
		require_once __DIR__ . '/class-wc-deposits-order-item-manager.php';
		require_once __DIR__ . '/class-wc-deposits-scheduled-order-manager.php';
		require_once __DIR__ . '/class-wc-deposits-product-manager.php';
		require_once __DIR__ . '/class-wc-deposits-plan.php';
		require_once __DIR__ . '/class-wc-deposits-my-account.php';
		require_once __DIR__ . '/compatibility/core/class-wc-deposits-core-compatibility.php';
		require_once __DIR__ . '/compatibility/class-wc-deposits-cot-compatibility.php';
		require_once __DIR__ . '/class-wc-deposits-blocks-compatibility.php';
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param  array  $links Plugin Row Meta.
	 * @param  string $file  Plugin Base file.
	 * @return array
	 */
	public function plugin_row_meta( $links, $file ) {
		if ( plugin_basename( WC_DEPOSITS_FILE ) === $file ) {
			$row_meta = array(
				'docs'    => '<a href="https://docs.woocommerce.com/document/woocommerce-deposits/">' . __( 'Docs', 'woocommerce-deposits' ) . '</a>',
				'support' => '<a href="https://woocommerce.com/my-account/">' . __( 'Premium Support', 'woocommerce-deposits' ) . '</a>',
			);
			return array_merge( $links, $row_meta );
		}
		return (array) $links;
	}

	/**
	 * Installer.
	 */
	public function install() {
		add_action( 'shutdown', array( $this, 'delayed_install' ) );

		if ( class_exists( 'WooCommerce' ) ) {
			$notice_html = '<strong>' . esc_html__( 'Deposits have been activated!', 'woocommerce-deposits' ) . '</strong><br><br>';
			/* translators: %s: Deposits settings page link */
			$notice_html .= sprintf( __( 'Add or edit a product to manage deposits in the Product Data section for individual products or go to the <a href="%s" target="_blank">Deposits setting page</a> to manage them storewide.', 'woocommerce-deposits' ), admin_url( 'admin.php?page=wc-settings&tab=products&section=deposits' ) );

			WC_Admin_Notices::add_custom_notice( 'woocommerce_deposits_activation', $notice_html );
		}
	}

	/**
	 * Cleanup on plugin deactivation.
	 *
	 * @since 1.3.5
	 */
	public function deactivate() {
		WC_Admin_Notices::remove_notice( 'woocommerce_deposits_activation' );
		// Delete flush rewrite rules option to force a rewrite rules flush on next activation.
		delete_option( 'woocommerce_deposits_flush_rewrite_rules' );
	}

	/**
	 * Installer (delayed).
	 */
	public function delayed_install() {
		global $wpdb, $wp_roles;

		$wpdb->hide_errors();

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty( $wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty( $wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta(
			"CREATE TABLE {$wpdb->wc_deposits_payment_plans} (
				ID bigint(20) unsigned NOT NULL auto_increment,
				name varchar(255) NOT NULL,
				description longtext NOT NULL,
				PRIMARY KEY  (ID)
			) $collate;
			CREATE TABLE {$wpdb->wc_deposits_payment_plans_schedule} (
				schedule_id bigint(20) unsigned NOT NULL auto_increment,
				schedule_index bigint(20) unsigned NOT NULL default 0,
				plan_id bigint(20) unsigned NOT NULL,
				amount varchar(255) NOT NULL,
				interval_amount varchar(255) NOT NULL,
				interval_unit varchar(255) NOT NULL,
				PRIMARY KEY  (schedule_id),
				KEY plan_id (plan_id)
			) $collate;"
		);

		// Cron.
		wp_clear_scheduled_hook( 'woocommerce_invoice_scheduled_orders' );
		wp_schedule_event( time(), 'hourly', 'woocommerce_invoice_scheduled_orders' );

		// Update version.
		update_option( 'wc_deposits_version', WC_DEPOSITS_VERSION );
	}

	/**
	 * Add deposits filter in multicurrency get props filters to Convert fixed deposit amount.
	 *
	 * @param string[] $filter_tags The array of filter tags (the filter names).
	 *
	 * @return string[]
	 */
	public function deposits_multicurrency_get_props_filters( $filter_tags ) {
		$filter_tags[] = 'woocommerce_deposits_fixed_deposit_amount';

		return $filter_tags;
	}

	/**
	 * Declares support for HPOS
	 *
	 * @since 2.0.3
	 */
	public function declare_hpos_compatibility() {
		if ( ! class_exists( 'Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			return;
		}

		Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WC_DEPOSITS_ABSPATH . '/woocommerce-deposits.php', true );
	}

	/**
	 * WooPayments MultiCurrency fixed price compatibility.
	 *
	 * This function is used to convert fixed price deposits to a specific
	 * currency when using WooPayments MultiCurrency support.
	 *
	 * Note: This is not related to the WooCommerce Multicurrency extension.
	 *
	 * @since 2.2.2
	 *
	 * @param float $amount  Fixed amount deposit value.
	 */
	public static function get_woopayments_multicurrency_price( $amount ) {
		if ( ! class_exists( 'WCPay\\MultiCurrency\\MultiCurrency' ) ) {
			return $amount;
		}

		// Unable to `use` classes that may not exist.
		$multi_currency = WCPay\MultiCurrency\MultiCurrency::instance();
		return $multi_currency->get_price( $amount, 'product' );
	}
}
