<?php
/**
 * Customer send store credit email.
 *
 * @package WC_Store_Credit/Templates/Emails
 * @version 3.1.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php
if ( $coupon->get_description() ) :
	echo wp_kses_post( wpautop( wptexturize( $coupon->get_description() ) ) );
endif;
?>

<p><?php echo esc_html_x( 'To redeem your store credit use the following code during checkout:', 'email text', 'woocommerce-store-credit' ); ?></p>

<p style="margin: 40px 0;">
	<strong style="display: block; font-size: 2em; line-height: 1.2em; text-align: center;"><?php echo esc_html( $coupon->get_code() ); ?></strong>
</p>

<?php
if ( $coupon->get_date_expires() ) :
	echo '<p style="text-align: center;">';
	echo wp_kses_post(
		sprintf(
			/* translators: %s expiration date */
			_x( 'This credit can be redeemed until %s.', 'email text', 'woocommerce-store-credit' ),
			'<strong>' . esc_html( wc_format_datetime( $coupon->get_date_expires() ) ) . '</strong>'
		)
	);
	echo '</p>';
endif;

if ( $additional_content ) :
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
endif;

do_action( 'woocommerce_email_footer', $email );
