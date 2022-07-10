<?php
/**
 * Default email content template
 *
 * @package woocommerce-box-office
 */

echo esc_html_x( 'Hi there!', 'default-email-content', 'woocommerce-box-office' );
?>


<?php
echo esc_html_x( 'Thank you so much for purchasing a ticket and hope to see you soon at our event. You can edit your information at any time before the event, by visiting the following link:', 'default-email-content', 'woocommerce-box-office' );
?>


{ticket_link}

<?php
echo esc_html_x( 'Ticket ID:', 'default-email-content', 'woocommerce-box-office' ) . ' {ticket_id}';
?>


<?php
echo esc_html_x( 'Let us know if you have any questions!', 'default-email-content', 'woocommerce-box-office' );
