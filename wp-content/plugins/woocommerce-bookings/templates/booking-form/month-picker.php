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
 * @version 1.8.0
 * @since   1.0.0
 * @package WooCommerce Bookings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

wp_enqueue_script( 'wc-bookings-booking-form' );
extract( $field ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

$current_resource = '';
if ( $product->has_resources() ) {
	if ( $product->is_resource_assignment_type( 'customer' ) ) {
		// Get currently selected resource.
		$resources        = $product->get_resource_ids();
		$current_resource = $resources[0] ?? '';
	} else {
		// Get total resources.
		$resources      = $product->get_resource_ids();
		$total_resource = count( $resources );
	}
}

?>
<div class="form-field form-field-wide current_resource_<?php echo esc_attr( $current_resource ); ?>">
	<div class="picker" data-is_range_picker_enabled="<?php echo $is_range_picker_enabled ? 1 : 0; ?>"></div>
	<?php
	if ( 'always_visible' !== $display ) :
		?>
	<span class="label"><?php echo esc_html( $label ); ?></span>:
	<?php endif; ?>
	<ul class="block-picker month-picker">
		<?php
		foreach ( $blocks as $block => $available ) {
			$fully_booked_class = '';
			$unavailable_class  = 'unavailable' === $available ? ' unavailable' : '';

			$month_year = date( 'Y-n', $block );
			if ( $product->has_resources() ) {
				if ( $product->is_resource_assignment_type( 'customer' ) ) {
					// Mark 'full_booked' only for the current (selected) resource.
					if ( isset( $fully_booked_months[ $month_year ][ $current_resource ] ) ) {
						$fully_booked_class = 'fully_booked';
					}
				} elseif ( isset( $fully_booked_months[ $month_year ] )
						&& count( $fully_booked_months[ $month_year ] ) === $total_resource ) {
					// Mark 'full_booked' only when all resources are out.
					$fully_booked_class = 'fully_booked';
				}
			} else {
				// Mark 'full_booked' according to the product qty. (when no resources are allocated).
				$fully_booked_class = isset( $fully_booked_months[ $month_year ][0] ) ? 'fully_booked' : '';
			}

			$title_attribute = __( 'This month is available', 'woocommerce-bookings' );
			if ( ! empty( $fully_booked_class ) ) {
				$title_attribute = __( 'This month is fully booked and unavailable', 'woocommerce-bookings' );
			} elseif ( ! empty( $unavailable_class ) ) {
				$title_attribute = __( 'This month is unavailable', 'woocommerce-bookings' );
			}

			echo '<li class="' . esc_attr( $fully_booked_class ) . esc_attr( $unavailable_class ) . '" data-block="' . esc_attr( date( 'Ym', $block ) ) . '" title="' . esc_attr( $title_attribute ) . '"><a href="#" data-value="' . esc_attr( date( 'Y-m', $block ) ) . '">' . esc_html( date_i18n( 'M Y', $block ) ) . '</a></li>';
		}
		?>
	</ul>
	<input type="hidden" name="<?php echo esc_attr( $name ); ?>_yearmonth" id="<?php echo esc_attr( $name ); ?>" />
</div>
