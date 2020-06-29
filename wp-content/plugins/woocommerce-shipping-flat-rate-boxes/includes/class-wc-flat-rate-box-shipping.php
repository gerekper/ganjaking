<?php
/**
 * Main plugin class.
 *
 * @package woocommerce-shipping-flat-rate-boxes
 */

/**
 * Main Flat Rate Box Shipping Class.
 */
class WC_Flat_Rate_Box_Shipping {
	/**
	 * Constructor.
	 */
	public function __construct() {
		define( 'BOX_SHIPPING_DEBUG', defined( 'WP_DEBUG' ) && 'true' === WP_DEBUG && ( ! defined( 'WP_DEBUG_DISPLAY' ) || 'true' === WP_DEBUG_DISPLAY ) );
		$this->init();
	}

	/**
	 * Register method for usage.
	 *
	 * @param  array $shipping_methods List of shipping methods.
	 * @return array
	 */
	public function woocommerce_shipping_methods( $shipping_methods ) {
		$shipping_methods['flat_rate_boxes'] = 'WC_Shipping_Flat_Rate_Boxes';
		return $shipping_methods;
	}

	/**
	 * Init Flat Rate Boxes.
	 */
	public function init() {
		include_once __DIR__ . '/functions-ajax.php';
		include_once __DIR__ . '/functions-admin.php';

		/**
		 * Install check (for updates).
		 */
		if ( get_option( 'box_shipping_version' ) < WC_BOX_SHIPPING_VERSION ) {
			wc_shipping_flat_rate_boxes_install();
		}

		// 2.6.0+ supports zones and instances.
		if ( version_compare( WC_VERSION, '2.6.0', '>=' ) ) {
			add_filter( 'woocommerce_shipping_methods', array( $this, 'woocommerce_shipping_methods' ) );
		} else {
			if ( ! defined( 'SHIPPING_ZONES_TEXTDOMAIN' ) ) {
				define( 'SHIPPING_ZONES_TEXTDOMAIN', 'woocommerce-shipping-flat-rate-boxes' );
			}
			if ( ! class_exists( 'WC_Shipping_zone' ) ) {
				include_once __DIR__ . '/legacy/shipping-zones/class-wc-shipping-zones.php';
			}
			add_action( 'woocommerce_load_shipping_methods', array( $this, 'load_shipping_methods' ) );
			add_action( 'admin_notices', array( $this, 'welcome_notice' ) );
		}

		// Hooks.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
		add_action( 'woocommerce_shipping_init', array( $this, 'shipping_init' ) );
	}

	/**
	 * Localisation
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'woocommerce-shipping-flat-rate-boxes', false, dirname( plugin_basename( __DIR__ ) ) . '/languages/' );
	}

	/**
	 * Plugin row meta.
	 *
	 * @param  array  $links List of links.
	 * @param  string $file  Current plugin.
	 * @return array
	 */
	public function plugin_row_meta( $links, $file ) {
		if ( plugin_basename( __DIR__ ) === $file ) {
			$row_meta = array(
				'docs'    => '<a href="' . esc_url( apply_filters( 'woocommerce_flat_rate_boxes_shipping_docs_url', 'https://woocommerce.com/document/flat-rate-box-shipping/' ) ) . '" title="' . esc_attr( __( 'View Documentation', 'woocommerce-shipping-flat-rate-boxes' ) ) . '">' . __( 'Docs', 'woocommerce-shipping-flat-rate-boxes' ) . '</a>',
				'support' => '<a href="' . esc_url( apply_filters( 'woocommerce_flat_rate_boxes_support_url', 'https://support.woocommerce.com/' ) ) . '" title="' . esc_attr( __( 'Visit Premium Customer Support Forum', 'woocommerce-shipping-flat-rate-boxes' ) ) . '">' . __( 'Premium Support', 'woocommerce-shipping-flat-rate-boxes' ) . '</a>',
			);
			return array_merge( $links, $row_meta );
		}
		return (array) $links;
	}

	/**
	 * Admin welcome notice.
	 */
	public function welcome_notice() {
		if ( get_option( 'hide_box_shipping_welcome_notice' ) ) {
			return;
		}
		wp_enqueue_style( 'woocommerce-activation', WC()->plugin_url() . '/assets/css/activation.css', array(), WC_VERSION );
		?>
		<div id="message" class="updated woocommerce-message wc-connect">
			<div class="squeezer">
				<h4><strong><?php echo wp_kses_post( __( '<strong>Flat Rate Box shipping is installed</strong> &#8211; Add some shipping zones to get started :)', 'woocommerce-shipping-flat-rate-boxes' ) ); ?></h4>
				<p class="submit">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=shipping' ) ); ?>" class="button-primary"><?php esc_html_e( 'Setup Zones', 'woocommerce-shipping-flat-rate-boxes' ); ?></a>
					<a class="skip button-primary" href="https://docs.woocommerce.com/document/flat-rate-box-shipping/"><?php esc_html_e( 'Documentation', 'woocommerce-shipping-flat-rate-boxes' ); ?></a>
				</p>
			</div>
		</div>
		<?php
		update_option( 'hide_box_shipping_welcome_notice', 1 );
	}

	/**
	 * Admin styles + scripts
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'woocommerce_shipping_flat_rate_boxes_styles', plugins_url( '/assets/css/admin.css', __DIR__ ), array(), WC_BOX_SHIPPING_VERSION );
		wp_register_script( 'woocommerce_shipping_flat_rate_box_rows', plugins_url( '/assets/js/flat-rate-box-rows.min.js', __DIR__ ), array( 'jquery', 'wp-util' ), WC_BOX_SHIPPING_VERSION, true );
		wp_localize_script(
			'woocommerce_shipping_flat_rate_box_rows',
			'woocommerce_shipping_flat_rate_box_rows',
			array(
				'i18n'             => array(
					'delete_rates' => __( 'Delete the selected boxes?', 'woocommerce-table-rate-shipping' ),
				),
				'delete_box_nonce' => wp_create_nonce( 'delete-box' ),
			)
		);
	}

	/**
	 * Load shipping class.
	 */
	public function shipping_init() {
		include_once __DIR__ . '/class-wc-shipping-flat-rate-boxes.php';
		include_once __DIR__ . '/class-wc-shipping-flat-rate-boxes-privacy.php';
	}

	/**
	 * Load shipping methods.
	 *
	 * @param mixed $package Shipping package.
	 */
	public function load_shipping_methods( $package ) {
		// Register the main class.
		woocommerce_register_shipping_method( 'WC_Shipping_Flat_Rate_Boxes' );

		if ( ! $package ) {
			return;
		}

		// Get zone for package.
		$zone = woocommerce_get_shipping_zone( $package );

		if ( BOX_SHIPPING_DEBUG ) {
			$notice_text = 'Customer matched shipping zone <strong>' . $zone->zone_name . '</strong> (#' . $zone->zone_id . ')';

			if ( ! wc_has_notice( $notice_text, 'notice' ) ) {
				wc_add_notice( $notice_text, 'notice' );
			}
		}

		if ( $zone->exists() ) {
			// Register zone methods.
			$zone->register_shipping_methods();
		}
	}

}
