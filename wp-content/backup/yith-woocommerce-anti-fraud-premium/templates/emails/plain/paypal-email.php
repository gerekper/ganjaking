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
 * Implements PayPal verify Mail for YWAF plugin (Plain)
 *
 * @class   YWAF_PayPal_Verify
 * @package Yithemes
 * @since   1.0.0
 * @author  Your Inspiration Themes
 */

$find        = array(
	'{site_title}',
	'{site_email}',
);
$replace     = array(
	get_option( 'blogname' ),
	get_option( 'admin_email' )
);
$mail_body   = str_replace( $find, $replace, wp_strip_all_tags( $mail_body ) );
$verify_arg  = array( 'ywaf_pvk' => $verify_key );
$verify_link = add_query_arg( $verify_arg, wc_get_page_permalink( 'myaccount' ) );

echo $email_heading . "\n\n";

echo $mail_body . "\n\n\n";

echo __( 'In order to complete the verification process, visit the following link:', 'yith-woocommerce-anti-fraud' ) . "\n\n";

echo $verify_link . "\n\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );