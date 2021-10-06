<?php
/**
 * The template for displaying the booking form and calendar with time blocks to customers.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce-bookings/booking-form/datetime-picker.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/bookings-templates/
 * @author  Automattic
 * @version 1.10.8
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

wp_enqueue_script( 'wc-bookings-booking-form' );
extract( $field );

$month_before_day = strpos( __( 'F j, Y', 'woocommerce-bookings' ), 'F' ) < strpos( __( 'F j, Y', 'woocommerce-bookings' ), 'j' );
?>
<fieldset class="wc-bookings-date-picker <?php echo esc_attr( implode( ' ', $class ) ); ?>">
	<p class="wc-bookings-date-picker-timezone-block" style="<?php echo 'no' === WC_Bookings_Timezone_Settings::get( 'display_timezone' ) ? 'display:none' : ''; ?>" align="center">
		<?php esc_html_e( 'Times are in ', 'woocommerce-bookings' ); ?>
		<span class="wc-bookings-date-picker-timezone"><?php echo esc_html( str_replace( '_', ' ', wc_booking_get_timezone_string() ) ); ?></span>
	</p>
	<div class="picker" data-display="<?php echo esc_attr( $display ); ?>" data-default-availability="<?php echo $default_availability ? 'true' : 'false'; ?>" data-min_date="<?php echo ! empty( $min_date_js ) ? esc_attr( $min_date_js ) : 0; ?>" data-max_date="<?php echo esc_attr( $max_date_js ); ?>" data-default_date="<?php echo esc_attr( $default_date ); ?>"></div>
	<?php
	if ( 'always_visible' !== $display ):
		?>
	<span class="label"><?php echo $label; ?></span>:
	<?php endif; ?>
	<div class="wc-bookings-date-picker-date-fields">
		<?php
		// woocommerce_bookings_mdy_format filter to choose between month/day/year and day/month/year format.
		if ( $month_before_day && apply_filters( 'woocommerce_bookings_mdy_format', true ) ) :
			?>
		<label>
			<input type="text" autocomplete="off" name="<?php echo esc_attr( $name ); ?>_month" placeholder="<?php esc_attr_e( 'mm', 'woocommerce-bookings' ); ?>" size="2" class="required_for_calculation booking_date_month" />
			<span><?php esc_html_e( 'Month', 'woocommerce-bookings' ); ?></span>
		</label> / <label>
			<input type="text" autocomplete="off" name="<?php echo esc_attr( $name ); ?>_day" placeholder="<?php esc_attr_e( 'dd', 'woocommerce-bookings' ); ?>" size="2" class="required_for_calculation booking_date_day" />
			<span><?php esc_html_e( 'Day', 'woocommerce-bookings' ); ?></span>
		</label>
		<?php else : ?>
		<label>
			<input type="text" autocomplete="off" name="<?php echo esc_attr( $name ); ?>_day" placeholder="<?php esc_attr_e( 'dd', 'woocommerce-bookings' ); ?>" size="2" class="required_for_calculation booking_date_day" />
			<span><?php esc_html_e( 'Day', 'woocommerce-bookings' ); ?></span>
		</label> / <label>
			<input type="text" autocomplete="off" name="<?php echo esc_attr( $name ); ?>_month" placeholder="<?php esc_attr_e( 'mm', 'woocommerce-bookings' ); ?>" size="2" class="required_for_calculation booking_date_month" />
			<span><?php esc_html_e( 'Month', 'woocommerce-bookings' ); ?></span>
		</label>
		<?php endif; ?>
		/ <label>
			<input type="text" autocomplete="off" value="<?php echo esc_attr( date( 'Y' ) ); ?>" name="<?php echo esc_attr( $name ); ?>_year" placeholder="<?php esc_attr_e( 'YYYY', 'woocommerce-bookings' ); ?>" size="4" class="required_for_calculation booking_date_year" />
			<span><?php esc_html_e( 'Year', 'woocommerce-bookings' ); ?></span>
		</label>
	</div>
</fieldset>
<div class="form-field form-field-wide">
	<?php
	if ( 'customer' === $product->get_duration_type() ) {
	?>
		<div class="block-picker wc-bookings-time-block-picker">
			<p><?php esc_html_e( 'Choose a date above to see available times.', 'woocommerce-bookings' ); ?></p>
		</div>
		<input type="hidden" name="wc_bookings_field_duration" value="" class="wc_bookings_field_duration" />
	<?php } else { ?>
		<ul class="block-picker">
			<li><?php esc_html_e( 'Choose a date above to see available times.', 'woocommerce-bookings' ); ?></li>
		</ul>
	<?php } ?>
	<input type="hidden" class="required_for_calculation" name="<?php echo esc_attr( $name ); ?>_time" id="<?php echo esc_attr( $name ); ?>" />
</div>
<div class="timezone-details" style="display: none;">
	<input type="hidden" name="<?php echo esc_attr( $name ); ?>_local_timezone" />
</div>
