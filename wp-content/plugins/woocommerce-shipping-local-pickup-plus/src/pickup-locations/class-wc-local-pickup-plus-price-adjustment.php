<?php
/**
 * WooCommerce Local Pickup Plus
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2022, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * Price adjustment helper object.
 *
 * In Local Pickup Plus price adjustments can be either discounts or costs,
 * expressed as fixed amounts or percentages.
 *
 * This helper class takes one string value and determines whether the adjustment
 * should be a cost or a discount, its amount, and ease calculations.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Price_Adjustment {


	/** @var int ID of the corresponding pickup location */
	private $location_id;

	/** @var null|float|string the price adjustment value */
	protected $value;


	/**
	 * Price adjustment constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param null|float|string $value for example "3%", "-10%", "5" or "-7.5" etc
	 * @param int $location_id optional, ID of the corresponding pickup location (useful to pass in hooks)
	 */
	public function __construct( $value = null, $location_id = 0 ) {

		if ( null !== $value ) {
			$this->value = $this->parse_value( $value );
		}

		$this->location_id = (int) $location_id;
	}


	/**
	 * Parse a string value to set the price adjustment properties.
	 *
	 * @since 2.0.0
	 *
	 * @param int|float|string $value for example "3%", "-10%", "5" or "-7.5" etc.
	 * @return float|string
	 */
	private function parse_value( $value ) {

		if ( $this->is_percentage( $value ) ) {
			$pieces = explode( '%', $value );
			$amount = (float) $pieces[0];
			$value  = "{$amount}%";
		} else {
			$value  = (float) $value;
		}

		return $value;
	}


	/**
	 * Make a price adjustment.
	 *
	 * Takes three arguments to output a numerical string as a standardized price adjustment.
	 *
	 * @since 2.0.0
	 *
	 * @param string $adjustment either 'cost' or 'discount'
	 * @param int|float $amount an absolute number
	 * @param string $type either 'fixed' or 'percentage'
	 */
	public function set_value( $adjustment, $amount, $type ) {

		$amount = (float) $amount;

		if ( in_array( $type, array( '%', 'pct', 'percent', 'percentage' ), true ) ) {
			$adjustment = 'discount' === $adjustment ? "-{$amount}%" : "{$amount}%";
		} else {
			$adjustment = 'discount' === $adjustment ? "-{$amount}"  : (string) $amount;
		}

		$this->value = $adjustment;
	}


	/**
	 * Get the price adjustment raw value.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_value() {
		return $this->value;
	}


	/**
	 * Whether the price adjustment is a percentage.
	 *
	 * @since 2.0.0
	 *
	 * @param null|float|string check if a value is a percentage
	 * @return bool
	 */
	public function is_percentage( $value = null ) {
		return Framework\SV_WC_Helper::str_ends_with( null !== $value ? $value : $this->value, '%' );
	}


	/**
	 * Whether the price adjustment is a fixed amount.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_fixed() {
		return ! $this->is_percentage();
	}


	/**
	 * Whether the price adjustment is invalid.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_null() {

		$amount = empty( $this->value ) ? null : $this->get_amount();

		return empty( $amount );
	}


	/**
	 * Whether the price adjustment is a cost.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_cost() {
		return $this->get_amount() > 0;
	}


	/**
	 * Whether the price adjustment is a discount.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_discount() {
		return $this->get_amount() < 0;
	}


	/**
	 * Get the amount of the price adjustment as a number.
	 *
	 * @since 2.0.0
	 *
	 * @param bool $absolute whether to return an absolute amount or relative (e.g. negative number)
	 * @return float
	 */
	public function get_amount( $absolute = false ) {

		$amount = $this->value;

		if ( null !== $amount && $this->is_percentage() ) {

			$pieces = explode( '%', $amount );
			$amount = $pieces[0];
		}

		if ( true === $absolute ) {
			$amount = abs( $amount );
		}

		return (float) $amount;
	}


	/**
	 * Get relative amount.
	 *
	 * @since 2.0.0
	 *
	 * @param int|float $base_amount the amount to calculate relative to
	 * @param bool $absolute whether to return an absolute amount or relative (e.g. negative number)
	 * @return float
	 */
	public function get_relative_amount( $base_amount, $absolute = false ) {

		$result      = 0;
		$base_amount = is_numeric( $base_amount ) ? (float) $base_amount : 0;

		if ( $base_amount >= 0 ) {

			$amount = $this->get_amount();
			$result = $base_amount > 0 || $amount > 0 ? $amount : 0;

			if ( $this->is_percentage() ) {

				$percentage = ( $base_amount * $this->get_amount( true ) ) / 100;
				$result     = $percentage;

				if ( $this->is_discount() ) {
					$result = "-{$percentage}";
				}
			}
		}

		return true === $absolute ? abs( $result ) : $result;
	}


	/**
	 * Get a price adjustment input field HTML.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args array of input field arguments
	 * @return string HTML
	 */
	public function get_field_html( array $args ) {

		$args = wp_parse_args( $args, array(
			'name'     => '',
			'disabled' => false,
		) );

		if ( empty( $args['name'] ) || ! is_string( $args['name'] ) ) {
			return '';
		}

		ob_start();

		?>
		<div class="wc-local-pickup-plus-field wc-local-pickup-plus-price-adjustment-field">
			<select
				name="<?php echo esc_attr( $args['name'] ); ?>"
				id="<?php echo esc_attr( $args['name'] ); ?>"
				class="select wc-local-pickup-plus-dropdown"
				<?php disabled( $args['disabled'], true, true ); ?>>
				<option
					<?php selected( true, $this->is_cost(), true ); ?>
					value="cost"><?php esc_html_e( 'Cost', 'woocommerce-shipping-local-pickup-plus' ); ?></option>
				<option
					<?php selected( true, $this->is_discount(), true ); ?>
					value="discount"><?php esc_html_e( 'Discount', 'woocommerce-shipping-local-pickup-plus' ); ?></option>
			</select>
			<input
				type="number"
				id="<?php echo esc_attr( $args['name'] . '_amount' ); ?>"
				name="<?php echo esc_attr( $args['name'] . '_amount' ); ?>"
				value="<?php echo esc_attr( $this->get_amount( true ) ); ?>"
				style="max-width: 80px; text-align: right;"
				placeholder="0"
				step="0.01"
				min="0"
				<?php disabled( $args['disabled'], true, true ); ?>
			/>
			<select
				id="<?php echo esc_attr( $args['name'] . '_type' ); ?>"
				name="<?php echo esc_attr( $args['name'] . '_type' ); ?>"
				class="select wc-local-pickup-plus-dropdown"
				<?php disabled( $args['disabled'], true, true ); ?>>
				<option
					value="fixed"
					title="<?php esc_attr_e( 'Fixed amount', 'woocommerce-shipping-local-pickup-plus' ); ?>"
					<?php selected( true, $this->is_fixed(), true ); ?>><?php echo esc_html( get_woocommerce_currency_symbol() ); ?></option>
				<option
					value="percentage"
					title="<?php esc_html_e( 'Percentage amount', 'woocommerce-shipping-local-pickup-plus' ); ?>"
					<?php selected( true, $this->is_percentage(), true ); ?>>%</option>
			</select>
			<?php echo ! empty( $args['desc_tip'] ) ? wc_help_tip( $args['desc_tip'] ) : ''; ?>
		</div>
		<?php

		return ob_get_clean();
	}


	/**
	 * Output a price adjustment input field HTML.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args array of arguments
	 */
	public function output_field_html( array $args ) {

		echo $this->get_field_html( $args );
	}


}
