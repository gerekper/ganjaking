<?php
defined( 'ABSPATH' ) or exit;

/*
 *  Sessions
 */

global $wpdb;
add_thickbox();

$page = isset( $_GET['p'] ) && $_GET['p'] > 1 ? $_GET['p'] : 1;
$results_per_page = get_option( 'yith-wcch-results_per_page' ) > 0 ? get_option( 'yith-wcch-results_per_page' ) : 50;
$sessions_offset = ( $page - 1 ) * $results_per_page;

$user_id = isset( $_GET['user_id'] ) ? $_GET['user_id'] : 0;
$user_query = $user_id > 0 ? "user_id='$user_id' AND" : '';

$rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}yith_wcch_sessions WHERE $user_query del='0' AND url LIKE '%::search::%'" );
$num_rows = $wpdb->num_rows;
$max_pages = ceil( $num_rows / $results_per_page );

?>

<div id="yith-woocommerce-customer-history">
    <div id="searches" class="wrap">

        <h1><?php echo __( 'Searches', 'yith-woocommerce-customer-history' ); ?></h1>
        <p><?php echo __( 'Complete searches list.', 'yith-woocommerce-customer-history' ); ?></p>

        <div class="tablenav top">
            <a href="" class="button"><i class="fa fa-repeat" aria-hidden="true" style="margin-right: 0px;"></i> <?php echo __( 'Check for new Searches', 'yith-woocommerce-customer-history' ); ?></a>
            <div class="tablenav-pages">
                <div class="pagination-links">
                    <?php echo __( 'Total', 'yith-woocommerce-customer-history' ) . ': ' . $num_rows; ?> &nbsp; | &nbsp;
                    <?php echo __( 'Page', 'yith-woocommerce-customer-history' ) . ': ' . $page . ' of ' . $max_pages; ?> &nbsp;
                    <?php if ( $page > 1 ) : ?>
                    <a class="prev-page" href="admin.php?page=yith-wcch-searches.php&user_id=<?php echo $user_id; ?>&p=1"><span aria-hidden="true">‹‹</span></a>
                    <a class="prev-page" href="admin.php?page=yith-wcch-searches.php&user_id=<?php echo $user_id; ?>&p=<?php echo $page - 1; ?>"><span aria-hidden="true">‹</span></a>
                    <?php else : ?>
                    <span class="tablenav-pages-navspan" aria-hidden="true">‹‹</span>
                    <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                    <?php endif; ?>
                    <?php if ( $page < $max_pages ) : ?>
                    <a class="next-page" href="admin.php?page=yith-wcch-searches.php&user_id=<?php echo $user_id; ?>&p=<?php echo $page + 1; ?>"><span aria-hidden="true">›</span></a>
                    <a class="next-page" href="admin.php?page=yith-wcch-searches.php&user_id=<?php echo $user_id; ?>&p=<?php echo $max_pages; ?>"><span aria-hidden="true">››</span></a>
                    <?php else : ?>
                    <span class="tablenav-pages-navspan" aria-hidden="true">›</span>
                    <span class="tablenav-pages-navspan" aria-hidden="true">››</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <table class="wp-list-table widefat fixed striped posts">
            <tr>
                <th class="user"><?php echo __( 'User', 'yith-woocommerce-customer-history' ); ?></th>
                <th><?php echo __( 'URL', 'yith-woocommerce-customer-history' ); ?></th>
                <th class="date"><?php echo __( 'Date', 'yith-woocommerce-customer-history' ); ?></th>
            </tr>

            <?php

            $query = "SELECT * FROM {$wpdb->prefix}yith_wcch_sessions WHERE $user_query url LIKE '%::search::%' ORDER BY reg_date DESC LIMIT $sessions_offset,$results_per_page";
            $rows = $wpdb->get_results( $query );
            if ( count( $rows ) > 0 ) :

                foreach ( $rows as $key => $value ) :

                    $tr_class = '';
                    $user = $value->user_id > 0 ? get_user_by( 'id', $value->user_id ) : NULL;
                    $is_session_action = true;

                    $url_array = explode( '::', $value->url );
                    $tr_class = 'action action-' . $url_array['1'];
                    $url = $url_array['2'];

                    $timezone = get_option('yith-wcch-timezone') ? get_option('yith-wcch-timezone') : 0;
                    $reg_date = date( 'Y-m-d  H:i:s', strtotime( $value->reg_date ) + 3600 * $timezone );

                    ?>

                    <tr class="<?php echo ( isset( $user->caps['administrator'] ) && $user->caps['administrator'] ? 'admin' : '' ) . ' ' . $tr_class; ?>">
                        <td class="user">
                            <?php if ( $user == NULL ) : echo '<i class="fa fa-user-secret" aria-hidden="true" style="margin-right: 5px;"></i> ' . __( 'Guest', 'yith-woocommerce-customer-history' ); else : ?>
                                <i class="fa fa-user" aria-hidden="true" style="margin-right: 5px;"></i>
                                <a href="admin.php?page=yith-wcch-customer.php&user_id=<?php echo esc_html( $user->ID ); ?>"><?php echo $user->first_name . ' ' . $user->last_name; ?></a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ( $is_session_action ) : echo '<strong><i class="fa fa-search" aria-hidden="true" style="margin-right: 5px;"></i> ' . $url . '</strong>'; else : ?>
                                <a href="<?php echo $url; ?>?KeepThis=true&TB_iframe=true&modal=false" onclick="return false;" class="thickbox"><?php echo $url; ?></a>
                            <?php endif; ?>
                        </td>
                        <td class="date"><i class="fa fa-calendar" aria-hidden="true" style="margin-right: 5px;"></i> <?php echo $reg_date; ?></td>
                    </tr>

                <?php endforeach; ?>
            <?php endif; ?>

        </table>

        <div class="tablenav top">
            <div class="tablenav-pages">
                <div class="pagination-links">
                    <?php echo __( 'Total', 'yith-woocommerce-customer-history' ) . ': ' . $num_rows; ?> &nbsp; | &nbsp;
                    <?php echo __( 'Page', 'yith-woocommerce-customer-history' ) . ' ' . $page . ' of ' . $max_pages; ?> &nbsp;
                    <?php if ( $page > 1 ) : ?>
                    <a class="prev-page" href="admin.php?page=yith-wcch-searches.php&user_id=<?php echo $user_id; ?>&p=1"><span aria-hidden="true">‹‹</span></a>
                    <a class="prev-page" href="admin.php?page=yith-wcch-searches.php&user_id=<?php echo $user_id; ?>&p=<?php echo $page - 1; ?>"><span aria-hidden="true">‹</span></a>
                    <?php else : ?>
                    <span class="tablenav-pages-navspan" aria-hidden="true">‹‹</span>
                    <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                    <?php endif; ?>
                    <?php if ( $page < $max_pages ) : ?>
                    <a class="next-page" href="admin.php?page=yith-wcch-searches.php&user_id=<?php echo $user_id; ?>&p=<?php echo $page + 1; ?>"><span aria-hidden="true">›</span></a>
                    <a class="next-page" href="admin.php?page=yith-wcch-searches.php&user_id=<?php echo $user_id; ?>&p=<?php echo $max_pages; ?>"><span aria-hidden="true">››</span></a>
                    <?php else : ?>
                    <span class="tablenav-pages-navspan" aria-hidden="true">›</span>
                    <span class="tablenav-pages-navspan" aria-hidden="true">››</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>
