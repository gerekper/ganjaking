<?php
/**
 * Booking PDF Template - Footer
 *
 * @var YITH_WCBK_Booking $booking  The booking.
 * @var string            $footer   Footer text.
 * @var bool              $is_admin Is admin flag.
 *
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;

?>
<div class="footer">
	<?php if ( ! ! $footer ) : ?>
		<div class="footer-content"><?php echo wp_kses_post( $footer ); ?></div>
	<?php endif; ?>
</div>
