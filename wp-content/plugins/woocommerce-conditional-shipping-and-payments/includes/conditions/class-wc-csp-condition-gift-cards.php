<?php
/**
 * WC_CSP_Condition_Gift_Cards class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.8.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gift Cards Condition.
 *
 * @class    WC_CSP_Condition_Gift_Cards
 * @version  1.15.0
 */
class WC_CSP_Condition_Gift_Cards extends WC_CSP_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                             = 'gift_cards';
		$this->title                          = __( 'Gift Cards', 'woocommerce-conditional-shipping-and-payments' );
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

		$cart_contents = WC()->cart->get_cart();

		if ( empty( $cart_contents ) ) {
			return false;
		}

		$message = false;

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'used' ) ) ) {
			$message = __( 'remove all gift cards from your cart', 'woocommerce-conditional-shipping-and-payments' );
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-used' ) ) ) {
			$message = __( 'add some gift cards products to your cart', 'woocommerce-conditional-shipping-and-payments' );
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

		if ( ! empty( $args[ 'order' ] ) ) {
			$gift_cards_used = $args[ 'order' ]->get_items( 'gift_card' );
		} else {
			$gift_cards_used = WC_GC()->giftcards->get();
		}

		$check = false;

		if ( empty( $gift_cards_used ) && $data[ 'modifier' ] === 'not-used' ) {
			$check = true;
		} elseif ( ! empty( $gift_cards_used ) && $data[ 'modifier' ] === 'used' ) {
			$check = true;
		}

		return $check;
	}

	/**
	 * Validate, process and return condition fields.
	 *
	 * @param  array  $posted_condition_data
	 * @return array
	 */
	public function process_admin_fields( $posted_condition_data ) {

		$processed_condition_data                   = array();
		$processed_condition_data[ 'condition_id' ] = $this->id;
		$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

		return $processed_condition_data;
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

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		?>
		<input type="hidden" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][condition_id]" value="<?php echo esc_attr( $this->id ); ?>" />
		<div class="condition_row_inner">
			<div class="condition_modifier">
				<div class="sw-enhanced-select">
					<select name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][modifier]">
						<option value="used" <?php selected( $modifier, 'used', true ); ?>><?php esc_html_e( 'used', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="not-used" <?php selected( $modifier, 'not-used', true ); ?>><?php esc_html_e( 'not used', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
					</select>
				</div>
			</div>
			<div class="condition_value condition--disabled"></div>
		</div>
		<?php
	}
}
