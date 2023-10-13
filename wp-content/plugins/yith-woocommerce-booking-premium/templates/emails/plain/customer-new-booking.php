<?php
/**
 * Customer new booking email - plain.
 *
 * @var YITH_WCBK_Booking $booking        The booking.
 * @var string            $email_heading  The heading.
 * @var WC_Email          $email          The email.
 * @var bool              $sent_to_admin  Is this sent to admin?
 * @var bool              $plain_text     Is this plain?
 * @var string            $custom_message The email message including booking details through {booking_details} placeholder.
 *
 * @package YITH\Booking\Templates\Emails
 */

defined( 'YITH_WCBK' ) || exit;

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html( wp_strip_all_tags( wptexturize( $custom_message ) ) ) . "\n\n";

echo esc_html( wp_strip_all_tags( wptexturize( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) ) ) );
