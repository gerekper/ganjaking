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

<p><?php _e( 'The commission has been credited successfully.', 'yith-woocommerce-product-vendors' ) ?></p>

<?php do_action( 'woocommerce_email_before_commission_table', $commission, $sent_to_admin, $plain_text ); ?>

<h2><a href="<?php echo $commission->get_view_url( 'admin' ); ?>"><?php printf( __( 'Commission #%s detail', 'yith-woocommerce-product-vendors'), $commission->id ); ?></a></h2>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<tbody>
	<?php echo $commission->email_commission_details_table(); ?>
	</tbody>
</table>

<?php do_action( 'woocommerce_email_after_commission_table', $commission, $sent_to_admin, $plain_text ); ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
