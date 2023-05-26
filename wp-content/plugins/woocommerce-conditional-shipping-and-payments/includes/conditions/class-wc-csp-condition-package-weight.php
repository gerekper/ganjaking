<?php
/**
 * WC_CSP_Condition_Package_Weight class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Package Weight Condition.
 *
 * @class    WC_CSP_Condition_Package_Weight
 * @version  1.15.0
 */
class WC_CSP_Condition_Package_Weight extends WC_CSP_Package_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                            = 'package_weight';
		$this->title                         = __( 'Package Weight', 'woocommerce-conditional-shipping-and-payments' );
		$this->priority                      = 20;
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

		// Empty conditions always apply (not evaluated).
		if ( ! isset( $data[ 'value' ] ) || $data[ 'value' ] === '' ) {
			return false;
		}

		if ( empty( $args[ 'package' ] ) || empty( $args[ 'package' ][ 'contents' ] ) ) {
			return false;
		}

		$package_count = $this->get_package_count( $args );
		$message       = false;

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'gte', 'min' ) ) ) {

			if ( 1 === $package_count ) {
				$message = sprintf( __( 'decrease the total weight of your shipment below %s', 'woocommerce-conditional-shipping-and-payments' ), wc_format_weight ( $data[ 'value' ]) );
			} else {
				$message = sprintf( __( 'decrease its total weight below %s', 'woocommerce-conditional-shipping-and-payments' ), wc_format_weight ( $data[ 'value' ]) );
			}

		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'lt', 'max' ) ) ) {

			if ( 1 === $package_count ) {
				$message = sprintf( __( 'increase the total weight of your shipment to %s or higher', 'woocommerce-conditional-shipping-and-payments' ), wc_format_weight ( $data[ 'value' ]) );
			} else {
				$message = sprintf( __( 'increase its total weight to %s or higher', 'woocommerce-conditional-shipping-and-payments' ), wc_format_weight ( $data[ 'value' ]) );
			}
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'gt' ) ) ) {

			if ( 1 === $package_count ) {
				$message = sprintf( __( 'decrease the total weight of your shipment to %s or lower', 'woocommerce-conditional-shipping-and-payments' ), wc_format_weight ( $data[ 'value' ]) );
			} else {
				$message = sprintf( __( 'decrease its total weight to %s or lower', 'woocommerce-conditional-shipping-and-payments' ), wc_format_weight ( $data[ 'value' ]) );
			}
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'lte' ) ) ) {

			if ( 1 === $package_count ) {
				$message = sprintf( __( 'increase the total weight of your shipment above %s', 'woocommerce-conditional-shipping-and-payments' ), wc_format_weight ( $data[ 'value' ]) );
			} else {
				$message = sprintf( __( 'increase its total weight above %s', 'woocommerce-conditional-shipping-and-payments' ), wc_format_weight ( $data[ 'value' ]) );
			}
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'eq' ) ) ) {

			if ( 1 === $package_count ) {
				$message = sprintf( __( 'make sure that the total weight of your shipment is either above or below %s', 'woocommerce-conditional-shipping-and-payments' ), wc_format_weight ( $data[ 'value' ]) );
			} else {
				$message = sprintf( __( 'make sure its total weight is either above or below %s', 'woocommerce-conditional-shipping-and-payments' ), wc_format_weight ( $data[ 'value' ]) );
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
		if ( ! isset( $data[ 'value' ] ) || $data[ 'value' ] === '' ) {
			return true;
		}

		if ( empty( $args[ 'package' ] ) || empty( $args[ 'package' ][ 'contents' ] ) ) {
			return true;
		}

		$pkg_weight = 0;

		foreach ( $args[ 'package' ][ 'contents' ] as $cart_item_key => $cart_item_data ) {

			$product = $cart_item_data[ 'data' ];

			if ( ! $product->needs_shipping() ) {
				continue;
			}

			$cart_item_weight = $product->get_weight();

			if ( $cart_item_weight ) {
				$pkg_weight += $cart_item_weight * $cart_item_data[ 'quantity' ];
			}
		}

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'gte', 'min' ) ) && $data[ 'value' ] <= $pkg_weight ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'lt', 'max' ) ) && $data[ 'value' ] > $pkg_weight ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'lte' ) ) && $data[ 'value' ] >= $pkg_weight ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'gt' ) ) && $data[ 'value' ] < $pkg_weight ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'eq' ) ) && $data[ 'value' ] == $pkg_weight ) {
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
			$processed_condition_data[ 'value' ]        = $posted_condition_data[ 'value' ] !== '0' ? floatval( stripslashes( $posted_condition_data[ 'value' ] ) ) : 0;
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

			if ( $processed_condition_data[ 'value' ] > 0 || $processed_condition_data[ 'value' ] === 0 ) {
				return $processed_condition_data;
			}
		}

		return false;
	}

	/**
	 * Get package weight condition content for admin global restriction metaboxes.
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$modifier       = 'lt';
		$package_weight = '';

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];

			// Max/Min  Backwards compatibility
			if ( 'max' === $modifier ) {
				$modifier = 'lt';
			} elseif ( 'min' === $modifier ) {
				$modifier = 'gte';
			}

		}

		if ( isset( $condition_data[ 'value' ] ) ) {
			$package_weight = $condition_data[ 'value' ];
		}

		?>
		<input type="hidden" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][condition_id]" value="<?php echo esc_attr( $this->id ); ?>" />
		<div class="condition_row_inner">
			<div class="condition_modifier">
				<div class="sw-enhanced-select">
					<select name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][modifier]">
						<option value="lt" <?php selected( $modifier, 'lt', true ); ?>><?php esc_html_e( '<', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="lte" <?php selected( $modifier, 'lte', true ); ?>><?php esc_html_e( '<=', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="gt" <?php selected( $modifier, 'gt', true ); ?>><?php esc_html_e( '>', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="gte" <?php selected( $modifier, 'gte', true ); ?>><?php esc_html_e( '>=', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="eq" <?php selected( $modifier, 'eq', true ); ?>><?php esc_html_e( '=', 'woocommerce-conditional-shipping-and-payments' ); ?></option>

					</select>
				</div>
			</div>
			<div class="condition_value">
				<input type="number" class="wc_input_decimal short" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][value]" value="<?php echo esc_attr( $package_weight ); ?>" placeholder="" step="any" min="0"/>

				<span class="condition_value--suffix">
					<?php echo esc_html( get_option( 'woocommerce_weight_unit' ) ); ?>
				</span>
			</div>
		</div>
		<?php
	}
}
