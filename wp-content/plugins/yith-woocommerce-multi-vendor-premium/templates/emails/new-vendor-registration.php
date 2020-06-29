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

<p><?php _e( 'New vendor registered', 'yith-woocommerce-product-vendors' ) ?></p>

<p><?php _e( 'A new user has made a request to become a vendor in your store.', 'yith-woocommerce-product-vendors' ) ?></p>

<?php do_action( 'woocommerce_email_before_commission_table', $vendor, $sent_to_admin, $plain_text ); ?>

<h2>
    <a href="<?php echo $vendor->get_url( 'admin' ); ?>">
        <?php _e( 'Vendor detail', 'yith-woocommerce-product-vendors ') ?>
    </a>
</h2>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<tbody>
		<?php yith_wcpv_get_template( 'new-vendor-detail-table', array( 'vendor' => $vendor, 'owner' => get_user_by( 'id', absint( $vendor->get_owner() ) ) ), 'emails' ); ?>
	</tbody>
</table>

<?php do_action( 'woocommerce_email_after_commission_table', $vendor, $sent_to_admin, $plain_text ); ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
