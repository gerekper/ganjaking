<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Customer Status email template for pretty email plugin
 *
 * @var string           $email_heading      Email heading.
 * @var YITH_WCCOS_Email $email              The Email.
 * @var string           $custom_message     The custom message.
 * @var bool             $display_order_info True if the order info needs to be displayed.
 * @var WC_Order         $order              The order.
 * @var bool             $sent_to_admin      True if this is sent to admin.
 * @var bool             $plain_text         True if this is a plain text.
 * @var string           $orderref           Order ref.
 * @var string           $bordercolor        Border color.
 * @var string           $missingstyle       Missing style.
 *
 * @see        http://docs.woothemes.com/document/template-structure/
 * @author     YITH <plugins@yithemes.com>
 * @package    YITH\CustomOrderStatus
 */

defined( 'YITH_WCCOS' ) || exit; // Exit if accessed directly.
?>

<?php require MBWPE_TPL_PATH . '/settings.php'; ?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php echo wp_kses_post( $custom_message ); ?></p>

<?php do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text ); ?>

<h2 <?php echo esc_attr( $orderref ); ?>>
	<?php
	// translators: %s is the order number.
	echo esc_html( sprintf( __( 'Order #%s', 'woocommerce' ), $order->get_order_number() ) );
	?>
</h2>

<table cellspacing="0" cellpadding="6" style="border-collapse:collapse; width: 100%; border: 1px solid <?php echo esc_attr( $bordercolor ); ?>;" border="1" bordercolor="<?php echo esc_attr( $bordercolor ); ?>">
	<thead>
	<tr>
		<th scope="col" width="50%" style="<?php echo esc_attr( $missingstyle ); ?>text-align:center; border: 1px solid <?php echo esc_attr( $bordercolor ); ?>;"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
		<th scope="col" width="25%" style="<?php echo esc_attr( $missingstyle ); ?>text-align:center; border: 1px solid <?php echo esc_attr( $bordercolor ); ?>;"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
		<th scope="col" width="25%" style="<?php echo esc_attr( $missingstyle ); ?>text-align:center; border: 1px solid <?php echo esc_attr( $bordercolor ); ?>;"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php require MBWPE_TPL_PATH . '/tbody.php'; ?>
	</tbody>
	<?php require MBWPE_TPL_PATH . '/tfoot.php'; ?>
</table>

<?php do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text ); ?>

<?php do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text ); ?>

<?php do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text ); ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>

<?php require MBWPE_TPL_PATH . '/treatments.php'; ?>
