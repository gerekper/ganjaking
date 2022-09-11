<?php
/**
 * Subscription Delivery Schedules list table
 *
 * @package YITH WooCommerce Subscription
 * @since   2.2.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="wrap ywsbs-subscription-delivery-schedules-list-table yith-plugin-fw  yit-admin-panel-container">
	<div class="yit-admin-panel-content-wrap">
		<form id="plugin-fw-wc" method="get">
			<h2 class="wp-heading-inline"><?php esc_html_e( 'Subscription Delivery Schedules', 'yith-woocommerce-subscription' ); ?>
				<div class="ywsbs-export" style="display: inline-block;">
					<a class="button-primary " href="<?php echo esc_url( add_query_arg( array( 'action' => 'ywsbs_export_shipping_list' ), admin_url( 'admin.php' ) ) ); ?>"><i class="ywsbs-icon-save_alt"></i><?php esc_html_e('Download shipping list','yith-woocommerce-subscription'); ?></a>
				</div>
			</h2>
			<div class="delivery-content">
				<input type="hidden" name="page" value="yith_woocommerce_subscription"/>
				<input type="hidden" name="tab" value="delivery"/>
				<?php
				$this->cpt_obj_delivery_schedules->prepare_items();
				$this->cpt_obj_delivery_schedules->display();
				?>
			</div>
			<div id="yith-shipped-confirm" title="<?php esc_html_e( 'You are going to set this item as "Shipped". ', 'yith-woocommerce-subscription' ); ?>" style="display:none;">
				<p><?php printf( wp_kses_post( 'This will automatically send a confirmation email to the customer.%sDo you want to continue?', 'yith-woocommerce-subscription' ) ,'<br/>' ); ?></p>
			</div>
		</form>
	</div>
</div>
