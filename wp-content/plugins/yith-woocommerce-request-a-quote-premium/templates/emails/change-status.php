<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH Woocommerce Request A Quote
 */

/**
 * HTML Template Email Request a Quote
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.1.6
 * @version 2.2.7
 * @author  YITH
 *
 * @var $email_heading array
 * @var $email string
 * @var $order WC_Order
 * @var $email_title string
 * @var $reason string
 */
$order_id     = $order->get_id();
$quote_number = apply_filters( 'ywraq_quote_number', $order_id );

do_action( 'woocommerce_email_header', $email_heading, $email );

echo wp_kses_post( $email_description );
?>


<?php if ( 'accepted' === $status ) : ?>
	<p><?php printf( esc_html__( 'The Proposal #%s has been accepted', 'yith-woocommerce-request-a-quote' ), esc_html( $quote_number ) ); ?></p>
<?php else : ?>
	<p><?php printf( esc_html__( 'The Proposal #%s has been rejected.', 'yith-woocommerce-request-a-quote' ), esc_html( $quote_number ) ); ?></p>
	<?php echo '"' . wp_kses_post( stripcslashes( $reason ) ) . '"'; ?>
<?php endif ?>
	<p></p>
	<p><?php printf( '%1$s <a href="%2$s">#%3$s</a>', esc_html( __( 'You can see details here:', 'yith-woocommerce-request-a-quote' ) ), esc_url( admin_url( 'post.php?post=' . $order_id . '&action=edit' ) ), esc_html( $quote_number ) ); ?></p>
<?php
do_action( 'woocommerce_email_footer', $email );
?>
