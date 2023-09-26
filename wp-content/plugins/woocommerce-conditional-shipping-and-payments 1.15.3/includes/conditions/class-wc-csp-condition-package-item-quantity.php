<?php
/**
 * WC_CSP_Condition_Package_Item_Quantity
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Package Item Quantity Condition.
 *
 * @class    WC_CSP_Condition_Package_Item_Quantity
 * @version  1.15.0
 */
class WC_CSP_Condition_Package_Item_Quantity extends WC_CSP_Package_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                            = 'items_in_package';
		$this->title                         = __( 'Package Item Count', 'woocommerce-conditional-shipping-and-payments' );
		$this->supported_global_restrictions = array( 'shipping_methods', 'shipping_countries', 'payment_gateways' );
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
		$package_count   = $this->get_package_count( $args );
		$condition_value = absint( $data[ 'value' ] );

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'gt', 'min' ) ) ) {

			if ( $package_count > 1 ) {

				if ( isset( $args[ 'restriction_data' ][ 'restriction_id' ] ) && 'payment_gateways' === $args[ 'restriction_data' ][ 'restriction_id' ] ) {
					$message = sprintf( __( 'make sure that there are no shipping packages with more than %s items', 'woocommerce-conditional-shipping-and-payments' ), $condition_value );
				} else {
					$message = sprintf( __( 'make sure that there are no more than %s items in it', 'woocommerce-conditional-shipping-and-payments' ), $condition_value );
				}

			} else {
				$message = sprintf( __( 'make sure that there are no more than %s items in your cart', 'woocommerce-conditional-shipping-and-payments' ), $condition_value );
			}

		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'lt', 'max' ) ) ) {

			if ( $package_count > 1 ) {

				if ( isset( $args[ 'restriction_data' ][ 'restriction_id' ] ) && 'payment_gateways' === $args[ 'restriction_data' ][ 'restriction_id' ] ) {
					$message = sprintf( __( 'make sure there are at least %s items in every shipping package', 'woocommerce-conditional-shipping-and-payments' ), $condition_value );
				} else {
					$message = sprintf( __( 'make sure it contains at least %s items', 'woocommerce-conditional-shipping-and-payments' ), $condition_value );
				}

			} else {
				$message = sprintf( __( 'make sure that your cart contains at least %s items', 'woocommerce-conditional-shipping-and-payments' ), $condition_value );
			}
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'lte' ) ) ) {

			if ( $package_count > 1 ) {

				if ( isset( $args[ 'restriction_data' ][ 'restriction_id' ] ) && 'payment_gateways' === $args[ 'restriction_data' ][ 'restriction_id' ] ) {
					$message = sprintf( __( 'make sure there are at least %s items in every shipping package', 'woocommerce-conditional-shipping-and-payments' ), $condition_value );
				} else {
					$message = sprintf( __( 'make sure it contains more than %s items', 'woocommerce-conditional-shipping-and-payments' ), $condition_value );
				}

			} else {
				$message = sprintf( __( 'make sure that your cart contains more than %s items', 'woocommerce-conditional-shipping-and-payments' ), $condition_value );
			}
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'gte' ) ) ) {

			if ( $package_count > 1 ) {

				if ( isset( $args[ 'restriction_data' ][ 'restriction_id' ] ) && 'payment_gateways' === $args[ 'restriction_data' ][ 'restriction_id' ] ) {
					$message = sprintf( __( 'make sure that there are less than %s items in every shipping package', 'woocommerce-conditional-shipping-and-payments' ), $condition_value );
				} else {
					$message = sprintf( __( 'make sure it contains less than %s items', 'woocommerce-conditional-shipping-and-payments' ), $condition_value );
				}

			} else {
				$message = sprintf( __( 'make sure that your cart contains less than %s items', 'woocommerce-conditional-shipping-and-payments' ), $condition_value );
			}
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'eq' ) ) ) {

			if ( $package_count > 1 ) {

				if ( isset( $args[ 'restriction_data' ][ 'restriction_id' ] ) && 'payment_gateways' === $args[ 'restriction_data' ][ 'restriction_id' ] ) {

					if ( $condition_value === 1 ) {
						$message = sprintf( __( 'make sure that there are more than %s items in every shipping package', 'woocommerce-conditional-shipping-and-payments' ), $condition_value );
					} else {
						$message = sprintf( __( 'make sure that there are either more or fewer than %s items in every shipping package', 'woocommerce-conditional-shipping-and-payments' ), $condition_value );
					}

				} else {

					if ( $condition_value === 1 ) {
						$message = sprintf( __( 'make sure it contains more than %s items', 'woocommerce-conditional-shipping-and-payments' ), $condition_value );
					} else {
						$message = sprintf( __( 'make sure it contains either more or fewer than %s items', 'woocommerce-conditional-shipping-and-payments' ), $condition_value );
					}

				}
			} else {

				if ( $condition_value === 1 ) {
					$message = sprintf( __( 'make sure that your cart contains more than %s items', 'woocommerce-conditional-shipping-and-payments' ), $condition_value );
				} else {
					$message = sprintf( __( 'make sure that your cart contains either more or fewer than %s items', 'woocommerce-conditional-shipping-and-payments' ), $condition_value );
				}
	
			}
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

		// Empty conditions always apply (not evaluated).
		if ( empty( $data[ 'value' ] ) ) {
			return true;
		}

		$is_matching_package = false;

		if ( ! empty( $args[ 'package' ] ) ) {

			$is_matching_package = $this->is_matching_package( $args[ 'package' ], $data, $args );

		} else {

			$shipping_packages = $this->get_packages();

			if ( ! empty( $shipping_packages ) ) {
				foreach ( $shipping_packages as $shipping_package ) {
					if ( $this->is_matching_package( $shipping_package, $data, $args ) ) {
						$is_matching_package = true;
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
	 * @param  array  $package
	 * @param  array  $data
	 * @param  array  $args
	 * @return boolean
	 */
	protected function is_matching_package( $package, $data, $args ) {

		$limit          = absint( $data[ 'value' ] );
		$total_quantity = $this->get_items_in_package( $package, $data, $args );
		$is_matching    = false;

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'gt', 'min' ) ) && $limit < $total_quantity ) {
			$is_matching = true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'lt', 'max' ) ) && $limit > $total_quantity ) {
			$is_matching = true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'gte' ) ) && $limit <= $total_quantity ) {
			$is_matching = true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'lte' ) ) && $limit >= $total_quantity ) {
			$is_matching = true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'eq' ) ) && $limit === $total_quantity ) {
			$is_matching = true;
		}

		return $is_matching;
	}

	/**
	 * Get items in package.
	 *
	 * @since  1.4.0
	 *
	 * @param  array  $package
	 * @param  array  $data
	 * @param  array  $args
	 * @return int
	 */
	protected function get_items_in_package( $package, $data, $args ) {

		$total_quantity = 0;

		foreach ( $package[ 'contents' ] as $cart_item_key => $cart_item_data ) {
			$total_quantity += $cart_item_data[ 'quantity' ];
		}

		return apply_filters( 'woocommerce_csp_package_item_quantity_count', $total_quantity, $package, $data, $args ) ;
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
			$processed_condition_data[ 'value' ]        = absint( stripslashes( $posted_condition_data[ 'value' ] ) );
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

			if ( $processed_condition_data[ 'value' ] > 0 ) {
				return $processed_condition_data;
			}
		}

		return false;
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

		$modifier = 'lt';
		$quantity = '';

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];

			// Max/Min  Backwards compatibility
			if ( 'max' === $modifier ) {
				$modifier = 'lt';
			} elseif ( 'min' === $modifier ) {
				$modifier = 'gt';
			}

		}

		if ( ! empty( $condition_data[ 'value' ] ) ) {
			$quantity = absint( $condition_data[ 'value' ] );
		}

		?>
		<input type="hidden" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][condition_id]" value="<?php echo esc_attr( $this->id ); ?>" />
		<div class="condition_row_inner">
			<div class="condition_modifier">
				<div class="sw-enhanced-select">
					<select name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][modifier]">
						<option value="lt" <?php selected( $modifier, 'lt', true ); ?>><?php esc_html_e( '<', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="lte" <?php selected( $modifier, 'lte', true ); ?>><?php esc_html_e( '<=', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="gte" <?php selected( $modifier, 'gte', true ); ?>><?php esc_html_e( '>=', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="gt" <?php selected( $modifier, 'gt', true ); ?>><?php esc_html_e( '>', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="eq" <?php selected( $modifier, 'eq', true ); ?>><?php esc_html_e( '=', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
					</select>
				</div>
			</div>
			<div class="condition_value">
				<input type="number" class="short qty" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][value]" value="<?php echo esc_attr( $quantity ); ?>" placeholder="" step="any" min="0"/>
			</div>
		</div>
		<?php
	}
}
