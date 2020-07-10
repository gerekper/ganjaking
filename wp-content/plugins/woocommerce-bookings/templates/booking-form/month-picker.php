<?php
/**
 * The template used for the month picker on the booking form.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce-bookings/booking-form/month-picker.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/bookings-templates/
 * @author  Automattic
 * @version 1.8.0
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

wp_enqueue_script( 'wc-bookings-booking-form' );
extract( $field );

$fully_booked_months = array_keys( $fully_booked_months );
?>
<div class="form-field form-field-wide">
	<div class="picker" data-is_range_picker_enabled="<?php echo $is_range_picker_enabled ? 1 : 0; ?>"></div>
	<?php
	if ( 'always_visible' !== $display ):
		?>
	<span class="label"><?php echo $label; ?></span>:
	<?php endif; ?>
	<ul class="block-picker">
		<?php
		foreach ( $blocks as $block ) {
			$fully_booked_class = in_array( date( 'Y-n', $block ), $fully_booked_months ) ? 'fully_booked' : '';
			echo '<li class="' . esc_attr( $fully_booked_class ) . '" data-block="' . esc_attr( date( 'Ym', $block ) ) . '"><a href="#" data-value="' . esc_attr( date( 'Y-m', $block ) ) . '">' . esc_html( date_i18n( 'M Y', $block ) ) . '</a></li>';
		}
		?>
	</ul>
	<input type="hidden" name="<?php echo esc_attr( $name ); ?>_yearmonth" id="<?php echo esc_attr( $name ); ?>" />
</div>

