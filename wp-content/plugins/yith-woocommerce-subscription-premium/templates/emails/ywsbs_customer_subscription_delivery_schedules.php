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
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
do_action( 'woocommerce_email_header', $email_heading, $email );
?>

	<?php /* translators: %s: Customer first name */ ?>
	<p><?php printf( esc_html__( 'Hi %s,', 'yith-woocommerce-subscription' ), esc_html( $subscription->get_billing_first_name() ) ); ?></p>
	<p><?php printf( '%s <strong>%s</strong> %s', esc_html_x( 'We confirm you that we\'ve just shipped your product', 'email delivery scheduled ', 'yith-woocommerce-subscription' ), wp_kses_post( $subscription->get_product_name() ), esc_html_x( 'to your address:', 'email delivery scheduled content part', 'yith-woocommerce-subscription' ) ); ?></p>

	<p>
		<?php
		$shipping = $subscription->get_address_fields( 'shipping', true );
		echo $shipping ? WC()->countries->get_formatted_address( $shipping, '<br/>' ) : ''; //phpcs:ignore
		?>
	</p>

<p><?php echo esc_html_x( 'Regards,', 'closing email delivery schedules', 'yith-woocommerce-subscription' ); ?><br />
	<?php echo esc_html_x( 'Staff of', 'closing email delivery schedules', 'yith-woocommerce-subscription' ) . ' ' . wp_kses_post( get_bloginfo( 'name' ) ); ?>
</p>

<?php
do_action( 'woocommerce_email_footer', $email );
