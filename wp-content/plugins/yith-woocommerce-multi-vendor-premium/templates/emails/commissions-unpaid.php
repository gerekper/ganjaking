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

<p><?php _e( 'Some commissions have not been credited properly.', 'yith-woocommerce-product-vendors' ) ?></p>

<?php do_action( 'woocommerce_email_before_commission_table', $commission, $sent_to_admin, $plain_text ); ?>

<h2><?php _e( 'Details of commissions not paid', 'yith-woocommerce-product-vendors') ?></h2>

<h3><a href="<?php echo $commission->get_view_url( 'admin' ); ?>"><?php printf( __( 'Commission #%s', 'yith-woocommerce-product-vendors'), $commission->id ); ?></a> (<?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', strtotime( $commission->get_date() ) ), date_i18n( wc_date_format(), strtotime( $commission->get_date() ) ) ); ?>)</h3>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<tbody>
	<?php echo $commission->email_commission_details_table(); ?>
	</tbody>
</table>

<?php do_action( 'woocommerce_email_after_commission_table', $commission, $sent_to_admin, $plain_text ); ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
