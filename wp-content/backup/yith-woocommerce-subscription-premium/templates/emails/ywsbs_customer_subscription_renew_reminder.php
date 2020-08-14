<?php
/**
 * This is the email sent to the customer when his subscription is expired
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

<p><?php printf( esc_html( __( 'Subscription #%1$d is going to renew on %2$s', 'yith-woocommerce-subscription' ) ), esc_html( $subscription->id ), esc_html( date_i18n( wc_date_format(), $next_activity_date ) ) ); ?></p>

<h2><a class="link" href="<?php echo esc_url( $subscription->get_view_subscription_url() ); ?>"><?php printf( esc_html( __( 'Subscription #%s', 'yith-woocommerce-subscription' ) ), esc_html( $subscription->id ) ); ?></a></h2>


<?php
wc_get_template( 'emails/email-subscription-detail-table.php', array( 'subscription' => $subscription ), '', YITH_YWSBS_TEMPLATE_PATH . '/' );
?>

<?php
wc_get_template( 'emails/email-subscription-customer-details.php', array( 'subscription' => $subscription ), '', YITH_YWSBS_TEMPLATE_PATH . '/' );
?>

<?php
do_action( 'woocommerce_email_footer', $email );
