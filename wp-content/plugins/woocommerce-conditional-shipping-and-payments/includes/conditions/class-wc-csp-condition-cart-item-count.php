<?php
/**
 * WC_CSP_Condition_Cart_Item_Count
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.10.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cart Item Count Condition.
 *
 * @class    WC_CSP_Condition_Cart_Item_Count
 * @version  1.15.0
 */
class WC_CSP_Condition_Cart_Item_Count extends WC_CSP_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                            = 'items_in_cart';
		$this->title                         = __( 'Cart Item Count', 'woocommerce-conditional-shipping-and-payments' );
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
		if ( empty( $data[ 'value' ] ) ) {
			return false;
		}

		$message         = false;
		$condition_value = absint( $data[ 'value' ] );

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'gt' ) ) ) {

			$message = sprintf( __( 'make sure that there are no more than %s items in your cart', 'woocommerce-conditional-shipping-and-payments' ), $condition_value );

		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'lt' ) ) ) {

			$message = sprintf( __( 'make sure that your cart contains at least %s items', 'woocommerce-conditional-shipping-and-payments' ), $condition_value );

		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'lte' ) ) ) {

			$message = sprintf( __( 'make sure that your cart contains more than %s items', 'woocommerce-conditional-shipping-and-payments' ), $condition_value );

		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'gte' ) ) ) {

			$message = sprintf( __( 'make sure that your cart contains less than %s items', 'woocommerce-conditional-shipping-and-payments' ), $condition_value );
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'eq' ) ) ) {

			if ( $condition_value === 1 ) {
				$message = sprintf( __( 'make sure that your cart contains more than %s items', 'woocommerce-conditional-shipping-and-payments' ), $condition_value );
			} else {
				$message = sprintf( __( 'make sure that your cart contains either more or fewer than %s items', 'woocommerce-conditional-shipping-and-payments' ), $condition_value );
			}
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
		if ( empty( $data[ 'value' ] ) ) {
			return true;
		}

		$total_quantity = WC()->cart->get_cart_contents_count();
		$limit          = absint( $data[ 'value' ] );
		$is_matching    = false;

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'gt' ) ) && $limit < $total_quantity ) {
			$is_matching = true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'lt' ) ) && $limit > $total_quantity ) {
			$is_matching = true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'gte' ) ) && $limit <= $total_quantity ) {
			$is_matching = true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'lte' ) ) && $limit >= $total_quantity ) {
			$is_matching = true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'eq' ) ) && $limit === $total_quantity ) {
			$is_matching = true;
		}

		return $is_matching;
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
			$processed_condition_data[ 'value' ]        = absint(  $posted_condition_data[ 'value' ] );
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

			if ( $processed_condition_data[ 'value' ] > 0 ) {
				return $processed_condition_data;
			}
		}

		return false;
	}

	/**
	 * Get quantity conditions content for admin global-level restriction metaboxes.
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$modifier = 'lt';
		$quantity = '';

		if ( ! empty( $condition_data[ 'value' ] ) ) {
			$quantity = absint( $condition_data[ 'value' ] );
		}

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		?>
		<input type="hidden" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][condition_id]" value="<?php echo esc_attr( $this->id ); ?>" />
		<div class="condition_row_inner">
			<div class="condition_modifier">
				<div class="sw-enhanced-select">
					<select name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][modifier]">
						<option value="lt" <?php selected( $modifier, 'lt', true ); ?>><?php esc_html_e( '<', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="lte" <?php selected( $modifier, 'lte', true ); ?>><?php esc_html_e( '<=', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="gte" <?php selected( $modifier, 'gte', true ); ?>><?php esc_html_e( '>=', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="gt" <?php selected( $modifier, 'gt', true ); ?>><?php esc_html_e( '>', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="eq" <?php selected( $modifier, 'eq', true ); ?>><?php esc_html_e( '=', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
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
