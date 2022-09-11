<?php
/**
 * The template for the waitlist double optin email (HTML)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/waitlist-optin.php.
 *
 * HOWEVER, on occasion WooCommerce Waitlist will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @version 2.2.3
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php echo esc_html_x( 'Hi There,', 'Email salutation', 'woocommerce-waitlist' ); ?></p>

<p>
	<?php
	printf( __( 'Please click the link below to confirm your email address and be added to the waitlist for %1$s at %2$s.', 'woocommerce-waitlist' ), esc_html( $product_title ), esc_html( get_bloginfo( 'name' ) ) );
	?>
</p>
<p>
	<?php
	$link = add_query_arg( array(
    'wcwl_user_optin' => esc_attr( $email ),
    'product_id'      => absint( $product_id ),
		'products'			  => $products,
		'key'             => $key,
		'lang'            => $lang,
	), $product_link );
	print( '<a href="' . esc_url( $link ) . '">' . esc_html( $product_link ) . '</a>' );
	?>
<p>
	<?php _e( 'If you did not make this request please ignore this email.', 'woocommerce-waitlist' ); ?>
</p>
<?php
do_action( 'woocommerce_email_footer', $email );
?>
