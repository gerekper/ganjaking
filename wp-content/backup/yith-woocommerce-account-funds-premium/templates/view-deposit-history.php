<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

$my_deposit_history = array( 'description' => __( 'Description', 'yith-woocommerce-account-funds' ), 'log-date' => __( 'Date', 'yith-woocommerce-account-funds' ), 'deposit-amount' => __( 'Transactions', 'yith-woocommerce-account-funds' ), );

$fund_totals = 0;
$filter_type = isset( $_POST[ 'filter_deposit_type' ] ) ? $_POST[ 'filter_deposit_type' ] : '';
$operation_type = ywf_get_operation_type();
?>

<div class="ywf_history_container">
   
        <?php if( $show_filter_form ): ?>
        <form method="post">
            <div class="filter_container">
                <select name="filter_deposit_type">
                    <option value="" <?php selected( '', $filter_type ); ?>><?php _e( 'Show All', 'yith-woocommerce-account-funds'  );?></option>
                    <?php foreach( $operation_type as $key => $type_description ):?>
                        <option value="<?php echo $key;?>" <?php selected( $filter_type, $key );?>><?php echo $type_description;?></option>
                    <?php endforeach;?>
                </select>
                <input type="submit" class="button ywf_button" value="<?php _e( 'Filter', 'yith-woocommerce-account-funds' ); ?>">
            </div>
        </form>
       <?php endif;?>
    <?php if ( count( $user_log_items ) > 0 ): ?>
        <table class="shop_table shop_table_responsive my_account_orders my_funds_history">
            <thead>
            <tr>
                <?php foreach ( $my_deposit_history as $column_id => $column_name ) : ?>
                    <th class="<?php echo esc_attr( $column_id ); ?>"><span
                            class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
                <?php endforeach; ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ( $user_log_items as $log ):


                $date_deposit = sprintf( 'date-type_operation="%s"', $log->type_operation );
                ?>
                <tr class="order" <?php echo $date_deposit; ?> >
                    <?php foreach ( $my_deposit_history as $column_id => $column_name ) :
                        switch ( $column_id ) {

                            case 'description':

                                $funds_used = get_post_meta( $log->order_id, '_order_funds', true );

                                    $order = wc_get_order( $log->order_id );
                                    $order_link ='';
                                    $item_count = 0;
                                if( $order ) {
                                    $item_count = $order->get_item_count();
                                    $order_link = sprintf( '<a href="%s">%s%s</a>', esc_url( $order->get_view_order_url() ), _x( '#', 'hashtag before order number', 'yith-woocommerce-account-funds' ), $order->get_order_number() );
                                } $reason = '';
                                    if( !empty( $log->description ) ) {
                                        $reason = sprintf( '%s: %s', __( 'Reason', 'yith-woocommerce-account-funds' ), $log->description );
                                    }

                                    if( 'pay' === $log->type_operation ) {

                                        $sub_content = sprintf( _n( '%s item', '%s items', $item_count, 'yith-woocommerce-account-funds' ), $item_count );
                                        $content = sprintf( '<p>%s %s %s %s</p>', __( 'Funds used to purchase', 'yith-woocommerce-account-funds' ), $sub_content, __( 'in order', 'yith-woocommerce-account-funds' ), $order_link );

                                    }
                                    elseif( 'deposit' === $log->type_operation ) {

                                        $content = sprintf( '<p>%s %s</p>', __( 'Order Credited Funds', 'yith-woocommerce-account-funds' ), $order_link );

                                    }
                                    elseif( 'restore' === $log->type_operation ) {

                                        $order_status = $order ? wc_get_order_status_name( $order->get_status() ) : __('deleted','yith-woocommerce-account-funds' );
                                        $content = sprintf( '<p>%s %s %s %s <small>%s</small></p>', __( 'Funds restored because order', 'yith-woocommerce-account-funds' ), $order_link, __( 'switched to', 'yith-woocommerce-account-funds' ), $order_status, $reason );


                                    }
                                    elseif( 'remove' === $log->type_operation ) {

                                        $content = sprintf( '<p>%s %s <small>%s</small></p>', __( 'Refunded credits in order', 'yith-woocommerce-account-funds' ), $order_link, $reason );
                                    }
                                    else {

                                        $content = sprintf( '<p>%s</p>', $log->description );
                                    }
                                    break;

                            case 'log-date':
                                $content = sprintf( '<time datetime="%s" title="%s">%s</time>', date( 'Y-m-d', strtotime( $log->date_added ) ), esc_attr( strtotime( $log->date_added ) ), date_i18n( get_option( 'date_format' ), strtotime( $log->date_added ) ) );
                                break;

                            case 'deposit-amount':

	                            $fund_user =  apply_filters( 'yith_show_used_funds', $log->fund_user );
                                $fund_totals += floatval( $fund_user );
                                $sign = $log->fund_user > 0 ? '+' : '';
                                $content = sprintf( '<span class="ywf_%s">%s</span>', $log->type_operation, $sign . wc_price( $fund_user ) );

                                break;
                        }
                        ?>
                        <td class="<?php echo esc_attr( $column_id ); ?>"
                            data-title="<?php echo esc_attr( $column_name ); ?>">
                            <?php echo $content; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            <?php if( $show_total ):

                $user_id = get_current_user_id();
	            $customer    = new YITH_YWF_Customer( $user_id );
                $total = apply_filters( 'yith_show_available_funds', $customer->get_funds() );
                ?>
            <tr class="fund_totals">
                <td colspan="<?php echo count( $my_deposit_history ) - 1; ?>"><?php echo __( 'Your available funds', 'yith-woocommerce-account-funds' ); ?></td>
                <td colspan="1"><?php echo wc_price(  $total  ); ?></td>
            </tr>
            <?php endif;?>
            <?php if ( !empty( $page_links ) ) : ?>
                <tr class="pagination-row">
                    <td colspan="<?php echo count( $my_deposit_history ); ?>"><?php echo $page_links ?></td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    <?php else: ?>
        <span><?php _e( 'No deposit history for this user', 'yith-woocommerce-account-funds' ); ?></span>
    <?php endif; ?>
</div>
