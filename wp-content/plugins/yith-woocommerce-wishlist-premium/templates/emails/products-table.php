<?php
/**
 * Product table template (part of various emails)
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $email_heading string Email heading string
 * @var $email \WC_Email Email object
 * @var $email_content string Email content (HTML)
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$text_align = is_rtl() ? 'right' : 'left';

?>

<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; margin: 0 0 16px;" border="1" >
	<thead>
	<tr>
		<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Product', 'yith-woocommerce-wishlist' ); ?></th>
		<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php _e( 'Price', 'yith-woocommerce-wishlist' ); ?></th>
		<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"></th>
	</tr>
	</thead>
	<tbody>
		<?php foreach( $items as $item ): ?>

        <?php
            /**
             * @var $product \WC_Product
             */
            $product = $item->get_product();

            if( ! $product ){
                continue;
            }
        ?>

		<tr class="<?php echo esc_attr( apply_filters( 'yith_wcwl_wishlist_item_class', 'wishlist_item', $item ) ); ?>">
			<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
				<?php echo sprintf( '<a href="%s">%s</a>', $product->get_permalink(), $product->get_name() ) ?>
			</td>
			<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
				<?php echo $item->get_formatted_product_price() ?>
			</td>
            <td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
                <a style="display: inline-block; background-color: #ebe9eb; color: #515151; white-space: nowrap; padding: .618em 1em; border-radius: 3px; text-decoration: none;" href="<?php echo add_query_arg( 'add-to-cart', $product->get_id(), $product->get_permalink() ) ?>"><?php echo $product->add_to_cart_text() ?></a>
            </td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
