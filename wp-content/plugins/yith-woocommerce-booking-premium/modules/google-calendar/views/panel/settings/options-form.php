<?php
/**
 * View for Google Calendar Options
 *
 * @var array $calendars The calendars.
 * @var array $options   The options.
 *
 * @package YITH\Booking\Views\GoogleCalendar
 */

defined( 'YITH_WCBK' ) || exit();
?>
<div class="yith-wcbk-google-calendar-form">
	<table>
		<tr>
			<th><?php esc_html_e( 'Select a calendar', 'yith-booking-for-woocommerce' ); ?></th>
			<td>
				<select name='yith-wcbk-gcal-options[calendar-id]'>
					<option value=''><?php esc_html_e( '- Disabled -', 'yith-booking-for-woocommerce' ); ?></option>
					<?php foreach ( $calendars as $calendar ) : ?>
						<?php
						$selected = selected( $calendar->id === $options['calendar-id'], true, false );
						?>

						<option value='<?php echo esc_attr( $calendar->id ); ?>'
							<?php selected( $calendar->id === $options['calendar-id'], true ); ?>
						><?php echo esc_html( $calendar->summary ); ?></option>";
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
	</table>
</div>
