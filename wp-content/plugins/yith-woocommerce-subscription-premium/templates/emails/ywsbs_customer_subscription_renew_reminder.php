<?php //phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * This is the email sent to the customer when his subscription is expired
 *
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 *
 * @var                    $email_heading
 * @var                    $email
 * @var YWSBS_Subscription $subscription
 * @var $next_activity_date
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
	// translators: 1. subscription number, 2. renew date.
	printf( esc_html_x( 'Subscription %1$s is going to renew on %2$s', '1. subscription number, 2. renew date', 'yith-woocommerce-subscription' ), esc_html( $subscription->get_number() ), esc_html( date_i18n( wc_date_format(), $next_activity_date ) ) );
?>
	</p>

<h2><a class="link" href="<?php echo esc_url( ywsbs_get_view_subscription_url( $subscription->get_id() ) ); ?>">
		<?php
		// translators: placeholder subscription number.
		printf( esc_html_x( 'Subscription %s', 'the placeholder is the subscription number', 'yith-woocommerce-subscription' ), esc_html( $subscription->get_number() ) );
		?>
	</a></h2>


<?php
wc_get_template( 'emails/email-subscription-detail-table.php', array( 'subscription' => $subscription ), '', YITH_YWSBS_TEMPLATE_PATH . '/' );
?>

<?php
wc_get_template( 'emails/email-subscription-customer-details.php', array( 'subscription' => $subscription ), '', YITH_YWSBS_TEMPLATE_PATH . '/' );
?>

<?php
do_action( 'woocommerce_email_footer', $email );
