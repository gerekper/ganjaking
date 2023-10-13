<?php
/**
 * Booking form meta
 *
 * @var WC_Product_Booking $product
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Templates
 */

defined( 'ABSPATH' ) || exit;

$checkin  = $product->get_check_in();
$checkout = $product->get_check_out();
?>
<?php if ( ! ! $checkin || ! ! $checkout ) : ?>
	<div class="yith-booking-meta">
		<?php if ( ! ! $checkin ) : ?>
			<div class="yith-booking-checkin">
				<span class="yith-booking-meta__label"><?php echo esc_html( yith_wcbk_get_label( 'check-in' ) ); ?></span>
				<span class="yith-booking-meta__value"><?php echo esc_html( $checkin ); ?></span>
			</div>
		<?php endif; ?>

		<?php if ( ! ! $checkout ) : ?>
			<div class="yith-booking-checkout">
				<span class="yith-booking-meta__label"><?php echo esc_html( yith_wcbk_get_label( 'check-out' ) ); ?></span>
				<span class="yith-booking-meta__value"><?php echo esc_html( $checkout ); ?></span>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>
