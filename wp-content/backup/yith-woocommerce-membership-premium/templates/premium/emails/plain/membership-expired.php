<?php
/**
 * Customer Membership Expired PLAIN
 *
 * @author        Yithemes
 * @package       YITH WooCommerce Membership Premium
 * @version       1.0.0
 */

if ( !defined ( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

echo "= " . $email_heading . " =\n\n";
echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo $custom_message;

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );

?>
