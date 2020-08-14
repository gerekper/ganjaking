<?php
/**
 * Email for admin notification of new message received.
 *
 * @author  Yithemes
 * @package yith-woocommerce-automatic-role-changer.premium\templates\emails
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$body        = $email->email_body;
$message_id  = $email->message_id;
$message     = new YITH_Request_Message( $message_id );
$request_id  = $email->request_id;
$request     = new YITH_Refund_Request( $request_id );
$customer_id = $request->customer_id;
$customer    = new WP_User( $customer_id );
$order_id    = $request->order_id;
$order       = wc_get_order( $order_id );
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php
if ( $email->is_customer_email() ) {
	$customer_name = ucwords( $customer->display_name );
	$author        = esc_html_x( 'Shop Manager', 'Author of messages from shop', 'yith-advanced-refund-system-for-woocommerce' );
	$order_url     = $order->get_view_order_url();
	$request_url   = $request->get_view_request_url();
} else {
	$customer_name = version_compare( WC()->version, '3.0.0', '<' ) ? $request->get_customer_link_legacy() : $request->get_customer_link();
	$author        = ucwords( $customer->display_name );

	// Get order admin URL
	$order_post = get_post( $order_id );
	$order_post_type_object = get_post_type_object( $order_post->post_type );
	if ( ( $order_post_type_object ) && ( $order_post_type_object->_edit_link ) ) {
		$order_url = admin_url( sprintf( $order_post_type_object->_edit_link . '&action=edit', $order_id ) );
	} else {
		$order_url = '';
	}

	// Get request admin URL
	$request_post = get_post( $request_id );
	$request_post_type_object = get_post_type_object( $request_post->post_type );
	if ( ( $request_post_type_object ) && ( $request_post_type_object->_edit_link ) ) {
		$request_url = admin_url( sprintf( $request_post_type_object->_edit_link . '&action=edit', $request_id ) );
	} else {
		$request_url = '';
	}
}

$order_link   = '<a href="' . esc_attr( $order_url ) . '">#' . $order_id . '</a>';
$request_link = '<a href="' . esc_attr( $request_url ) . '">#' . $request_id . '</a>';
$body = nl2br(
	str_replace(
		array( '{customer_name}', '{request_number}', '{order_number}' ),
		array( $customer_name, $request_link, $order_link ),
		$body
	)
);
?>
    <p><?php echo $body; ?></p>
    <div class="ywcars_refund_info_message_box">
        <div>
            <div class="ywcars_refund_info_message_author"><?php echo $author; ?></div>
            <span class="ywcars_refund_info_message_date"><?php echo $message->date; ?></span>
        </div>
        <div class="ywcars_refund_info_message_body">
            <span><?php echo nl2br( $message->message ); ?></span>
        </div>
		<?php if ( $message->get_message_metas() ) : ?>
            <div class="ywcars_attachments_line_separator"></div>
            <div class="ywcars_attachments">
                <div class="ywcars_attachments_title"><?php esc_html_e( 'Message attachments:', 'yith-advanced-refund-system-for-woocommerce' ); ?></div>
				<?php foreach ( $message->get_message_metas() as $name => $url ) : ?>
                    <div class="ywcars_single_attachment">
                        <a target="_blank" href="<?php echo $url; ?>">
                            <img class="ywcars_attachment_thumbnail" src="<?php echo $url; ?>">
                            <span><?php echo $name; ?></span>
                        </a>
                    </div>
				<?php endforeach; ?>
            </div>
		<?php endif; ?>
    </div>



<?php do_action( 'woocommerce_email_footer' );