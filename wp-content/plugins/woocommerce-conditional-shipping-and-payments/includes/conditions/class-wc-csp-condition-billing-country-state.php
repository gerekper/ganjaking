<?php
/**
 * WC_CSP_Condition_Billing_Country_State class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.8.6
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Selected Billing Country Condition.
 *
 * @class    WC_CSP_Condition_Billing_Country_State
 * @version  1.9.0
 */
class WC_CSP_Condition_Billing_Country_State extends WC_CSP_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                             = 'billing_country';
		$this->title                          = __( 'Billing Country/State', 'woocommerce-conditional-shipping-and-payments' );
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

		$country = WC_CSP_Core_Compatibility::is_wc_version_gte( '2.7' ) ? WC()->customer->get_billing_country() : WC()->customer->get_country();

		if ( empty( $data[ 'states' ] ) || empty( $data[ 'states' ][ $country ] ) ) {
			$mismatch_string = __( 'Country', 'woocommerce-conditional-shipping-and-payments' );
		} else {
			$locale          = WC()->countries->get_country_locale();
			$mismatch_string = isset( $locale[ $country ][ 'state' ][ 'label' ] ) ? $locale[ $country ][ 'state' ][ 'label' ] : __( 'State / County', 'woocommerce' );
		}

		$message = sprintf( __( 'choose a different billing %s', 'woocommerce-conditional-shipping-and-payments' ), $mismatch_string );

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

		$billing_country = WC_CSP_Core_Compatibility::is_wc_version_gte( '2.7' ) ? WC()->customer->get_billing_country() : WC()->customer->get_country();
		$billing_state   = WC_CSP_Core_Compatibility::is_wc_version_gte( '2.7' ) ? WC()->customer->get_billing_state() : WC()->customer->get_state();

		$is_matching      = false;
		$showing_excluded = ! empty( $args[ 'restriction_data' ][ 'show_excluded' ] ) && 'yes' === $args[ 'restriction_data' ][ 'show_excluded' ];

		if ( ! $showing_excluded && empty( $billing_state ) ) {
			$billing_state = apply_filters( 'woocommerce_csp_billing_country_condition_default_state', $billing_state, $data, $args );
		}

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) ) {

			if ( in_array( $billing_country, $data[ 'value' ] ) ) {

				// No states defined in condition?
				if ( empty( $data[ 'states' ] ) || empty( $data[ 'states' ][ $billing_country ] ) ) {

					$is_matching = true;

				} else {

					// No state selected?
					if ( empty( $billing_state ) ) {
						$is_matching = ! $showing_excluded && apply_filters( 'woocommerce_csp_billing_country_condition_match_empty_state', false, $data, $args );
					// State selected is in those defined in condition?
					} elseif ( in_array( $billing_state, $data[ 'states' ][ $billing_country ] ) ) {
						$is_matching = true;
					}
				}
			}

		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-in') ) ) {

			// Country defined in condition not selected?
			if ( ! in_array( $billing_country, $data[ 'value' ] ) ) {

				$is_matching = true;

			// States defined in condition?
			} elseif ( ! empty( $data[ 'states' ] ) && ! empty( $data[ 'states' ][ $billing_country ] ) ) {

				// No state selected?
				if ( empty( $billing_state ) ) {
					$is_matching = ! $showing_excluded && apply_filters( 'woocommerce_csp_billing_country_condition_match_empty_state', true, $data, $args );
				// State selected is not in those defined in condition?
				} elseif ( ! in_array( $billing_state, $data[ 'states' ][ $billing_country ] ) ) {
					$is_matching = true;
				}
			}
		}

		return $is_matching;
	}

	/**
	 * Validate, process and return condition field data.
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

			if ( ! empty( $posted_condition_data[ 'states' ] ) ) {

				$processed_condition_data[ 'states' ] = array();

				$country_states = array_map( 'stripslashes', $posted_condition_data[ 'states' ] );

				foreach ( $country_states as $country_state_key ) {
					$country_state_key = explode( ':', $country_state_key );
					$country_key       = current( $country_state_key );
					$state_key         = end( $country_state_key );

					if ( in_array( $country_key, $processed_condition_data[ 'value' ] ) ) {
						$processed_condition_data[ 'states' ][ $country_key ][] = $state_key;
					}
				}
			}

			return $processed_condition_data;
		}

		return false;
	}

	/**
	 * Get billing countries condition content for restriction metaboxes.
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$countries = array();
		$states    = array();
		$modifier  = '';

		if ( ! empty( $condition_data[ 'value' ] ) ) {
			$countries = $condition_data[ 'value' ];
		}

		if ( ! empty( $condition_data[ 'states' ] ) ) {
			$states = $condition_data[ 'states' ];
		}

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		$billing_countries = WC()->countries->get_allowed_countries();
		$billing_states    = WC()->countries->get_allowed_country_states();

		?>
		<input type="hidden" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][condition_id]" value="<?php echo $this->id; ?>" />
		<div class="condition_row_inner">
			<div class="condition_modifier">
				<div class="sw-enhanced-select">
					<select name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][modifier]">
						<option value="in" <?php selected( $modifier, 'in', true ) ?>><?php echo __( 'is', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="not-in" <?php selected( $modifier, 'not-in', true ) ?>><?php echo __( 'is not', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
					</select>
				</div>
			</div>
			<div class="condition_value select-field">
				<select class="csp_billing_countries multiselect sw-select2" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][value][]" multiple="multiple" data-placeholder="<?php _e( 'Select billing countries&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>">
					<?php
						foreach ( $billing_countries as $key => $val ) {
							echo '<option value="' . esc_attr( $key ) . '" ' . selected( in_array( $key, $countries ), true, false ).'>' . $val . '</option>';
						}
					?>
				</select>
				<div class="condition_form_row">
					<a class="wccsp_select_all button" href="#"><?php _e( 'All', 'woocommerce' ); ?></a>
					<a class="wccsp_select_none button" href="#"><?php _e( 'None', 'woocommerce' ); ?></a>
				</div>
			</div>
		</div>

		<div class="condition_row_inner">
			<div class="condition_modifier">
			</div>
			<div class="condition_value excluded_states select-field">
				<select class="csp_billing_states multiselect sw-select2" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][states][]" multiple="multiple" data-placeholder="<?php _e( 'Limit restriction to specific states or regions&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>">
					<?php
						if ( ! empty( $countries ) ) {
							foreach ( $countries as $country_key ) {

								if ( ! isset( $billing_countries[ $country_key ] ) ) {
									continue;
								}

								if ( empty( $billing_states[ $country_key ] ) ) {
									continue;
								}

								if ( $country_states = $billing_states[ $country_key ] ) {

									$country_value = $billing_countries[ $country_key ];

									echo '<optgroup label="' . esc_attr( $country_value ) . '">';
										foreach ( $country_states as $state_key => $state_value ) {
											echo '<option value="' . esc_attr( $country_key ) . ':' . $state_key . '"';
											if ( ! empty( $states[ $country_key ] ) && in_array( $state_key, $states[ $country_key ] ) ) {
												echo ' selected="selected"';
											}
											echo '>' . $country_value . ' &ndash; ' . $state_value . '</option>';
										}
									echo '</optgroup>';
								}
							}
						}
					?>
				</select>
				<div class="condition_form_row">
					<a class="wccsp_select_all button" href="#"><?php _e( 'All', 'woocommerce' ); ?></a>
					<a class="wccsp_select_none button" href="#"><?php _e( 'None', 'woocommerce' ); ?></a>
				</div>
			</div>
		</div><?php
	}
}
