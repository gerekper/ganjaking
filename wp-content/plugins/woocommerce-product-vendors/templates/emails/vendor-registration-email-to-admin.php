<?php
/**
 * Vendor registration email to admin.
 *
 * @version 2.0.0
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<h3><?php esc_html_e( 'Hello! A vendor has requested to be registered.', 'woocommerce-product-vendors' ); ?></h3>

<p><?php esc_html_e( 'Vendor information:', 'woocommerce-product-vendors' ); ?></p>

<ul>
	<li><?php printf( esc_html__( 'Email', 'woocommerce-product-vendors' ) . ': %s', esc_html( $user_email ) ); ?></li>
	<li><?php printf( esc_html__( 'First Name', 'woocommerce-product-vendors' ) . ': %s', esc_html( $first_name ) ); ?></li>
	<li><?php printf( esc_html__( 'Last Name', 'woocommerce-product-vendors' ) . ': %s', esc_html( $last_name ) ); ?></li>
	<li><?php printf( esc_html__( 'Vendor Name', 'woocommerce-product-vendors' ) . ': %s', esc_html( stripslashes( $vendor_name ) ) ); ?></li>
	<li><?php printf( esc_html__( 'Vendor Description', 'woocommerce-product-vendors' ) . ':<br />%s', esc_html( stripslashes( $vendor_desc ) ) ); ?></li>
</ul>

<?php /* translators: %1$s is the pending vendors list url. */ ?>
<p><?php printf( wp_kses_post( __( 'You can approve this vendor at <a href="%1$s">%1$s</a>.', 'woocommerce-product-vendors' ) ), esc_url( admin_url( 'users.php?role=wc_product_vendors_pending_vendor' ) ) ); ?></p>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
