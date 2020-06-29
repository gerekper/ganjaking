<?php
defined( 'ABSPATH' ) or exit;

/*
 *  Sessions
 */

global $wpdb;
add_thickbox();

if ( isset( $_GET['act'] ) && $_GET['act'] == 'hide' && isset( $_GET['id'] ) && $_GET['id'] > 0 ) {
    $result = $wpdb->query( "UPDATE {$wpdb->prefix}yith_wcch_sessions SET del='1' WHERE id='" . $_GET['id'] . "'" );
    if ( $result ) : ?><div class="notice notice-success is-dismissible"><p><?php echo __( 'Your session was deleted, you can find it in <a href="admin.php?page=yith-wcch-trash.php">' . __( 'trash', 'yith-woocommerce-customer-history' ) . '</a>', 'yith-woocommerce-customer-history' ); ?></p></div><?php endif;
}

$page = isset( $_GET['p'] ) && $_GET['p'] > 1 ? $_GET['p'] : 1;
$results_per_page = get_option( 'yith-wcch-results_per_page' ) > 0 ? get_option( 'yith-wcch-results_per_page' ) : 50;
$sessions_offset = ( $page - 1 ) * $results_per_page;

$user_id = isset( $_GET['user_id'] ) ? $_GET['user_id'] : 0;
$user_query = $user_id > 0 ? "user_id='$user_id' AND" : '';

$num_rows = $wpdb->get_var( "SELECT COUNT(id) FROM {$wpdb->prefix}yith_wcch_sessions WHERE $user_query del='0'" );
$max_pages = ceil( $num_rows / $results_per_page );

$trash_rows = $wpdb->get_var( "SELECT COUNT(id) FROM {$wpdb->prefix}yith_wcch_sessions WHERE $user_query del='1'" );

?>

