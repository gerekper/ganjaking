<?php
/**
 * Customer send store credit email.
 *
 * @package WC_Store_Credit/Templates/Emails
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

/*
 * @hooked WC_Emails::email_header() Output the email header.
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); // phpcs:ignore WooCommerce.Commenting.CommentHooks ?>

<?php
if ( $coupon->get_description() ) :
	echo wp_kses_post( wpautop( wptexturize( $coupon->get_description() ) ) );
endif;
?>

<p><?php echo esc_html_x( 'To redeem your store credit use the following code during checkout:', 'email text', 'woocommerce-store-credit' ); ?></p>

<div class="store-credit-wrapper text-center">
	<span class="store-credit-code"><?php echo esc_html( $coupon->get_code() ); ?></span>
</div>

<div class="store-credit-wrapper text-center">
	<?php
	printf(
		'<a class="store-credit-cta-button" href="%1$s" target="_blank">%2$s</a>',
		esc_url( wc_store_credit_get_redeem_url( $coupon ) ),
		esc_html( $email->get_button_text() )
	);
	?>
</div>

<?php
if ( $coupon->get_date_expires() ) :
	echo '<p class="text-center">';
	echo wp_kses_post(
		sprintf(
			/* translators: %s expiration date */
			_x( 'This credit can be redeemed until %s.', 'email text', 'woocommerce-store-credit' ),
			'<strong>' . esc_html( wc_format_datetime( $coupon->get_date_expires() ) ) . '</strong>'
		)
	);
	echo '</p>';
endif;

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) :
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
endif;

/*
 * @hooked WC_Emails::email_footer() Output the email footer.
 */
do_action( 'woocommerce_email_footer', $email ); // phpcs:ignore WooCommerce.Commenting.CommentHooks
