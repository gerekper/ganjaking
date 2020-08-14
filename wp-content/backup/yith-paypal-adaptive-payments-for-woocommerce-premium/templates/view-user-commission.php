<?php
if( !defined('ABSPATH')){
    exit;
}

$user_commission = YITH_PADP_Receiver_Commission()->get_transaction( $args['user_id'], false, $args['offset'], $args['limit'] );
$has_commission = count( $user_commission )> 0;
$commission_columns = yith_paypal_adaptive_payments_get_account_commission_columns();
do_action( 'yith_paypal_adaptive_payments_before_account_commission', $has_commission );
if( $has_commission ):
?>
<table class="shop_table shop_table_responsive my_account_commission">
    <thead>
    <tr>
        <?php foreach ( yith_paypal_adaptive_payments_get_account_commission_columns() as $column_id => $column_name ) : ?>
            <th class="<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach( $user_commission as $commission ):?>
        <tr class="order">
            <?php foreach( $commission_columns as $column_id => $column_name ):?>
            <td class="<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
               <?php
                    if( 'order_id' == $column_id ){

                        $columns_value = sprintf('#%s ', $commission['order_id'] );

                        echo $columns_value;

                    }elseif( 'transaction_value' == $column_id ){

                        echo wc_price( $commission['transaction_value'] );

                    }elseif( 'transaction_status' == $column_id ){

                        $columns_value = sprintf('<span class="%1$s %2$s">%2$s</span>', 'ywpadp_transaction_status',$commission['transaction_status'] );

                        echo $columns_value;

                    }elseif( 'transaction_date' == $column_id ){

                        $columns_value = sprintf('<time datetime="%s" title="%s">%s</time>', date( 'Y-m-d', strtotime( $commission['transaction_date'] ) ), strtotime( $commission['transaction_date'] ), date_i18n( get_option( 'date_format' ), strtotime( $commission['transaction_date'] ) )  );

                        echo $columns_value;
                    }else{

                        if( has_action( 'yith_paypal_adaptive_payments_column_'.$column_id ) ) {

                            do_action( 'yith_paypal_adaptive_payments_column_' . $column_id, $column_id, $column_name, $commission );
                        }
                    }
             ?>
               </td>
            <?php endforeach;?>
        </tr>
    
    <?php endforeach;?>
    <?php if ( $args['page_links'] ) : ?>
        <tr class="pagination-row">
            <td colspan="<?php echo count( $commission_columns ); ?>"><?php echo $args['page_links'];?></td>
        </tr>
    <?php endif; ?>
    </tbody>
    
</table>
<?php else:?>
    <div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
        <a class="woocommerce-Button button" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
            <?php _e( 'Go Shopping', 'yith-paypal-adaptive-payments-for-woocommerce' ) ?>
        </a>
        <?php _e( 'No commissions found.', 'yith-paypal-adaptive-payments-for-woocommerce' ); ?>
    </div>
<?php
endif;

do_action( 'yith_paypal_adaptive_payments_after_account_commission', $has_commission );