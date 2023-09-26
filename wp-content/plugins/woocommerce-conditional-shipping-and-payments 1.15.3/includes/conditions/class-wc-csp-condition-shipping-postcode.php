<?php
/**
 * WC_CSP_Condition_Shipping_Postcode class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.3.0
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Zip Code Condition.
 *
 * @class    WC_CSP_Condition_Shipping_Postcode
 * @version  1.15.0
 */
class WC_CSP_Condition_Shipping_Postcode extends WC_CSP_Package_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                             = 'zip_code';
		$this->title                          = __( 'Shipping Postcode', 'woocommerce-conditional-shipping-and-payments' );
		$this->priority                       = 30;
		$this->supported_global_restrictions  = array( 'shipping_methods', 'payment_gateways', 'shipping_countries' );
		$this->supported_product_restrictions = array( 'shipping_methods', 'payment_gateways', 'shipping_countries' );
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

		return __( 'choose a valid shipping postcode', 'woocommerce-conditional-shipping-and-payments' );
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
		if ( empty( $data[ 'value' ] ) ) {
			return true;
		}

		$is_matching_package = false;

		if ( ! empty( $args[ 'order' ] ) ) {

			$order = $args[ 'order' ];

			$order_postcode      = WC_CSP_Core_Compatibility::is_wc_version_gte( '3.0' ) ? $order->get_shipping_postcode() : $order->shipping_postcode;
			$postcode            = WC_CSP_Core_Compatibility::wc_normalize_postcode( wc_clean( $order_postcode ) );
			$country             = WC_CSP_Core_Compatibility::is_wc_version_gte( '3.0' ) ? $order->get_shipping_country() : $order->shipping_country;
			$is_matching_package = $this->is_matching_package( $postcode, $country, $data );

		} elseif ( ! empty( $args[ 'package' ] ) ) {

			$package = $args[ 'package' ];

			if ( ! empty( $package[ 'destination' ][ 'postcode' ] ) ) {

				$postcode            = WC_CSP_Core_Compatibility::wc_normalize_postcode( wc_clean( $package[ 'destination' ][ 'postcode' ] ) );
				$is_matching_package = $this->is_matching_package( $postcode, $package[ 'destination' ][ 'country' ], $data );

			} else {

				$is_shipping_methods_restriction = ! empty( $args[ 'restriction_data' ][ 'restriction_id' ] ) && 'shipping_methods' === $args[ 'restriction_data' ][ 'restriction_id' ];
				$showing_excluded                = ! empty( $args[ 'restriction_data' ][ 'show_excluded' ] ) && 'yes' === $args[ 'restriction_data' ][ 'show_excluded' ];

				if ( $is_shipping_methods_restriction && 'yes' === get_option( 'woocommerce_shipping_cost_requires_address' ) ) {
					$is_matching_package = true;
				}

				if ( ! $is_matching_package ) {
					$is_matching_package = ! $showing_excluded && apply_filters( 'woocommerce_csp_shipping_postcode_condition_match_empty_postcode', $this->modifier_is( $data[ 'modifier' ], array( 'not-in' ) ), $data, $args );
				}
			}

		} else {

			$shipping_packages = $this->get_packages();

			if ( ! empty( $shipping_packages ) ) {
				foreach ( $shipping_packages as $shipping_package ) {

					$postcode = WC_CSP_Core_Compatibility::wc_normalize_postcode( wc_clean( $shipping_package[ 'destination' ][ 'postcode' ] ) );

					if ( empty( $postcode ) ) {
						$is_matching_package = apply_filters( 'woocommerce_csp_shipping_postcode_condition_match_empty_postcode', $this->modifier_is( $data[ 'modifier' ], array( 'not-in' ) ), $data, $args );
					} elseif ( $this->is_matching_package( $postcode, $shipping_package[ 'destination' ][ 'country' ], $data ) ) {
						$is_matching_package = true;
					}

					if ( $is_matching_package ) {
						break;
					}
				}
			}
		}

		return $is_matching_package;
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
	protected function is_matching_package( $postcode, $country, $data ) {

		$is_matching      = false;
		$postcode_objects = array();

		foreach ( $data[ 'value' ] as $validation_postcode ) {

			$postcode_object                = new stdClass();
			$postcode_object->location_code = trim( strtoupper( str_replace( chr( 226 ) . chr( 128 ) . chr( 166 ), '...', $validation_postcode ) ) );
			$postcode_object->value         = 0;
			$postcode_objects[]             = $postcode_object;
		}

		$matches = wc_postcode_location_matcher( $postcode, $postcode_objects, 'value', 'location_code', $country );

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) && ! empty( $matches ) ) {
			$is_matching = true;
		}

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'not-in' ) ) && empty( $matches ) ) {
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
	 * Get shipping postcode condition content for restriction metaboxes.
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$modifier  = '';
		$zip_codes = '';

		if ( ! empty( $condition_data[ 'value' ] ) && is_array( $condition_data[ 'value' ] ) ) {
			$zip_codes = implode( "\n", $condition_data[ 'value' ] );
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
				<textarea class="input-text" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][value]" placeholder="<?php esc_attr_e( 'List one postcode per line&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>" cols="25" rows="5"><?php echo esc_textarea( $zip_codes ); ?></textarea>
				<span class="description"><?php echo wp_kses_post( __( 'List one postcode per line. Postcodes containing wildcards (e.g. CB23*) and fully numeric ranges (e.g. <code>90210...99000</code>) are also supported.', 'woocommerce-conditional-shipping-and-payments' ) ); ?></span>
			</div>
		</div>
		<?php
	}
}
