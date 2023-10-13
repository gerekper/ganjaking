<?php
/**
 * Month Field in booking form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/booking-form/dates/month.php
 *
 * @var WC_Product_Booking $product The booking product.
 * @var array              $date_info
 * @var array              $not_available_months
 *
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;

$all_day_booking = $product->is_full_day();

list( $min_date_timestamp, $max_date_timestamp ) = yith_plugin_fw_extract( $date_info, 'min_date_timestamp', 'max_date_timestamp' );

?>
<div class="yith-wcbk-form-section yith-wcbk-form-section-dates">

	<label for="yith-wcbk-booking-start-month-<?php echo esc_attr( $product->get_id() ); ?>" class='yith-wcbk-form-section__label yith-wcbk-booking-form__label'><?php esc_html_e( 'Start month', 'yith-booking-for-woocommerce' ); ?></label>
	<div class="yith-wcbk-form-section__content">
		<?php
		yith_wcbk_print_field(
			array(
				'type'        => 'month-picker',
				'id'          => 'yith-wcbk-booking-start-month-' . $product->get_id(),
				'name'        => 'from',
				'class'       => 'yith-wcbk-month-picker',
				'value_class' => 'yith-wcbk-booking-date yith-wcbk-booking-start-date',
				'options'     => array(
					'not_available_months' => $not_available_months,
					'min_date'             => $min_date_timestamp,
					'max_date'             => $max_date_timestamp,
				),
			)
		);
		?>
	</div>
</div>
