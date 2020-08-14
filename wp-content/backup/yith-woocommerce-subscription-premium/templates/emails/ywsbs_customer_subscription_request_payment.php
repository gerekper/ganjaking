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


<p><?php printf( esc_html( __( 'Your recent subscription renewal order on %s is late for payment.', 'yith-woocommerce-subscription' ) ), wp_kses_post( get_option( 'blogname' ) ) ); ?></p>

<p><?php printf( wp_kses_post( __( 'If you do not pay it by <strong>%1$s</strong>, your subscription #%2$d will be <strong>%3$s</strong>.', 'yith-woocommerce-subscription' ) ), esc_html( date_i18n( wc_date_format(), $next_activity_date ) ), esc_html( $subscription->id ), esc_html( $next_activity ) ); ?></p>

<p><?php esc_html_e( 'To pay for this order, please, click on the following link:', 'yith-woocommerce-subscription' ); ?></p>
<p style="padding:10px 0;"><a style="background-color:#eee;padding:10px 15px;text-decoration:none;" href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>"><?php esc_html_e( 'pay now', 'yith-woocommerce-subscription' ); ?></a></p>

<h2><a class="link" href="<?php echo esc_url( $subscription->get_view_subscription_url() ); ?>"><?php printf( esc_html( __( 'Subscription #%s', 'yith-woocommerce-subscription' ) ), esc_html( $subscription->id ) ); ?></a> (<?php printf( '<time datetime="%s">%s</time>', esc_html( date_i18n( 'c', time() ) ), esc_html( date_i18n( wc_date_format(), time() ) ) ); ?>)</h2>

<?php
wc_get_template( 'emails/email-subscription-detail-table.php', array( 'subscription' => $subscription ), '', YITH_YWSBS_TEMPLATE_PATH . '/' );
?>

<?php
wc_get_template( 'emails/email-subscription-customer-details.php', array( 'subscription' => $subscription ), '', YITH_YWSBS_TEMPLATE_PATH . '/' );
?>

<?php
do_action( 'woocommerce_email_footer', $email );
