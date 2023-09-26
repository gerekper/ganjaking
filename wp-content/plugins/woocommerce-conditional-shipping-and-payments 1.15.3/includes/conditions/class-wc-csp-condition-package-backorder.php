<?php
/**
 * WC_CSP_Condition_Package_Backorder class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Backorder in Package Condition.
 *
 * @class    WC_CSP_Condition_Package_Backorder
 * @version  1.15.0
 */
class WC_CSP_Condition_Package_Backorder extends WC_CSP_Package_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                            = 'backorder_in_package';
		$this->title                         = __( 'Backorder', 'woocommerce-conditional-shipping-and-payments' );
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

		if ( empty( $args[ 'package' ] ) || empty( $args[ 'package' ][ 'contents' ] ) ) {
			return false;
		}

		$package_count = $this->get_package_count( $args );

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) ) {

			if ( 1 === $package_count ) {
				$message = __( 'remove all backordered products from your cart', 'woocommerce-conditional-shipping-and-payments' );
			} else {
				$message = __( 'remove all backordered products from it', 'woocommerce-conditional-shipping-and-payments' );
			}

		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-in' ) ) ) {

			if ( 1 === $package_count ) {
				$message = __( 'add some backordered products to your cart', 'woocommerce-conditional-shipping-and-payments' );
			} else {
				$message = __( 'make sure it contains some backordered products', 'woocommerce-conditional-shipping-and-payments' );
			}

		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'all-in' ) ) ) {

			if ( 1 === $package_count ) {
				$message = __( 'make sure that your cart doesn\'t contain only products on backorder', 'woocommerce-conditional-shipping-and-payments' );
			} else {
				$message = __( 'make sure it does not contain only products on backorder', 'woocommerce-conditional-shipping-and-payments' );
			}

		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-all-in' ) ) ) {

			if ( 1 === $package_count ) {
				$message = __( 'make sure that your cart contains only products on backorder', 'woocommerce-conditional-shipping-and-payments' );
			} else {
				$message = __( 'make sure it contains only products on backorder', 'woocommerce-conditional-shipping-and-payments' );
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

		if ( empty( $args[ 'package' ] ) || empty( $args[ 'package' ][ 'contents' ] ) ) {
			return true;
		}

		$contains_items_on_backorder = false;
		$all_items_on_backorder      = true;

		foreach ( $args[ 'package' ][ 'contents' ] as $cart_item_key => $cart_item_data ) {

			$product = $cart_item_data[ 'data' ];

			if ( $product->is_on_backorder( $cart_item_data[ 'quantity' ] ) ) {

				$contains_items_on_backorder = true;

				if ( $this->modifier_is( $data[ 'modifier' ], array( 'in', 'not-in' ) ) ) {
					break;
				}
			} else {

				$all_items_on_backorder = false;

				if ( $this->modifier_is( $data[ 'modifier' ], array( 'all-in', 'not-all-in' ) ) ) {
					break;
				}
			}
		}

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) && $contains_items_on_backorder ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-in' ) ) && ! $contains_items_on_backorder ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'all-in' ) ) && $all_items_on_backorder ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-all-in' ) ) && ! $all_items_on_backorder ) {
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

		$processed_condition_data                   = array();
		$processed_condition_data[ 'condition_id' ] = $this->id;
		$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

		return $processed_condition_data;
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

	?>
	<input type="hidden" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][condition_id]" value="<?php echo esc_attr( $this->id ); ?>" />
	<div class="condition_row_inner">
		<div class="condition_modifier">
			<div class="sw-enhanced-select">
				<select name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][modifier]">
					<option value="in" <?php selected( $modifier, 'in', true ); ?>><?php esc_html_e( 'in package', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
					<option value="not-in" <?php selected( $modifier, 'not-in', true ); ?>><?php esc_html_e( 'not in package', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
					<option value="all-in" <?php selected( $modifier, 'all-in', true ); ?>><?php esc_html_e( 'all package items', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
					<option value="not-all-in" <?php selected( $modifier, 'not-all-in', true ); ?>><?php esc_html_e( 'not all package items', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
				</select>
			</div>
		</div>
		<div class="condition_value condition--disabled"></div>
	</div><?php
	}
}
