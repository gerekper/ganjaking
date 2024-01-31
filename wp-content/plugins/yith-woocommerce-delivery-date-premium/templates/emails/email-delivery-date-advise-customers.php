<?php
/**
 * HTML Template Email Delivery Date
 *
 * @package YITH WooCommerce Delivery Date
 * @since   1.0.0
 * @author  YITHEMES
 */


do_action( 'woocommerce_email_header', $email_heading , $email );

echo nl2br( get_option('ywcdd_mail_content') );

do_action( 'woocommerce_email_footer', $email );