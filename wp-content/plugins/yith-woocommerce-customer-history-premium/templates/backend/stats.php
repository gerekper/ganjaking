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

$rows = $wpdb->get_results( "SELECT COUNT(id) as qta, url FROM {$wpdb->prefix}yith_wcch_sessions WHERE del='0' AND url NOT LIKE '%::%' GROUP BY url" );
$num_rows = $wpdb->num_rows;
$max_pages = ceil( $num_rows / $results_per_page );

?>

<div id="yith-woocommerce-customer-history">
    <div id="statistics" class="wrap">

        <h1><?php echo __( 'Statistics', 'yith-woocommerce-customer-history' ); ?></h1>
        <p><?php echo __( 'Check your shop statistics.', 'yith-woocommerce-customer-history' ); ?></p>

        <div class="tablenav top">
            <ul class="subsubsub" style="margin-top: 4px;">
                <li class="stats"><a href="admin.php?page=yith-wcch-stats.php" class="current"><?php echo __( 'Site Pages', 'yith-woocommerce-customer-history' ); ?></a> |</li>
                <li class="searches"><a href="admin.php?page=yith-wcch-stats-searches.php"><?php echo __( 'Searches', 'yith-woocommerce-customer-history' ); ?></a> |</li>
                <li class="spent"><a href="admin.php?page=yith-wcch-stats-spent.php"><?php echo __( 'Total spent by user', 'yith-woocommerce-customer-history' ); ?></a></li>
            </ul>
            <div class="tablenav-pages">
                <div class="pagination-links">
                    <?php echo __( 'Total', 'yith-woocommerce-customer-history' ) . ': ' . $num_rows; ?> &nbsp; | &nbsp;
                    <?php echo __( 'Page', 'yith-woocommerce-customer-history' ) . ': ' . $page . ' of ' . $max_pages; ?> &nbsp;
                    <?php if ( $page > 1 ) : ?>
                    <a class="prev-page" href="admin.php?page=yith-wcch-stats.php&p=1"><span aria-hidden="true">‹‹</span></a>
                    <a class="prev-page" href="admin.php?page=yith-wcch-stats.php&p=<?php echo $page - 1; ?>"><span aria-hidden="true">‹</span></a>
                    <?php else : ?>
                    <span class="tablenav-pages-navspan" aria-hidden="true">‹‹</span>
                    <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                    <?php endif; ?>
                    <?php if ( $page < $max_pages ) : ?>
                    <a class="next-page" href="admin.php?page=yith-wcch-stats.php&p=<?php echo $page + 1; ?>"><span aria-hidden="true">›</span></a>
                    <a class="next-page" href="admin.php?page=yith-wcch-stats.php&p=<?php echo $max_pages; ?>"><span aria-hidden="true">››</span></a>
                    <?php else : ?>
                    <span class="tablenav-pages-navspan" aria-hidden="true">›</span>
                    <span class="tablenav-pages-navspan" aria-hidden="true">››</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <table class="wp-list-table widefat fixed striped posts">
            <tr>
                <th style="width: 70px;"><?php echo __( 'Sessions', 'yith-woocommerce-customer-history' ); ?></th>
                <th><?php echo __( 'URL', 'yith-woocommerce-customer-history' ); ?></th>
            </tr>

            <?php
            $query = "SELECT COUNT(id) as qta, url FROM {$wpdb->prefix}yith_wcch_sessions WHERE del='0' AND url NOT LIKE '%::%' GROUP BY url ORDER BY qta DESC LIMIT $offset,$results_per_page";
            $rows = $wpdb->get_results( $query );
            if ( count( $rows ) > 0 ) :
                foreach ( $rows as $key => $value ) :
                    $url = get_site_url() . '/' . $value->url; ?>

                    <tr>
                        <td class="qta"><i class="fa fa-files-o" aria-hidden="true" style="margin-right: 5px;"></i> <strong><?php echo $value->qta; ?></strong></td>
                        <td>
                            <i class="fa fa-angle-right" aria-hidden="true" style="margin-right: 5px;"></i>
                            <a href="<?php echo $url; ?>?KeepThis=true&TB_iframe=true&modal=false" onclick="return false;" class="thickbox"><?php echo $url; ?></a>
                        </td>
                    </tr>

                <?php endforeach; ?>
            <?php endif; ?>

        </table>

        <div class="tablenav top">
            <div class="tablenav-pages">
                <div class="pagination-links">
                    <?php echo __( 'Total', 'yith-woocommerce-customer-history' ) . ': ' . $num_rows; ?> &nbsp; | &nbsp;
                    <?php echo __( 'Page', 'yith-woocommerce-customer-history' ) . ': ' . $page . ' of ' . $max_pages; ?> &nbsp;
                    <?php if ( $page > 1 ) : ?>
                    <a class="prev-page" href="admin.php?page=yith-wcch-stats.php&p=1"><span aria-hidden="true">‹‹</span></a>
                    <a class="prev-page" href="admin.php?page=yith-wcch-stats.php&p=<?php echo $page - 1; ?>"><span aria-hidden="true">‹</span></a>
                    <?php else : ?>
                    <span class="tablenav-pages-navspan" aria-hidden="true">‹‹</span>
                    <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                    <?php endif; ?>
                    <?php if ( $page < $max_pages ) : ?>
                    <a class="next-page" href="admin.php?page=yith-wcch-stats.php&p=<?php echo $page + 1; ?>"><span aria-hidden="true">›</span></a>
                    <a class="next-page" href="admin.php?page=yith-wcch-stats.php&p=<?php echo $max_pages; ?>"><span aria-hidden="true">››</span></a>
                    <?php else : ?>
                    <span class="tablenav-pages-navspan" aria-hidden="true">›</span>
                    <span class="tablenav-pages-navspan" aria-hidden="true">››</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>
