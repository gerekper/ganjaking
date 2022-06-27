<?php
/**
 * WC_CSP_KLC_Compatibility class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Klarna Checkout Compatibility.
 *
 * @since  1.12.1
 */
class WC_CSP_KLC_Compatibility {

	public static function init() {

		// Restore WC checkout template if Klarna Checkout is disabled.
		add_filter( 'woocommerce_locate_template', array( __CLASS__, 'restore_template' ), 11, 3 );

		// Modify saved data.
		add_action( 'woocommerce_csp_process_admin_global_fields', array( __CLASS__, 'remove_billing_country_condition' ), 10, 3 );
	}

	/**
	 * Restore WC checkout template if Klarna Checkout is disabled and disabled gateways are not displayed.
	 *
	 * @param  string  $template
	 * @param  string  $template_name
	 * @param  string  $template_path
	 * @return string
	 */
	public static function restore_template( $template, $template_name, $template_path ) {

		if ( is_checkout() && 'checkout/form-checkout.php' === $template_name ) {

			// Overridden already = chosen?
			if ( strpos( $template, 'klarna-checkout.php' ) !== false ) {

				// Restricted?
				if ( WC_CSP_Compatibility::is_gateway_restricted( 'kco' ) ) {
					$kco_templates = Klarna_Checkout_For_WooCommerce_Templates::get_instance();
					remove_filter( 'woocommerce_locate_template', array( $kco_templates, 'override_template' ), 10 );
					$template = wc_locate_template( 'checkout/form-checkout.php' );
				}
			}
		}

		return $template;
	}

	/**
	 * Prevent Billing Country condition from saving when excluding Klarna Checkout.
	 * Force 'Show Excluded' to 'no'.
	 *
	 * @param  array   $processed_data
	 * @param  array   $posted_data
	 * @param  string  $restriction_id
	 * @return array
	 */
	public static function remove_billing_country_condition( $processed_data, $posted_data, $restriction_id ) {

		if ( 'payment_gateways' !== $restriction_id ) {
			return $processed_data;
		}

		$restriction = WC_CSP()->restrictions->get_restriction( 'payment_gateways' );
		$rules       = $restriction->get_global_restriction_data();

		$error_rules = array();

		if ( in_array( 'kco', $processed_data[ 'gateways' ] ) ) {

			// Force 'Show Excluded' to off.
			if ( isset( $processed_data[ 'show_excluded' ] ) && 'yes' === $processed_data[ 'show_excluded' ] ) {
				$processed_data[ 'show_excluded' ] = 'no';
				WC_Admin_Settings::add_error( sprintf( __( 'Show Excluded has been disabled in rule %s. The option is not supported by Klarna Checkout.', 'woocommerce-conditional-shipping-and-payments' ), '#' . ( $processed_data[ 'index' ] + 1 ) ) );
			}

			// Disallow Billing Country condition.
			if ( ! empty( $processed_data[ 'conditions' ] ) ) {
				foreach ( $processed_data[ 'conditions' ] as $condition_key => $condition_data ) {
					if ( 'billing_country' === $condition_data[ 'condition_id' ] ) {
						unset( $processed_data[ 'conditions' ][ $condition_key ] );
						$error_rules[] = '#' . ( $processed_data[ 'index' ] + 1 );
					}
				}
			}
		}

		if ( count( $error_rules ) > 0 ) {

			if ( count( $error_rules ) === 1 ) {

				WC_Admin_Settings::add_error( sprintf( __( 'Failed to save Billing Country condition in rule %s. The Billing Country condition is not supported by Klarna Checkout.', 'woocommerce-conditional-shipping-and-payments' ), $error_rules[ 0 ] ) );

			} else {

				$merged = sprintf( '%s', $error_rules[ 0 ] );

				for ( $i = 1; $i < count( $error_rules ) - 1; $i++ ) {
					$merged = sprintf( _x( '%1$s, %2$s', 'items in comma separated list', 'woocommerce-conditional-shipping-and-payments' ), $merged, $error_rules[ $i ] );
				}

				if ( count( $error_rules ) > 1 ) {
					$merged = sprintf( _x( '%1$s and %2$s', 'last item in comma separated list', 'woocommerce-conditional-shipping-and-payments' ), $merged, end( $error_rules ) );
				}

				WC_Admin_Settings::add_error( sprintf( __( 'Failed to save Billing Country condition in rules %s. The Billing Country condition is not supported by Klarna Checkout.', 'woocommerce-conditional-shipping-and-payments' ), $merged ) );
			}
		}

		return $processed_data;
	}
}

WC_CSP_KLC_Compatibility::init();
