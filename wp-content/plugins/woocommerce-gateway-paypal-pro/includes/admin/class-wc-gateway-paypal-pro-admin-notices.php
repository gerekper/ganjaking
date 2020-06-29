<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Class that represents admin notices.
 *
 * @since 4.5.0
 */
class WC_PayPal_Pro_Admin_Notices {

	/**
	 * Notices (array)
	 * @var array
	 */
	public $notices = array();

	/**
	 * Constructor
	 *
	 * @since 4.1.0
	 */
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * Allow this class and other classes to add slug keyed notices (to avoid duplication).
	 *
	 * @since 4.5.0
	 */
	public function add_admin_notice( $slug, $class, $message, $dismissible = false ) {
		$this->notices[ $slug ] = array(
			'class'       => $class,
			'message'     => $message,
			'dismissible' => $dismissible,
		);
	}
	/**
	 * Display any notices we've collected thus far.
	 *
	 * @since 4.5.0
	 */
	public function admin_notices() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$this->check_environment();

		foreach ( (array) $this->notices as $notice_key => $notice ) {
			echo '<div class="' . esc_attr( $notice['class'] ) . '" style="position:relative;">';
			if ( $notice['dismissible'] ) {
				?>
				<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wc-paypal-pro-hide-notice', $notice_key ), 'wc_paypal_pro_hide_notices_nonce', '_wc_paypal_pro_notice_nonce' ) ); ?>" class="woocommerce-message-close notice-dismiss" style="position:relative;float:right;padding:9px 0px 9px 9px 9px;text-decoration:none;"></a>
				<?php
			}
			echo '<p><b>' . esc_html( __( 'WooCommerce PayPal Pro', 'woocommerce-payments' ) ) . '</b></p>';
			echo '<p>';
			echo wp_kses( $notice['message'], array( 'a' => array( 'href' => array(), 'target' => array() ), 'br' => array() ) );
			echo '</p></div>';
		}
	}

	/**
	 * The basic sanity check.
	 *
	 * @since 4.5.0
	 */
	public function check_environment() {
		$options = get_option( 'woocommerce_paypal_pro_settings' );
		$enabled = isset( $options['enabled'] ) && 'yes' === $options['enabled'];

		if ( ! $enabled ) {
			return;
		}

		if ( ! function_exists( 'wc_get_base_location' ) ) {
			$message = sprintf(
				__( 'This extension requires the <a href="%1$s">WooCommerce plugin</a> to be installed and active.', 'woocommerce-gateway-paypal-pro' ),
				esc_url( 'https://wordpress.org/plugins/woocommerce/' )
			);
			$this->add_admin_notice( 'woocommerce-required', 'notice notice-warning', $message );
			return;
		}

		$enable_cruise   = isset( $options['enable_3dsecure_cruise'] ) && 'yes' === $options['enable_3dsecure_cruise'];
		$enable_centinel = isset( $options['enable_3dsecure'] ) && 'yes' === $options['enable_3dsecure'];
		$base_location   = wc_get_base_location();
		$base_country    = $base_location['country'];

		$cruise_messages = [];
		if ( 'GB' === $base_country && ! $enable_cruise ) {
			$cruise_messages[] = __( 'To avoid unnecessary declines due to <a href="%2$s">Strong Customer Authentication</a>, you should <a href="%1$s">enable 3D Secure 2</a>.', 'woocommerce-gateway-paypal-pro' );
		}
		if ( $enable_cruise && ( empty( $options['cruise_api_id'] ) || empty( $options['cruise_api_key'] ) || empty( $options['cruise_org_unit_id'] ) ) ) {
			$cruise_messages[] = __( 'The "3D Secure 2" setting was enabled but will behave as if disabled until <a href="%1$s">additional credentials are provided</a>.', 'woocommerce-gateway-paypal-pro' );
		}
		if ( $enable_centinel && ! $enable_cruise ) {
			$cruise_messages[] = __( 'To avoid disruption to the checkout flow due to the limitations of 3D Secure 1, we recommend <a href="%1$s">enabling 3D Secure 2</a>.', 'woocommerce-gateway-paypal-pro' );
		}

		if ( ! empty( $cruise_messages ) ) {
			$cruise_messages[] = __( '(Note that this requires <a href="%3$s">requesting an upgrade to Cardinal Cruise</a> for your Cardinal account.)', 'woocommerce-gateway-paypal-pro' );

			$message = sprintf(
				implode( '<br/>', $cruise_messages ),
				esc_url( $this->get_paypal_pro_setting_link() ),
				esc_url( 'https://woocommerce.com/posts/introducing-strong-customer-authentication-sca/' ),
				esc_url( 'https://paypal3dsregistration.cardinalcommerce.com/UI/registrationcontactpage.aspx' )
			);
			$this->add_admin_notice( 'cruise', 'notice notice-warning', $message );
		}
	}

	/**
	 * Get setting link.
	 *
	 * @since 4.5.0
	 *
	 * @return string Setting link
	 */
	public function get_paypal_pro_setting_link() {
		$use_id_as_section = function_exists( 'WC' ) ? version_compare( WC()->version, '2.6', '>=' ) : false;
		$section_slug = $use_id_as_section ? 'paypal_pro' : strtolower( 'WC_Gateway_PayPal_Pro' );
		return admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . $section_slug );
	}
}

new WC_PayPal_Pro_Admin_Notices();