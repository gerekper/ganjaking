<?php
/**
 * People Tab in WC Product Panel
 *
 * @var WC_Product_Booking|false $booking_product The booking product or false (if it's not a booking product)
 * @var string                   $prod_type       The booking product type
 * @var int                      $post_id         The post ID
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.
?>

<div class="yith-wcbk-product-metabox-options-panel yith-plugin-ui options_group show_if_<?php echo esc_attr( $prod_type ); ?>">

	<div class="yith-wcbk-settings-section">
		<div class="yith-wcbk-settings-section__title">
			<h3><?php esc_html_e( 'People Settings', 'yith-booking-for-woocommerce' ); ?></h3>
		</div>
		<div class="yith-wcbk-settings-section__content">
			<?php
			yith_wcbk_form_field(
				array(
					'class'  => '_yith_booking_has_persons_field',
					'title'  => __( 'Enable people option', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Enable this option to assign people to this product.', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						'yith-field' => true,
						'type'       => 'onoff',
						'value'      => wc_bool_to_string( $booking_product ? $booking_product->get_enable_people( 'edit' ) : false ),
						'id'         => '_yith_booking_has_persons',
						'name'       => '_yith_booking_has_persons',
					),
				)
			);

			yith_wcbk_form_field(
				array(
					'class'  => 'bk_show_if_booking_has_persons yith_booking_multi_fields align-baseline',
					'title'  => __( 'Min/Max number of people', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Set the minimum and the maximum number of people per booking.', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						array(
							'type'   => 'section',
							'class'  => 'yith-wcbk-field-with-top-label',
							'fields' => array(
								array(
									'type'  => 'label',
									'value' => __( 'Min', 'yith-booking-for-woocommerce' ),
								),
								array(
									'yith-field'        => true,
									'type'              => 'number',
									'value'             => $booking_product ? $booking_product->get_minimum_number_of_people( 'edit' ) : 1,
									'id'                => '_yith_booking_min_persons',
									'name'              => '_yith_booking_min_persons',
									'class'             => 'yith-wcbk-mini-field',
									'custom_attributes' => array(
										'step' => 1,
										'min'  => 1,
									),
								),
							),
						),
						array(
							'type'   => 'section',
							'class'  => 'yith-wcbk-field-with-top-label',
							'fields' => array(
								array(
									'type'  => 'label',
									'value' => __( 'Max', 'yith-booking-for-woocommerce' ),
								),
								array(
									'yith-field'        => true,
									'type'              => 'number',
									'value'             => $booking_product ? $booking_product->get_maximum_number_of_people( 'edit' ) : 0,
									'id'                => '_yith_booking_max_persons',
									'name'              => '_yith_booking_max_persons',
									'class'             => 'yith-wcbk-mini-field',
									'custom_attributes' => array(
										'step' => 1,
										'min'  => 0,
									),
								),
							),
						),
					),
				)
			);

			yith_wcbk_form_field(
				array(
					'class'  => '_yith_booking_count_persons_as_bookings_field bk_show_if_booking_has_persons',
					'title'  => __( 'Count people as separated bookings', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'If you need to book only for a fixed number of people and you set the number in "Max bookings per unit" option, check this option to count each person as a separate booking.', 'yith-booking-for-woocommerce' ) .
								'<br />' .
								__( 'Example: you set 10 people for a Yoga class and you set 10 in "Max bookings per unit" option; every person will be a separate booking until number 10 is reached.', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						'yith-field' => true,
						'type'       => 'onoff',
						'value'      => wc_bool_to_string( $booking_product ? $booking_product->get_count_people_as_separate_bookings( 'edit' ) : false ),
						'id'         => '_yith_booking_count_persons_as_bookings',
						'name'       => '_yith_booking_count_persons_as_bookings',
					),
				)
			);

			yith_wcbk_form_field(
				array(
					'class'  => '_yith_booking_enable_person_types_field bk_show_if_booking_has_persons',
					'title'  => __( 'Enable people types', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'You can use types, for example, to set different prices for different people. Example: Adult 30$, Child 10$.', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						'yith-field' => true,
						'type'       => 'onoff',
						'value'      => wc_bool_to_string( $booking_product ? $booking_product->get_enable_people_types( 'edit' ) : false ),
						'id'         => '_yith_booking_enable_person_types',
						'name'       => '_yith_booking_enable_person_types',
					),
				)
			);
			?>
		</div>
	</div>
	<?php
	$people_types = $booking_product ? $booking_product->get_people_types( 'edit' ) : array();
	yith_wcbk_get_module_view( 'people', 'product-tabs/people/people-types.php', compact( 'people_types' ) );
	?>
</div>
