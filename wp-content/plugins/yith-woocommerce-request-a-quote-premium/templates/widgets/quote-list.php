<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH Woocommerce Request A Quote
 */

/**
 * Table view to Request A Quote in the widget
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/widgets/quote-list.php.
 *
 * HOWEVER, on occasion YITH will need to update template files and you
 * will need to copy the new files to your theme to maintain compatibility.
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @author  YITH
 * @version 2.2.7
 *
 * @var  array $raq_content
 * @var  bool  $show_thumbnail
 * @var  bool  $show_price
 * @var  bool  $show_quantity
 * @var  bool  $show_variations
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$tax_display_list = apply_filters( 'ywraq_tax_display_list', get_option( 'woocommerce_tax_display_cart' ) );
$show_thumbnail   = ( 'true' == $show_thumbnail || 1 == $show_thumbnail ) ? 1 : 0;
$show_price       = ( 'true' == $show_price || 1 == $show_price ) ? 1 : 0;
$show_quantity    = ( 'true' == $show_quantity || 1 == $show_quantity ) ? 1 : 0;
$show_variations  = ( 'true' == $show_variations || 1 == $show_variations ) ? 1 : 0;
?>

<?php do_action( 'ywraq_before_raq_list_widget' ); ?>
<div class="yith-ywraq-list-wrapper">
	<?php
	if ( count( $raq_content ) == 0 ) :
		?>
		<p><?php echo wp_kses_post( apply_filters( 'yith_ywraq_quote_list_empty_message', __( 'No products in the list', 'yith-woocommerce-request-a-quote' ) ) ); ?></p>
	<?php else : ?>
		<ul class="yith-ywraq-list">
			<?php
			foreach ( $raq_content as $key => $raq ) :
				$_product = wc_get_product( isset( $raq['variation_id'] ) ? $raq['variation_id'] : $raq['product_id'] );

				if ( ! $_product ) {
					continue;
				}

				$thumbnail    = ( $show_thumbnail ) == true ? $_product->get_image() : '';
				$product_name = $_product->get_title();
				?>

				<li class="yith-ywraq-list-item">
					<?php
					echo apply_filters( 'yith_ywraq_item_remove_link', sprintf( '<a href="#"  data-remove-item="%s" data-wp_nonce="%s"  data-product_id="%d" class="yith-ywraq-item-remove remove" title="%s">&times;</a>', esc_attr( $key ), esc_attr( wp_create_nonce( 'remove-request-quote-' . $_product->get_id() ) ), esc_attr( $_product->get_id() ), esc_attr( __( 'Remove this item', 'yith-woocommerce-request-a-quote' ) ) ), $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>

					<?php if ( ! $_product->is_visible() || ! apply_filters( 'ywraq_list_show_product_permalinks', true, 'widget_quote' ) ) : ?>
						<?php
						echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ) . $product_name . '&nbsp;'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					<?php else : ?>
						<a href="<?php echo esc_url( $_product->get_permalink() ); ?>">
							<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ) . $product_name . '&nbsp;'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					<?php endif; ?>
					<?php if ( isset( $raq['variations'] ) && $show_variations ) : ?>
						<small><?php yith_ywraq_get_product_meta( $raq ); ?></small>
					<?php endif ?>

					<?php if ( $show_quantity || $show_price ) : ?>
						<span class="quantity">
							<?php
							echo esc_html( ( $show_quantity ) ? $raq['quantity'] : '' );
							if ( $show_price ) {
								$x = ( $show_quantity ) ? ' x ' : '';
								do_action( 'ywraq_quote_adjust_price', $raq, $_product );
								$price = ( 'incl' === $tax_display_list ) ? wc_get_price_including_tax( $_product, array( 'qty' => $raq['quantity'] ) ) : wc_get_price_excluding_tax( $_product, array( 'qty' => $raq['quantity'] ) );
								if ( $price && WC()->cart ) {
									$price = apply_filters( 'yith_ywraq_product_price_html', WC()->cart->get_product_price( $_product ), $_product, $raq );
								} else {
									$price = wc_price( 0 );
								}

								$x = ( $show_quantity ) ? ' x ' : '';
								echo wp_kses_post( apply_filters( 'yith_ywraq_hide_price_template', $x . $price, $_product->get_id(), $raq ) );
							}
							?>
						</span>
					<?php endif; ?>
					<?php do_action( 'ywraq_list_widget_view_item', $raq_content, $key ); ?>
				</li>

			<?php endforeach ?>


		</ul>
		<a href="<?php echo esc_url( YITH_Request_Quote()->get_raq_page_url() ); ?>"
			class="button"><?php echo esc_html( apply_filters( 'yith_ywraq_quote_list_button_label', $button_label ) ); ?></a>
	<?php endif ?>
</div>


<?php do_action( 'ywraq_after_raq_list_widget' ); ?>
