<?php
/**
 * WC_CSP_Condition_Package_Total
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
 * Package Total Condition.
 *
 * @class    WC_CSP_Condition_Package_Total
 * @version  1.4.0
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

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'min' ) ) ) {

			if ( 1 === $package_count ) {
				$message = sprintf( __( 'make sure that the total value of your shipment does not exceed %s', 'woocommerce-conditional-shipping-and-payments' ), wc_price( $data[ 'value' ] ) );
			} else {
				$message = sprintf( __( 'make sure that its total value does not exceed %s', 'woocommerce-conditional-shipping-and-payments' ), wc_price( $data[ 'value' ] ) );
			}

		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'max' ) ) ) {

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

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'min' ) ) && wc_format_decimal( $data[ 'value' ] ) <= $package_total ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'max' ) ) && wc_format_decimal( $data[ 'value' ] ) > $package_total ) {
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

		$modifier      = '';
		$package_total = '';

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		} else {
			$modifier = 'max';
		}

		if ( isset( $condition_data[ 'value' ] ) ) {
			$package_total = wc_format_localized_price( $condition_data[ 'value' ] );
		}

		?>
		<input type="hidden" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][condition_id]" value="<?php echo $this->id; ?>" />
		<div class="condition_row_inner">
			<div class="condition_modifier">
				<div class="sw-enhanced-select">
					<select name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][modifier]">
						<option value="max" <?php selected( $modifier, 'max', true ) ?>><?php echo __( '<', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="min" <?php selected( $modifier, 'min', true ) ?>><?php echo __( '>=', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
					</select>
				</div>
			</div>
			<div class="condition_value">
				<input type="text" class="wc_input_price short" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][value]" value="<?php echo $package_total; ?>" placeholder="" step="any" min="0"/>
				<span class="condition_value--suffix">
					<?php echo get_woocommerce_currency_symbol() ?>
				</span>
			</div>
		</div>
		<?php
	}
}
