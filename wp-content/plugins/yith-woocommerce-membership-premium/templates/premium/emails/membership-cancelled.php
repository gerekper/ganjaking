<?php
/**
 * Customer membership cancelled email
 *
 * @author        Yithemes
 * @package       YITH WooCommerce Membership Premium
 * @version       1.1.2
 *
 * @var string                    $email_heading
 * @var YITH_WCMBS_Cancelled_Mail $email
 * @var string                    $custom_message
 */

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php echo nl2br( $custom_message ); ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
