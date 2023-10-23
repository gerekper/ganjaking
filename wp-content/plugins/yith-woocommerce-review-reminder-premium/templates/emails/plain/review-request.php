<?php
/**
 * Plain email templatr
 *
 * @package YITH\ReviewReminder
 * @var $order
 * @var $lang
 * @var $template_type
 * @var $item_list
 * @var $days_ago
 * @var $mail_body
 * @var $email_heading
 * @var $email
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Implements Request Mail for YWRR plugin (Plain text)
 *
 * @class   YWRR_Request_Mail
 * @since   1.0.0
 * @author  YITH <plugins@yithemes.com>
 * @package YITH
 */

if ( ! $order ) {
	$the_current_user   = wp_get_current_user();
	$billing_email      = $the_current_user->user_email;
	$order_date         = current_time( 'mysql' );
	$modified_date      = current_time( 'mysql' );
	$order_id           = '0';
	$customer_id        = $the_current_user->ID;
	$billing_first_name = $the_current_user->user_login;
} else {

	$billing_email      = $order->get_billing_email();
	$order_date         = $order->get_date_created();
	$modified_date      = $order->get_date_modified();
	$modified_date      = ! $modified_date ? $order->get_date_created() : $modified_date;
	$order_id           = $order->get_id();
	$customer_id        = $order->get_user_id();
	$billing_first_name = $order->get_billing_first_name();
}

$query_args = array(
	'id'    => rawurlencode( base64_encode( ! empty( $customer_id ) ? $customer_id : 0 ) ), //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	'email' => rawurlencode( base64_encode( $billing_email ) ), //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	'type'  => 'ywrr',
);


$page_id = get_option( 'ywrr_unsubscribe_page_id' );
if ( ! $page_id ) {
	$page_id = get_option( 'ywrac_unsubscribe_page_id' );
}

$unsubscribe_page_id = yit_wpml_object_id( $page_id, 'page', true, $lang );
$unsubscribe_url     = esc_url( add_query_arg( $query_args, get_permalink( $unsubscribe_page_id ) ) );
$unsubscribe_text    = apply_filters( 'wpml_translate_single_string', get_option( 'ywrr_mail_unsubscribe_text' ), 'admin_texts_ywrr_mail_unsubscribe_text', 'ywrr_mail_unsubscribe_text', $lang );
$review_list         = '';

foreach ( $item_list as $item ) {
	$review_list .= $item['name'] . ' - ' . get_permalink( $item['id'] ) . "\n";
}

$find = array(
	'{customer_name}',
	'{customer_email}',
	'{site_title}',
	'{order_id}',
	'{order_date}',
	'{order_date_completed}',
	'{order_list}',
	'{days_ago}',
);

if ( class_exists( 'YITH_WooCommerce_Sequential_Order_Number' ) && $order ) {
	$order_id = get_option( 'ywson_order_prefix' ) . $order->get_meta( '_ywson_custom_number_order' ) . get_option( 'ywson_order_suffix' );
}

$replace = array(
	$billing_first_name,
	$billing_email,
	get_option( 'blogname' ),
	$order_id,
	ywrr_format_date( gmdate( 'Y-m-d H:i:s', yit_datetime_to_timestamp( $order_date ) ) ),
	ywrr_format_date( gmdate( 'Y-m-d H:i:s', yit_datetime_to_timestamp( $modified_date ) ) ),
	$review_list,
	$days_ago,
);

$mail_body = str_replace( $find, $replace, apply_filters( 'wpml_translate_single_string', get_option( 'ywrr_mail_body' ), 'admin_texts_ywrr_mail_body', 'ywrr_mail_body', $lang ) );

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html( wp_strip_all_tags( wptexturize( $mail_body ) ) );
echo "\n\n\n";

echo esc_html( $unsubscribe_text ) . ' - ' . esc_url( $unsubscribe_url ) . "\n\n";

echo "\n\n----------------------------------------\n\n";

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
