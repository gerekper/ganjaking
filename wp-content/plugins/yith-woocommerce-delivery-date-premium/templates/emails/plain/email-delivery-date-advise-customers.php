<?php
/**
 * plain Template Email Delivery Date
 *
 * @package YITH WooCommerce Delivery Date
 * @since   1.0.0
 * @author  YITHEMES
 */

echo "= ".$email_heading." \n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo  nl2br( get_option('ywcdd_mail_content') );

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
