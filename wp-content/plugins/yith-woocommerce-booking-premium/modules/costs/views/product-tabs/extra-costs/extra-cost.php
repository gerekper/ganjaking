<?php
/**
 * Template options in WC Product Panel
 *
 * @var YITH_WCBK_Product_Extra_Cost $extra_cost       one product extra cost
 * @var int                          $extra_cost_id    the id of the extra cost
 * @var string                       $extra_cost_title the title of the extra cost
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

$extra_cost_id     = $extra_cost_id ?? $extra_cost->get_id();
$extra_cost_title  = $extra_cost_title ?? $extra_cost->get_name();
$field_name_prefix = "_yith_booking_extra_costs[{$extra_cost_id}]";
?>

<tr class="yith-wcbk-extra-cost">
	<td class="extra-cost__name">
		<?php

		echo esc_html( $extra_cost_title );

		yith_wcbk_print_field(
			array(
				'type'  => 'hidden',
				'value' => $extra_cost_id,
				'name'  => $field_name_prefix . '[id]',
			)
		)
		?>
	</td>
	<td class="extra-cost__amount">
		<?php
		echo esc_html( get_woocommerce_currency_symbol() );
		yith_wcbk_print_field(
			array(
				'yith-field'                     => true,
				'type'                           => 'text',
				'value'                          => $extra_cost->get_cost(),
				'name'                           => $field_name_prefix . '[cost]',
				'id'                             => "_yith_booking_extra_cost_{$extra_cost_id}_cost",
				'class'                          => 'wc_input_price yith-wcbk-mini-field yith-wcbk-extra-cost__cost',
				'yith-wcbk-field-show-container' => false,
				'custom_attributes'              => array(
					'autocomplete' => 'off',
				),
			)
		)
		?>
	</td>
	<td class="extra-cost__multiply-by">
		<?php
		yith_wcbk_print_field(
			array(
				'type'   => 'section',
				'class'  => 'extra-cost__multiply-by__fields',
				'fields' => array(
					array(
						'type'   => 'section',
						'class'  => 'yith-wcbk-settings-checkbox-container bk_show_if_booking_has_persons',
						'fields' => array(
							array(
								'type'  => 'checkbox',
								'value' => wc_bool_to_string( $extra_cost->get_multiply_by_number_of_people() ),
								'name'  => $field_name_prefix . '[multiply_by_number_of_people]',
								'id'    => "_yith_booking_extra_cost_{$extra_cost_id}_multiply_fixed_base_fee_by_number_of_people",
							),
							array(
								'type'  => 'label',
								'value' => __( 'People', 'yith-booking-for-woocommerce' ),
								'for'   => "_yith_booking_extra_cost_{$extra_cost_id}_multiply_fixed_base_fee_by_number_of_people",
							),
						),
					),
					array(
						'type'   => 'section',
						'class'  => 'yith-wcbk-settings-checkbox-container',
						'fields' => array(
							array(
								'type'  => 'checkbox',
								'value' => wc_bool_to_string( $extra_cost->get_multiply_by_duration() ),
								'name'  => $field_name_prefix . '[multiply_by_duration]',
								'id'    => "_yith_booking_extra_cost_{$extra_cost_id}_multiply_by_duration",
							),
							array(
								'type'  => 'label',
								'value' => __( 'Duration', 'yith-booking-for-woocommerce' ),
								'for'   => "_yith_booking_extra_cost_{$extra_cost_id}_multiply_by_duration",
							),
						),
					),
				),
			)
		);
		?>
	</td>
	<td class="extra-cost__actions"></td>
</tr>
