<?php
/**
 * WC_CSP_Condition_Cart_Subtotal class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.8.9
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cart Subtotal Condition.
 *
 * @class    WC_CSP_Condition_Cart_Subtotal
 * @version  1.15.0
 */
class WC_CSP_Condition_Cart_Subtotal extends WC_CSP_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                             = 'cart_subtotal';
		$this->title                          = __( 'Cart Subtotal', 'woocommerce-conditional-shipping-and-payments' );
		$this->supported_global_restrictions  = array( 'shipping_methods', 'shipping_countries' );
		$this->supported_product_restrictions = array( 'shipping_methods', 'shipping_countries' );
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

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'gte', 'min' ) ) ) {
			$message = sprintf( __( 'decrease your cart subtotal below %s', 'woocommerce-conditional-shipping-and-payments' ), wc_price( $data[ 'value' ] ) );
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'lt', 'max' ) ) ) {
			$message = sprintf( __( 'increase your cart subtotal above %s', 'woocommerce-conditional-shipping-and-payments' ), wc_price( $data[ 'value' ] ) );
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'gt' ) ) ) {
			$message = sprintf( __( 'decrease your cart subtotal to %s or lower', 'woocommerce-conditional-shipping-and-payments' ), wc_price( $data[ 'value' ] ) );
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'lte' ) ) ) {
			$message = sprintf( __( 'increase your cart subtotal above %s', 'woocommerce-conditional-shipping-and-payments' ), wc_price( $data[ 'value' ] ) );
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

		$cart_subtotal     = WC_CSP_Core_Compatibility::is_wc_version_gte( '3.2' ) ? WC()->cart->get_subtotal() : WC()->cart->subtotal_ex_tax;
		$cart_subtotal_tax = WC_CSP_Core_Compatibility::is_wc_version_gte( '3.2' ) ? WC()->cart->get_subtotal_tax() : WC()->cart->subtotal - WC()->cart->subtotal_ex_tax ;

		$cart_subtotal_tax = apply_filters( 'woocommerce_csp_cart_subtotal_condition_incl_tax', 'incl' === get_option( 'woocommerce_tax_display_cart' ), $data, $args ) ? $cart_subtotal_tax : 0.0;
		$cart_subtotal     += $cart_subtotal_tax;

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'gte', 'min' ) ) && wc_format_decimal( $data[ 'value' ] ) <= $cart_subtotal ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'lt', 'max' ) ) && wc_format_decimal( $data[ 'value' ] ) > $cart_subtotal ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'lte' ) ) && wc_format_decimal( $data[ 'value' ] ) >= $cart_subtotal ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'gt' ) ) && wc_format_decimal( $data[ 'value' ] ) < $cart_subtotal ) {
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
	 * Get cart total conditions content for admin restriction metaboxes.
	 *
	 * @param  int    $index
	 * @param  int    $condition_ndex
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$modifier   = 'lt';
		$cart_total = '';
		$suffix     = get_woocommerce_currency_symbol();

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];

			// Max/Min  Backwards compatibility
			if ( 'max' === $modifier ) {
				$modifier = 'lt';
			} elseif ( 'min' === $modifier ) {
				$modifier = 'gte';
			}

		}

		if ( isset( $condition_data[ 'value' ] ) ) {
			$cart_total = wc_format_localized_price( $condition_data[ 'value' ] );
		}

		if ( 'incl' === get_option( 'woocommerce_tax_display_cart' ) ) {
			$suffix .= __( ' (incl. tax)', 'woocommerce-conditional-shipping-and-payments' );
		} else {
			$suffix .= __( ' (excl. tax)', 'woocommerce-conditional-shipping-and-payments' );
		}

		?>
		<input type="hidden" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][condition_id]" value="<?php echo esc_attr( $this->id ); ?>" />
		<div class="condition_row_inner">
			<div class="condition_modifier">
				<div class="sw-enhanced-select">
					<select name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][modifier]">
						<option value="lt" <?php selected( $modifier, 'lt', true ); ?>><?php esc_html_e( '<', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="lte" <?php selected( $modifier, 'lte', true ); ?>><?php esc_html_e( '<=', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="gt" <?php selected( $modifier, 'gt', true ); ?>><?php esc_html_e( '>', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="gte" <?php selected( $modifier, 'gte', true ); ?>><?php esc_html_e( '>=', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
					</select>
				</div>
			</div>
			<div class="condition_value">
				<input type="text" class="wc_input_price short" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][value]" value="<?php echo esc_attr( $cart_total ); ?>" placeholder="" step="any" min="0"/>
				<span class="condition_value--suffix tips" data-tip="<?php esc_attr_e( sprintf( __( 'Calculated %s tax, for consistency with how WooCommerce is currently configured to display prices in the Cart and Checkout pages. Can be controlled under <strong>WooCommerce > Settings > Tax > Display Prices During Cart and Checkout</strong>.', 'woocommerce-conditional-shipping-and-payments' ), 'incl' === get_option( 'woocommerce_tax_display_cart' ) ? 'including' : 'excluding' ) ); ?>">
					<?php echo esc_html( $suffix ); ?>
				</span>
			</div>
		</div>
		<?php
	}
}
