<?php
/**
 * Services in Booking data meta-box
 *
 * @var YITH_WCBK_Booking $booking The booking.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Services
 */

defined( 'YITH_WCBK' ) || exit;

$service_ids = $booking->get_service_ids( 'edit' );
?>
<?php if ( $service_ids ) : ?>
	<h4><?php esc_html_e( 'Booking Services', 'yith-booking-for-woocommerce' ); ?></h4>
	<table class="yith-plugin-fw__classic-table yith-wcbk-booking-services-table widefat">
		<?php foreach ( $service_ids as $service_id ) : ?>
			<?php
			$service = yith_wcbk_get_service( $service_id );
			?>
			<?php if ( $service ) : ?>
				<tr class="yith-wcbk-booking-services-table__row">
					<td class="yith-wcbk-booking-services-table__row__label">
						<?php if ( $service->is_quantity_enabled() ) : ?>
							<label class="yith-wcbk-service-quantity__label"><?php echo esc_html( $service->get_name() ); ?></label>
						<?php else : ?>
							<?php echo esc_html( $service->get_name() ); ?>
						<?php endif ?>
					</td>
					<td class="yith-wcbk-booking-services-table__row__value">
						<?php if ( $service->is_quantity_enabled() ) : ?>
							<?php $quantity = $booking->get_service_quantity( $service_id ); ?>
							<input type="number" class="yith-wcbk-service-quantity"
									name="yith_booking_service_quantities[<?php echo esc_attr( $service_id ); ?>]"
									value="<?php echo esc_attr( $quantity ); ?>"
							/>
						<?php endif ?>
					</td>
				</tr>
			<?php endif ?>
		<?php endforeach; ?>
	</table>
<?php endif; ?>
