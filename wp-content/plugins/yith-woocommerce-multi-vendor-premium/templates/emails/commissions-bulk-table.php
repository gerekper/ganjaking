<?php
/**
 * Admin new order email
 *
 * @author WooThemes
 * @package WooCommerce/Templates/Emails/HTML
 * @version 2.0.0
 *
 * @var YITH_Commission $commission
 * @var YITH_Vendor $vendor
 * @var WC_Product $product
 * @var WC_Order $order
 * @var array $item
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$commissions_total = $shipping_fee_total = 0;
$wc_price_args = apply_filters( 'yith_wcmv_commissions_bulk_email_wc_price_args', array() );
add_filter( 'yith_wcmv_commission_have_been_calculated_text', '__return_empty_string' );
?>

    <tr style="border: 1px solid #eee;">
        <td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php _e( 'Commission ID', 'yith-woocommerce-product-vendors' ) ?></td>
        <td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php _e( 'Order ID', 'yith-woocommerce-product-vendors' ) ?></td>
        <td style="text-align:center; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php _e( 'SKU', 'yith-woocommerce-product-vendors' ) ?></td>
        <td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php _e( 'Amount', 'yith-woocommerce-product-vendors' ) ?></td>
        <td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php printf( "%s%s", '%', _x( 'Rate', '[Email]: meanse commissions rate', 'yith-woocommerce-product-vendors' ) ); ?></td>
        <td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php _e( 'New Status', 'yith-woocommerce-product-vendors' ) ?></td>

        <?php if( $show_note ) : ?>
            <td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php _e( 'Note', 'yith-woocommerce-product-vendors' ) ?></td>
        <?php endif; ?>

    </tr>

    <?php if( ! is_array( $commissions ) ) : ?>
        <?php $commissions = array( $commissions ); ?> 
    <?php endif; ?>

    <?php foreach( $commissions as $commission ) : ?>
        <?php if( 'shipping' == $commission->type ){
		    $shipping_fee_total = $shipping_fee_total + $commission->get_amount();
        }

        else {
            $commissions_total = $commissions_total + $commission->get_amount();
        }?>
        <tr style="border: 1px solid #eee;">
            <td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;">
                <a href="<?php echo $commission->get_view_url( 'admin' )?>">
    	            <?php echo "#" . $commission->id ?>
                </a>
            </td>
            <td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;">
                <?php $order_id = $commission->get_order() instanceof WC_Order ? $commission->get_order()->get_id() : ''; ?>
                <?php if( ! empty( $order_id ) ) : ?>
                    <?php $order_uri = apply_filters( 'yith_wcmv_commissions_list_table_order_url', admin_url( 'post.php?post=' . absint( $order_id ) . '&action=edit' ), $commission, $commission->get_order() ); ?>
                    <a href="<?php echo $order_uri; ?>">
                <?php endif; ?>

                <?php echo  "#" . $order_id; ?>

                <?php if( ! empty( $order_id ) ) : ?>
                    </a>
                <?php endif; ?>
            </td>
            <td style="text-align:center; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;">
	            <?php
	            $currency = $commission->get_order() instanceof WC_Order ? $commission->get_order()->get_currency() : '';
                if ( 'shipping' == $commission->type ) {
		            $info = _x( 'Shipping', '[admin] part of shipping fee details', 'yith-woocommerce-product-vendors' );
	            }

	            else {
                    $info = '-';
                    $item = $commission->get_item();
                    if( $item instanceof WC_Order_Item ){
                        $product = $commission->get_product();

                        if( $product ){
                            $sku = $product->get_sku( 'view' );

                            if( ! empty( $sku ) ) {
                                if( apply_filters( 'yith_wcmv_show_product_uri_in_commissions_bulk_email', true ) ){
	                                $product_url = apply_filters( 'yith_wcmv_commissions_list_table_product_url', get_edit_post_link( $product->get_id() ), $product, $commission );
	                                $info = sprintf( '<a href="%s">%s</a>', $product_url, $sku );
                                }

                                else{
                                    $info = $sku;
                                }
                            }
                        }
                    }
                }

	            echo $info
	            ?>
            </td>
            <td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php echo $commission->get_amount( 'display', array( 'currency' => $currency ) ) ?></td>
            <td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php echo $commission->get_rate( 'display' ) ?></td>
            <td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php echo $new_commission_status; ?></td>

	        <?php if( $show_note ) : ?>
                <td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word; ">
                    <?php
                    $msg = '-';

                    if( $item instanceof WC_Order_Item_Product ){
                        /**
                         * Check if the commissions included tax
                         */
                        $commission_included_tax = wc_get_order_item_meta( $item->get_id(), '_commission_included_tax', true );
                        /**
                         * Check if the commissions included coupon
                         */
                        $commission_included_coupon = wc_get_order_item_meta( $item->get_id(), '_commission_included_coupon', true );

                        $msg = YITH_Commissions::get_tax_and_coupon_management_message( $commission_included_tax, $commission_included_coupon );
                    }

                    echo $msg;

                    ?>
                </td>
	        <?php endif; ?>

        </tr>
    <?php endforeach; ?>

    <?php if( ! empty( $commissions_total ) ) : ?>
        <tr style="border: 1px solid #eee;">
            <td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;">
                <strong>
                    <?php _ex( 'Total product commissions', '[Email commissions report]: Total commissions amount', 'yith-woocommerce-product-vendors' ); ?>
                </strong>
            </td>
            <td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;">
                <?php echo wc_price( $commissions_total, $wc_price_args ); ?>
            </td>
        </tr>
    <?php endif; ?>

    <?php if( ! empty( $shipping_fee_total ) ) : ?>
        <tr style="border: 1px solid #eee;">
            <td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;">
                <strong>
                    <?php _ex( 'Total Shipping Fee', '[Email commissions report]: Total commissions amount', 'yith-woocommerce-product-vendors' ); ?>
                </strong>
            </td>
            <td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;">
                <?php echo wc_price( $shipping_fee_total, $wc_price_args );  ?>

            </td>
        </tr>
    <?php endif; ?>

<?php remove_filter( 'yith_wcmv_commission_have_been_calculated_text', '__return_empty_string' ); ?>

