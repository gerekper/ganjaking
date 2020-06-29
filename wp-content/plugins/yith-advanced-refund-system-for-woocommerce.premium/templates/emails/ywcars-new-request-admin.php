<?php
/**
 * Email for user notification of role granted
 *
 * @author  Yithemes
 * @package yith-woocommerce-automatic-role-changer.premium\templates\emails
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$body          = ! empty( $email->email_body ) ? nl2br( htmlspecialchars( $email->email_body ) ) : '';
$request_id    = ! empty( $email->request_id ) ? $email->request_id : '';
$request       = new YITH_Refund_Request( $request_id );
$order_id      = $request->order_id;
$customer_name = version_compare( WC()->version, '3.0.0', '<' ) ? $request->get_customer_link_legacy() : $request->get_customer_link();

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

$order_link   = '<a href="' . esc_attr( $order_url ) . '">#' . $order_id . '</a>';
$request_link = '<a href="' . esc_attr( $request_url ) . '">#' . $request_id . '</a>';

ob_start();
wc_get_template( 'ywcars-items-table-for-email.php',
	array( 'request_id' => $request_id ),
	'',
	YITH_WCARS_TEMPLATE_PATH . '/' );

$items_table = ob_get_clean();

?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email );

echo '<p>';
$body = str_replace(
	array(
		'{customer_name}',
		'{order_number}',
		'{request_number}',
		'{items_table}'
	),
	array(
		$customer_name,
		$order_link,
		$request_link,
		$items_table
	),
	$body
);
echo $body;
echo '</p>';



do_action( 'woocommerce_email_footer' );