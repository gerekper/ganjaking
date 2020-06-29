<?php
/**
 * WC_CSP_Condition_Order_Total class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Order Total Condition.
 *
 * @class    WC_CSP_Condition_Order_Total
 * @version  1.5.6
 */
class WC_CSP_Condition_Order_Total extends WC_CSP_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                             = 'order_total';
		$this->title                          = __( 'Order Total', 'woocommerce-conditional-shipping-and-payments' );
		$this->supported_global_restrictions  = array( 'payment_gateways' );
		$this->supported_product_restrictions = array( 'payment_gateways' );
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
		if ( ! isset( $data[ 'value' ] ) || $data[ 'value' ] === '' ) {
			return false;
		}

		$message = false;

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'min' ) ) ) {
			$message = sprintf( __( 'decrease your order total below %s', 'woocommerce-conditional-shipping-and-payments' ), wc_price( $data[ 'value' ] ) );
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'max' ) ) ) {
			$message = sprintf( __( 'increase your order total above %s', 'woocommerce-conditional-shipping-and-payments' ), wc_price( $data[ 'value' ] ) );
		}

		return $message;
	}

	/**
	 * Evaluate if the condition is in effect or not.
	 *
	 * @param  string $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restrictions.
	 * @return boolean
	 */
	public function check_condition( $data, $args ) {

		// Empty conditions always apply (not evaluated).
		if ( ! isset( $data[ 'value' ] ) || $data[ 'value' ] === '' ) {
			return true;
		}

		if ( WC_CSP_Core_Compatibility::is_wc_version_gte( '3.2' ) ) {

			$include_taxes = apply_filters( 'woocommerce_csp_order_total_condition_incl_contents_tax', true, $data, $args );

			if ( ! apply_filters( 'woocommerce_csp_order_total_contents_only', false, $data, $args ) ) {

				if ( ! empty( $args[ 'order' ] ) ) {

					$order_total_amount = $args[ 'order' ]->get_total();
					$order_total_tax    = $args[ 'order' ]->get_total_tax();

				} else {

					$order_total_amount = WC()->cart->get_total( 'edit' );
					$order_total_tax    = WC()->cart->get_total_tax();
				}

				$order_total = $include_taxes ? $order_total_amount : $order_total_amount - $order_total_tax;

			} else {

				if ( ! empty( $args[ 'order' ] ) ) {

					$order_contents_total = $args[ 'order' ]->get_subtotal() - $args[ 'order' ]->get_discount_total();
					$order_contents_tax   = $args[ 'order' ]->get_cart_tax();

				} else {

					$order_contents_total = WC()->cart->get_cart_contents_total();
					$order_contents_taxes = WC()->cart->get_cart_contents_taxes();

					$order_contents_tax   = array_sum( $order_contents_taxes );
				}

				$order_total = $include_taxes ? $order_contents_total + $order_contents_tax : $order_contents_total;
			}

		} else {

			if ( ! empty( $args[ 'order' ] ) ) {
				$order_total = $args[ 'order' ]->get_total();
			} else {
				$order_total = WC()->cart->total;
			}
		}

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'min' ) ) && wc_format_decimal( $data[ 'value' ] ) <= $order_total ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'max' ) ) && wc_format_decimal( $data[ 'value' ] ) > $order_total ) {
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

		if ( isset( $posted_condition_data[ 'value' ] ) ) {
			$processed_condition_data[ 'condition_id' ] = $this->id;
			$processed_condition_data[ 'value' ]        = $posted_condition_data[ 'value' ] !== '0' ? wc_format_decimal( stripslashes( $posted_condition_data[ 'value' ] ), '' ) : 0;
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

			if ( $processed_condition_data[ 'value' ] > 0 || $processed_condition_data[ 'value' ] === 0 ) {
				return $processed_condition_data;
			}
		}

		return false;
	}

	/**
	 * Get order total conditions content for admin restriction metaboxes.
	 *
	 * @param  int    $index
	 * @param  int    $condition_ndex
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$modifier    = '';
		$order_total = '';

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		} else {
			$modifier = 'max';
		}

		if ( isset( $condition_data[ 'value' ] ) ) {
			$order_total = wc_format_localized_price( $condition_data[ 'value' ] );
		}

		?>
		<input type="hidden" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][condition_id]" value="<?php echo $this->id; ?>" />
		<div class="condition_row_inner">
			<div class="condition_modifier">
				<div class="sw-enhanced-select">
					<select name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][modifier]">
						<option value="max" <?php selected( $modifier, 'max', true ) ?>><?php echo __( '<', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="min" <?php selected( $modifier, 'min', true ) ?>><?php echo __( '>=', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
					</select>
				</div>
			</div>
			<div class="condition_value">
				<input type="text" class="wc_input_price short" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][value]" value="<?php echo $order_total; ?>" placeholder="" step="any" min="0"/>
				<span class="condition_value--suffix">
					<?php echo get_woocommerce_currency_symbol() ?>
				</span>
			</div>
		</div>
		<?php
	}
}