<div id="yith-woocommerce-customer-history">
    <div id="sessions" class="wrap">

        <h1><?php echo __( 'Sessions', 'yith-woocommerce-customer-history' ) . ( $user_id > 0 ? ' <small>(user #' . $user_id . ')</small>' : '' ); ?></h1>
        <p><?php echo __( 'Complete sessions list.', 'yith-woocommerce-customer-history' ); ?></p>

        <div class="tablenav top">
            <a href="" class="button"><i class="fa fa-refresh" aria-hidden="true" style="margin-right: 0px;"></i> <?php echo __( 'Check for new Sessions', 'yith-woocommerce-customer-history' ); ?></a>
            <ul class="subsubsub" style="margin: 1px 0px 0px 10px; display: inline-block; float: none;">
                <li class="sessions"><a href="admin.php?page=yith-wcch-sessions.php&user_id=<?php echo $user_id; ?>" class="current"><?php echo __( 'Sessions', 'yith-woocommerce-customer-history' ); ?></a> |</li>
                <li class="trash"><a href="admin.php?page=yith-wcch-trash.php&user_id=<?php echo $user_id; ?>" style="padding-right: 0;"><?php echo __( 'Trash', 'yith-woocommerce-customer-history' ); ?></a> (<?php echo $trash_rows; ?>)</li>
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
                <?php if ( get_option('yith-wcch-save_user_ip') ) : ?>
                    <th class="ip"><?php echo __( 'IP', 'yith-woocommerce-customer-history' ); ?></th>
                <?php endif; ?>
                <th><?php echo __( 'URL', 'yith-woocommerce-customer-history' ); ?></th>
                <th class="referer"><?php echo __( 'Referer', 'yith-woocommerce-customer-history' ); ?></th>
                <th class="date"><?php echo __( 'Date', 'yith-woocommerce-customer-history' ); ?></th>
                <th class="actions" style="width: 100px;"><?php echo __( 'Actions', 'yith-woocommerce-customer-history' ); ?></th>
            </tr>

            <?php

            $query = "SELECT * FROM {$wpdb->prefix}yith_wcch_sessions WHERE $user_query del='0' ORDER BY reg_date DESC LIMIT $sessions_offset,$results_per_page";
            $rows = $wpdb->get_results( $query );
            if ( count( $rows ) > 0 ) :

                foreach ( $rows as $key => $value ) :

                    $is_session_action = false;
                    $tr_class = '';
                    $user = NULL;

                    if ( $value->user_id != 999999999999999 ) { $user = get_user_by( 'id', $value->user_id ); }
                    else if ( $value->user_id > 0 ) { $user = 'bot'; }

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

                                $_product = wc_get_product( $url_array['2'] );
                                $product_sku = $_product->get_sku() ? ' (' . $_product->get_sku() . ')' : '';
                                $url = __( 'Add to cart', 'yith-woocommerce-customer-history' ) . ': x' . $url_array['3'] . ' products #' . $url_array['2'] . $product_sku;
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

                    } else if ( strpos( $value->url, 'OT::' ) == 1 ) {

                        if ( get_option( 'yith-wcch-show_bot_sessions' ) ) {
                            $is_session_action = true;
                            $url_array = explode( '::', $value->url );
                            $url = '<a href="">' . get_site_url() . '/' . $url_array[2] . '</a><br /><i class="fa fa-bullseye" aria-hidden="true" style="margin-right: 5px; "></i> ' . $url_array[1] . '<a>';
                            $tr_class = 'bot';
                            $icon = 'file';
                        } else { $url = ''; }

                    } else { $url = get_site_url() . '/' . $value->url; }

                    $timezone = get_option('yith-wcch-timezone') ? get_option('yith-wcch-timezone') : 0;
                    $reg_date = date( 'Y-m-d  H:i:s', strtotime( $value->reg_date ) + 3600 * $timezone );

                    if ( $url ) : ?>

                        <tr class="<?php echo ( isset( $user->caps['administrator'] ) && $user->caps['administrator'] ? 'admin' : '' ) . ' ' . $tr_class; ?>">
                            <td class="user">
                                <?php if ( $user == NULL ) : echo '<i class="fa fa-user-secret" aria-hidden="true" style="margin-right: 5px;"></i> ' . __( 'Guest', 'yith-woocommerce-customer-history' ); ?>
                                <?php elseif ( $user == 'bot' ) : echo '<i class="fa fa-user-secret" aria-hidden="true" style="margin-right: 5px;"></i> ' . __( 'Bot', 'yith-woocommerce-customer-history' ); else : ?>
                                    <i class="fa fa-user" aria-hidden="true" style="margin-right: 5px;"></i>
                                    <a href="admin.php?page=yith-wcch-customer.php&user_id=<?php echo esc_html( $user->ID ); ?>">
                                        <?php echo ( $user->first_name != '' || $user->last_name != '' ) ? $user->first_name . ' ' . $user->last_name : $user->user_nicename; ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <?php if ( get_option('yith-wcch-save_user_ip') ) : ?>
                                <td class="ip"><?php echo isset( $value->ip ) ? $value->ip : '<i>' . __( 'Unregistered IP address', 'yith-woocommerce-customer-history' ) . '</i>'; ?></td>
                            <?php endif; ?>
                            <td>
                                <?php if ( $is_session_action ) : echo '<strong><i class="fa fa-' . $icon . '" aria-hidden="true" style="margin-right: 5px;"></i> ' . $url . '</strong>'; else : ?>
                                    <i class="fa fa-file-o" aria-hidden="true" style="margin-right: 5px;"></i>
                                    <a href="<?php echo $url; ?>?KeepThis=true&TB_iframe=true&modal=false" title="<?php echo $url; ?>" onclick="return false;" class="thickbox"><?php echo $url; ?></a>
                                <?php endif; ?>
                            </td>
                            <td class="referer" title="<?php echo isset( $value->referer ) ? $value->referer : ''; ?>"><?php echo isset( $value->referer ) ? $value->referer : ''; ?></td>
                            <td class="date"><i class="fa fa-calendar" aria-hidden="true" style="margin-right: 5px;"></i> <?php echo $reg_date; ?></td>
                            <td class="actions">
                                <?php if ( $is_session_action ) : ?>
                                    <a href="" onclick="return false;" class="button disabled"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                <?php else : ?>
                                    <a href="<?php echo $url; ?>?KeepThis=true&TB_iframe=true&modal=false" onclick="return false;" class="thickbox button"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                <?php endif; ?>
                                <a href="admin.php?page=yith-wcch-sessions.php&act=hide&id=<?php echo $value->id; ?>" class="button"><i class="fa fa-trash" aria-hidden="true"></i></a>
                            </td>
                        </tr>

                    <?php endif; ?>
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

    </div>
</div>
