<?php
/*
 * Template for Reports Page
 */

$per_page = !empty( $_REQUEST[ 'per_page' ] ) && intval( $_REQUEST[ 'per_page' ] ) > 0 ? intval( $_REQUEST[ 'per_page' ] ) : 20;
$order_by = !empty( $_REQUEST[ 'orderby' ] ) ? $_REQUEST[ 'orderby' ] : 'date';
$order    = !empty( $_REQUEST[ 'order' ] ) ? $_REQUEST[ 'order' ] : 'DESC';
$user_id  = isset( $_REQUEST[ 'user_id' ] ) ? absint( $_REQUEST[ 'user_id' ] ) : false;


$query_args = array(
    'select' => 'COUNT(product_id) as downloads, COUNT(Distinct product_id) as distinct_downloads',
    'where'  => array(
        array(
            'key'   => 'user_id',
            'value' => $user_id
        )
    )
);

$results        = YITH_WCMBS_Downloads_Report()->get_download_reports( $query_args );
$user_downloads = is_array( $results ) && current( $results ) ? current( $results ) : false;
?>

<div class="yith-wcmbs-download-reports-downloads-details-by-user-totals" data-user_id="<?php echo $user_id; ?>">
    <?php
    $user_info = get_userdata( $user_id );

    $user_name = sprintf( _x( 'User #%s', 'Download reports table: user id', 'yith-woocommerce-membership' ), $user_id );

    if ( !empty( $user_info ) ) {
        $user_name = '<h1><a target="_blank" href="user-edit.php?user_id=' . absint( $user_info->ID ) . '">';
        $user_name .= esc_html( ucfirst( $user_info->display_name ) );
        $user_name .= '</a>';
        if ( $user_info->first_name || $user_info->last_name ) {
            $user_name .= ' ' . esc_html( sprintf( _x( '(%1$s %2$s)', 'full name between parentheses', 'yith-woocommerce-membership' ), ucfirst( $user_info->first_name ), ucfirst( $user_info->last_name ) ) );
        }
        $user_name .= '</h1>';
    }

    if ( $user_downloads ) {
        /*$downloads = sprintf( _x( 'Downloads: <strong>%1$s</strong> | Different Downloads: <strong>%2$s</strong>', 'Download reports - Downloads: 124', 'yith-woocommerce-membership' ),
                              $user_downloads->downloads,
                              $user_downloads->distinct_downloads );*/

        $downloads = "<table><tr><th>" . __( 'Downloads', 'yith-woocommerce-membership' ) . "</th><td>$user_downloads->downloads</td><th>" . __( 'Different Downloads', 'yith-woocommerce-membership' ) . "</th><td>$user_downloads->distinct_downloads</td></tr></table>";
        echo "<div class='yith-wcmbs-download-reports-downloads-details-by-user-totals-user-details'>$user_name $downloads</div>";
    }

    ?>
</div>

<div class="yith-wcmbs-download-reports-downloads-details-by-user-graphics-and-membership-info">
    <div class="yith-wcmbs-download-reports-downloads-details-by-user-graphics">
        <?php include YITH_WCMBS_TEMPLATE_PATH . '/reports/download-reports-graphics.php'; ?>
    </div>
    <div class="yith-wcmbs-download-reports-downloads-details-by-user-membership-info">
        <?php
        $member       = YITH_WCMBS_Members()->get_member( $user_id );
        $member_plans = $member->get_plans();
        if ( !!$member_plans ) : ?>
            <h3><?php _e( 'User Membership Plans', 'yith-woocommerce-membership' ) ?></h3>
            <div class="yith-wcmbs-download-reports-downloads-details-by-user-membership-info-table-wrapper">
                <table class="widefat fixed striped">
                    <?php foreach ( $member_plans as $membership ) :
                        if ( $membership instanceof YITH_WCMBS_Membership ) :?>
                            <tr class="yith-wcmbs-download-reports-downloads-details-by-user-membership-info-single-membership">
                                <td class="membership-title">
                                    <?php
                                    $membership_url  = get_edit_post_link( $membership->id );
                                    $membership_link = "<a target='_blank' href ='{$membership_url}'>";
                                    $membership_link .= $membership->get_plan_title();
                                    $membership_link .= "</a>";
                                    echo $membership_link;
                                    ?>
                                </td>
                                <td><span class='yith-wcmbs-membership-status <?php echo $membership->status ?>'><?php echo $membership->get_status_text() ?></span></td>
                                <td class="membership-order-info">
                                    <?php if ( $membership->order_id ) {
                                        $order_url  = get_edit_post_link( $membership->order_id );
                                        $order_link = "<a target='_blank' href ='{$order_url}'>";
                                        $order_link .= sprintf( __( 'Order #%s', 'yith-woocommerce-membership' ), $membership->order_id );
                                        $order_link .= "</a>";
                                        echo $order_link;
                                    } else {
                                        _e( 'Created by Admin', 'yith-woocommerce-membership' );
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endif;
                    endforeach; ?>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <div class="clear"></div>
</div>

<div id="yith-wcmbs-download-reports-downloads-details-by-user-<?php echo $user_id; ?>" class="yith-wcmbs-ajax-table"
     data-action="yith_wcmbs_get_download_reports_details_by_user"
     data-per_page="<?php echo $per_page; ?>"
     data-order="<?php echo $order; ?>"
     data-orderby="<?php echo $order_by; ?>"
     data-user_id="<?php echo $user_id; ?>"
>
    <div class="yith-wcmbs-reports-filters">
        <label><?php _e( 'Items per page', 'yith-woocommerce-membership' ) ?></label>
        <input type="number" class="yith-wcmbs-ajax-table-per-page" value="<?php echo $per_page ?>">
        <input type="button" class="yith-wcmbs-ajax-table-apply-button button" value="<?php _ex( 'Apply', 'Download reports: apply button', 'yith-woocommerce-membership' ) ?>">
    </div>

    <div class="yith-wcmbs-reports-download-reports-table">
        <?php $table = new YITH_WCMBS_Download_Reports_Details_By_User_Table();
        $table->prepare_items();
        $table->display(); ?>
    </div>

</div>