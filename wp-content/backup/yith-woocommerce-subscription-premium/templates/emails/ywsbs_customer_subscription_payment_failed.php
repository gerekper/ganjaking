<?php
/**
 * This is the email sent to the customer when his subscription is in overdue
 *
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
do_action( 'woocommerce_email_header', $email_heading, $email );
?>


<p><?php esc_html_e( 'The payment to renew your subscription failed. Please, verify your available funds for the card specified during subscription and/or verify that your card is not expired.', 'yith-woocommerce-subscription' ); ?></p>



<h2><a class="link" href="<?php echo esc_url( $subscription->get_view_subscription_url() ); ?>"><?php printf( esc_html( __( 'Subscription #%s', 'yith-woocommerce-subscription' ) ), esc_html( $subscription->id ) ); ?></a> (<?php printf( '<time datetime="%s">%s</time>', esc_html( date_i18n( 'c', time() ) ), esc_html( date_i18n( wc_date_format(), time() ) ) ); ?>)</h2>

<?php
wc_get_template( 'emails/email-subscription-detail-table.php', array( 'subscription' => $subscription ), '', YITH_YWSBS_TEMPLATE_PATH . '/' );
?>

<?php
wc_get_template( 'emails/email-subscription-customer-details.php', array( 'subscription' => $subscription ), '', YITH_YWSBS_TEMPLATE_PATH . '/' );
?>

<?php
do_action( 'woocommerce_email_footer', $email );
