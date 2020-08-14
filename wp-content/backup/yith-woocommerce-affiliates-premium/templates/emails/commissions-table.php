<?php
/**
 * Commission table template part
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php
$text_align = is_rtl() ? 'right' : 'left';

if( ! empty( $commissions ) ): ?>
    <h2><?php _e( 'Commissions', 'yith-woocommerce-affiliates' ) ?></h2>
	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; margin-bottom: 40px;" border="1">
        <thead>
        <tr>
            <th class="td" scope="col" style="text-align:<?php echo $text_align; ?>;"><?php _e( 'ID', 'yith-woocommerce-affiliates' ); ?></th>
            <th class="td" scope="col" style="text-align:<?php echo $text_align; ?>;"><?php _e( 'Affiliate', 'yith-woocommerce-affiliates' ); ?></th>
            <th class="td" scope="col" style="text-align:<?php echo $text_align; ?>;"><?php _e( 'Product', 'yith-woocommerce-affiliates' ); ?></th>
            <th class="td" scope="col" style="text-align:<?php echo $text_align; ?>;"><?php _e( 'Total', 'yith-woocommerce-affiliates' ); ?></th>
            <th class="td" scope="col" style="text-align:<?php echo $text_align; ?>;"><?php _e( 'Rate', 'yith-woocommerce-affiliates' ); ?></th>
            <th class="td" scope="col" style="text-align:<?php echo $text_align; ?>;"><?php _e( 'Commission', 'yith-woocommerce-affiliates' ); ?></th>
        </tr>
        </thead>
        <tbody>
            <?php
                foreach ( $commissions as $item ):
                    if ( apply_filters( 'yith_wcaf_commission_visible', true, $item ) ):
                        ?>
                        <tr class="<?php echo esc_attr( apply_filters( 'yith_wcaf_commission_class', 'commission', $item, $order ) ); ?>">
                            <td class="td" style="text-align:<?php echo $text_align; ?>; vertical-align:middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
                                <?php
                                $column = sprintf( '<strong>#%d</strong>', $item['ID'] );

                                echo $column;
                                ?>
                            </td>

                            <td class="td" style="text-align:<?php echo $text_align; ?>; vertical-align:middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
                                <?php
                                $column = '';
                                $user_id = $item['user_id'];
                                $user_data = get_userdata( $user_id );

                                if( $user_data ){
	                                $user_email = $user_data->user_email;

	                                $username = '';
	                                if ( $user_data->first_name || $user_data->last_name ) {
		                                $username .= esc_html( ucfirst( $user_data->first_name ) . ' ' . ucfirst( $user_data->last_name ) );
	                                }
	                                else {
		                                $username .= esc_html( ucfirst( $user_data->display_name ) );
	                                }

	                                $column .= sprintf( '<a href="%s">%s</a>', add_query_arg( '_user_id', $user_id ), $username );

	                                echo $column;
                                }
                                ?>
                            </td>

                            <td class="td" style="text-align:<?php echo $text_align; ?>; vertical-align:middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
                                <?php
                                $product_id = $item['product_id'];
                                $order_id = $item['order_id'];
                                $order = wc_get_order( $order_id );

                                if( $order ){
	                                $line_items = $order->get_items();
	                                $line_item = isset( $line_items[ $item['line_item_id'] ] ) ? $line_items[ $item['line_item_id'] ] : false;

	                                if( $line_item ){
		                                /**
		                                 * @var $line_item \WC_Order_Item_Product
		                                 */
		                                $product = is_object( $line_item ) ? $line_item->get_product() : $order->get_product_from_item( $line_item );
	                                }
                                }
                                else{
	                                $product = wc_get_product( $product_id );
                                }

                                if( isset( $product ) && $product ){
	                                $column = sprintf( '<a href="%s">%s</a>', add_query_arg( '_product_id', $product_id ), $product->get_title() );

	                                if( $product->is_type( 'variation' ) ){
		                                $column .= sprintf( '<div class="wc-order-item-name"><strong>%s</strong> %s</div>', __( 'Variation ID:', 'yith-woocommerce-affiliates' ), yit_get_product_id( $product ) );
	                                }

	                                echo apply_filters( 'yith_wcaf_product_column', $column, $product_id, 'commissions' );
                                }
                                ?>
                            </td>

                            <td class="td" style="text-align:<?php echo $text_align; ?>; vertical-align:middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
                                <?php
                                $exclude_tax = YITH_WCAF_Commission_Handler()->get_option( 'exclude_tax' );

                                $line_items = $order->get_items( 'line_item' );

                                if( ! empty( $line_items ) ){
	                                $line_item = isset( $line_items[ $item['line_item_id'] ] ) ? $line_items[ $item['line_item_id'] ] : '';

	                                if( ! empty( $line_item ) ){
		                                $column = '';
		                                $column .= wc_price( $order->get_item_subtotal( $line_item, 'yes' != $exclude_tax ) * $line_item['qty'], array( 'currency' => apply_filters( 'yith_wcaf_email_currency', $order->get_currency() ) ) );

		                                echo $column;
	                                }
                                }
                                ?>
                            </td>

                            <td class="td" style="text-align:<?php echo $text_align; ?>; vertical-align:middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
                                <?php
                                $column = '';
                                $column .= sprintf( '%.2f%%', number_format( round( $item['rate'], 2 ), 2 ) );

                                echo $column;
                                ?>
                            </td>

                            <td class="td" style="text-align:<?php echo $text_align; ?>; vertical-align:middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
                                <?php
                                $column = '';
                                $column .= '<strong>' . wc_price( $item['amount'] ) . '</strong>';

                                echo $column;
                                ?>
                            </td>
                        </tr>
                        <?php
                    endif;
                endforeach;
            ?>
        </tbody>
    </table>
<?php endif;