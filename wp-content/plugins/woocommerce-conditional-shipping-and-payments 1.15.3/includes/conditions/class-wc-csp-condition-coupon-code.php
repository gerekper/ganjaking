<?php
/**
 * WC_CSP_Condition_Coupon_Code class
 *
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
 * @version  1.15.0
 */
class WC_CSP_Condition_Coupon_Code extends WC_CSP_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                            = 'coupon_code_used';
		$this->title                         = __( 'Coupon Code', 'woocommerce-conditional-shipping-and-payments' );
		$this->priority                      = 20;
		$this->supported_global_restrictions = array( 'shipping_methods', 'payment_gateways', 'shipping_countries' );
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
		if ( ! isset( $data[ 'value' ] ) ) {
			return false;
		}

		$message = false;

		if ( $this->modifier_is( $data[ 'modifier' ], 'used' ) ) {

			if ( empty( $data[ 'value' ] ) ) {

				$message = __( 'remove all applied coupons', 'woocommerce-conditional-shipping-and-payments' );

			} else {

				$coupons        = $this->get_condition_violation_subjects( $data, $args );
				$merged_coupons = $this->merge_titles( $coupons );

				if ( count( $coupons ) > 1 ) {
					$message = sprintf( __( 'remove coupons %s', 'woocommerce-conditional-shipping-and-payments' ), $merged_coupons );
				} else {
					$message = sprintf( __( 'remove coupon %s', 'woocommerce-conditional-shipping-and-payments' ), $merged_coupons );
				}
			}

		} elseif ( $this->modifier_is( $data[ 'modifier' ], 'not-used' ) ) {

			$message = __( 'apply a qualifying coupon', 'woocommerce-conditional-shipping-and-payments' );

		} elseif ( $this->modifier_is( $data[ 'modifier' ], 'free-shipping' ) ) {

			$message = __( 'remove all free shipping coupons from your cart', 'woocommerce-conditional-shipping-and-payments' );

		} elseif ( $this->modifier_is( $data[ 'modifier' ], 'not-free-shipping' ) ) {

			$message = __( 'apply a free shipping coupon', 'woocommerce-conditional-shipping-and-payments' );
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
		return $this->merge_titles( $this->get_condition_violation_subjects( $data, $args ) );
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

			if ( $this->modifier_is( $data[ 'modifier' ], 'used' ) && in_array( $coupon_code, $data[ 'value' ] ) ) {
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
		if ( ! isset( $data[ 'value' ] ) ) {
			return true;
		}

		$active_coupons = array();

		if ( ! empty( $args[ 'order' ] ) ) {
			$active_coupons = $args[ 'order' ]->get_items( 'coupon' );
		} else {
			$active_coupons = WC_CSP_Core_Compatibility::is_wc_version_gte( '3.2' ) ? WC()->cart->get_coupons() : WC()->cart->coupons;
		}

		// No coupons applied, and 'not used' modifier selected?
		if ( empty( $active_coupons ) && self::modifier_is( $data[ 'modifier' ], 'not-used' ) ) {
			return true;
		// Coupons applied, and 'used' modifier selected with empty value (=used any)?
		} elseif ( ! empty( $active_coupons ) && self::modifier_is( $data[ 'modifier' ], 'used' ) && empty( $data[ 'value' ] ) ) {
			return true;
		// Coupons applied, and 'not-used' modifier selected with empty value (=used none)?
		} elseif ( ! empty( $active_coupons ) && self::modifier_is( $data[ 'modifier' ], 'not-used' ) && empty( $data[ 'value' ] ) ) {
			return false;
		}

		$found_coupon          = false;
		$free_shipping_granted = false;
		$active_coupon_codes   = array();

		// Gather active coupon codes.
		foreach ( $active_coupons as $coupon ) {

			$coupon_code = WC_CSP_Core_Compatibility::is_wc_version_gte( '3.0' ) ? $coupon->get_code() : $coupon->code;

			$active_coupon_codes[] = $coupon_code;

			if ( self::modifier_is( $data[ 'modifier' ], array( 'free-shipping', 'not-free-shipping' ) ) ) {

				if ( ! ( $coupon instanceof WC_Coupon ) ) {
					$coupon = new WC_Coupon( $coupon_code );
				}

				$enables_free_shipping = WC_CSP_Core_Compatibility::is_wc_version_gte( '3.0' ) ? $coupon->get_free_shipping() : $coupon->enable_free_shipping();

				if ( $enables_free_shipping ) {
					$free_shipping_granted = true;
				}
			}
		}

		if ( ! empty( $data[ 'value' ] ) ) {
			if ( self::modifier_is( $data[ 'modifier' ], array( 'used', 'not-used' ) ) ) {
				foreach ( $data[ 'value' ] as $check_code ) {

					// Wildcards.
					if ( false !== strpos( $check_code, '*' ) ) {

						$excluded_code_regex = preg_quote( $check_code, '/' );
						$excluded_code_regex = str_replace( preg_quote( '*', '/' ), '.*?', $excluded_code_regex );
						$excluded_code_regex = "/$excluded_code_regex$/i";
						$matched_coupons     = preg_grep( $excluded_code_regex, $active_coupon_codes );

						if ( count( $matched_coupons ) ) {
							$found_coupon = true;
							break;
						}

					} elseif ( in_array( $check_code, $active_coupon_codes ) ) {
						$found_coupon = true;
						break;
					}
				}
			}
		}

		if ( self::modifier_is( $data[ 'modifier' ], 'used' ) && $found_coupon ) {
			return true;
		} elseif ( self::modifier_is( $data[ 'modifier' ], 'not-used' ) && false === $found_coupon ) {
			return true;
		} elseif ( self::modifier_is( $data[ 'modifier' ], 'free-shipping' ) && $free_shipping_granted ) {
			return true;
		} elseif ( self::modifier_is( $data[ 'modifier' ], 'not-free-shipping' ) && false === $free_shipping_granted ) {
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

		$modifier              = 'used';
		$zero_config_modifiers = array( 'free-shipping', 'not-free-shipping' );
		$coupon_codes          = '';

		if ( ! empty( $condition_data[ 'value' ] ) && is_array( $condition_data[ 'value' ] ) ) {
			$coupon_codes = implode( ",", $condition_data[ 'value' ] );
		}

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		?>
		<input type="hidden" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][condition_id]" value="<?php echo esc_attr( $this->id ); ?>" />
		<div class="condition_row_inner">
			<div class="condition_modifier">
				<div class="sw-enhanced-select">
					<select name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][modifier]" data-zero_config_mods="<?php echo esc_attr( json_encode( $zero_config_modifiers ) ); ?>">
						<option value="used" <?php selected( $modifier, 'used', true ); ?>><?php esc_html_e( 'used', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="not-used" <?php selected( $modifier, 'not-used', true ); ?>><?php esc_html_e( 'not used', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="free-shipping" <?php selected( $modifier, 'free-shipping', true ); ?>><?php esc_html_e( 'enables free shipping', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="not-free-shipping" <?php selected( $modifier, 'not-free-shipping', true ); ?>><?php esc_html_e( 'does not enable free shipping', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
					</select>
				</div>
			</div>
			<div class="condition_value" style="<?php echo in_array( $modifier, $zero_config_modifiers ) ? 'display:none;' : '' ; ?>">
				<input type="text"  name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][value]" value="<?php echo esc_attr( $coupon_codes ); ?>" placeholder="<?php esc_attr_e( "Enter coupon codes, separated by comma (,).", 'woocommerce-conditional-shipping-and-payments' ) ?>" step="any" min="0"/>
				<span class="description"><?php esc_attr_e( "Enter coupon codes, separated by comma (,). You may also use wildcards, such as 'discount*'.", 'woocommerce-conditional-shipping-and-payments' ) ?></span>
			</div>
			<div class="condition_value condition--disabled" style="<?php echo ! in_array( $modifier, $zero_config_modifiers ) ? 'display:none;' : '' ; ?>"></div>
		</div>
		<?php
	}
}
