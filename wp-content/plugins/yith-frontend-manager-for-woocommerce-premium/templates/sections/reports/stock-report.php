<?php

defined( 'ABSPATH' ) or exit;

$_GET['tab'] = $GLOBALS['hook_suffix'] = 'stock';
$page_id     = isset( $_GET['page_id'] ) && $_GET['page_id'] > 0 ? sanitize_text_field( $_GET['page_id'] ) : '';
$report      = isset( $_GET['report'] ) ? sanitize_text_field( $_GET['report'] ) : '';

if ( current_user_can( 'view_woocommerce_reports' ) ) : ?>

<div id="yith-wcmf-reports">

	<h1><?php echo __( 'Stock', 'woocommerce' ); ?></h1>

	<div class="buttons">
		<a href="?<?php echo $page_id > 0 ? 'page_id=' . $page_id . '&amp;' : ''; ?>reports=stock-report&amp;report=low_in_stock" class="button <?php echo $report == 'low_in_stock' ? 'current' : ''; ?>"><?php echo __( 'Low stock', 'yith-frontend-manager-for-woocommerce' ); ?></a>
		<a href="?<?php echo $page_id > 0 ? 'page_id=' . $page_id . '&amp;' : ''; ?>reports=stock-report&amp;report=out_of_stock" class="button <?php echo $report == 'out_of_stock' ? 'current' : ''; ?>"><?php echo __( 'Out of stock', 'yith-frontend-manager-for-woocommerce' ); ?></a>
		<a href="?<?php echo $page_id > 0 ? 'page_id=' . $page_id . '&amp;' : ''; ?>reports=stock-report&amp;report=most_stocked" class="button <?php echo $report == 'most_stocked' ? 'current' : ''; ?>"><?php echo __( 'High-stocked', 'yith-frontend-manager-for-woocommerce' ); ?></a>
	</div>

	<?php YITH_Frontend_Manager_Section_Reports::require_reports_core_files(); ?>
	<?php WC_Admin_Reports::output(); ?>

</div>

<?php else : ?>

<p><?php echo __( 'Only users with "Shop Reports" capabilities can view this page.', 'yith-frontend-manager-for-woocommerce' ); ?></p>

<?php endif; ?>

<?php
do_action( 'yith_wcfm_reports' );
