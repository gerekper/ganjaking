<?php
defined( 'ABSPATH' ) or exit;

/*
 *  Statistics
 */

global $wpdb;
add_thickbox();

$page = isset( $_GET['p'] ) && $_GET['p'] > 1 ? $_GET['p'] : 1;
$results_per_page = get_option( 'yith-wcch-results_per_page' ) > 0 ? get_option( 'yith-wcch-results_per_page' ) : 50;
$offset = ( $page - 1 ) * $results_per_page;

$num_users = count_users()['total_users'];
$max_pages = ceil( $num_users / $results_per_page );

$users = get_users();
$spent = array();

foreach ( $users as $key => $user) {
    $order_count = count( get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => $user->ID,
        'post_type'   => 'shop_order',
        'post_status' =>  'any',
        'post_parent' => '0',
    ) ) );
    if ( $order_count > 0 ) {
        $spent[$user->ID] = yith_ch_get_customer_total_spent( $user->ID );
    }
}
arsort( $spent );

?>

<div id="yith-woocommerce-customer-history">
    <div id="statistics" class="wrap">

        <h1><?php echo __( 'Statistics', 'yith-woocommerce-customer-history' ); ?></h1>
        <p><?php echo __( 'Check your shop statistics.', 'yith-woocommerce-customer-history' ); ?></p>

        <div class="tablenav top">
            <ul class="subsubsub" style="margin-top: 4px;">
                <li class="stats"><a href="admin.php?page=yith-wcch-stats.php"><?php echo __( 'Site Pages', 'yith-woocommerce-customer-history' ); ?></a> |</li>
                <li class="searches"><a href="admin.php?page=yith-wcch-stats-searches.php"><?php echo __( 'Searches', 'yith-woocommerce-customer-history' ); ?></a> |</li>
                <li class="spent"><a href="admin.php?page=yith-wcch-stats-spent.php" class="current"><?php echo __( 'Total spent by user', 'yith-woocommerce-customer-history' ); ?></a></li>
            </ul>
            <div class="tablenav-pages">
                <div class="pagination-links">
                    <?php echo __( 'Total', 'yith-woocommerce-customer-history' ) . ': ' . $num_users; ?> &nbsp; | &nbsp;
                    <?php echo __( 'Page', 'yith-woocommerce-customer-history' ) . ': ' . $page . ' of ' . $max_pages; ?> &nbsp;
                    <?php if ( $page > 1 ) : ?>
                    <a class="prev-page" href="admin.php?page=yith-wcch-stats-spent.php&p=1"><span aria-hidden="true">‹‹</span></a>
                    <a class="prev-page" href="admin.php?page=yith-wcch-stats-spent.php&p=<?php echo $page - 1; ?>"><span aria-hidden="true">‹</span></a>
                    <?php else : ?>
                    <span class="tablenav-pages-navspan" aria-hidden="true">‹‹</span>
                    <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                    <?php endif; ?>
                    <?php if ( $page < $max_pages ) : ?>
                    <a class="next-page" href="admin.php?page=yith-wcch-stats-spent.php&p=<?php echo $page + 1; ?>"><span aria-hidden="true">›</span></a>
                    <a class="next-page" href="admin.php?page=yith-wcch-stats-spent.php&p=<?php echo $max_pages; ?>"><span aria-hidden="true">››</span></a>
                    <?php else : ?>
                    <span class="tablenav-pages-navspan" aria-hidden="true">›</span>
                    <span class="tablenav-pages-navspan" aria-hidden="true">››</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <table class="wp-list-table widefat fixed striped posts">
            <tr>
                <th style="width: 150px;"><?php echo __( 'Total spent', 'yith-woocommerce-customer-history' ); ?></th>
                <th><?php echo __( 'User', 'yith-woocommerce-customer-history' ); ?></th>
            </tr>

            <?php foreach ( $spent as $key => $value ) : $user = get_user_by( 'id', $key ); ?>

            <tr>
                <td><i class="fa fa-money" aria-hidden="true" style="margin-right: 5px;"></i> <strong><?php echo wc_price( $value ); ?></strong></td>
                <td><i class="fa fa-user" aria-hidden="true" style="margin-right: 5px;"></i> <a href="admin.php?page=yith-wcch-customer.php&user_id=<?php echo esc_html( $user->ID ); ?>"><?php echo esc_html( $user->display_name ); ?></a></td>
            </tr>
                
            <?php endforeach; ?>

        </table>

        <div class="tablenav top">
            <div class="tablenav-pages">
                <div class="pagination-links">
                    <?php echo __( 'Total', 'yith-woocommerce-customer-history' ) . ': ' . $num_users; ?> &nbsp; | &nbsp;
                    <?php echo __( 'Page', 'yith-woocommerce-customer-history' ) . ': ' . $page . ' of ' . $max_pages; ?> &nbsp;
                    <?php if ( $page > 1 ) : ?>
                    <a class="prev-page" href="admin.php?page=yith-wcch-stats-spent.php&p=1"><span aria-hidden="true">‹‹</span></a>
                    <a class="prev-page" href="admin.php?page=yith-wcch-stats-spent.php&p=<?php echo $page - 1; ?>"><span aria-hidden="true">‹</span></a>
                    <?php else : ?>
                    <span class="tablenav-pages-navspan" aria-hidden="true">‹‹</span>
                    <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                    <?php endif; ?>
                    <?php if ( $page < $max_pages ) : ?>
                    <a class="next-page" href="admin.php?page=yith-wcch-stats-spent.php&p=<?php echo $page + 1; ?>"><span aria-hidden="true">›</span></a>
                    <a class="next-page" href="admin.php?page=yith-wcch-stats-spent.php&p=<?php echo $max_pages; ?>"><span aria-hidden="true">››</span></a>
                    <?php else : ?>
                    <span class="tablenav-pages-navspan" aria-hidden="true">›</span>
                    <span class="tablenav-pages-navspan" aria-hidden="true">››</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<script>jQuery( document ).ready(function() { yit_open_admin_menu( 'toplevel_page_yith-wcch-customers' ); });</script>
