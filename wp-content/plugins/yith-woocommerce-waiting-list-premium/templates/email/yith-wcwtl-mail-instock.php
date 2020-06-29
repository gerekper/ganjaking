<?php
/**
 * YITH WooCommerce Waiting List Mail Template
 *
 * @author  YITH
 * @package YITH WooCommerce Waiting List
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCWTL' ) ) {
	exit;
} // Exit if accessed directly.

do_action( 'woocommerce_email_header', $email_heading, $email );
?>
<?php if ( $product_thumb ) : ?>
	<p>
		<?php echo '<a href="' . esc_url_raw( $product_link ) . '">' . $product_thumb . '</a>';  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</p>
<?php endif; ?>
<?php echo wp_kses_post( wpautop( wptexturize( $email_content ) ) ); ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
