<?php
/**
 * View for Google Calendar Actions
 *
 * @var array                     $actions         The actions.
 * @var YITH_WCBK_Google_Calendar $google_calendar Google Calendar object.
 *
 * @package YITH\Booking\Views\GoogleCalendar
 */

defined( 'YITH_WCBK' ) || exit();
?>
<div class='yith-wcbk-google-calendar-actions__container'>
	<div class='yith-wcbk-google-calendar-actions'>
		<?php
		foreach ( $actions as $_action ) {
			$google_calendar->get_view( $_action . '-form.php' );
		}
		?>
	</div>
</div>
