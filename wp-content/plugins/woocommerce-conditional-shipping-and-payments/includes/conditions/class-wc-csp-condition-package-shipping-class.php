<?php
/**
 * WC_CSP_Condition_Package_Shipping_Class class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shipping Class in Package Condition.
 *
 * @class    WC_CSP_Condition_Package_Shipping_Class
 * @version  1.15.0
 */
class WC_CSP_Condition_Package_Shipping_Class extends WC_CSP_Package_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                            = 'shipping_class_in_package';
		$this->title                         = __( 'Shipping Class', 'woocommerce-conditional-shipping-and-payments' );
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

		// Empty conditions always return false (not evaluated).
		if ( empty( $data[ 'value' ] ) ) {
			return false;
		}

		if ( empty( $args[ 'package' ] ) || empty( $args[ 'package' ][ 'contents' ] ) ) {
			return false;
		}

		$package_count = $this->get_package_count( $args );
		$message       = false;

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) ) {

			if ( 1 === $package_count ) {
				$message = sprintf( __( 'remove %s from your cart', 'woocommerce-conditional-shipping-and-payments' ), $this->get_condition_resolution_placeholder( $data, $args ) );
			} else {
				$message = sprintf( __( 'remove %s from it', 'woocommerce-conditional-shipping-and-payments' ), $this->get_condition_resolution_placeholder( $data, $args ) );
			}

		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'all-in', 'not-in' ) ) ) {

			if ( 1 === $package_count ) {
				$message = __( 'add some qualifying products to your cart', 'woocommerce-conditional-shipping-and-payments' );
			} else {
				$message = __( 'add some qualifying products to it', 'woocommerce-conditional-shipping-and-payments' );
			}

		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-all-in' ) ) ) {

			if ( 1 === $package_count ) {
				$message = __( 'make sure that your cart contains only qualifying products', 'woocommerce-conditional-shipping-and-payments' );
			} else {
				$message = __( 'make sure it contains qualifying products only', 'woocommerce-conditional-shipping-and-payments' );
			}
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
	 * Returns condition resolution subjects.
	 *
	 * @since  1.4.0
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restriction.
	 * @return array
	 */
	public function get_condition_violation_subjects( $data, $args ) {

		$subjects = array();

		foreach ( $args[ 'package' ][ 'contents' ] as $cart_item_key => $cart_item ) {

			$product           = $cart_item[ 'data' ];
			$shipping_class_id = $product->get_shipping_class_id();
			$found_item        = in_array( $shipping_class_id, $data[ 'value' ] );

			// Only 'in' and 'not-all-in' modifiers can have violations.
			if ( $found_item && $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) ) {
				$subjects[] = $cart_item[ 'data' ]->get_name();
			} elseif ( ! $found_item && $this->modifier_is( $data[ 'modifier' ], array( 'not-all-in' ) ) ) {
				$subjects[] = $cart_item[ 'data' ]->get_name();
			}
		}

		return $subjects;
	}

	/**
	 * Evaluate if the condition is in effect or not.
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

		if ( ! empty( $args[ 'package' ] ) ) {
			$package = $args[ 'package' ];
		} else {
			return true;
		}

		$contains_qualifying_products = false;
		$all_products_qualify         = true;

		foreach ( $package[ 'contents' ] as $cart_item_key => $cart_item_data ) {

			$product           = $cart_item_data[ 'data' ];
			$shipping_class_id = $product->get_shipping_class_id();

			if ( in_array( $shipping_class_id, $data[ 'value' ] ) ) {

				$contains_qualifying_products = true;

				if ( $this->modifier_is( $data[ 'modifier' ], array( 'in', 'not-in' ) ) ) {
					break;
				}

			} else {

				$all_products_qualify = false;

				if ( $this->modifier_is( $data[ 'modifier' ], array( 'all-in', 'not-all-in' ) ) ) {
					break;
				}
			}
		}

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) && $contains_qualifying_products ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-in' ) ) && ! $contains_qualifying_products ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'all-in' ) ) && $all_products_qualify ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-all-in' ) ) && ! $all_products_qualify ) {
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
			$processed_condition_data[ 'value' ]        = array_map( 'intval', $posted_condition_data[ 'value' ] );
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

			return $processed_condition_data;
		}

		return false;
	}

	/**
	 * Get shipping-class-in-package condition content for global restrictions.
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$modifier         = '';
		$shipping_classes = array();

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		if ( ! empty( $condition_data[ 'value' ] ) ) {
			$shipping_classes = $condition_data[ 'value' ];
		}

		$product_shipping_classes = ( array ) get_terms( 'product_shipping_class', array( 'get' => 'all' ) );

		?>
		<input type="hidden" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][condition_id]" value="<?php echo esc_attr( $this->id ); ?>" />
		<div class="condition_row_inner">
			<div class="condition_modifier">
				<div class="sw-enhanced-select">
					<select name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][modifier]">
						<option value="in" <?php selected( $modifier, 'in', true ); ?>><?php esc_html_e( 'in package', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="not-in" <?php selected( $modifier, 'not-in', true ); ?>><?php esc_html_e( 'not in package', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="all-in" <?php selected( $modifier, 'all-in', true ); ?>><?php esc_html_e( 'all items in package', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="not-all-in" <?php selected( $modifier, 'not-all-in', true ); ?>><?php esc_html_e( 'not all items in package', 'woocommerce-conditional-shipping-and-payments' ); ?></option>

					</select>
				</div>
			</div>
			<div class="condition_value">
				<select name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][value][]" class="multiselect sw-select2" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select shipping classes&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>">
					<?php
						foreach ( $product_shipping_classes as $shipping_class ) {
							echo '<option value="' . esc_attr( $shipping_class->term_id ) . '" ' . selected( in_array( $shipping_class->term_id, $shipping_classes ), true, false ).'>' . esc_html( $shipping_class->name ) . '</option>';
						}
					?>
				</select>
			</div>
		</div>
		<?php
	}
}
