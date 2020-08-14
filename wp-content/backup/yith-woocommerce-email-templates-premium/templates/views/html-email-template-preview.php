<?php
/**
 * Admin View: Email Template Preview
 */

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


// Load Template
$template = get_option( 'yith-wcet-email-template' );

// used for preview
if ( isset( $template_id ) ) {
    $template = $template_id;
}

$meta = yith_wcet_get_template_meta( $template );

$show_image         = ( isset( $meta[ 'show_prod_thumb' ] ) ) ? $meta[ 'show_prod_thumb' ] : 0;
$premium_mail_style = ( !empty( $meta[ 'premium_mail_style' ] ) ) ? $meta[ 'premium_mail_style' ] : 0;

?>

    <p><?php printf( __( 'You have received an order from %s. The order is as follows:', 'woocommerce' ), __( 'User', 'woocommerce' ) ); ?></p>

    <h2><a href=""><?php printf( __( 'Order #%s', 'woocommerce' ), 1 ); ?></a>
        (<?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', strtotime( date( 'F j, Y' ) ) ), date_i18n( wc_date_format(), strtotime( date( 'F j, Y' ) ) ) ); ?>
        )</h2>

    <table id="yith-wcet-order-items-table" cellspacing="0" cellpadding="6" style="width: 100%;">
        <thead>
        <tr>
            <th id="yith-wcet-th-title-product" class="yith-wcet-order-items-table-element" scope="col" style="padding:6px"><?php _e( 'Product', 'woocommerce' ); ?></th>
            <th id="yith-wcet-th-title-quantity" class="yith-wcet-order-items-table-element" scope="col" style="padding:6px"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
            <th id="yith-wcet-th-title-price" class="yith-wcet-order-items-table-element" scope="col" style="padding:6px"><?php _e( 'Price', 'woocommerce' ); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php // echo $order->email_order_items_table( false, true ); ?>
        <?php
        $img_path = YITH_WCET_ASSETS_URL . '/images/preview-emails/';
        $items    = array(
            array(
                'img'   => $img_path . '1.jpg',
                'name'  => 'Test 1',
                'qty'   => '1',
                'price' => wc_price( 30 )
            ),
            array(
                'img'   => $img_path . '2.jpg',
                'name'  => 'Test 2',
                'qty'   => '3',
                'price' => wc_price( 23 )
            ),
        );

        $count_items = count( $items );
        $i           = 0;
        foreach ( $items as $item ) :
            $i++;
            $last_class = $i == $count_items ? 'last' : 'not_last';
            ?>
            <tr>
                <td class="yith-wcet-order-items-table-element <?php echo $last_class; ?>" style="text-align:left; vertical-align:middle; word-wrap:break-word;">
                    <table class="yith-wcet-order-items-table-product-title">
                        <tr>
                            <?php
                            // Show title/image etc
                            if ( $show_image ) {
                                $image_size = apply_filters( 'yith_wcet_product_thumbnail_size', array( 32, 32 ), $template, 'preview' );
                                echo '<td class="yith-wcet-no-border" width="' . $image_size[ 0 ] . 'px" style="vertical-align:middle; padding: 0 10px 0 0 !important;">';
                                echo apply_filters( 'woocommerce_order_item_thumbnail', '<img src="' . $item[ 'img' ] . '" alt="' . __( 'Product Image', 'woocommerce' ) . '" height="' . $image_size[ 1 ] . '" width="' . $image_size[ 0 ] . '" style="vertical-align:middle;" />' );
                                echo '</td>';
                            }
                            // Product name
                            echo '<td class="yith-wcet-no-border" style="vertical-align:middle; padding: 0 !important;">';
                            echo $item[ 'name' ];
                            echo '</td>';
                            ?>
                        </tr>
                    </table>
                </td>
                <td class="yith-wcet-order-items-table-el-quantity-<?php echo $last_class; ?>" style="vertical-align:middle;"><?php echo $item[ 'qty' ]; ?></td>
                <td class="yith-wcet-order-items-table-el-price-<?php echo $last_class; ?>" style="vertical-align:middle;"><?php echo $item[ 'price' ]; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <?php if ( $premium_mail_style < 2 ) { ?>
            <tfoot>
            <?php
            $totals  = array(
                array(
                    'label' => __( 'Subtotal', 'woocommerce' ),
                    'value' => wc_price( 99 )
                ),
                array(
                    'label' => __( 'Shipping', 'woocommerce' ),
                    'value' => __( 'Free Shipping', 'woocommerce' )
                ),
                array(
                    'label' => __( 'Payment', 'woocommerce' ),
                    'value' => __( 'Paypal', 'woocommerce' )
                ),
                array(
                    'label' => __( 'Total', 'woocommerce' ),
                    'value' => wc_price( 99 )
                ),
            );
            $i       = 0;
            $t_count = count( $totals );
            foreach ( $totals as $total ) {
                $i++;
                $last_class  = $i == $t_count ? 'last' : 'not_last';
                $total_label = apply_filters( 'yith_wcet_total_label', $total[ 'label' ], 'preview' );
                ?>
                <tr>
                <th class="yith-wcet-order-items-table-element <?php if ( $i == 1 ) echo 'yith-wcet-order-items-table-element-bigtop'; ?> <?php echo $last_class; ?>" scope="row"
                    colspan="2"><?php echo $total_label; ?></th>
                <td class="yith-wcet-order-items-table-element <?php if ( $i == 1 ) echo 'yith-wcet-order-items-table-element-bigtop'; ?> <?php echo $last_class; ?>"><?php echo $total[ 'value' ]; ?></td>
                </tr><?php
            }
            ?>
            </tfoot>
        <?php } ?>
    </table>

