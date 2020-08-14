<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Implements Coupon Mail for YWCES plugin (HTML)
 *
 * @class   YWCES_Coupon_Mail
 * @package Yithemes
 * @since   1.0.0
 * @author  Your Inspiration Themes
 */

$email_templates = false;

if ( defined( 'YWCES_PREMIUM' ) ) {
    $email_templates = YITH_WCES()->is_email_templates_active() && get_option( 'ywces_mail_template_enable' ) == 'yes';
}


if ( $email_templates || ( !$template_type ) || ( $template_type == 'base' ) ) {

    do_action( 'woocommerce_email_header', $email_heading, $email );

}
else {

    do_action( 'ywces_email_header', $email_heading, $template_type );

}

echo $mail_body;


if ( $email_templates || ( !$template_type ) || ( $template_type == 'base' ) ) {

    do_action( 'woocommerce_email_footer', $email );

}
else {

    do_action( 'ywces_email_footer', $template_type );

}