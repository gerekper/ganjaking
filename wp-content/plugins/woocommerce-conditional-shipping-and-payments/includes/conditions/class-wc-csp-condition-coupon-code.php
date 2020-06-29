<?php
/**
 * WC_CSP_Condition_Coupon_Code class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Coupon Code Condition.
 *
 * @class    WC_CSP_Condition_Coupon_Code
 * @version  1.7.6
 */
class WC_CSP_Condition_Coupon_Code extends WC_CSP_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                             = 'coupon_code_used';
		$this->title                          = __( 'Coupon Code', 'woocommerce-conditional-shipping-and-payments' );
		$this->supported_global_restrictions  = array( 'shipping_methods', 'payment_gateways' );
	}

	/**
	 * Return condition field-specific resolution message which is combined along with others into a single restriction "resolution message".
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restriction.
	 * @return string|false
	 */
	public function get_condition_resolution( $data, $args ) {

		// Empty conditions always apply (not evaluated).
		if ( empty( $data[ 'value' ] ) ) {
			return false;
		}

		$message = false;

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'used' ) ) ) {

			$coupons        = $this->get_condition_violation_subjects( $data, $args );
			$merged_coupons = WC_CSP_Condition::merge_titles( $coupons );

			if ( sizeof( $coupons ) > 1 ) {
				$message = sprintf( __( 'remove coupons %s', 'woocommerce-conditional-shipping-and-payments' ), $merged_coupons );
			} else {
				$message = sprintf( __( 'remove coupon %s', 'woocommerce-conditional-shipping-and-payments' ), $merged_coupons );
			}

		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-used' ) ) ) {
			$message = __( 'use a qualifying coupon', 'woocommerce-conditional-shipping-and-payments' );
		}

		return $message;
	}

	/**
	 * Returns condition resolution placeholder.
	 *
	 * @since  1.4.0
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restriction.
	 * @return array
	 */
	public function get_condition_resolution_placeholder( $data, $args ) {
		return WC_CSP_Condition::merge_titles( $this->get_condition_violation_subjects( $data, $args ) );
	}

	/**
	 * Returns condition violation subjects.
	 *
	 * @since  1.4.0
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restriction.
	 * @return array
	 */
	public function get_condition_violation_subjects( $data, $args ) {

		$subjects       = array();
		$active_coupons = WC_CSP_Core_Compatibility::is_wc_version_gte( '3.2' ) ? WC()->cart->get_coupons() : WC()->cart->coupons;

		foreach ( $active_coupons as $coupon ) {

			$coupon_code = WC_CSP_Core_Compatibility::is_wc_version_gte( '3.0' ) ? $coupon->get_code() : $coupon->code;

			if ( $this->modifier_is( $data[ 'modifier' ], array( 'used' ) ) && in_array( $coupon_code, $data[ 'value' ] ) ) {
				$subjects[] = $coupon_code;
			}
		}

		return array_unique( $subjects );
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

		$active_coupons = array();

		if ( ! empty( $args[ 'order' ] ) ) {
			$active_coupons = $args[ 'order' ]->get_items( 'coupon' );
		} else {
			$active_coupons = WC_CSP_Core_Compatibility::is_wc_version_gte( '3.2' ) ? WC()->cart->get_coupons() : WC()->cart->coupons;
		}

		if ( empty( $active_coupons ) && $data[ 'modifier' ] === 'not-used' ) {
			return true;
		}

		$condition_matching  = false;
		$active_coupon_codes = array();

		// Gather active coupon codes.
		foreach ( $active_coupons as $coupon ) {
			$active_coupon_codes[] = WC_CSP_Core_Compatibility::is_wc_version_gte( '3.0' ) ? $coupon->get_code() : $coupon->code;
		}

		foreach ( $data[ 'value' ] as $coupon_req ) {

			if ( $this->modifier_is( $data[ 'modifier' ], array( 'used' ) ) && in_array( $coupon_req, $active_coupon_codes ) ) {
				$condition_matching = true;
				break;
			} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-used' ) ) && false === in_array( $coupon_req, $active_coupon_codes ) ) {
				$condition_matching = true;
				break;
			}
		}

		return $condition_matching;
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
			$processed_condition_data[ 'value' ]        = array_filter( array_map( 'wc_clean', explode( ",", $posted_condition_data[ 'value' ] ) ) );
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

			return $processed_condition_data;
		}

		return false;
	}

	/**
	 * Get cart total conditions content for admin restriction metaboxes.
	 *
	 * @param  int    $index
	 * @param  int    $condition_ndex
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$modifier     = '';
		$coupon_codes = '';

		if ( ! empty( $condition_data[ 'value' ] ) && is_array( $condition_data[ 'value' ] ) ) {
			$coupon_codes = implode( ",", $condition_data[ 'value' ] );
		}

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		?>
		<input type="hidden" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][condition_id]" value="<?php echo $this->id; ?>" />
		<div class="condition_row_inner">
			<div class="condition_modifier">
				<div class="sw-enhanced-select">
					<select name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][modifier]">
						<option value="used" <?php selected( $modifier, 'used', true ) ?>><?php echo __( 'used', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="not-used" <?php selected( $modifier, 'not-used', true ) ?>><?php echo __( 'not used', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
					</select>
				</div>
			</div>
			<div class="condition_value">
				<input type="text"  name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][value]" value="<?php echo $coupon_codes; ?>" placeholder="" step="any" min="0"/>
			</div>
		</div>
		<?php
	}
}
