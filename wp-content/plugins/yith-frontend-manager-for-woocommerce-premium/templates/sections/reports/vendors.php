<?php

defined( 'ABSPATH' ) or exit;

$_GET['tab'] = $GLOBALS['hook_suffix'] = 'vendors_sales';
$page_id     = isset( $_GET['page_id'] ) && $_GET['page_id'] > 0 ? $_GET['page_id'] : '';
$report_type = ! empty( $_GET['report'] ) ? $_GET['report'] : 'vendors_sales';

if ( current_user_can( 'view_woocommerce_reports' ) ) : ?>

<div id="yith-wcmf-reports">

	<h1><?php echo __( 'Vendors', 'yith-frontend-manager-for-woocommerce' ); ?></h1>

	<div class="buttons">
		<a href="?<?php echo $page_id > 0 ? 'page_id=' . $page_id . '&amp;' : ''; ?>reports=vendors&amp;report=vendors_sales" class="button <?php echo $report_type == 'vendors_sales' ? 'current' : ''; ?>"><?php echo __( 'Vendors Sales', 'yith-frontend-manager-for-woocommerce' ); ?></a>
		<a href="?<?php echo $page_id > 0 ? 'page_id=' . $page_id . '&amp;' : ''; ?>reports=vendors&amp;report=vendors_registered" class="button <?php echo $report_type == 'vendors_registered' ? 'current' : ''; ?>"><?php echo __( 'Vendors Registered', 'yith-frontend-manager-for-woocommerce' ); ?></a>
		<a href="?<?php echo $page_id > 0 ? 'page_id=' . $page_id . '&amp;' : ''; ?>reports=vendors&amp;report=commissions_by_vendor" class="button <?php echo $report_type == 'commissions_by_vendor' ? 'current' : ''; ?>"><?php echo __( 'Commissions by Vendor', 'yith-frontend-manager-for-woocommerce' ); ?></a>
	</div>

	<div class="wrap woocommerce">

		<?php

		YITH_Frontend_Manager_Section_Reports::require_reports_core_files();

		require_once YITH_WPV_PATH . '/includes/class.yith-reports.php';

		$report_tab = isset( $_GET['report'] ) ? $_GET['report'] : 'vendors_sales';

		if ( $report_tab == 'vendors_sales' ) {

			require_once YITH_WPV_PATH . '/includes/reports/class.yith-report-vendors-sales.php';
			$report = new YITH_Report_Vendors_Sales();
			$report->output_report();

		} elseif ( $report_tab == 'vendors_registered' ) {

			require_once YITH_WPV_PATH . '/includes/reports/class.yith-report-vendors-registered.php';
			$report = new YITH_Report_Vendors_Registered();
			$report->output_report();

		} elseif ( $report_tab == 'commissions_by_vendor' ) {

			require_once YITH_WPV_PATH . '/includes/reports/class.yith-report-commissions-by-vendor.php';
			$report = new YITH_Report_Commissions_By_Vendor();
			$report->output_report();
		}

		?>

	</div>

</div>

<?php else : ?>

<p><?php echo __( 'Only users with "Shop Reports" capabilities can view this page.', 'yith-frontend-manager-for-woocommerce' ); ?></p>

<?php endif; ?>

<?php
do_action( 'yith_wcfm_reports' );
