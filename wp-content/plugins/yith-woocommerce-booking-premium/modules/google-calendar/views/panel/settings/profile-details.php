<?php
/**
 * Google Calendar profile.
 *
 * @var string $name    The user name.
 * @var string $picture User avatar URL.
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

?>
<div class='yith-wcbk-google-calendar-profile'>

	<img class='yith-wcbk-google-calendar-profile__image' src='<?php echo esc_url( $picture ); ?>'/>

	<div class='yith-wcbk-google-calendar-profile__title'>
		<?php echo esc_html( $name ); ?>
	</div>

	<div class='clear'></div>
</div>

