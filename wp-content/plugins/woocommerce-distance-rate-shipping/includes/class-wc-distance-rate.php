<?php
/**
 * Plugin main class.
 *
 * @package woocommerce-shippings-distance-rate
 */

/**
 * Distance rate plugin class.
 */
class WC_Distance_Rate {
	/**
	 * Plugin's version.
	 *
	 * @since 1.0.5
	 *
	 * @var string
	 */
	public $version;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->version = WC_DISTANCE_RATE_VERSION;

		add_action( 'admin_init', array( $this, 'maybe_install' ), 5 );
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( WC_DISTANCE_RATE_FILE ), array( $this, 'plugin_action_links' ) );
		add_action( 'woocommerce_shipping_init', array( $this, 'shipping_init' ) );
		add_filter( 'woocommerce_shipping_methods', array( $this, 'shipping_methods' ) );
		add_action( 'admin_notices', array( $this, 'upgrade_notice' ) );
		add_action( 'wp_ajax_distance_rate_dismiss_upgrade_notice', array( $this, 'dismiss_upgrade_notice' ) );
		add_action( 'woocommerce_loaded', array( $this, 'load_post_wc_class' ) );
	}

	/**
	 * Loads any class that needs to check for WC loaded.
	 *
	 * @since 1.0.9
	 */
	public function load_post_wc_class() {
		require_once __DIR__ . '/class-wc-distance-rate-privacy.php';
	}

	/**
	 * Localisation.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'woocommerce-distance-rate-shipping', false, dirname( plugin_basename( __DIR__ ) ) . '/languages/' );
	}

	/**
	 * Add plugin action links to the plugins page.
	 *
	 * @param  array $links Links.
	 * @return array
	 */
	public function plugin_action_links( $links ) {
		$plugin_links = array(
			'<a href="' . admin_url( ( function_exists( 'WC' ) ? 'admin.php?page=wc-settings&tab=shipping&section=wc_shipping_distance_rate' : 'admin.php?page=woocommerce_settings&tab=shipping&section=WC_Distance_Rate' ) ) . '">' . __( 'Settings', 'woocommerce-distance-rate-shipping' ) . '</a>',
			'<a href="https://support.woocommerce.com/">' . __( 'Support', 'woocommerce-distance-rate-shipping' ) . '</a>',
			'<a href="https://docs.woocommerce.com/document/woocommerce-distance-rate-shipping/">' . __( 'Docs', 'woocommerce-distance-rate-shipping' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Load our shipping class.
	 */
	public function shipping_init() {
		if ( version_compare( WC_VERSION, '2.6.0', '<' ) ) {
			include_once __DIR__ . '/class-wc-shipping-distance-rate-deprecated.php';
		} else {
			include_once __DIR__ . '/class-wc-shipping-distance-rate.php';
		}
	}

	/**
	 * Add our shipping method to woocommerce.
	 *
	 * @param  array $methods Shipping methods.
	 * @return array
	 */
	public function shipping_methods( $methods ) {
		if ( version_compare( WC_VERSION, '2.6.0', '<' ) ) {
			$methods[] = 'WC_Shipping_Distance_Rate';
		} else {
			$methods['distance_rate'] = 'WC_Shipping_Distance_Rate';
		}

		return $methods;
	}

	/**
	 * Checks the plugin version.
	 *
	 * @access public
	 * @since 1.0.5
	 * @version 1.0.5
	 * @return bool
	 */
	public function maybe_install() {
		// Only need to do this for versions less than 1.0.5 to migrate
		// settings to shipping zone instance.
		$doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;
		if ( ! $doing_ajax
				&& ! defined( 'IFRAME_REQUEST' )
				&& version_compare( WC_VERSION, '2.6.0', '>=' )
				&& version_compare( get_option( 'wc_distance_rate_version' ), '1.0.5', '<' ) ) {

			$this->install();

		}

		return true;
	}

	/**
	 * Update/migration script.
	 *
	 * @since 1.0.5
	 * @version 1.0.5
	 * @access public
	 */
	public function install() {
		// Get all saved settings and cache it.
		$distance_rate_settings = get_option( 'woocommerce_distance_rate_settings', false );

		// Rules are stored in separate tables.
		$distance_rate_rules = get_option( 'woocommerce_distance_rate_rules', array() );

		// Settings exists.
		if ( $distance_rate_settings ) {
			global $wpdb;

			// Unset un-needed settings.
			unset( $distance_rate_settings['enabled'] );
			unset( $distance_rate_settings['availability'] );
			unset( $distance_rate_settings['countries'] );

			// Merge rules into settings.
			$distance_rate_settings['rules'] = $distance_rate_rules;

			// First add it to the "rest of the world" zone when no distance rate
			// instance.
			if ( ! $this->is_zone_has_distance_rate( 0 ) ) {
				$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}woocommerce_shipping_zone_methods ( zone_id, method_id, method_order, is_enabled ) VALUES ( %d, %s, %d, %d )", 0, 'distance_rate', 1, 1 ) );
				// Add settings to the newly created instance to options table.
				$instance = $wpdb->insert_id;
				add_option( 'woocommerce_distance_rate_' . $instance . '_settings', $distance_rate_settings );
			}

			update_option( 'woocommerce_distance_rate_show_upgrade_notice', 'yes' );
		}

		update_option( 'wc_distance_rate_version', $this->version );
	}

	/**
	 * Show the user a notice for plugin updates.
	 *
	 * @since 1.0.5
	 */
	public function upgrade_notice() {
		$show_notice = get_option( 'woocommerce_distance_rate_show_upgrade_notice' );

		if ( 'yes' !== $show_notice ) {
			return;
		}

		$query_args = array(
			'page' => 'wc-settings',
			'tab'  => 'shipping',
		);

		$zones_admin_url = add_query_arg( $query_args, get_admin_url() . 'admin.php' );
		?>
		<div class="notice notice-success is-dismissible wc-distance-rate-notice">
			<p>
			<?php
				/* translators: %1s: Shipping zones link start %2s: Link end */
				echo sprintf( __( 'Distance Rate now supports shipping zones. The zone settings were added to a new Distance Rate method on the "Rest of the World" Zone. See the zones %1$shere%2$s ', 'woocommerce-distance-rate-shipping' ), '<a href="' . $zones_admin_url . '">', '</a>' );
			?>
			</p>
		</div>

		<script type="application/javascript">
			jQuery( '.notice.wc-distance-rate-notice' ).on( 'click', '.notice-dismiss', function () {
				wp.ajax.post( 'distance_rate_dismiss_upgrade_notice' );
			});
		</script>
		<?php
	}

	/**
	 * Turn of the dismisable upgrade notice.
	 *
	 * @since 1.0.5
	 */
	public function dismiss_upgrade_notice() {
		update_option( 'woocommerce_distance_rate_show_upgrade_notice', 'no' );
	}

	/**
	 * Helper method to check whether given zone_id has distance rate method instance.
	 *
	 * @since 1.0.5
	 *
	 * @param int $zone_id Zone ID.
	 *
	 * @return bool True if given zone_id has distance rate method instance
	 */
	public function is_zone_has_distance_rate( $zone_id ) {
		global $wpdb;

		return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(instance_id) FROM {$wpdb->prefix}woocommerce_shipping_zone_methods WHERE method_id = 'distance_rate' AND zone_id = %d", $zone_id ) ) > 0;
	}
}
