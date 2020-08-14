<?php
/**
 * HTML Template Email Funds
 *
 * @package YITH WooCommerce Funds
 * @since   1.0.0
 * @author  YITHEMES
 */


do_action( 'woocommerce_email_header', $email_heading, $email );
echo $email_content;
do_action( 'woocommerce_email_footer', $email );