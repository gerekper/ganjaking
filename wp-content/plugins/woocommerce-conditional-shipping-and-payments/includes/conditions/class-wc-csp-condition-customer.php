<?php
/**
 * WC_CSP_Condition_Customer class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Customer Condition (e-mail).
 *
 * @class    WC_CSP_Condition_Customer
 * @version  1.4.0
 */
class WC_CSP_Condition_Customer extends WC_CSP_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                             = 'customer';
		$this->title                          = __( 'Customer', 'woocommerce-conditional-shipping-and-payments' );
		$this->supported_product_restrictions = array( 'shipping_countries', 'payment_gateways', 'shipping_methods' );
		$this->supported_global_restrictions  = array( 'shipping_countries', 'payment_gateways', 'shipping_methods' );
	}

	/**
	 * Return condition field-specific resolution message which is combined along with others into a single restriction "resolution message".
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restriction.
	 * @return string|false
	 */
	public function get_condition_resolution( $data, $args ) {

		// Empty conditions always return false (not evaluated).
		if ( empty( $data[ 'value' ] ) ) {
			return false;
		}

		$message = false;

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) ) {
			$message = __( 'use an authorized account', 'woocommerce-conditional-shipping-and-payments' );
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-in' ) ) ) {
			$message = __( 'use an authorized account', 'woocommerce-conditional-shipping-and-payments' );
		}

		return $message;
	}

	/**
	 * Evaluate if the condition is in effect or not.
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restrictions.
	 * @return boolean
	 */
	public function check_condition( $data, $args ) {

		// Empty conditions always apply (not evaluated).
		if ( empty( $data[ 'value' ] ) ) {
			return true;
		}

		$check_emails      = array();
		$restricted_emails = array_map( 'sanitize_email', $data[ 'value' ] );

		if ( is_user_logged_in() ) {
			$current_user   = wp_get_current_user();
			$check_emails[] = $current_user->user_email;
		}

		if ( ! empty( $args[ 'order' ] ) ) {

			$order         = $args[ 'order' ];
			$billing_email = WC_CSP_Core_Compatibility::is_wc_version_gte( '2.7' ) ? $order->get_billing_email() : $order->billing_email;

			if ( $billing_email ) {
				$check_emails[] = $billing_email;
			}

		} else {

			// Validating checkout fields?
			if ( ! empty( $_POST[ 'billing_email' ] ) ) {

				$check_emails[] = wc_clean( $_POST[ 'billing_email' ] );

			// Updating order review?
			} elseif ( did_action( 'woocommerce_checkout_update_order_review' ) && ! empty( $_POST[ 'post_data' ] ) ) {
				parse_str( $_POST[ 'post_data' ], $billing_data );
				if ( is_array( $billing_data ) && isset( $billing_data[ 'billing_email' ] ) ) {
					$check_emails[] = wc_clean( $billing_data[ 'billing_email' ] );
				}
			}

			$check_emails = array_map( 'sanitize_email', array_map( 'strtolower', $check_emails ) );
		}

		$identified_email = false;

		if ( ! empty( $check_emails ) ) {
			foreach ( $check_emails as $check_email ) {
				if ( in_array( $check_email, $restricted_emails ) ) {
					$identified_email = true;
					break;
				}
			}
		}

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) && $identified_email ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-in' ) ) && ! $identified_email ) {
			return true;
		}

		return false;
	}

	/**
	 * Validate, process and return condition fields.
	 *
	 * @param  array  $posted_condition_data
	 * @return array
	 */
	public function process_admin_fields( $posted_condition_data ) {

		$processed_condition_data = array();

		if ( ! empty( $posted_condition_data[ 'value' ] ) ) {
			$processed_condition_data[ 'condition_id' ] = $this->id;
			$processed_condition_data[ 'value' ]        = array_filter( array_map( 'trim', explode( ',', wc_clean( $posted_condition_data[ 'value' ] ) ) ) );
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

			return $processed_condition_data;
		}

		return false;
	}

	/**
	 * Get categories-in-cart condition content for global restrictions.
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$modifier = '';
		$emails   = '';

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		if ( ! empty( $condition_data[ 'value' ] ) ) {
			$emails = implode( ', ', $condition_data[ 'value' ] );
		}

		?>
		<input type="hidden" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][condition_id]" value="<?php echo $this->id; ?>" />
		<div class="condition_row_inner">
			<div class="condition_modifier">
				<div class="sw-enhanced-select">
					<select name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][modifier]">
						<option value="in" <?php selected( $modifier, 'in', true ) ?>><?php echo __( 'e-mail is', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="not-in" <?php selected( $modifier, 'not-in', true ) ?>><?php echo __( 'e-mail is not', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
					</select>
				</div>
			</div>
			<div class="condition_value">
				<textarea class="short" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][value]" placeholder="<?php _e( 'List of e-mails separated by comma.', 'woocommerce-conditional-shipping-and-payments' ); ?>"><?php echo esc_textarea( $emails ) ?></textarea>
			</div>
		</div><?php
	}
}
