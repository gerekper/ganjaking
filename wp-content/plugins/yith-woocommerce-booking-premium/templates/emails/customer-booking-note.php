<?php
/**
 * Customer booking note email.
 *
 * @var YITH_WCBK_Booking $booking        The booking.
 * @var string            $email_heading  The heading.
 * @var WC_Email          $email          The email.
 * @var bool              $sent_to_admin  Is this sent to admin?
 * @var bool              $plain_text     Is this plain?
 * @var string            $custom_message The email message including booking details through {booking_details} placeholder.
 *
 * @package YITH\Booking
 */

defined( 'ABSPATH' ) || exit;
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php echo wp_kses_post( wpautop( wptexturize( $custom_message ) ) ); ?>

<?php
do_action( 'woocommerce_email_footer', $email );
