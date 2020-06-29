<?php
/**
 * Admin subscription delivery note email
 *
 * @package WC_OD/Templates/Emails
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php esc_html_e( 'A delivery note has been added to the subscription:', 'woocommerce-order-delivery' ); ?></p>

<blockquote><?php echo wpautop( wptexturize( $note ) ) ?></blockquote>

<p><?php esc_html_e( 'For your reference, the subscription details are shown below.', 'woocommerce-order-delivery' ); ?></p>

<?php

/*
 * @hooked WC_Subscriptions_Email::order_details() Shows the order details table.
 */
do_action( 'woocommerce_subscriptions_email_order_details', $subscription, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $subscription, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $subscription, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
