<?php
/**
 * Admin display.
 *
 * @package woocommerce-product-addons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$global_addons = WC_Product_Addons_Groups::get_all_global_groups();

?>
<div class="wrap woocommerce woocommerce-product-addons">
	<h1><?php esc_html_e( 'Global Add-ons', 'woocommerce-product-addons' ); ?> <a href="<?php echo esc_url( add_query_arg( 'add', true, admin_url( 'edit.php?post_type=product&page=addons' ) ) ); ?>" class="page-title-action"><?php esc_html_e( 'Add new', 'woocommerce-product-addons' ); ?></a></h1><br/><?php

	if ( $global_addons ) {
		?><form id="addons-table" method="GET"><?php
			$table->display();
			$page = isset( $_REQUEST[ 'page' ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ 'page' ] ) ) : '';
		?><input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>"/>
		<input type="hidden" name="post_type" value="product" />
		</form><?php
	} else {
		?><div class="woocommerce-BlankState">
			<h2 class="woocommerce-BlankState-message"><span><?php echo esc_html__( 'Use global add-ons to add free or paid options to your products in bulk.', 'woocommerce-product-addons' ); ?></span>
			<a class="woocommerce-BlankState-cta button-primary button" href="<?php echo esc_url( add_query_arg( 'add', true, admin_url( 'edit.php?post_type=product&page=addons' ) ) ); ?>"><?php echo esc_html__( 'Create your first add-on', 'woocommerce-product-addons' ); ?></a>
			<a class="woocommerce-BlankState-cta button" target="_blank" href="https://woo.com/document/product-add-ons"><?php echo esc_html__( 'Learn more about add-ons', 'woocommerce-product-addons' ); ?></a>
		</div><?php
	}
	?>
</div>
