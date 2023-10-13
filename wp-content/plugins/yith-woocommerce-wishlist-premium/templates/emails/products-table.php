<?php
/**
 * Product table template (part of various emails)
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Templates\Emails
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $email_heading string Email heading string
 * @var $email         \WC_Email Email object
 * @var $email_content string Email content (HTML)
 * @var $items         YITH_WCWL_Wishlist_Item[]
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$text_align = is_rtl() ? 'right' : 'left';

?>

<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; margin: 0 0 16px;" border="1">
	<thead>
	<tr>
		<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Product', 'yith-woocommerce-wishlist' ); ?></th>
		<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Price', 'yith-woocommerce-wishlist' ); ?></th>
		<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ( $items as $item ) : ?>

		<?php
		/**
		 * Product for current item
		 *
		 * @var $product \WC_Product
		 */
		$product = $item->get_product();

		if ( ! $product ) {
			continue;
		}
		?>

		<?php
		/**
		 * APPLY_FILTERS: yith_wcwl_wishlist_item_class
		 *
		 * Filter the CSS class added to each product row inside the emails.
		 *
		 * @param string                  $class CSS class
		 * @param YITH_WCWL_Wishlist_Item $item  Wishlist item object
		 *
		 * @return string
		 */
		?>
		<tr class="<?php echo esc_attr( apply_filters( 'yith_wcwl_wishlist_item_class', 'wishlist_item', $item ) ); ?>">
			<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
				<?php printf( '<a href="%s">%s</a>', esc_url( $product->get_permalink() ), wp_kses_post( $product->get_name() ) ); ?>
			</td>
			<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
				<?php echo wp_kses_post( $item->get_formatted_product_price() ); ?>
			</td>
			<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
				<a style="display: inline-block; background-color: #ebe9eb; color: #515151; white-space: nowrap; padding: .618em 1em; border-radius: 3px; text-decoration: none;" href="<?php echo esc_url( add_query_arg( 'add-to-cart', $product->get_id(), $product->get_permalink() ) ); ?>">
					<?php echo wp_kses_post( $product->add_to_cart_text() ); ?>
				</a>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
