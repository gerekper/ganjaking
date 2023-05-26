<?php
/**
 * WC_CSP_Condition_Shipping_Method class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Selected Shipping Method Condition.
 *
 * @class    WC_CSP_Condition_Shipping_Method
 * @version  1.15.0
 */
class WC_CSP_Condition_Shipping_Method extends WC_CSP_Package_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                             = 'shipping_method';
		$this->title                          = __( 'Shipping Method', 'woocommerce-conditional-shipping-and-payments' );
		$this->supported_global_restrictions  = array( 'payment_gateways' );
		$this->supported_product_restrictions = array( 'payment_gateways' );
	}

	/**
	 * True if a rate defined in the condition is selected.
	 *
	 * @param  string                  $selected_rate_id
	 * @param  array                   $rate_ids
	 * @param  WC_Shipping_Rate|false  $rate
	 * @return boolean
	 */
	private function is_selected( $selected_rate_id, $rate_ids, $selected_rate = false ) {

		$is_selected                = false;
		$legacy_flat_rate_method_id = 'legacy_flat_rate';

		foreach ( $rate_ids as $rate_id ) {

			if ( (string) $selected_rate_id === (string) $rate_id ) {

				$is_selected = true;
				break;

			} elseif ( $rate_id !== $legacy_flat_rate_method_id && 0 === strpos( $selected_rate_id, $rate_id ) && in_array( substr( $selected_rate_id, strlen( $rate_id ), 1 ), array( ':', '-' ) ) ) {

				$is_selected = true;
				break;

			} elseif ( is_object( $selected_rate ) && WC_CSP_Core_Compatibility::is_wc_version_gte( '3.2' ) ) {

				$method_id   = $selected_rate->get_method_id();
				$instance_id = $selected_rate->get_instance_id();

				// When a rate is mapped to a known method ID and instance ID (attached to specific Shipping Zones), attempt to construct & evaluate its canonical rate ID.
				if ( $method_id && $instance_id ) {

					$canonical_rate_id = $method_id . ':' . $instance_id;

					if ( self::is_selected( $canonical_rate_id, $rate_ids ) ) {
						$is_selected = true;
						break;
					}
				}
			}
		}

		return $is_selected;
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

		$message        = false;
		$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
		$packages       = $this->get_packages();
		$package_count  = count( $packages );
		$bad_packages   = array();
		$package_index  = 0;

		foreach ( $chosen_methods as $package_key => $chosen_rate_id ) {

			$package_index++;

			$chosen_rate = ! empty( $packages ) && ! empty( $packages[ $package_key ][ 'rates' ][ $chosen_rate_id ] ) ? $packages[ $package_key ][ 'rates' ][ $chosen_rate_id ] : false;

			if ( ! $chosen_rate ) {
				continue;
			}

			if ( $this->is_selected( $chosen_rate_id, $data[ 'value' ], $chosen_rate ) && $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) ) {

				$bad_packages[] = $package_index;

				if ( 1 === $package_count ) {
					$message = __( 'select a different shipping method', 'woocommerce-conditional-shipping-and-payments' );
				} else {
					$message = sprintf( __( 'choose a different shipping method for package #%s', 'woocommerce-conditional-shipping-and-payments' ), $package_index );
				}

			} elseif ( ! $this->is_selected( $chosen_rate_id, $data[ 'value' ], $chosen_rate ) && $this->modifier_is( $data[ 'modifier' ], array( 'not-in' ) ) ) {

				$bad_packages[] = $package_index;
			}
		}

		if ( 1 === $package_count ) {

			$message = __( 'choose a different shipping method', 'woocommerce-conditional-shipping-and-payments' );

		} else {

			if ( 1 === count( $bad_packages ) ) {
				$message = sprintf( __( 'choose a different shipping method for package #%s', 'woocommerce-conditional-shipping-and-payments' ), $bad_packages[ 0 ] );
			} else {
				$message = sprintf( __( 'choose a different shipping method for packages %s', 'woocommerce-conditional-shipping-and-payments' ), $this->merge_titles( $bad_packages, array( 'prefix' => '#', 'quotes' => false ) ) );
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

			$chosen_methods    = array();
			$shipping_packages = array();

			$order_shipping_methods = $args[ 'order' ]->get_shipping_methods();

			if ( ! empty( $order_shipping_methods ) ) {
				foreach ( $order_shipping_methods as $order_shipping_method ) {
					$chosen_methods[] = $order_shipping_method[ 'method_id' ];
				}
			}

		} else {

			$chosen_methods    = WC()->session->get( 'chosen_shipping_methods' );
			$shipping_packages = $this->get_packages();
		}

		$rates_in_condition = $data[ 'value' ];

		if ( ! empty( $chosen_methods ) ) {
			foreach ( $chosen_methods as $package_key => $chosen_rate_id ) {

				$chosen_rate = ! empty( $shipping_packages ) && ! empty( $shipping_packages[ $package_key ][ 'rates' ][ $chosen_rate_id ] ) ? $shipping_packages[ $package_key ][ 'rates' ][ $chosen_rate_id ] : false;

				if ( $this->is_selected( $chosen_rate_id, $rates_in_condition, $chosen_rate ) && $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) ) {
					return true;
				} elseif ( ! $this->is_selected( $chosen_rate_id, $rates_in_condition, $chosen_rate ) && $this->modifier_is( $data[ 'modifier' ], array( 'not-in' ) ) ) {
					return true;
				}
			}
		}

		return false;
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

			return $processed_condition_data;
		}

		return false;
	}

	/**
	 * Get shipping methods condition content for admin product restriction metaboxes.
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$modifier = '';
		$methods  = array();

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		if ( ! empty( $condition_data[ 'value' ] ) ) {
			$methods = $condition_data[ 'value' ];
		}

		$shipping_methods = WC_CSP_Helpers::cache_get( 'wc_shipping_methods' );

		if ( is_null( $shipping_methods ) ) {
			$shipping_methods = WC()->shipping->load_shipping_methods();
			WC_CSP_Helpers::cache_set( 'wc_shipping_methods', $shipping_methods );
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
				<select name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][value][]" class="multiselect sw-select2" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select shipping methods&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>">
					<?php
						foreach ( $shipping_methods as $key => $val ) {
							do_action( 'woocommerce_csp_admin_shipping_method_option', $key, $val, $methods );
						}
					?>
				</select>
			</div>
		</div>
		<?php

	}
}
