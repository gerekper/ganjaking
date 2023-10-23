<?php

defined( 'ABSPATH' ) or exit;

$GLOBALS['hook_suffix'] = $_GET['tab'] = 'orders';
$section_uri = yith_wcfm_get_section_url( 'current' );

$allowed_reports = apply_filters( 'yith_wcfm_orders_reports_type',
    array(
        'sales_by_date'     => __('Sales by date', 'woocommerce'),
        'sales_by_product'  => __('Sales by product', 'woocommerce'),
        'sales_by_category' => __('Sales by category', 'woocommerce'),
        'coupon_usage'      => __('Coupons by date', 'woocommerce'),
        'downloads'         => __('Customer downloads', 'woocommerce')
    )
);

if ( current_user_can( 'view_woocommerce_reports' ) ) : ?>

<div id="yith-wcmf-reports">

    <h1><?php echo __('Orders', 'woocommerce'); ?></h1>

    <div class="buttons">
        <?php foreach( $allowed_reports as $report_id => $report_name ) : ?>
            <a href="<?php echo add_query_arg( array( 'report' => $report_id ), $section_uri ) ?>" class="button <?php echo isset( $_GET['report'] ) && $_GET['report'] == $report_id ? 'current' : ''; ?>">
                <?php echo $report_name; ?>
            </a>
        <?php endforeach; ?>
	</div>

    <?php YITH_Frontend_Manager_Section_Reports::require_reports_core_files(); ?>
    <?php WC_Admin_Reports::output(); ?>

</div>

<?php else : ?>

<p><?php echo __( 'Only users with "Shop Reports" capabilities can view this page.', 'yith-frontend-manager-for-woocommerce'); ?></p>

<?php endif; ?>

<?php do_action( 'yith_wcfm_reports' );
