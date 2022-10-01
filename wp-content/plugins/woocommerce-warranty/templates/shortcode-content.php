<div id="primary">
    <div id="wcContent" role="main">
        <?php
        if ( isset($_GET['updated']) ) {
            echo '<div class="woocommerce-message">'. $_GET['updated'] .'</div>';
        }

        $order          = wc_get_order( $order_id );

        if ( 'completed' === WC_Warranty_Compatibility::get_order_prop( $order, 'status' ) && Warranty_Order::order_has_warranty( $order ) ) {
            if ( empty( $_GET['idx'] ) ) {
				// show products in an order.
				$completed = $order->get_date_completed() ? $order->get_date_completed()->date( 'Y-m-d H:i:s' ) : false;
				$items     = $order->get_items();

                if ( empty($completed) ) {
                    $completed = false;
                }

                $args = compact( 'woocommerce', 'completed', 'items', 'order_id', 'order', 'product_id' );

                wc_get_template( 'shortcode-order-items.php', $args, 'warranty', WooCommerce_Warranty::$base_path .'/templates/' );
            } else {
                // Request warranty on selected product
                $items  = $order->get_items();
                $idxs   = $_GET['idx'];

                $args = compact( 'woocommerce', 'items', 'order_id', 'order', 'idxs' );
                wc_get_template( 'shortcode-request-form.php', $args, 'warranty', WooCommerce_Warranty::$base_path .'/templates/' );
            }
        } else {
            echo '<div class="woocommerce-error">'. __('There are no valid warranties for this order', 'wc_warranty') .'</div>';
            echo '<p><a href="'. get_permalink(wc_get_page_id('myaccount')) .'" class="button">'. __('Back to My Account', 'wc_warranty') .'</a></p>';
        }

        ?>
    </div>
</div>
