<?php

defined( 'ABSPATH' ) or exit;

$page_id = isset( $_GET['page_id'] ) && $_GET['page_id'] > 0 ?  $_GET['page_id'] : '';

?>

<div id="yith-wcfm-orders">

    <h1><?php echo apply_filters( 'yith_wcfm_orders_section_title',__('Orders', 'yith-frontend-manager-for-woocommerce') ); ?></h1>
    <form id="orders-search" classe="orders-search" action="<?php echo $section_obj->get_url(); ?>" method="GET">
        <input class="text-field" type="text" name="search" value="<?php echo isset( $_GET['search'] ) ? $_GET['search'] : '' ?>" />
        <input class="search-submit" type="submit" value="<?php _ex( 'Search', 'Frontend Button Label', 'yith-frontend-manager-for-woocommerce' ); ?>" />
    </form>

    <?php

    if ( isset( $_GET['trashed'] ) && $_GET['trashed'] == 'ok' ) {
        wc_print_notice( __('1 order moved to the Trash.', 'yith-frontend-manager-for-woocommerce'), 'success' );
    }?>

    <div class="orders-filter">
        <ul class="yith-wcfm-order-status subsubsub">
            <?php
                $all_class = 'all';
                if( empty( $_GET['order_status'] ) || ( isset( $_GET['order_status'] ) && 'all' == $_GET['order_status'] ) ){
                    $all_class .= ' current';
                }
            ?>
            <li class="<?php echo $all_class; ?>" >
                <a href="<?php echo $section_obj->get_url(); ?>"><?php _ex( 'All', 'Order status label', 'yith-frontend-manager-for-woocommerce' ); ?></a> (<?php echo $pagination_args['total_items']; ?>)
            </li>
            <?php
            $status = wc_get_order_statuses();
            $orders_count = wp_count_posts( 'shop_order' );
            foreach ( $status as $key => $label ) {
                $order_count = isset( $orders_count->$key ) ? $orders_count->$key : 0;
                if( $order_count != 0 ){
	                $class = $key;
	                if( isset(  $_GET['order_status'] ) && $key == $_GET['order_status'] ){
		                $class .= ' current';
	                }
	                $section_url = add_query_arg( array( 'order_status' => $key ), $section_obj->get_url() );
	                printf( '<li class="%s"><a href="%s">%s</a> (%s)</li>', $class, $section_url, $label, $order_count );
                }
            }

            ?>
        </ul>
        <nav class="yith-wcfm-pagination">
	        <?php $section_obj->pagination( 'top' ); ?>
        </nav>
    </div>



    <table class="table">
        <tr>
            <?php foreach( $columns as $column ) : ?>
            <?php $class = strtolower( str_replace( ' ', '_', $column ) ); ?>
                <th class="<?php echo $class; ?>"><?php echo $column ?></th>
            <?php endforeach; ?>
        </tr>
    <?php

    if( count( $orders ) > 0 ) :
        foreach ( $orders as $order ) :
            ?>
            <tr>
                <?php foreach( $columns as $column =>  $label ) : ?>
                    <?php $class = $column; ?>
                    <?php $class .= isset( $cols_class[ $column ] ) ? ' ' . $cols_class[ $column ] : ''; ?>
                    <td class="<?php echo $class ?>"><?php do_action( 'yith_wcfm_order_cols', $column, $order ) ?></td>
                <?php endforeach; ?>
            </tr>

        <?php endforeach;
    else : ?>

        <tr><td colspan="8"><?php echo __('No orders found', 'yith-frontend-manager-for-woocommerce'); ?></td></tr>

    <?php endif; ?>

    </table>

    <?php $section_obj->pagination( 'bottom' ); ?>

</div>

<?php wp_reset_query(); ?>
