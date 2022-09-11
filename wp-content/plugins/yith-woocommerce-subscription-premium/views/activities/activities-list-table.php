<?php
/**
 * Subscription Activities list table
 *
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="wrap ywsbs_subscription_activities yith-plugin-ui--classic-wp-list-style">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Subscription Activities', 'yith-woocommerce-subscription' ); ?></h1>
	<hr class="wp-header-end">
	<form method="post">
		<input type="hidden" name="page" value="yith_woocommerce_subscription" />
		<?php $this->cpt_obj_activities->search_box( __( 'Search', 'yith-woocommerce-subscription' ), 'search_id' ); ?>
	</form>
	<form id="posts-filter" method="post">
		<?php
		$this->cpt_obj_activities->prepare_items();
		$this->cpt_obj_activities->display();
		?>
	</form>
</div>