<?php if ( $premium_mail_style > 1 ) {
    $totals_table_width_percentage = absint( apply_filters( 'yith_wcet_order_details_table_totals_table_width_percentage', '50', 'preview', $premium_mail_style ) );
    $first_column_width_percentage = 100 - $totals_table_width_percentage;
    ?>
    <table width="100%">
        <tr>
            <td width="100%"></td>
        </tr>
    </table>
    <table class="yith-wcet-two-columns" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td width="<?php echo $first_column_width_percentage ?>%" style="padding:0px">

            </td>
            <td width="<?php echo $totals_table_width_percentage ?>%" style="padding:0px">
                <table id="yith-wcet-foot-price-list">
                    <?php
                    $totals  = array(
                        array(
                            'label' => __( 'Subtotal', 'woocommerce' ),
                            'value' => wc_price( 99 )
                        ),
                        array(
                            'label' => __( 'Shipping', 'woocommerce' ),
                            'value' => __( 'Free Shipping', 'woocommerce' )
                        ),
                        array(
                            'label' => __( 'Payment', 'woocommerce' ),
                            'value' => __( 'Paypal', 'woocommerce' )
                        ),
                        array(
                            'label' => __( 'Total', 'woocommerce' ),
                            'value' => wc_price( 99 )
                        ),
                    );
                    $i       = 0;
                    $t_count = count( $totals );
                    foreach ( $totals as $total ) {
                        $i++;
                        $last_class = $i == $t_count ? 'last' : 'not_last';
                        ?>
                        <tr>
                        <th <?php if ( $i == $t_count ) {
                            echo 'id="yith-wcet-total-title"';
                        } ?> class="<?php echo $last_class; ?>" scope="row" colspan="2"><?php echo $total[ 'label' ]; ?></th>
                        <td <?php if ( $i == $t_count ) {
                            echo 'id="yith-wcet-total-price"';
                        } ?> class="<?php echo $last_class; ?>"><?php echo $total[ 'value' ]; ?></td>
                        </tr><?php
                    }
                    ?>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>