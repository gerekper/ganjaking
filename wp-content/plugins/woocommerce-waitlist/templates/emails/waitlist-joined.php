<?php
/**
 * The template for the waitlist joined notification sent to a customer on sign up (HTML)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/waitlist-joined.php.
 *
 * HOWEVER, on occasion WooCommerce Waitlist will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @version 3.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$email = sanitize_email( $email );
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php echo _x( 'Hi There,', 'Email salutation', 'woocommerce-waitlist' ); ?></p>

<p>
	<?php
	printf( __( 'You have been sent this email because your email address was registered on a waitlist for %1$s at %2$s. ', 'woocommerce-waitlist' ), '<a href="' . esc_attr( $product_link ) . '">' . esc_html( $product_title ) . '</a>', esc_html( get_bloginfo( 'name' ) ) );
	?>
</p>
<p>
	<?php $product_link = apply_filters( 'wcwl_product_link_joined_email', add_query_arg( array(
    'wcwl_remove_user' => esc_attr( $email ),
    'product_id' => absint( $product_id ),
		'key' => $key,
	), $product_link ) ); ?>
	<?php printf( __( 'If you would like to remove your email address from the waitlist you can do so by clicking %1$shere%2$s.', 'woocommerce-waitlist' ), '<a href="' . esc_attr( $product_link ) . '">', '</a>' ); ?>
</p>
<?php
do_action( 'woocommerce_email_footer', $email ); ?>
