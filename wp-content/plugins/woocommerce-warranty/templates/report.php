<?php
/**
 * The template for displaying warranty options.
 *
 * @package WooCommerce_Warranty\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

$tab_status = ( isset( $_GET['status'] ) ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';
?>
<div class="wrap woocommerce">
	<h2><?php esc_html_e( 'Reports', 'wc_warranty' ); ?></h2>

	<div class="icon32"><img src="<?php echo esc_url( plugins_url() . '/woocommerce-warranty/assets/images/icon.png' ); ?>" /><br></div>
	<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
		<a href="admin.php?page=warranties-reports" class="nav-tab <?php echo ! $tab_status ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Active', 'wc_warranty' ); ?></a>
		<a href="admin.php?page=warranties-reports&status=completed" class="nav-tab <?php echo 'completed' === $tab_status ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Completed', 'wc_warranty' ); ?></a>
	</h2>
<?php

if ( empty( $tab_status ) ) {
	require WooCommerce_Warranty::$includes_path . '/class.warranty_active_reports_list_table.php';
} else {
	require WooCommerce_Warranty::$includes_path . '/class.warranty_completed_reports_list_table.php';
}
