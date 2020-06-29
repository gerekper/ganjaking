<?php
defined( 'ABSPATH' ) or exit;

/*
 *  Trash
 */

global $wpdb;
add_thickbox();

if ( isset( $_GET['act'] ) && $_GET['act'] == 'show' && isset( $_GET['id'] ) && $_GET['id'] > 0 ) {
    $result = $wpdb->query( "UPDATE {$wpdb->prefix}yith_wcch_sessions SET del='0' WHERE id='" . $_GET['id'] . "'" );
    if ( $result ) : ?><div class="notice notice-success is-dismissible"><p><?php echo __( 'Your session was restored, you can find it in <a href="admin.php?page=yith-wcch-sessions.php">' . __( 'sessions', 'yith-woocommerce-customer-history' ) . ' list</a>', 'yith-woocommerce-customer-history' ); ?></p></div><? endif;
}

$page = isset( $_GET['p'] ) && $_GET['p'] > 1 ? $_GET['p'] : 1;
$results_per_page = get_option( 'yith-wcch-results_per_page' ) > 0 ? get_option( 'yith-wcch-results_per_page' ) : 50;
$sessions_offset = ( $page - 1 ) * $results_per_page;

$user_id = isset( $_GET['user_id'] ) ? $_GET['user_id'] : 0;
$user_query = $user_id > 0 ? "user_id='$user_id' AND" : '';

$rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}yith_wcch_sessions WHERE $user_query del='1'" );
$num_rows = $wpdb->num_rows;
$max_pages = ceil( $num_rows / $results_per_page );

?>

