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

$vendor_uri = add_query_arg( array(
	'page'          => 'yith_vendor_commissions',
	'vendor_id'     => $current_vendor->id,
	'filter_action' => 'Filter'
), admin_url( 'admin.php' ) );
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php do_action( 'woocommerce_email_before_commissions_table', $commissions, $sent_to_admin, $plain_text ); ?>

<h2><?php printf( '%s <a href="%s">%s</a>', _x( 'Commissions Report for', 'yith-woocommerce-product-vendors' ), $vendor_uri, $current_vendor->name ); ?></h2>

<style>
    #yith_wcmv_commissions_bulk_edit, #yith_wcmv_commissions_bulk_edit th, #yith_wcmv_commissions_bulk_edit td{border:2px solid #eee !important;}
    table#template_header {width: 100%;}
    table#template_header h1 {text-align: center;}

</style>

<table id="yith_wcmv_commissions_bulk_edit" cellspacing="0" cellpadding="6" style="border-collapse: collapse; width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<tbody>
	<?php echo $email->email_commission_bulk_table( $commissions, $new_commission_status, $show_note ); ?>
	</tbody>
</table>

<?php do_action( 'woocommerce_email_after_commission_table', $commissions, $sent_to_admin, $plain_text ); ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
