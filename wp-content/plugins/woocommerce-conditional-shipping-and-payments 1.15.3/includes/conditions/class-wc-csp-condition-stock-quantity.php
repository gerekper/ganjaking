<?php
/**
 * WC_CSP_Condition_Stock_Quantity class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.9.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stock Quantity Condition.
 *
 * @class    WC_CSP_Condition_Stock_Quantity
 * @version  1.15.0
 */
class WC_CSP_Condition_Stock_Quantity extends WC_CSP_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                             = 'stock_quantity';
		$this->title                          = __( 'Stock Quantity', 'woocommerce-conditional-shipping-and-payments' );
		$this->priority                       = 20;
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
		if ( empty( $data[ 'value' ] ) && 0 !== $data[ 'value' ] ) {
			return false;
		}

		if ( empty( $args[ 'cart_item_data' ] ) ) {
			return false;
		}

		// No message to return. By default the message would be to remove the item from the cart.
		return false;
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
		if ( ! isset( $data[ 'value' ] ) ||
		     ( empty( $data[ 'value' ] ) && 0 != $data[ 'value' ] ) ) {
			return true;
		}

		$product = null;

		if ( ! empty( $args[ 'order' ] ) && ! empty( $args[ 'order_item_data' ] ) ) {
			$order      = $args[ 'order' ];
			$order_item = $args[ 'order_item_data' ];
			$product    = WC_CSP_Core_Compatibility::is_wc_version_gte( '4.4' ) ? $order_item->get_product() : $order->get_product_from_item( $order_item );
		} else if ( ! empty( $args[ 'cart_item_data' ] ) ) {
			$product = $args[ 'cart_item_data' ][ 'data' ];
		} else {
			return true;
		}

		// If for whatever reason, product is not a WC_Product
		// or it doesn't manage stock, do not evaluate the condition.
		if ( ! ( $product instanceof WC_Product ) ) {
			return false;
		}

		// If product does not manage stock we consider the stock to be infinite.
		// If the condition is gt/gte we return false and allow the request (condition stock > 5 -> inf > 5 -> allow).
		// If the condition is lt/lte we return true and block the request (condition stock < 5 -> inf < 5 -> block).
		if ( ! $product->managing_stock() ) {
			if ( $this->modifier_is( $data[ 'modifier' ], array( 'gt', 'gte' ) ) ) {
				return apply_filters( 'woocommerce_csp_stock_quantity_product_not_managing_stock', false, $data, $args );
			}

			return apply_filters( 'woocommerce_csp_stock_quantity_product_not_managing_stock', true, $data, $args );
		}

		$stock_quantity = $product->get_stock_quantity();

		if ( $data[ 'value' ] <= $stock_quantity && $this->modifier_is( $data[ 'modifier' ], 'gte' ) ) {
			return true;
		} elseif ( $data[ 'value' ] > $stock_quantity && $this->modifier_is( $data[ 'modifier' ], 'lt' ) ) {
			return true;
		} elseif ( $data[ 'value' ] >= $stock_quantity && $this->modifier_is( $data[ 'modifier' ], 'lte' ) ) {
			return true;
		} elseif ( $data[ 'value' ] < $stock_quantity && $this->modifier_is( $data[ 'modifier' ], 'gt' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Validate, process and return condition fields.
	 *
	 * @param  array  $posted_condition_data
	 * @return array|boolean
	 */
	public function process_admin_fields( $posted_condition_data ) {

		$processed_condition_data = array();

		// Zero values are valid values.
		if ( isset( $posted_condition_data[ 'value' ] ) &&
		     ( ! empty( $posted_condition_data[ 'value' ] ) || 0 == $posted_condition_data[ 'value' ] )
		) {
			$processed_condition_data[ 'condition_id' ] = $this->id;
			$processed_condition_data[ 'value' ]        = intval( stripslashes( $posted_condition_data[ 'value' ] ) ); // Zero values are valid.
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

			return $processed_condition_data;
		}

		return false;
	}

	/**
	 * Get quantity conditions content for admin product-level restriction metaboxes.
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 * @return void
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$modifier       = 'lt';
		$stock_quantity = '';

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		if ( isset( $condition_data[ 'value' ] )
			 && ( ! empty( $condition_data[ 'value' ] ) || 0 == $condition_data[ 'value' ] )
		) {
			$stock_quantity = $condition_data[ 'value' ];
		}

		?>
		<input type="hidden" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][condition_id]" value="<?php echo esc_attr( $this->id ); ?>"/>
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
				<input type="number" class="short qty" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][value]" value="<?php echo esc_attr( $stock_quantity ); ?>" placeholder="" step="any"/>
			</div>
		</div>
		<?php
	}
}
