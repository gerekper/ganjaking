<?php
/**
 * WC_CSP_Condition_Package_Total
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Package Total Condition.
 *
 * @class    WC_CSP_Condition_Package_Total
 * @version  1.15.0
 */
class WC_CSP_Condition_Package_Total extends WC_CSP_Package_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                            = 'package_total';
		$this->title                         = __( 'Package Total', 'woocommerce-conditional-shipping-and-payments' );
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
		if ( empty( $data[ 'value' ] ) ) {
			return false;
		}

		if ( empty( $args[ 'package' ] ) || empty( $args[ 'package' ][ 'contents' ] ) ) {
			return false;
		}

		$package_count = $this->get_package_count( $args );
		$message       = false;

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'gte', 'min' ) ) ) {

			if ( 1 === $package_count ) {
				$message = sprintf( __( 'make sure that the total value of your shipment does not exceed %s', 'woocommerce-conditional-shipping-and-payments' ), wc_price( $data[ 'value' ] ) );
			} else {
				$message = sprintf( __( 'make sure that its total value does not exceed %s', 'woocommerce-conditional-shipping-and-payments' ), wc_price( $data[ 'value' ] ) );
			}

		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'lt', 'max' ) ) ) {

			if ( 1 === $package_count ) {
				$message = sprintf( __( 'make sure that the total value of your shipment is %s or higher', 'woocommerce-conditional-shipping-and-payments' ), wc_price( $data[ 'value' ] ) );
			} else {
				$message = sprintf( __( 'make sure that its total value is %s or higher', 'woocommerce-conditional-shipping-and-payments' ), wc_price( $data[ 'value' ] ) );
			}
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'gt' ) ) ) {

			if ( 1 === $package_count ) {
				$message = sprintf( __( 'make sure that the total value of your shipment is %s or lower', 'woocommerce-conditional-shipping-and-payments' ), wc_price( $data[ 'value' ] ) );
			} else {
				$message = sprintf( __( 'make sure that its total value is %s or lower', 'woocommerce-conditional-shipping-and-payments' ), wc_price( $data[ 'value' ] ) );
			}
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'lte' ) ) ) {

			if ( 1 === $package_count ) {
				$message = sprintf( __( 'make sure that the total value of your shipment is above %s', 'woocommerce-conditional-shipping-and-payments' ), wc_price( $data[ 'value' ] ) );
			} else {
				$message = sprintf( __( 'make sure that its total value is above %s', 'woocommerce-conditional-shipping-and-payments' ), wc_price( $data[ 'value' ] ) );
			}
		}

		return $message;
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
		if ( empty( $args[ 'package' ] ) || empty( $args[ 'package' ][ 'contents' ] ) ) {
			return true;
		}

		$package_contents_total = 0.0;
		$package_contents_tax   = 0.0;

		foreach ( $args[ 'package' ][ 'contents' ] as $cart_item ) {
			$package_contents_total += $cart_item[ 'line_total' ];
			$package_contents_tax   += $cart_item[ 'line_tax' ];
		}

		$package_contents_tax = apply_filters( 'woocommerce_csp_package_total_condition_incl_tax', true, $data, $args ) ? $package_contents_tax : 0.0;
		$package_total        = $package_contents_total + $package_contents_tax;

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'gte', 'min' ) ) && wc_format_decimal( $data[ 'value' ] ) <= $package_total ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'lt', 'max' ) ) && wc_format_decimal( $data[ 'value' ] ) > $package_total ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'lte' ) ) && wc_format_decimal( $data[ 'value' ] ) >= $package_total ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'gt' ) ) && wc_format_decimal( $data[ 'value' ] ) < $package_total ) {
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
			$processed_condition_data[ 'value' ]        = $posted_condition_data[ 'value' ] !== '0' ? wc_format_decimal( stripslashes( $posted_condition_data[ 'value' ] ), '' ) : 0;
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

			if ( $processed_condition_data[ 'value' ] > 0 || $processed_condition_data[ 'value' ] === 0 ) {
				return $processed_condition_data;
			}
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

		$modifier      = 'lt';
		$package_total = '';

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
			$package_total = wc_format_localized_price( $condition_data[ 'value' ] );
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
					</select>
				</div>
			</div>
			<div class="condition_value">
				<input type="text" class="wc_input_price short" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][value]" value="<?php echo esc_attr( $package_total ); ?>" placeholder="" step="any" min="0"/>
				<span class="condition_value--suffix">
					<?php echo esc_html( get_woocommerce_currency_symbol() ); ?>
				</span>
			</div>
		</div>
		<?php
	}
}