<div id="yith-woocommerce-customer-history">
    <div id="sessions" class="wrap">

        <h1><?php echo __( 'Trash', 'yith-woocommerce-customer-history' ) . ( $user_id > 0 ? ' <small>(user #' . $user_id . ')</small>' : '' ); ?></h1>
        <p><?php echo __( 'All the sessions that you have deleted.', 'yith-woocommerce-customer-history' ); ?></p>

        <div class="tablenav top">
            <ul class="subsubsub" style="margin-top: 4px; display: inline-block; float: none;">
                <li class="sessions"><a href="admin.php?page=yith-wcch-sessions.php&user_id=<?php echo $user_id; ?>"><?php echo __( 'Sessions', 'yith-woocommerce-customer-history' ); ?></a> |</li>
                <li class="trash"><a href="admin.php?page=yith-wcch-trash.php&user_id=<?php echo $user_id; ?>" class="current"><?php echo __( 'Trash', 'yith-woocommerce-customer-history' ); ?></a></li>
            </ul>
            <div class="tablenav-pages">
                <div class="pagination-links">
                    <?php echo __( 'Total', 'yith-woocommerce-customer-history' ) . ': ' . $num_rows; ?> &nbsp; | &nbsp;
                    <?php echo __( 'Page', 'yith-woocommerce-customer-history' ) . ': ' . $page . ' of ' . $max_pages; ?> &nbsp;
                    <?php if ( $page > 1 ) : ?>
                    <a class="prev-page" href="admin.php?page=yith-wcch-sessions.php&user_id=<?php echo $user_id; ?>&p=1"><span aria-hidden="true">‹‹</span></a>
                    <a class="prev-page" href="admin.php?page=yith-wcch-sessions.php&user_id=<?php echo $user_id; ?>&p=<?php echo $page - 1; ?>"><span aria-hidden="true">‹</span></a>
                    <?php else : ?>
                    <span class="tablenav-pages-navspan" aria-hidden="true">‹‹</span>
                    <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                    <?php endif; ?>
                    <?php if ( $page < $max_pages ) : ?>
                    <a class="next-page" href="admin.php?page=yith-wcch-sessions.php&user_id=<?php echo $user_id; ?>&p=<?php echo $page + 1; ?>"><span aria-hidden="true">›</span></a>
                    <a class="next-page" href="admin.php?page=yith-wcch-sessions.php&user_id=<?php echo $user_id; ?>&p=<?php echo $max_pages; ?>"><span aria-hidden="true">››</span></a>
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
                <th class="actions" style="width: 100px;"><?php echo __( 'Actions', 'yith-woocommerce-customer-history' ); ?></th>
            </tr>

            <?php

            $query = "SELECT * FROM {$wpdb->prefix}yith_wcch_sessions WHERE $user_query del='1' ORDER BY reg_date DESC LIMIT $sessions_offset,$results_per_page";
            $rows = $wpdb->get_results( $query );
            if ( count( $rows ) > 0 ) :

                foreach ( $rows as $key => $value ) :

                    $tr_class = '';
                    $user = $value->user_id > 0 ? get_user_by( 'id', $value->user_id ) : NULL;
                    $is_session_action = false;

                    if ( strpos( $value->url, 'CTION::' ) == 1 ) {

                        $is_session_action = true;

                        $url_array = explode( '::', $value->url );
                        $tr_class = 'action action-' . $url_array['1'];

                        switch ( $url_array['1'] ) {
                            case 'search':

                                $url = __( 'Search', 'yith-woocommerce-customer-history' ) . ': ' . $url_array['2'];
                                $icon = 'search';
                                break;

                             case 'add_to_cart':

                                $url = __( 'Add to cart', 'yith-woocommerce-customer-history' ) . ': x' . $url_array['3'] . ' products #' . $url_array['2'];
                                $icon = 'shopping-cart';
                                break;

                            case 'new_order':

                                $url = __( 'New order', 'yith-woocommerce-customer-history' ) . ': #' . $url_array['2'];
                                $icon = 'check-circle';
                                break;

                            default:
                                $url = 'default_action';
                                break;
                        }

                    } else { $url = get_site_url() . '/' . $value->url; }

                    $timezone = get_option('yith-wcch-timezone') ? get_option('yith-wcch-timezone') : 0;
                    $reg_date = date( 'Y-m-d  H:i:s', strtotime( $value->reg_date ) + 3600 * $timezone );

                    ?>

                    <tr class="<?php echo ( isset( $user->caps['administrator'] ) && $user->caps['administrator'] ? 'admin' : '' ) . ' ' . $tr_class; ?>">
                        <td class="user">
                            <?php if ( $user == NULL ) : echo __( 'Guest', 'yith-woocommerce-customer-history' ); else : ?>
                                <a href="admin.php?page=yith-wcch-customer.php&user_id=<?php echo esc_html( $user->ID ); ?>">
                                    <?php echo ( $user->first_name != '' || $user->last_name != '' ) ? $user->first_name . ' ' . $user->last_name : $user->user_nicename; ?>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ( $is_session_action ) : echo '<strong><i class="fa fa-' . $icon . '" aria-hidden="true" style="margin-right: 5px;"></i> ' . $url . '</strong>'; else : ?>
                                <i class="fa fa-file-o" aria-hidden="true" style="margin-right: 5px;"></i>
                                <a href="<?php echo $url; ?>?KeepThis=true&TB_iframe=true&modal=false" onclick="return false;" class="thickbox"><?php echo $url; ?></a>
                            <?php endif; ?>
                        </td>
                        <td class="date"><?php echo $reg_date; ?></td>
                        <td class="actions">
                            <?php if ( $is_session_action ) : ?>
                                <a href="" onclick="return false;" class="button disabled"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            <?php else : ?>
                                <a href="<?php echo $url; ?>?KeepThis=true&TB_iframe=true&modal=false" onclick="return false;" class="thickbox button"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            <?php endif; ?>
                            <a href="admin.php?page=yith-wcch-trash.php&act=show&id=<?php echo $value->id; ?>" class="button"><i class="fa fa-undo" aria-hidden="true"></i></a>
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
                    <a class="prev-page" href="admin.php?page=yith-wcch-sessions.php&user_id=<?php echo $user_id; ?>&p=1"><span aria-hidden="true">‹‹</span></a>
                    <a class="prev-page" href="admin.php?page=yith-wcch-sessions.php&user_id=<?php echo $user_id; ?>&p=<?php echo $page - 1; ?>"><span aria-hidden="true">‹</span></a>
                    <?php else : ?>
                    <span class="tablenav-pages-navspan" aria-hidden="true">‹‹</span>
                    <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                    <?php endif; ?>
                    <?php if ( $page < $max_pages ) : ?>
                    <a class="next-page" href="admin.php?page=yith-wcch-sessions.php&user_id=<?php echo $user_id; ?>&p=<?php echo $page + 1; ?>"><span aria-hidden="true">›</span></a>
                    <a class="next-page" href="admin.php?page=yith-wcch-sessions.php&user_id=<?php echo $user_id; ?>&p=<?php echo $max_pages; ?>"><span aria-hidden="true">››</span></a>
                    <?php else : ?>
                    <span class="tablenav-pages-navspan" aria-hidden="true">›</span>
                    <span class="tablenav-pages-navspan" aria-hidden="true">››</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <style>

            #sessions table tr td { white-space: nowrap; }
            #sessions table tr .date, #sessions table tr .user { width: 15%; }

            /* #sessions table tr.admin { display: none; }
            #sessions table tr.admin td.user a { color: #a00; } */

            #sessions table tr.action-search { background-color: #def; }
            #sessions table tr.action-add_to_cart { background-color: #fed; }
            #sessions table tr.action-new_order { background-color: #dfd; }
            #sessions table tr:hover { background-color: #eee; }

        </style>

    </div>
</div>

<script>jQuery( document ).ready(function() { yit_open_admin_menu( 'toplevel_page_yith-wcch-customers' ); });</script>
