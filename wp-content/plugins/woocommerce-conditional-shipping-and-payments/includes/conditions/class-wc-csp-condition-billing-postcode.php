<?php
/**
 * WC_CSP_Condition_Billing_Postcode class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.9.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Billing Postcode Condition.
 *
 * @class    WC_CSP_Condition_Billing_Postcode
 * @version  1.15.0
 */
class WC_CSP_Condition_Billing_Postcode extends WC_CSP_Package_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                             = 'billing_postcode';
		$this->title                          = __( 'Billing Postcode', 'woocommerce-conditional-shipping-and-payments' );
		$this->priority                       = 30;
		$this->supported_global_restrictions  = array( 'shipping_methods', 'payment_gateways' );
		$this->supported_product_restrictions = array( 'shipping_methods', 'payment_gateways' );
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

		return __( 'choose a valid billing postcode', 'woocommerce-conditional-shipping-and-payments' );
	}

	/**
	 * Evaluate if the condition is in effect or not.
	 *
	 * @param  array $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restrictions.
	 * @return boolean
	 */
	public function check_condition( $data, $args ) {

		// Empty conditions always apply (not evaluated).
		if ( empty( $data[ 'value' ] ) ) {
			return true;
		}

		$is_matching_postcode = false;

		if ( ! empty( $args[ 'order' ] ) ) {

			$order = $args[ 'order' ];

			$order_postcode       = WC_CSP_Core_Compatibility::is_wc_version_gte( '3.0' ) ? $order->get_billing_postcode() : $order->billing_postcode;
			$postcode             = WC_CSP_Core_Compatibility::wc_normalize_postcode( wc_clean( $order_postcode ) );
			$country              = WC_CSP_Core_Compatibility::is_wc_version_gte( '3.0' ) ? $order->get_billing_country() : $order->billing_country;
			$is_matching_postcode = $this->is_matching_postcode( $postcode, $country, $data );

		} else {

			$customer_postcode = WC_CSP_Core_Compatibility::is_wc_version_gte( '2.7' ) ? WC()->customer->get_billing_postcode() : WC()->customer->get_postcode();
			$postcode          = WC_CSP_Core_Compatibility::wc_normalize_postcode( wc_clean( $customer_postcode ) );
			$country           = WC_CSP_Core_Compatibility::is_wc_version_gte( '2.7' ) ? WC()->customer->get_billing_country() : WC()->customer->get_country();

			if ( empty( $postcode ) ) {
				$is_matching_postcode = apply_filters( 'woocommerce_csp_billing_postcode_condition_match_empty_postcode', $this->modifier_is( $data[ 'modifier' ], array( 'not-in' ) ), $data, $args );
			} elseif ( $this->is_matching_postcode( $postcode, $country, $data ) ) {
				$is_matching_postcode = true;
			}

		}

		return $is_matching_postcode;
	}

	/**
	 * Condition matching package?
	 *
	 * @since  1.4.0
	 *
	 * @param  string $postcode
	 * @param  string $country
	 * @param  array  $data
	 * @return boolean
	 */
	protected function is_matching_postcode( $postcode, $country, $data ) {

		$is_matching      = false;
		$postcode_objects = array();

		foreach ( $data[ 'value' ] as $validation_postcode ) {
			$postcode_object                = new stdClass();
			$postcode_object->location_code = trim( strtoupper( str_replace( chr( 226 ) . chr( 128 ) . chr( 166 ), '...', $validation_postcode ) ) );
			$postcode_object->value         = 0;
			$postcode_objects[]             = $postcode_object;
		}

		$matches = wc_postcode_location_matcher( $postcode, $postcode_objects, 'value', 'location_code', $country );

		if ( ! empty( $matches ) && $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) ) {
			$is_matching = true;
		}

		if ( empty( $matches ) && $this->modifier_is( $data[ 'modifier' ], array( 'not-in' ) ) ) {
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
			$processed_condition_data[ 'value' ]        = array_filter( array_map( 'strtoupper', array_map( 'wc_clean', explode( "\n", $posted_condition_data[ 'value' ] ) ) ) );
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

			return $processed_condition_data;
		}

		return false;
	}
	/**
	 * Get billing postcode condition content for restriction metaboxes.
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$modifier  = '';
		$postcodes = '';

		if ( ! empty( $condition_data[ 'value' ] ) && is_array( $condition_data[ 'value' ] ) ) {
			$postcodes = implode( "\n", $condition_data[ 'value' ] );
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
						<option value="in" <?php selected( $modifier, 'in', true ); ?>><?php esc_html_e( 'is', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="not-in" <?php selected( $modifier, 'not-in', true ); ?>><?php esc_html_e( 'is not', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
					</select>
				</div>
			</div>
			<div class="condition_value">
				<textarea class="input-text" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][value]" placeholder="<?php esc_attr_e( 'List one postcode per line&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>" cols="25" rows="5"><?php echo esc_textarea( $postcodes ); ?></textarea>
				<span class="description"><?php echo wp_kses_post( __( 'List one postcode per line. Postcodes containing wildcards (e.g. CB23*) and fully numeric ranges (e.g. <code>90210...99000</code>) are also supported.', 'woocommerce-conditional-shipping-and-payments' ) ); ?></span>
			</div>
		</div>
		<?php
	}
}
