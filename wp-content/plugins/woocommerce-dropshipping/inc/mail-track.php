<?php
	$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
	require $parse_uri[0] . 'wp-load.php';
	$parse_uri1 = explode( 'wp-content', $_SERVER['REQUEST_URI'] );
	$id = $_GET['orderid'];
	$suppid = $_GET['suppid'];
	$from_email = $_GET['from'];
	$sup_name = $_GET['sup_name'];
	// $order = wc_get_order($id);
	$get_id = get_post_meta( $id, '_' . $id . '_' . $suppid );

if ( empty( $get_id ) || ! isset( $get_id ) ) {
	$to_email = get_option( 'admin_email' );
	$to = $to_email;
	$subject = 'Email Receipts';
	$message = '<h3>Hi there!</h3>';
	$message .= '<h4>Email has been opened by => ' . $sup_name . '</h4>';
	$message .= '<h4>Supplier Id => ' . $suppid . '</h4>';
	$message .= '<h4>Order Id =>' . $id . '</h4>';
	// $headers = $from_email;
	// $headers .= "From:".$from_email;
	// $headers .= "MIME-Version: 1.0" . "\r\n";
	// $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	// $headers .= 'From: ' .$from_email ."\r\n";

	wp_mail( $to, $subject, $message );
	update_post_meta( $id, '_' . $id . '_' . $suppid, $id . '_' . $suppid );
}
