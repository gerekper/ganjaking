<?php
/**
 * New collection HTML email notification.
 *
 * @author  WooThemes
 * @package WC_Photography/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<p><?php _e( 'The following photo collection(s) has been added to your account:', 'woocommerce-photography' ); ?></p>

<ul>
	<?php foreach ( $collections as $collection_id => $collection_name ) : ?>
		<li><strong><a href="<?php echo esc_url( get_term_link( $collection_id, 'images_collections' ) ); ?>"><?php echo esc_html( $collection_name ); ?></a></strong></li>
	<?php endforeach; ?>
</ul>

<p><?php echo sprintf( __( 'You can view your collections on the %s page.', 'woocommerce-photography' ), '<a href="' . get_permalink( wc_get_page_id( 'myaccount' ) ) . '">' . __( 'My Account', 'woocommerce-photography' ) . '</a>' ); ?></p>

<?php do_action( 'woocommerce_email_footer' ); ?>
