<?php
/**
 * WC_CSP_Condition_Cart_Item_Quantity class
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
 * Cart Item Quantity Condition.
 *
 * @class    WC_CSP_Condition_Cart_Item_Quantity
 * @version  1.4.0
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

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'min' ) ) ) {
			$message = sprintf( __( 'decrease the quantity of &quot;%1$s&quot; below %2$s', 'woocommerce-conditional-shipping-and-payments' ), $cart_item_data[ 'data' ]->get_title(), $data[ 'value' ] );
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'max' ) ) ) {
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

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'min' ) ) && $data[ 'value' ] <= $product_quantity ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'max' ) ) && $data[ 'value' ] > $product_quantity ) {
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

		$modifier = '';
		$quantity = '';

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		if ( ! empty( $condition_data[ 'value' ] ) ) {
			$quantity = $condition_data[ 'value' ];
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
				<input type="number" class="short qty" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][value]" value="<?php echo $quantity; ?>" placeholder="" step="any" min="0"/>
			</div>
		</div>
		<?php
	}
}
