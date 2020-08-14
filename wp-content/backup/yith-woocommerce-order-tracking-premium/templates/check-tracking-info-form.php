<?php

if ( isset( $_POST["ywot-check-order-id"] ) ){

    $order = wc_get_order( $_POST["ywot-check-order-id"] );

    $class = new YITH_WooCommerce_Order_Tracking_Premium();

    if ( is_object( $order ) ){
        echo  $class->show_tracking_information( $order, '' );
        echo '<br>';
        echo '<br>';
    }
    else{
        echo '<div style="font-weight: bolder">' . esc_html__( "This order don't have any tracking info.", 'yith-woocommerce-order-tracking' ) . '</div>';
        echo '<br>';
    }
}

?>

<form method="post" name="form-check-tracking-info">
    <fieldset>
        <label for="ywot-check-order-id"><?php echo esc_html__( "Order ID: ", 'yith-woocommerce-order-tracking' ); ?></label>
        <input type="text" name="ywot-check-order-id" id="ywot-check-order-id" value="">
    </fieldset>
    <button type="submit"><?php echo esc_html__( "Submit", 'yith-woocommerce-order-tracking' ); ?></button>
</form>

