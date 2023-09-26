<?php
/**
 * WC_CSP_Condition_Package_Recurring_Package class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.8.3
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Recurring frequency in Package Condition.
 *
 * @class    WC_CSP_Condition_Package_Recurring_Package
 * @version  1.15.0
 */
class WC_CSP_Condition_Package_Recurring_Package extends WC_CSP_Package_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                            = 'recurring_package';
		$this->title                         = __( 'Recurring Package', 'woocommerce-conditional-shipping-and-payments' );
		$this->priority                      = 40;
		$this->supported_global_restrictions = array( 'shipping_methods', 'shipping_countries' );
	}

	/**
	 * Return condition field-specific resolution message which is combined along with others into a single restriction "resolution message".
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restriction.
	 * @return string|false
	 */
	public function get_condition_resolution( $data, $args ) {

		if ( empty( $args[ 'package' ] ) || empty( $args[ 'package' ][ 'contents' ] ) ) {
			return false;
		}

		$message                    = false;
		$package_count              = $this->get_package_count( $args );
		$chosen_periods_placeholder = count( $data[ 'value' ] ) === 4 ? __( 'recurring', 'woocommerce-conditional-shipping-and-payments' ) : $this->merge_titles( $this->get_billing_period_adverb( $data[ 'value' ] ), array( 'rel' => 'or', 'quotes' => false ) );

		// Get the billing period if it's a recurring package.
		$billing_period = WC_CSP_Restriction::get_extra_package_variable( $args[ 'package' ], 'billing_period' );

		// Initial package.
		if ( ! $billing_period ) {

			if ( 'is' === $data[ 'modifier' ] ) {

				if ( 1 === $package_count ) {
					$message = sprintf( __( 'make sure that your cart does not contain any items shipped at %s intervals', 'woocommerce-conditional-shipping-and-payments' ), $chosen_periods_placeholder );
				} else {
					$message = sprintf( __( 'make sure it does not contain any items shipped at %s intervals', 'woocommerce-conditional-shipping-and-payments' ), $chosen_periods_placeholder );
				}

			} elseif ( 'is-not' === $data[ 'modifier' ] ) {

				if ( 1 === $package_count ) {
					$message = sprintf( __( 'make sure that your cart contains items shipped at %s intervals', 'woocommerce-conditional-shipping-and-payments' ), $chosen_periods_placeholder );
				} else {
					$message = sprintf( __( 'make sure it contains items shipped at %s intervals', 'woocommerce-conditional-shipping-and-payments' ), $chosen_periods_placeholder );
				}
			}

		// Recurring package.
		} else {
			$message = __( 'consider changing its shipping schedule', 'woocommerce-conditional-shipping-and-payments' );
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

		if ( empty( $args[ 'package' ] ) || empty( $args[ 'package' ][ 'contents' ] ) ) {
			return true;
		}

		$package_billing_period = WC_CSP_Restriction::get_extra_package_variable( $args[ 'package' ], 'billing_period' );

		// Current package is recurring.
		if ( $package_billing_period ) {

			if ( 'is' === $data[ 'modifier' ] ) {

				if ( in_array( $package_billing_period, $data[ 'value' ] ) ) {
					return true;
				}

			} elseif ( 'is-not' === $data[ 'modifier' ] ) {

				if ( ! in_array( $package_billing_period, $data[ 'value' ] ) ) {
					return true;
				}
			}

		// Manual renewal?
		} else {

			$contents = $args[ 'package' ][ 'contents' ];

			if ( empty( $contents ) ) {
				return false;
			}

			// Search for Renewal items.
			// Note: A Renewal item can't co-exist with a Subcription item in the same cart.
			$renewal = wcs_cart_contains_renewal();

			if ( $renewal ) {

				$matching_item        = false;
				$billing_period_match = false;

				// Fetch Subcription and renewal's billing period.
				$subscription_id = (int) $renewal[ 'subscription_renewal' ][ 'subscription_id' ];
				$subscription    = wcs_get_subscription( $subscription_id );
				$billing_period  = $subscription->get_billing_period();

				if ( in_array( $billing_period, $data[ 'value' ] ) ) {
					$billing_period_match = true;
				}

				foreach ( $contents as $cart_item_key => $cart_item ) {
					// Check for subscription renewal context.
					if ( isset( $cart_item[ 'subscription_renewal' ] ) ) {
						$matching_item = true;
					}
				}

				if ( 'is' === $data[ 'modifier' ] && $matching_item ) {
					return true;
				} elseif ( 'is-not' === $data[ 'modifier' ] && ! $matching_item ) {
					return true;
				}

			} else {
				return 'is-not' === $data[ 'modifier' ];
			}
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
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );
			$processed_condition_data[ 'value' ]        = array_map( 'wc_clean', $posted_condition_data[ 'value' ] );
		}

		return $processed_condition_data;
	}

	/**
	 * Returns a readable form of the subcription periods.
	 *
	 * @since  1.4.0
	 *
	 * @param  array|String  $periods  Periods to format.
	 * @return array|String
	 */
	private function get_billing_period_adverb( $periods ) {

		$return_array = true;

		// Transform type if String is passed.
		if ( ! is_array( $periods ) ) {

			$return_array = false;
			$periods      = array( $periods );
		}

		$mapper = array(
			'day'   => __( 'daily', 'woocommerce-conditional-shipping-and-payments' ),
			'week'  => __( 'weekly', 'woocommerce-conditional-shipping-and-payments' ),
			'month' => __( 'monthly', 'woocommerce-conditional-shipping-and-payments' ),
			'year'  => __( 'yearly', 'woocommerce-conditional-shipping-and-payments' )
		);

		$formatted = array();

		foreach ( $periods as $period ) {
			if ( isset( $mapper[ $period ] ) ) {
				$formatted[] = $mapper[ $period ];
			} else {
				$formatted[] = $period;
			}
		}

		return $return_array ? $formatted : end( $formatted );
	}

	/**
	 * Get backorders-in-cart condition content for global restrictions.
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

	$periods          = wcs_get_subscription_period_strings();
	$selected_periods = isset( $condition_data[ 'value' ] ) ? $condition_data[ 'value' ] : array();

	?>
	<input type="hidden" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][condition_id]" value="<?php echo esc_attr( $this->id ); ?>" />
	<div class="condition_row_inner">
		<div class="condition_modifier">
			<div class="sw-enhanced-select">
				<select name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][modifier]">
					<option value="is" <?php selected( $modifier, 'is', true ); ?>><?php esc_html_e( 'is', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
					<option value="is-not" <?php selected( $modifier, 'is-not', true ); ?>><?php esc_html_e( 'is not', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
				</select>
			</div>
		</div>
		<div class="condition_value">
			<select name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][value][]" class="multiselect sw-select2" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select billing period&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>">

				<?php
					foreach ( $periods as $value => $label ) {
						echo '<option value="' . esc_attr( $value ) . '" ' . selected( in_array( $value, $selected_periods ), true, false ).'>' . esc_html( $this->get_billing_period_adverb( $value ) ) . '</option>';
					}
				?>

			</select>
		</div>
	</div><?php
	}
}
