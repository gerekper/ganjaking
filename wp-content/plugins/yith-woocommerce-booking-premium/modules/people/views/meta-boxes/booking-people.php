<?php
/**
 * People in Booking data meta-box
 *
 * @var YITH_WCBK_Booking $booking The booking.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\People
 */

defined( 'YITH_WCBK' ) || exit;

?>

<?php if ( $booking->has_persons() ) : ?>

	<h4><?php esc_html_e( 'Booking People', 'yith-booking-for-woocommerce' ); ?></h4>

	<div class="form-field form-field-wide"><label><?php esc_html_e( 'People', 'yith-booking-for-woocommerce' ); ?>:</label>
		<?php
		if ( $booking->has_person_types() ) {
			echo esc_html( $booking->get_persons( 'edit' ) );
		} else {
			?>
			<input type="number" name="yith_booking_persons" id="yith_booking_persons" maxlength="10" value="<?php echo esc_attr( $booking->get_persons( 'edit' ) ); ?>"/>
			<?php
		}
		?>
	</div>

	<?php if ( $booking->has_person_types() ) : ?>
		<?php foreach ( $booking->get_person_types( 'edit' ) as $person_type ) : ?>
			<?php
			$person_type_id     = absint( $person_type['id'] );
			$person_type_title  = get_the_title( $person_type_id );
			$person_type_title  = ! ! $person_type_title ? $person_type_title : $person_type['title'];
			$person_type_number = absint( $person_type['number'] );
			?>
			<div class="form-field form-field-wide"><label><?php echo esc_html( $person_type_title ); ?>:</label>
				<input type="number" class="half-width" name="yith_booking_person_type[<?php echo esc_attr( $person_type_id ); ?>]"
						maxlength="10" value="<?php echo esc_attr( $person_type_number ); ?>"/>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>

<?php endif; ?>
