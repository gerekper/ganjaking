<?php

defined( 'ABSPATH' ) or exit;

$_GET['tab'] = $GLOBALS['hook_suffix'] = 'customers';
$page_id     = isset( $_GET['page_id'] ) && $_GET['page_id'] > 0 ? $_GET['page_id'] : '';
$report_type = ! empty( $_GET['report'] ) ? $_GET['report'] : 'customers';

if ( current_user_can( 'view_woocommerce_reports' ) ) : ?>

<div id="yith-wcmf-reports">

	<h1><?php echo __( 'Customers', 'woocommerce' ); ?></h1>

	<div class="buttons">
		<a href="?<?php echo $page_id > 0 ? 'page_id=' . $page_id . '&amp;' : ''; ?>reports=customers-report&amp;report=customers" class="button <?php echo $report_type == 'customers' ? 'current' : ''; ?>"><?php echo __( 'Customers vs Guests', 'yith-frontend-manager-for-woocommerce' ); ?></a>
		<a href="?<?php echo $page_id > 0 ? 'page_id=' . $page_id . '&amp;' : ''; ?>reports=customers-report&amp;report=customer_list" class="button <?php echo $report_type == 'customer_list' ? 'current' : ''; ?>"><?php echo __( 'Customer List', 'yith-frontend-manager-for-woocommerce' ); ?></a>
	</div>

	<?php YITH_Frontend_Manager_Section_Reports::require_reports_core_files(); ?>
	<?php WC_Admin_Reports::output(); ?>

</div>

<?php else : ?>

<p><?php echo __( 'Only users with "Shop Reports" capabilities can view this page.', 'yith-frontend-manager-for-woocommerce' ); ?></p>

<?php endif; ?>

<?php
do_action( 'yith_wcfm_reports' );
