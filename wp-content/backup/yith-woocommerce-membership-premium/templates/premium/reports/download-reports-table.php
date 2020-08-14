<?php
/*
 * Template for Download Reports Table
 */
?>
<?php

if ( !empty( $_REQUEST[ 'user_id' ] ) ) {
    $user_id = absint( $_REQUEST[ 'user_id' ] );
}
?>

<?php
$query_args = array(
    'group_by' => 'product_id',
    'select'   => 'product_id, COUNT(*) as count',
    'order_by' => 'count',
    'order'    => 'DESC'
);

if ( !empty( $user_id ) ) {
    $query_args[ 'where' ] = array(
        array(
            'key'   => 'user_id',
            'value' => $user_id
        )
    );
}

if ( isset( $_REQUEST[ 'order_by' ] ) ) {
    $query_args[ 'order_by' ] = $_REQUEST[ 'order_by' ];
}
if ( isset( $_REQUEST[ 'order' ] ) ) {
    $query_args[ 'order' ] = $_REQUEST[ 'order' ];
}

$results = YITH_WCMBS_Downloads_Report()->get_download_reports( $query_args );

$order                  = isset( $_REQUEST[ 'order' ] ) && $_REQUEST[ 'order' ] == 'ASC' ? 'DESC' : 'ASC';
$order_by_download_link = add_query_arg( array( 'order_by' => 'count', 'order' => $order ) );

$arrow_type = $order == 'DESC' ? 'up' : 'down';

if ( !empty( $results ) ) { ?>
    <table id="yith-wcmbs-reports-table-downloads" class="yith-wcmbs-reports-table-downloads fixed striped" data-order="<?php echo $order; ?>" data-user-id="<?php echo !empty( $user_id ) ? $user_id : ''; ?>">
        <thead>
        <tr>
            <th><?php _e( 'Product', 'yith-woocommerce-membership' ) ?></th>
            <th><?php
                $arrow = "<span class='dashicons dashicons-arrow-{$arrow_type}'></span>";
                echo '<a class="yith-wcmbs-reports-table-downloads-order-by-downloads" data-order-by="count" data-order="' . $order . '">' . __( 'Downloads', 'yith-woocommerce-membership' ) . $arrow . '</a>';
                ?></th>
        </tr>
        </thead>
        <?php foreach ( $results as $result ) : ?>
            <tr>
                <td class="title-col"><?php
                    $title     = get_the_title( $result->product_id );
                    $edit_link = get_edit_post_link( $result->product_id );
                    $view_link = get_permalink( $result->product_id );
                    echo "<a href='$view_link' target='_blank'><span class='dashicons dashicons-visibility'></span></a>";
                    echo "<a href='$edit_link' target='_blank'><span class='dashicons dashicons-admin-generic'></span></a>";
                    if ( $title ) {
                        echo $title;
                    } else {
                        echo __( 'Product', 'yith-woocommerce-membership' ) . ' #' . $result->product_id;
                    }

                    ?></td>
                <td class="downloads-col"><?php echo $result->count; ?></td>
            </tr>
        <?php endforeach ?>
    </table>
<?php } else {
    if ( !empty( $_REQUEST[ 'user_id' ] ) ) {
        $user_id        = absint( $_REQUEST[ 'user_id' ] );
        $username       = get_user_meta( $user_id, 'nickname', true );
        $user_edit_link = get_edit_user_link( $user_id );
        $username       = "<a href='$user_edit_link'>$username</a>";

        echo '<p>' . sprintf( __( 'No downloads for user %s', 'yith-woocommerce-membership' ), $username ) . '</p>';
    } else {
        echo '<p>';
        _e( 'No downloads', 'yith-woocommerce-membership' );
        echo '</p>';
    }
}
?>