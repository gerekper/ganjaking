<?php
/**
 * Costs Tab in WC Product Panel
 *
 * @var WC_Product_Booking|false $booking_product The booking product or false (if it's not a booking product)
 * @var string                   $prod_type       The booking product type
 * @var int                      $post_id         The post ID
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

$currency       = '<span class="yith-wcbk-field-currency">' . esc_html( get_woocommerce_currency_symbol() ) . '</span>';
$currency_field = array(
	'yith-field'                     => true,
	'type'                           => 'html',
	'html'                           => $currency,
	'yith-wcbk-field-show-container' => false,
);

?>
<div class="yith-wcbk-product-metabox-options-panel yith-plugin-ui options_group show_if_<?php echo esc_attr( $prod_type ); ?>">
	<div class="yith-wcbk-settings-section">
		<div class="yith-wcbk-settings-section__title">
			<h3><?php esc_html_e( 'Standard Prices', 'yith-booking-for-woocommerce' ); ?></h3>
		</div>
		<div class="yith-wcbk-settings-section__content">
			<?php
			yith_wcbk_form_field(
				array(
					'class'  => '_yith_booking_costs_block_cost_field',
					// translators: %s is the duration; ex: Base price for 1 day.
					'title'  => sprintf( __( 'Base price for %s', 'yith-booking-for-woocommerce' ), yith_wcbk_product_metabox_dynamic_duration() ),
					'desc'   => __( 'Set your booking price here.', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						array(
							'yith-field'        => true,
							'type'              => 'text',
							'value'             => wc_format_localized_price( $booking_product ? $booking_product->get_base_price( 'edit' ) : '' ),
							'id'                => '_yith_booking_block_cost',
							'name'              => '_yith_booking_block_cost',
							'class'             => 'wc_input_price yith-wcbk-mini-field',
							'custom_attributes' => array(
								'autocomplete' => 'off',
							),
						),
						$currency_field,
						array(
							'type'   => 'section',
							'class'  => '_yith_booking_multiply_base_price_by_number_of_people_field yith-wcbk-settings-checkbox-container bk_show_if_booking_has_persons',
							'fields' => array(
								array(
									'type'  => 'checkbox',
									'value' => wc_bool_to_string( $booking_product ? $booking_product->get_multiply_base_price_by_number_of_people( 'edit' ) : false ),
									'id'    => '_yith_booking_multiply_base_price_by_number_of_people',
									'name'  => '_yith_booking_multiply_base_price_by_number_of_people',
								),
								array(
									'type'  => 'label',
									'value' => __( 'Multiply by the number of people', 'yith-booking-for-woocommerce' ),
									'for'   => '_yith_booking_multiply_base_price_by_number_of_people',
								),
							),
						),
					),
				)
			);

			yith_wcbk_form_field(
				array(
					'class'  => '_yith_booking_extra_price_per_person_field yith-wcbk-show-conditional bk_show_if_booking_has_persons',
					'data'   => array(
						'field-id' => '_yith_booking_multiply_base_price_by_number_of_people',
						'value'    => 'no',
					),
					'title'  => __( 'Extra price', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Here you can set an extra cost for each person added to the specified number.', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						array(
							'yith-field'        => true,
							'type'              => 'text',
							'value'             => wc_format_localized_price( $booking_product ? $booking_product->get_extra_price_per_person( 'edit' ) : '' ),
							'id'                => '_yith_booking_extra_price_per_person',
							'name'              => '_yith_booking_extra_price_per_person',
							'class'             => 'wc_input_price yith-wcbk-mini-field',
							'custom_attributes' => array(
								'autocomplete' => 'off',
							),
						),
						$currency_field,
						array(
							'yith-field'        => true,
							'type'              => 'number',
							'title'             => __( 'for every person added to', 'yith-booking-for-woocommerce' ),
							'value'             => $booking_product ? $booking_product->get_extra_price_per_person_greater_than( 'edit' ) : 0,
							'id'                => '_yith_booking_extra_price_per_person_greater_than',
							'name'              => '_yith_booking_extra_price_per_person_greater_than',
							'class'             => 'mini',
							'custom_attributes' => array(
								'step' => 1,
								'min'  => 0,
							),
						),
					),
				)
			);

			yith_wcbk_form_field(
				array(
					'class'  => '_yith_booking_costs_base_cost_field',
					'title'  => __( 'Fixed base fee', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Here you can set a fixed base fee, that will not be multiplied by the booking duration.', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						array(
							'yith-field'        => true,
							'type'              => 'text',
							'value'             => wc_format_localized_price( $booking_product ? $booking_product->get_fixed_base_fee( 'edit' ) : '' ),
							'id'                => '_yith_booking_base_cost',
							'name'              => '_yith_booking_base_cost',
							'class'             => 'wc_input_price yith-wcbk-mini-field yith-wcbk-extra-cost__cost',
							'custom_attributes' => array(
								'autocomplete' => 'off',
							),
						),
						$currency_field,
						array(
							'type'   => 'section',
							'class'  => '_yith_booking_multiply_fixed_base_fee_by_number_of_people_field yith-wcbk-settings-checkbox-container bk_show_if_booking_has_persons',
							'fields' => array(
								array(
									'type'  => 'checkbox',
									'value' => wc_bool_to_string( $booking_product ? $booking_product->get_multiply_fixed_base_fee_by_number_of_people( 'edit' ) : false ),
									'id'    => '_yith_booking_multiply_fixed_base_fee_by_number_of_people',
									'name'  => '_yith_booking_multiply_fixed_base_fee_by_number_of_people',
								),
								array(
									'type'  => 'label',
									'value' => __( 'Multiply by the number of people', 'yith-booking-for-woocommerce' ),
									'for'   => '_yith_booking_multiply_fixed_base_fee_by_number_of_people',
								),
							),
						),
					),
				)
			);
			?>
		</div>
	</div>

	<?php
	/**
	 * DO_ACTION: yith_wcbk_costs_product_tab_after_standard_prices
	 * Hook to output something in the "costs" tab of the bookable product edit page after standard prices.
	 *
	 * @param WC_Product_Booking|false $booking_product The bookable product.
	 */
	do_action( 'yith_wcbk_costs_product_tab_after_standard_prices', $booking_product );
	?>

	<div class="yith-wcbk-settings-section">
		<div class="yith-wcbk-settings-section__title">
			<h3><?php esc_html_e( 'Advanced Price Rules', 'yith-booking-for-woocommerce' ); ?></h3>
			<div class="yith-wcbk-price-rules__expand-collapse">
				<span class="yith-wcbk-price-rules__expand"><?php esc_html_e( 'Expand all', 'yith-booking-for-woocommerce' ); ?></span>
				<span class="yith-wcbk-price-rules__collapse"><?php esc_html_e( 'Collapse all', 'yith-booking-for-woocommerce' ); ?></span>
			</div>
		</div>
		<div class="yith-wcbk-settings-section__content">
			<div class="yith-wcbk-settings-section__description"><?php esc_html_e( 'You can create advanced rules to set different prices for specific conditions (dates, months, durations).', 'yith-booking-for-woocommerce' ); ?></div>
			<?php
			$price_rules = $booking_product ? $booking_product->get_price_rules() : array();
			$field_name  = '_yith_booking_costs_range';
			yith_wcbk_get_view( 'product-tabs/utility/html-price-rules.php', compact( 'price_rules', 'field_name' ) );
			?>
		</div>
	</div>
</div>
