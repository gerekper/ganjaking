<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Implements Donation Mail for YWCDS plugin (HTML)
 *
 * @class YITH_WC_Donations_Email
 * @package Yithemes
 * @since 1.0.0
 * @author Your Inspiration Themes
 */

if ( ! $order ) {

    global $current_user;


    $billing_email      = $current_user->user_email;
    $order_date         = current_time( 'mysql' );
    $modified_date      = current_time( 'mysql' );
    $order_id           = '0';
    $customer_id        = $current_user->ID;
    $billing_first_name = $current_user->user_login;

} else {

    $order_id = $order->get_order_number();
    if( version_compare( WC()->version, '3.0.0','>=' ) ){
        $billing_email = $order->get_billing_email();
        $order_date = $order->get_date_created();
        $modified_date = $order->get_date_completed();
        $billing_first_name = $order->get_formatted_billing_full_name();
    }else {
        $billing_email = $order->billing_email;
        $order_date = $order->order_date;
        $modified_date = $order->modified_date;
        $billing_first_name = $order->billing_first_name.' '.$order->billing_last_name;
    }

    $customer_id = $order->get_user_id();
}

$query_args = array(
    'id'    => urlencode( base64_encode( ! empty( $customer_id ) ? $customer_id : 0 ) ),
    'email' => urlencode( base64_encode( $billing_email ) )
);


$find = array(
    '{customer_name}',
    '{customer_email}',
    '{site_title}',
    '{order_id}',
    '{order_date}',
    '{order_date_completed}',
    '{donation_list}'
);


$donation_list_html =   '';

foreach( $donation_list as $key=>$item ){

    $donation_list_html.='<li>'.$item['product_name'].'<span class="total" style="padding-left:5px;">'.wc_price( $item['total'] ).'</span></li>';
}
$replace = array(
    '<b>' . $billing_first_name . '</b>',
    '<b>' . $billing_email . '</b>',
    '<b>' . get_option( 'blogname' ) . '</b>',
    '<b>' . '#'.$order_id . '</b>',
    '<b>' . $order_date . '</b>',
    '<b>' . $modified_date . '</b>',
    '<ul>'. $donation_list_html.'</ul>'
);

$mail_body = str_replace($find, $replace, get_option( 'ywcds_mail_content' ));


    do_action( 'ywcds_email_header', $email_heading );
 ?>

    <p><?php echo wpautop( $mail_body ); ?></p>

<?php
    do_action( 'ywcds_email_footer' );
