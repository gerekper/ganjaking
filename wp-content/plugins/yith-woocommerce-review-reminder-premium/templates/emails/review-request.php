<?php
/**
 * HTML email templatr
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
 * Implements Request Mail for YWRR plugin (HTML)
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

$unsubscribe_page_id   = yit_wpml_object_id( $page_id, 'page', true, $lang );
$unsubscribe_url       = esc_url( add_query_arg( $query_args, get_permalink( $unsubscribe_page_id ) ) );
$unsubscribe_text      = apply_filters( 'wpml_translate_single_string', get_option( 'ywrr_mail_unsubscribe_text' ), 'admin_texts_ywrr_mail_unsubscribe_text', 'ywrr_mail_unsubscribe_text', $lang );
$show_unsubscribe_link = ( ( in_array( $template_type, ywrr_get_templates(), true ) || ( defined( 'YITH_WCET_PREMIUM' ) && 'yes' === get_option( 'ywrr_mail_template_enable' ) ) ) && apply_filters( 'ywrr_print_unsubscribe_link_in_footer', true ) );
$unsubscribe_link      = '<a class="ywrr-unsubscribe-link" href="' . $unsubscribe_url . '">' . $unsubscribe_text . '</a>';
$review_list           = ywrr_email_items_list( $item_list, $customer_id );

$find = array(
	'{customer_name}',
	'{customer_email}',
	'{site_title}',
	'{order_id_flat}',
	'{order_id}',
	'{order_date}',
	'{order_date_completed}',
	'{order_list}',
	'{days_ago}',
	'{unsubscribe_link}',
);

if ( class_exists( 'YITH_WooCommerce_Sequential_Order_Number' ) && $order ) {
	$order_id = get_option( 'ywson_order_prefix' ) . $order->get_meta( '_ywson_custom_number_order' ) . get_option( 'ywson_order_suffix' );
}

$wrapping_tag = apply_filters( 'ywrr_placeholder_wrapping_tags', 'b' );
$opening_tag  = ( '' !== $wrapping_tag ? '<' . $wrapping_tag . '>' : '' );
$closing_tag  = ( '' !== $wrapping_tag ? '</' . $wrapping_tag . '>' : '' );

$replace = array(
	$opening_tag . $billing_first_name . $closing_tag,
	$opening_tag . $billing_email . $closing_tag,
	$opening_tag . get_option( 'blogname' ) . $closing_tag,
	$order_id,
	$opening_tag . $order_id . $closing_tag,
	$opening_tag . ywrr_format_date( gmdate( 'Y-m-d H:i:s', yit_datetime_to_timestamp( $order_date ) ) ) . $closing_tag,
	$opening_tag . ywrr_format_date( gmdate( 'Y-m-d H:i:s', yit_datetime_to_timestamp( $modified_date ) ) ) . $closing_tag,
	$review_list,
	$opening_tag . $days_ago . $closing_tag,
	( $show_unsubscribe_link ? '' : $unsubscribe_link ),
);

$mail_body       = str_replace( $find, $replace, apply_filters( 'wpml_translate_single_string', $mail_body, 'admin_texts_ywrr_mail_body', 'ywrr_mail_body', $lang ) );
$email_templates = false;
if ( function_exists( 'ywrr_check_ywcet_active' ) ) {
	$email_templates = ywrr_check_ywcet_active() && get_option( 'ywrr_mail_template_enable' ) === 'yes';
}
if ( $email_templates || ( ! $template_type ) || ( 'base' === $template_type ) ) {
	do_action( 'woocommerce_email_header', $email_heading, $email );
} else {
	do_action( 'ywrr_email_header', $email_heading, $template_type );
}

?>
	<p>
		<?php echo wp_kses_post( wpautop( wptexturize( $mail_body ) ) ); ?>
	</p>
<?php

if ( $email_templates || ( ! $template_type ) || ( 'base' === $template_type ) ) {

	$args = $show_unsubscribe_link ? array( $unsubscribe_link ) : array();

	do_action( 'woocommerce_email_footer', $email, $args );

} else {
	do_action( 'ywrr_email_footer', $unsubscribe_url, $template_type, $unsubscribe_text );
}
