<?php
/**
 * Commission paid successfully email
 *
 * @author WooThemes
 * @package WooCommerce/Templates/Emails/HTML
 * @version 2.0.0
 *
 * @var string $email_heading
 * @var YITH_Commission $commission
 * @var bool $sent_to_admin
 * @var bool $plain_text
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n"; ?>

<?php do_action( 'woocommerce_email_before_commissions_table', $commissions, $sent_to_admin, $plain_text ); ?>

<?php printf( '%s %s', _x( 'Commissions Report for', 'yith-woocommerce-product-vendors' ), $current_vendor->name ); ?>

<?php echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n"; ?>

<?php echo $email->email_commission_bulk_table( $commissions, $new_commission_status, $show_note , true ); ?>

<?php do_action( 'woocommerce_email_after_commission_table', $commissions, $sent_to_admin, $plain_text ); ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
