<?php
/**
 * Recipient customer new account email
 *
 * @package WooCommerce Subscriptions Gifting/Templates/Emails
 * @version 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php printf( esc_html__( 'Hi there,', 'woocommerce-subscriptions-gifting' ) ); ?></p>
<p>
<?php
// Translators: 1) is the purchaser's name, 2) is the blog's name.
printf( esc_html__( '%1$s just purchased a subscription for you at %2$s so we\'ve created an account for you to manage the subscription.', 'woocommerce-subscriptions-gifting' ), wp_kses( $subscription_purchaser, wp_kses_allowed_html( 'user_description' ) ), esc_html( $blogname ) );
?>
</p>

<p>
<?php
// Translators: placeholder is a username.
printf( esc_html__( 'Your username is: %s', 'woocommerce-subscriptions-gifting' ), '<strong>' . esc_html( $user_login ) . '</strong>' );
?>
</p>
<p><a class="link" href="<?php echo esc_url( add_query_arg( array( 'key' => $reset_key, 'id' => $user_id ), wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) ) ) ); // phpcs:ignore WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound ?>">
	<?php esc_html_e( 'Click here to set your password', 'woocommerce-subscriptions-gifting' ); ?></a></p>

<p>
<?php
printf(
	/* Translators: placeholder is a link to "My Account" for setting up the recipient's details. */
	esc_html__( 'To complete your account we just need you to fill in your shipping address and you to change your password here: %s.', 'woocommerce-subscriptions-gifting' ),
	'<a href="' . esc_url( wc_get_endpoint_url( 'new-recipient-account', '', wc_get_page_permalink( 'myaccount' ) ) ) . '">' . esc_html__( 'My Account Details', 'woocommerce-subscriptions-gifting' ) . '</a>'
);
?>
</p>

<p>
<?php
printf(
	/* Translators: placeholder is a link to "My Account". */
	esc_html__( 'Once completed you may access your account area to view your subscription here: %s.', 'woocommerce-subscriptions-gifting' ),
	'<a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '">' . esc_html__( 'My Account', 'woocommerce-subscriptions-gifting' ) . '</a>'
);
?>
</p>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
