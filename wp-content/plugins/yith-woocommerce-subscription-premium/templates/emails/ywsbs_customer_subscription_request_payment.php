<?php //phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * This is the email sent to the customer when his subscription is in overdue
 *
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 *
 * @var                    $email_heading
 * @var                    $email
 * @var YWSBS_Subscription $subscription
 * @var $next_activity_date
 * @var $next_activity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'woocommerce_email_header', $email_heading, $email );
?>
<?php /* translators: %s: Customer first name */ ?>
	<p><?php printf( esc_html__( 'Hi %s,', 'yith-woocommerce-subscription' ), esc_html( $subscription->get_billing_first_name() ) ); ?></p>
<p>
<?php
	// translators: the placeholder is site name.
	printf( esc_html_x( 'Your recent subscription renewal order on %s is late for payment.', 'the placeholder is site name', 'yith-woocommerce-subscription' ), wp_kses_post( get_option( 'blogname' ) ) );
?>
</p>

<p>
<?php
	// translators: 1. date of event, 2 is the subscription number, 3 next subscription status.
	printf( wp_kses_post( __( 'If you do not pay it by <strong>%1$s</strong>, your subscription %2$s will be <strong>%3$s</strong>.', 'yith-woocommerce-subscription' ) ), esc_html( date_i18n( wc_date_format(), $next_activity_date ) ), esc_html( $subscription->get_number() ), esc_html( $next_activity ) );
?>
</p>

<p><?php esc_html_e( 'To pay for this order, please, click on the following link:', 'yith-woocommerce-subscription' ); ?></p>
<p style="padding:10px 0;"><a style="background-color:#eee;padding:10px 15px;text-decoration:none;" href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>"><?php esc_html_e( 'pay now', 'yith-woocommerce-subscription' ); ?></a></p>

<h2><a class="link" href="<?php echo esc_url( ywsbs_get_view_subscription_url( $subscription->get_id() ) ); ?>">
		<?php
		// translators: the placeholder is the subscription number.
		printf( esc_html_x( 'Subscription %s', 'the placeholder is subscription number', 'yith-woocommerce-subscription' ), esc_html( $subscription->get_number() ) );
		?>
	</a> (<?php printf( '<time datetime="%s">%s</time>', esc_html( date_i18n( 'c', time() ) ), esc_html( date_i18n( wc_date_format(), time() ) ) ); ?>)</h2>

<?php
wc_get_template( 'emails/email-subscription-detail-table.php', array( 'subscription' => $subscription ), '', YITH_YWSBS_TEMPLATE_PATH . '/' );
?>

<?php
wc_get_template( 'emails/email-subscription-customer-details.php', array( 'subscription' => $subscription ), '', YITH_YWSBS_TEMPLATE_PATH . '/' );
?>

<?php
do_action( 'woocommerce_email_footer', $email );
