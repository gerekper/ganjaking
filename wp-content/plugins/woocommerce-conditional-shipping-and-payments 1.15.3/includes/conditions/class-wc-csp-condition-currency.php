<?php
/**
 * WC_CSP_Condition_Currency
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Selected Currency Condition.
 *
 * @class    WC_CSP_Condition_Currency
 * @version  1.15.0
 */
class WC_CSP_Condition_Currency extends WC_CSP_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                             = 'currency';
		$this->title                          = __( 'Currency', 'woocommerce-conditional-shipping-and-payments' );
		$this->supported_product_restrictions = array( 'shipping_countries', 'payment_gateways', 'shipping_methods' );
		$this->supported_global_restrictions  = array( 'shipping_countries', 'payment_gateways', 'shipping_methods' );
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
		$active_currency = get_woocommerce_currency();

		// Add currency symbols.
		$currencies = array();
		foreach ( $data[ 'value' ] as $code ) {
			$currencies[] = $code . ' (' . get_woocommerce_currency_symbol( $code ) . ')';
		}

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) ) {

			$currencies_titles = $this->merge_titles( $currencies );

			if ( count( $currencies ) < 4 ) {
				$message = sprintf( __( 'choose a currency other than %s', 'woocommerce-conditional-shipping-and-payments' ), $currencies_titles );
			} else {
				$message = sprintf( __( 'choose a different currency', 'woocommerce-conditional-shipping-and-payments' ) );
			}

		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-in' ) ) ) {

			$currencies_titles = $this->merge_titles( $currencies, array( 'rel' => 'or' ) );

			if ( count( $currencies ) < 4 ) {
				$message = sprintf( __( 'checkout using %s', 'woocommerce-conditional-shipping-and-payments' ), $currencies_titles );
			} else {
				$message = sprintf( __( 'choose a different currency', 'woocommerce-conditional-shipping-and-payments' ) );
			}
		}

		return $message;
	}

	/**
	 * Evaluate if a condition field is in effect or not.
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restrictions.
	 * @return boolean
	 */
	public function check_condition( $data, $args ) {

		// Empty conditions always apply (not evaluated).
		if ( empty( $data[ 'value' ] ) ) {
			return true;
		}

		if ( ! empty( $args[ 'order' ] ) ) {

			$order           = $args[ 'order' ];
			$active_currency = $order->get_currency();

		} else {

			$active_currency = get_woocommerce_currency();
		}

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) && in_array( $active_currency, $data[ 'value' ] ) ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-in' ) ) && ! in_array( $active_currency, $data[ 'value' ] ) ) {
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
	 * Get currency condition content for product-level restriction metaboxes.
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$currency = array();
		$modifier = '';

		if ( ! empty( $condition_data[ 'value' ] ) ) {
			$currency = $condition_data[ 'value' ];
		}

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		// Get all currency names.
		$currencies = get_woocommerce_currencies();

		// Add currency symbols.
		foreach ( $currencies as $code => $name ) {
			$currencies[ $code ] = $name . ' (' . get_woocommerce_currency_symbol( $code ) . ')';
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
			<div class="condition_value select-field">
				<select class="csp_currencies multiselect sw-select2" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][value][]" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select currency&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>">
					<?php
						foreach ( $currencies as $key => $val ) {
							echo '<option value="' . esc_attr( $key ) . '" ' . selected( in_array( $key, $currency ), true, false ).'>' . esc_html( $val ) . '</option>';
						}
					?>
				</select>
				<div class="condition_form_row">
					<a class="wccsp_select_all button" href="#"><?php esc_html_e( 'All', 'woocommerce' ); ?></a>
					<a class="wccsp_select_none button" href="#"><?php esc_html_e( 'None', 'woocommerce' ); ?></a>
				</div>
			</div>
		</div><?php
	}
}
