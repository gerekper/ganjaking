<?php
/**
 * WC_CSP_Condition_Cart_Item_Quantity class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cart Item Quantity Condition.
 *
 * @class    WC_CSP_Condition_Cart_Item_Quantity
 * @version  1.15.0
 */
class WC_CSP_Condition_Cart_Item_Quantity extends WC_CSP_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                            = 'cart_item_quantity';
		$this->title                         = __( 'Item Quantity', 'woocommerce-conditional-shipping-and-payments' );
		$this->supported_product_restrictions = array( 'payment_gateways', 'shipping_methods', 'shipping_countries' );
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

		if ( empty( $args[ 'cart_item_data' ] ) ) {
			return false;
		}

		$message        = false;
		$cart_item_data = $args[ 'cart_item_data' ];

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'gte', 'min' ) ) ) {
			$message = sprintf( __( 'decrease the quantity of &quot;%1$s&quot; below %2$s', 'woocommerce-conditional-shipping-and-payments' ), $cart_item_data[ 'data' ]->get_title(), $data[ 'value' ] );
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'lt', 'max' ) ) ) {
			$message = sprintf( __( 'increase the quantity of &quot;%1$s&quot; to %2$s or higher', 'woocommerce-conditional-shipping-and-payments' ), $cart_item_data[ 'data' ]->get_title(), $data[ 'value' ] );
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'gt' ) ) ) {
			$message = sprintf( __( 'decrease the quantity of &quot;%1$s&quot; to %2$s or lower', 'woocommerce-conditional-shipping-and-payments' ), $cart_item_data[ 'data' ]->get_title(), $data[ 'value' ] );
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'lte' ) ) ) {
			$message = sprintf( __( 'increase the quantity of &quot;%1$s&quot; above %2$s', 'woocommerce-conditional-shipping-and-payments' ), $cart_item_data[ 'data' ]->get_title(), $data[ 'value' ] );
		}

		return $message;
	}

	/**
	 * Evaluate if the condition is in effect or not.
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restriction.
	 * @return boolean
	 */
	public function check_condition( $data, $args ) {

		// Empty conditions always apply (not evaluated).
		if ( ! isset( $data[ 'value' ] ) || $data[ 'value' ] === '' ) {
			return true;
		}

		if ( ! empty( $args[ 'order_item_data' ] ) ) {
			$order_item_data  = $args[ 'order_item_data' ];
			$product_quantity = $order_item_data[ 'qty' ];
		} else if ( ! empty( $args[ 'cart_item_data' ] ) ) {
			$cart_item_data   = $args[ 'cart_item_data' ];
			$product_quantity = $cart_item_data[ 'quantity' ];
		} else {
			return true;
		}

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'gte', 'min' ) ) && $data[ 'value' ] <= $product_quantity ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'lt', 'max' ) ) && $data[ 'value' ] > $product_quantity ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'lte' ) ) && $data[ 'value' ] >= $product_quantity ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'gt' ) ) && $data[ 'value' ] < $product_quantity ) {
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
			$processed_condition_data[ 'value' ]        = intval( stripslashes( $posted_condition_data[ 'value' ] ) );
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

			if ( $processed_condition_data[ 'value' ] > 0 ) {
				return $processed_condition_data;
			}
		}

		return false;
	}

	/**
	 * Get quantity conditions content for admin product-level restriction metaboxes.
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$modifier = 'lt';
		$quantity = '';

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];

			// Max/Min  Backwards compatibility
			if ( 'max' === $modifier ) {
				$modifier = 'lt';
			} elseif ( 'min' === $modifier ) {
				$modifier = 'gte';
			}

		}

		if ( ! empty( $condition_data[ 'value' ] ) ) {
			$quantity = $condition_data[ 'value' ];
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
				<input type="number" class="short qty" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][value]" value="<?php echo esc_attr( $quantity ); ?>" placeholder="" step="any" min="0"/>
			</div>
		</div>
		<?php
	}
}
