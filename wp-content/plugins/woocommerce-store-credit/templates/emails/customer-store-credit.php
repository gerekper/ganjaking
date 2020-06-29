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
if ( $additional_content ) :
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
endif;

do_action( 'woocommerce_email_footer', $email );
