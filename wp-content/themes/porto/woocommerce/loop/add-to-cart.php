<?php
/**
 * Loop Add to Cart
 *
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $porto_settings, $product, $woocommerce_loop;

$wishlist  = class_exists( 'YITH_WCWL' ) && $porto_settings['product-wishlist'];
$quickview = $porto_settings['product-quickview'];

$porto_woo_version = porto_get_woo_version_number();

?>
<div class="add-links-wrap">
	<div class="add-links<?php echo ! $wishlist && ! $quickview ? ' no-effect' : ''; ?> clearfix">
		<?php
		global $porto_settings;
		$viewcart_style = 'viewcart-style-' . ( $porto_settings['add-to-cart-notification'] ? '2' : '1' );
		$catalog_mode   = false;
		if ( $porto_settings['catalog-enable'] ) {
			if ( $porto_settings['catalog-admin'] || ( ! $porto_settings['catalog-admin'] && ! ( current_user_can( 'administrator' ) && is_user_logged_in() ) ) ) {
				if ( ! $porto_settings['catalog-cart'] ) {
					$catalog_mode = true;
				}
			}
		}
		if ( ! $catalog_mode ) {
			if ( version_compare( $porto_woo_version, '2.5', '<' ) ) {
				echo apply_filters(
					'woocommerce_loop_add_to_cart_link',
					sprintf(
						'<%s rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="button ' . $viewcart_style . ' %s product_type_%s">%s</%s>',
						( $product->is_purchasable() && $product->is_in_stock() && isset( $porto_settings['category-addlinks-convert'] ) && $porto_settings['category-addlinks-convert'] ) ? 'span' : 'a',
						esc_url( $product->add_to_cart_url() ),
						esc_attr( isset( $quantity ) ? $quantity : 1 ),
						esc_attr( $product->id ),
						esc_attr( $product->get_sku() ),
						$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : 'add_to_cart_read_more',
						esc_attr( $product->product_type ),
						esc_html( $product->add_to_cart_text() ),
						( $product->is_purchasable() && $product->is_in_stock() && isset( $porto_settings['category-addlinks-convert'] ) && $porto_settings['category-addlinks-convert'] ) ? 'span' : 'a'
					),
					$product
				);
			} elseif ( version_compare( $porto_woo_version, '3.3', '<' ) ) {
				echo apply_filters(
					'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
					sprintf(
						'<%s rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="' . $viewcart_style . ' %s" %s>%s</%s>',
						( $product->is_purchasable() && $product->is_in_stock() && isset( $porto_settings['category-addlinks-convert'] ) && $porto_settings['category-addlinks-convert'] ) ? 'span' : 'a',
						esc_url( $product->add_to_cart_url() ),
						esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
						esc_attr( $product->get_id() ),
						esc_attr( $product->get_sku() ),
						esc_attr( ( isset( $args['class'] ) ? $args['class'] : 'button' ) . ( $product->is_purchasable() && $product->is_in_stock() ? '' : ' add_to_cart_read_more' ) ),
						isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
						esc_html( $product->add_to_cart_text() ),
						( $product->is_purchasable() && $product->is_in_stock() && isset( $porto_settings['category-addlinks-convert'] ) && $porto_settings['category-addlinks-convert'] ) ? 'span' : 'a'
					),
					$product
				);
			} else {
				echo apply_filters(
					'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
					sprintf(
						'<%s href="%s" data-quantity="%s" class="' . $viewcart_style . ' %s" %s>%s</%s>',
						( $product->is_purchasable() && $product->is_in_stock() && isset( $porto_settings['category-addlinks-convert'] ) && $porto_settings['category-addlinks-convert'] ) ? 'span' : 'a',
						esc_url( $product->add_to_cart_url() ),
						esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
						esc_attr( ( isset( $args['class'] ) ? $args['class'] . '' : 'button' ) . ( $product->is_purchasable() && $product->is_in_stock() ? '' : ' add_to_cart_read_more' ) ),
						isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
						esc_html( $product->add_to_cart_text() ),
						( $product->is_purchasable() && $product->is_in_stock() && isset( $porto_settings['category-addlinks-convert'] ) && $porto_settings['category-addlinks-convert'] ) ? 'span' : 'a'
					),
					$product
				);
			}
		} else {
			$more_link   = apply_filters( 'the_permalink', get_permalink() );
			$more_target = '';
			if ( $porto_settings['catalog-readmore'] && 'all' === $porto_settings['catalog-readmore-archive'] ) {
				$link = get_post_meta( get_the_id(), 'product_more_link', true );
				if ( $link ) {
					$more_link = $link;
				}
				$more_target = $porto_settings['catalog-readmore-target'] ? 'target="' . esc_attr( $porto_settings['catalog-readmore-target'] ) . '"' : '';
			}
			echo apply_filters(
				'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
				sprintf(
					'<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="button ' . $viewcart_style . ' %s product_type_%s" %s %s>%s</a>',
					esc_url( $more_link ),
					esc_attr( $product->get_id() ),
					esc_attr( $product->get_sku() ),
					esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
					'add_to_cart_read_more',
					esc_attr( $product->get_type() ),
					$more_target,
					isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
					esc_html( $porto_settings['catalog-readmore-label'] )
				),
				$product
			);
		}

		if ( $wishlist ) {
			echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
		}
		if ( $quickview && ( ! isset( $woocommerce_loop['addlinks_pos'] ) || 'onimage2' !== $woocommerce_loop['addlinks_pos'] ) ) {
			$label = ( ( isset( $porto_settings['product-quickview-label'] ) && $porto_settings['product-quickview-label'] ) ? $porto_settings['product-quickview-label'] : __( 'Quick View', 'porto' ) );
			echo '<div class="quickview" data-id="' . esc_attr( $product->get_id() ) . '" title="' . esc_attr( $label ) . '">' . esc_html( $label ) . '</div>';
		}
		?>
	</div>
	<?php
	if ( isset( $woocommerce_loop['addlinks_pos'] ) && 'onimage2' == $woocommerce_loop['addlinks_pos'] ) {
		$label = ( ( isset( $porto_settings['product-quickview-label'] ) && $porto_settings['product-quickview-label'] ) ? $porto_settings['product-quickview-label'] : __( 'Quick View', 'porto' ) );
		echo '<div class="quickview" data-id="' . esc_attr( $product->get_id() ) . '" title="' . esc_attr( $label ) . '">' . esc_html( $label ) . '</div>';
	}
	?>
</div>
