<?php
/**
 * WC_CSP_Condition_Order_Status class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.9.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Order Status Condition.
 *
 * @class    WC_CSP_Condition_Order_Status
 * @version  1.15.0
 */
class WC_CSP_Condition_Order_Status extends WC_CSP_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                            = 'order_status';
		$this->title                         = __( 'Order Status', 'woocommerce-conditional-shipping-and-payments' );
		$this->supported_global_restrictions = array( 'payment_gateways' );
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
		if ( ! isset( $data[ 'value' ] ) || '' === $data[ 'value' ] ) {
			return false;
		}

		$message = false;

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'in', 'not-in' ) ) ) {

			/**
			 * Filter the condition resolution message.
			 *
			 * @since  1.9.0
			 *
			 * @param  string  $message
			 * @param  array   $data
			 * @param  array   $args
			 */

			$message = apply_filters(
				'woocommerce_csp_order_status_condition_resolution_message',
				__( 'please contact the site administrator', 'woocommerce-conditional-shipping-and-payments' ),
				$data,
				$args
			);

		}

		return $message;
	}

	/**
	 * Evaluate if the condition is in effect or not.
	 *
	 * @param  array  $data   condition field data
	 * @param  array  $args   optional arguments passed by restrictions
	 * @return boolean
	 */
	public function check_condition( $data, $args ) {

		// Empty conditions always apply (not evaluated).
		if ( empty( $data[ 'value' ] ) ) {
			return true;
		}

		/* @var WC_Order $order */
		$order = null;

		// Set the order status to default pending and set the default status to
		// pending, in case we don't get a status from the order of the session
		$order_status = 'wc-pending';

		if ( isset( $args[ 'order' ] ) ) {
			// checkout/order-pay endpoint

			$order        = $args[ 'order' ];
			$order_status = 'wc-' . $order->get_status();
		} else {
			// If there is no args['order'] let's try and get the order from the session
			// WC Subs, is also using `order_awaiting_payment` to get the ID of the related order

			if ( isset( WC()->session ) && WC()->session->has_session() ) {
				$order_id  = absint( WC()->session->get( 'order_awaiting_payment' ) );
				$cart_hash = WC()->cart->get_cart_hash();
				$order     = $order_id ? wc_get_order( $order_id ) : null;

				if ( $order && $order->has_cart_hash( $cart_hash ) ) {
					$order_status = 'wc-' . $order->get_status();
				}
			}

		}

		$has_qualifying_status = false;
		if ( in_array( $order_status, $data[ 'value' ], true ) ) {
			$has_qualifying_status = true;
		}

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) && $has_qualifying_status ) {
			return true;
		}

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'not-in' ) ) && ! $has_qualifying_status ) {
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
			$processed_condition_data[ 'value' ]        = array_map( 'stripslashes', $posted_condition_data[ 'value' ] );
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

			return $processed_condition_data;
		}

		return false;
	}

	/**
	 * Get order statuses condition content for global restrictions.
	 * Modifiers: 'in', 'not-in'
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$modifier           = '';
		$condition_statuses = array();

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		if ( ! empty( $condition_data[ 'value' ] ) ) {
			$condition_statuses = $condition_data[ 'value' ];
		}

		$all_statuses = wc_get_order_statuses();
		?>

		<input type="hidden" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][condition_id]" value="<?php echo esc_attr( $this->id ); ?>"/>
		<div class="condition_row_inner">
			<div class="condition_modifier">
				<div class="sw-enhanced-select">
					<select name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][modifier]">
						<option value="in" <?php selected( $modifier, 'in', true ); ?>><?php esc_html_e( 'in', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="not-in" <?php selected( $modifier, 'not-in', true ); ?>><?php esc_html_e( 'not in', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
					</select>
				</div>
			</div>
			<div class="condition_value">
				<select name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][value][]" class="multiselect sw-select2" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select statuses&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>">
					<?php
					foreach ( $all_statuses as $status => $label ) {
						echo '<option value="' . esc_attr( $status ) . '" ' . selected( in_array( $status, $condition_statuses, true ), true, false ) . '>' . esc_html( $label ) . '</option>';
					}
					?>
				</select>
				<span class="description"><?php echo wp_kses_post( __( 'Condition applies only in <code>order-pay</code> endpoint and checkout.', 'woocommerce-conditional-shipping-and-payments' ) ); ?></span>
			</div>
		</div>
		<?php
	}
}
