<?php
/**
 * Discounts in "Costs" product tabs
 *
 * @var WC_Product_Booking|false $booking_product The booking product or false (if it's not a booking product)
 *
 * @package YITH\Booking\Modules\Costs
 */

defined( 'YITH_WCBK' ) || exit;
?>

<div class="yith-wcbk-settings-section">
	<div class="yith-wcbk-settings-section__title">
		<h3><?php esc_html_e( 'Discounts', 'yith-booking-for-woocommerce' ); ?></h3>
		<span class="yith-wcbk-settings-section__toggle"><span class="dashicons dashicons-arrow-up-alt2"></span></span>
	</div>
	<div class="yith-wcbk-settings-section__content">
		<?php
		yith_wcbk_form_field(
			array(
				'class'  => '_yith_booking_costs_weekly_discount bk_show_if_customer_one_day',
				'title'  => __( 'Weekly discount', 'yith-booking-for-woocommerce' ),
				'desc'   => __( 'Encourage users to book longer stays by offering weekly discounts.', 'yith-booking-for-woocommerce' ),
				'fields' => array(
					array(
						'type'              => 'percentage',
						'value'             => $booking_product && $booking_product->get_weekly_discount( 'edit' ) ? $booking_product->get_weekly_discount( 'edit' ) : '',
						'id'                => '_yith_booking_weekly_discount',
						'class'             => 'yith-wcbk-mini-field',
						'custom_attributes' => array(
							'step' => 'any',
							'min'  => 0,
							'max'  => 100,
						),
					),
				),
			)
		);

		yith_wcbk_form_field(
			array(
				'class'  => '_yith_booking_costs_monthly_discount bk_show_if_customer_one_day',
				'title'  => __( 'Monthly discount', 'yith-booking-for-woocommerce' ),
				'desc'   => __( 'Encourage users to book longer stays by offering monthly discounts.', 'yith-booking-for-woocommerce' ),
				'fields' => array(
					array(
						'type'              => 'percentage',
						'value'             => $booking_product && $booking_product->get_monthly_discount( 'edit' ) ? $booking_product->get_monthly_discount( 'edit' ) : '',
						'id'                => '_yith_booking_monthly_discount',
						'class'             => 'yith-wcbk-mini-field',
						'custom_attributes' => array(
							'step' => 'any',
							'min'  => 0,
							'max'  => 100,
						),
					),
				),
			)
		);

		yith_wcbk_form_field(
			array(
				'class'  => '_yith_booking_costs_last_minute_discounts',
				'title'  => __( 'Last minute discount', 'yith-booking-for-woocommerce' ),
				'desc'   => __( 'Encourage users to book by offering last minute discounts.', 'yith-booking-for-woocommerce' ),
				'fields' => array(
					array(
						'yith-field'                     => true,
						'type'                           => 'html',
						'yith-wcbk-field-show-container' => false,
						'html'                           => sprintf(
						// translators: 1. percentage discount field; 2. numeric field. Ex: Discount: 10% for bookings placed 10 days before the start date.
							esc_html__( '%1$s for bookings placed %2$s days before the start date.', 'yith-booking-for-woocommerce' ),
							yith_wcbk_print_field(
								array(
									'type'              => 'percentage',
									'value'             => $booking_product && $booking_product->get_last_minute_discount( 'edit' ) ? $booking_product->get_last_minute_discount( 'edit' ) : '',
									'id'                => '_yith_booking_last_minute_discount',
									'class'             => 'yith-wcbk-mini-field',
									'custom_attributes' => array(
										'step' => 'any',
										'min'  => 0,
										'max'  => 100,
									),
								),
								false
							),
							yith_wcbk_print_field(
								array(
									'type'              => 'number',
									'value'             => $booking_product ? $booking_product->get_last_minute_discount_days_before_arrival( 'edit' ) : 0,
									'id'                => '_yith_booking_last_minute_discount_days_before_arrival',
									'class'             => 'mini',
									'custom_attributes' => array(
										'step' => 1,
										'min'  => 0,
									),
								),
								false
							)
						),
					),
				),
			)
		);
		?>
	</div>
</div>
