<?php
/**
 * The template for displaying the list of bookings in the summary for customers.
 * It is used in:
 * - templates/order/booking-display.php
 * - templates/order/admin/booking-display.php
 * It will display in four places:
 * - After checkout,
 * - In the order confirmation email, and
 * - When customer reviews order in My Account > Orders,
 * - When reviewing a customer order in the admin area.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce-bookings/order/booking-summary-list.php.
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
 * @since   1.10.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
	<ul class="wc-booking-summary-list">
		<li>
			<?php echo esc_html( apply_filters( 'wc_bookings_summary_list_date', $booking_date, $booking->get_start(), $booking->get_end() ) ); ?>
			<?php
			if ( wc_should_convert_timezone( $booking ) ) :
				/* translators: %s: timezone name */
				echo esc_html( sprintf( __( 'in timezone: %s', 'woocommerce-bookings' ), $booking_timezone ) );
			endif;
			?>
		</li>

		<?php if ( $resource ) : ?>
			<li>
			<?php
			/* translators: 1: label 2: resource name */
			echo esc_html( sprintf( __( '%1$s: %2$s', 'woocommerce-bookings' ), $label, $resource->get_name() ) );
			?>
			</li>
		<?php endif; ?>

		<?php
		if ( $product && $product->has_persons() ) {
			if ( $product->has_person_types() ) {
				$person_types  = $product->get_person_types();
				$person_counts = $booking->get_person_counts();

				if ( ! empty( $person_types ) && is_array( $person_types ) ) {
					foreach ( $person_types as $person_type ) {

						if ( empty( $person_counts[ $person_type->get_id() ] ) ) {
							continue;
						}

						?>
						<li><?php echo esc_html( sprintf( '%s: %d', $person_type->get_name(), $person_counts[ $person_type->get_id() ] ) ); ?></li>
						<?php
					}
				}
			} else {
				?>
				<li>
				<?php
				/* translators: 1: person count */
				echo esc_html( sprintf( __( '%d Persons', 'woocommerce-bookings' ), array_sum( $booking->get_person_counts() ) ) );
				?>
				</li>
				<?php
			}
		}
		?>
	</ul>
