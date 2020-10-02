<?php

/**
 * Addons integration class.
 */
class WC_Bookings_Addons {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_product_addons_show_grand_total', array( $this, 'addons_show_grand_total' ), 20, 2 );
		add_action( 'woocommerce_product_addons_panel_before_options', array( $this, 'addon_options' ), 20, 3 );
		add_filter( 'woocommerce_product_addons_save_data', array( $this, 'save_addon_options' ), 20, 2 );
		add_filter( 'woocommerce_product_addons_import_data', array( $this, 'save_addon_options' ), 20, 3 );
		add_filter( 'woocommerce_product_addon_cart_item_data', array( $this, 'addon_price' ), 20, 4 );
		add_filter( 'woocommerce_bookings_calculated_booking_cost_success_output', array( $this, 'filter_output_cost' ), 10, 3 );
		add_filter( 'woocommerce_product_addons_adjust_price', array( $this, 'adjust_price' ), 20, 2 );
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data_adjust_booking_cost' ), 30 ); // Must happen after bookings AND addons hooks.
	}

	/**
	 * Show grand total or not?
	 * @param  bool $show_grand_total
	 * @param  object $product
	 * @return bool
	 */
	public function addons_show_grand_total( $show_grand_total, $product ) {
		if ( $product->is_type( 'booking' ) && defined( 'WC_PRODUCT_ADDONS_VERSION' ) && version_compare( WC_PRODUCT_ADDONS_VERSION, '3.0', '<' ) ) {
			$show_grand_total = false;
		}
		return $show_grand_total;
	}

	/**
	 * Show options
	 */
	public function addon_options( $post, $addon, $loop ) {
		$css_classes = 'show_if_booking';

		if ( is_object( $post ) ) {
			$product = wc_get_product( $post->ID );
			if ( ! $product->is_type( 'booking' ) ) {
				$css_classes .= ' hide_initial_booking_addon_options';
			}
		} else {
			$css_classes .= ' hide_initial_booking_addon_options';
		}

		if ( defined( 'WC_PRODUCT_ADDONS_VERSION' ) && version_compare( WC_PRODUCT_ADDONS_VERSION, '3.0', '<' ) ) {
		?>
		<tr class="<?php echo esc_attr( $css_classes ); ?>">
			<td class="addon_wc_booking_person_qty_multiplier addon_required" width="50%">
				<label for="addon_wc_booking_person_qty_multiplier_<?php echo esc_attr( $loop ); ?>"><?php esc_html_e( 'Bookings: Multiply cost by person count', 'woocommerce-bookings' ); ?></label>
				<input type="checkbox" id="addon_wc_booking_person_qty_multiplier_<?php echo esc_attr( $loop ); ?>" name="addon_wc_booking_person_qty_multiplier[<?php echo esc_attr( $loop ); ?>]" <?php checked( ! empty( $addon['wc_booking_person_qty_multiplier'] ), true ); ?> />
			</td>
			<td class="addon_wc_booking_block_qty_multiplier addon_required" width="50%">
				<label for="addon_wc_booking_block_qty_multiplier_<?php echo esc_attr( $loop ); ?>"><?php esc_html_e( 'Bookings: Multiply cost by block count', 'woocommerce-bookings' ); ?></label>
				<input type="checkbox" id="addon_wc_booking_block_qty_multiplier_<?php echo esc_attr( $loop ); ?>" name="addon_wc_booking_block_qty_multiplier[<?php echo esc_attr( $loop ); ?>]" <?php checked( ! empty( $addon['wc_booking_block_qty_multiplier'] ), true ); ?> />
			</td>
		</tr>
		<?php
		} else {
		?>
		<div class="<?php echo esc_attr( $css_classes ); ?>">
			<div class="addon_wc_booking_person_qty_multiplier">
				<label for="addon_wc_booking_person_qty_multiplier_<?php echo esc_attr( $loop ); ?>">
				<input type="checkbox" id="addon_wc_booking_person_qty_multiplier_<?php echo esc_attr( $loop ); ?>" name="addon_wc_booking_person_qty_multiplier[<?php echo esc_attr( $loop ); ?>]" <?php checked( ! empty( $addon['wc_booking_person_qty_multiplier'] ), true ); ?> /> <?php esc_html_e( 'Bookings: Multiply cost by person count', 'woocommerce-bookings' ); ?>
				<?php echo wc_help_tip( __( 'Only applies to options which use quantity based prices.', 'woocommerce-bookings' ) ); ?>
				</label>
			</div>

			<div class="addon_wc_booking_block_qty_multiplier">
				<label for="addon_wc_booking_block_qty_multiplier_<?php echo esc_attr( $loop ); ?>">
				<input type="checkbox" id="addon_wc_booking_block_qty_multiplier_<?php echo esc_attr( $loop ); ?>" name="addon_wc_booking_block_qty_multiplier[<?php echo esc_attr( $loop ); ?>]" <?php checked( ! empty( $addon['wc_booking_block_qty_multiplier'] ), true ); ?> /> <?php esc_html_e( 'Bookings: Multiply cost by block count', 'woocommerce-bookings' ); ?>
				<?php echo wc_help_tip( __( 'Only applies to options which use quantity based prices.', 'woocommerce-bookings' ) ); ?>
				</label>
			</div>
		</div>
		<?php
		}
	}

	/**
	 * Save options
	 */
	public function save_addon_options( $data, $i ) {
		$data['wc_booking_person_qty_multiplier'] = isset( $_POST['addon_wc_booking_person_qty_multiplier'][ $i ] ) ? 1 : 0;
		$data['wc_booking_block_qty_multiplier']  = isset( $_POST['addon_wc_booking_block_qty_multiplier'][ $i ] ) ? 1 : 0;

		return $data;
	}

	/**
	 * Sanitize imported options.
	 *
	 * @param array  $sanitized Addon as sanatized by plugin.
	 * @param array  $addon     Original addon before sanitation.
	 * @param string $key       Addon index in array.
	 */
	public function import_addon_options( $sanitized, $addon, $key ) {
		$sanitized['wc_booking_person_qty_multiplier'] = ! empty( $addon['wc_booking_person_qty_multiplier'] ) ? 1 : 0;
		$sanitized['wc_booking_block_qty_multiplier']  = ! empty( $addon['wc_booking_block_qty_multiplier'] ) ? 1 : 0;

		return $sanitized;
	}

	/**
	 * Change addon price based on settings
	 * @return float
	 */
	public function addon_price( $cart_item_data, $addon, $product_id, $post_data ) {
		// Make sure we know if multipliers are enabled so adjust_booking_cost can see them.
		foreach ( $cart_item_data as $key => $data ) {
			if ( ! empty( $addon['wc_booking_person_qty_multiplier'] ) ) {
				$cart_item_data[ $key ]['wc_booking_person_qty_multiplier'] = 1;
			}
			if ( ! empty( $addon['wc_booking_block_qty_multiplier'] ) ) {
				$cart_item_data[ $key ]['wc_booking_block_qty_multiplier'] = 1;
			}
		}

		return $cart_item_data;
	}

	/**
	 * Don't adjust price for bookings since the booking form class adds the costs itself
	 * @return bool
	 */
	public function adjust_price( $bool, $cart_item ) {
		if ( $cart_item['data']->is_type( 'booking' ) ) {
			return false;
		}
		return $bool;
	}

	/**
	 * Adjust the final booking cost
	 *
	 * @param array $cart_item_data Extra data for cart item.
	 * @return array
	 */
	public function add_cart_item_data_adjust_booking_cost( $cart_item_data ) {

		if ( ! empty( $cart_item_data['addons'] ) && ! empty( $cart_item_data['booking'] ) ) {
			$addon_costs  = 0;
			$booking_data = $cart_item_data['booking'];

			// Save the price before price type calculations to be used later.
			$cart_item_data['addons_price_before_calc'] = $cart_item_data['booking']['_cost'];

			foreach ( $cart_item_data['addons'] as &$addon ) {
				$person_multiplier = 1;
				$duration_multipler = 1;

				$addon['price'] = ( ! empty( $addon['price'] ) ) ? $addon['price'] : 0;

				if ( ! empty( $addon['wc_booking_person_qty_multiplier'] ) && ! empty( $booking_data['_persons'] ) && array_sum( $booking_data['_persons'] ) ) {
					$person_multiplier = array_sum( $booking_data['_persons'] );
				}
				if ( ! empty( $addon['wc_booking_block_qty_multiplier'] ) && ! empty( $booking_data['_duration'] ) ) {
					$duration_multipler = (int) $booking_data['_duration'];
				}

				if ( defined( 'WC_PRODUCT_ADDONS_VERSION' ) && version_compare( WC_PRODUCT_ADDONS_VERSION, '3.0', '>=' ) ) {
					$price_type  = $addon['price_type'];
					$addon_price = $addon['price'];

					switch ( $price_type ) {
						case 'percentage_based':
							$addon_costs += (float) ( ( $booking_data['_cost'] * ( $addon_price / 100 ) ) * ( $person_multiplier * $duration_multipler ) );
							$addon['price'] = $addon['price'] * $person_multiplier * $duration_multipler;
							break;
						case 'flat_fee':
							$addon_costs += (float) $addon_price;
							break;
						default:
							$addon_costs += (float) ( $addon_price * $person_multiplier * $duration_multipler );
							$addon['price'] = $addon['price'] * $person_multiplier * $duration_multipler;
							break;
					}
				} else {
					$addon_costs += floatval( $addon['price'] ) * $person_multiplier * $duration_multipler;
				}
			}
			$cart_item_data['booking']['_cost'] += $addon_costs;
		}

		return $cart_item_data;
	}

	/**
	 * Filter the cost display of bookings after booking selection.
	 * This only filters on success.
	 *
	 * @since 1.11.4
	 * @param html $output
	 * @param string $display_price
	 * @param object $product
	 * @return JSON Filtered results
	 */
	public function filter_output_cost( $output, $display_price, $product ) {
		parse_str( $_POST['form'], $posted );

		$booking_data = wc_bookings_get_posted_data( $posted, $product );
		$cost         = WC_Bookings_Cost_Calculation::calculate_booking_cost( $booking_data, $product );

		wp_send_json(
			array(
				'result'    => 'SUCCESS',
				'html'      => $output,
				'raw_price' => (float) wc_get_price_to_display( $product, array( 'price' => $cost ) ),
			)
		);
	}
}
