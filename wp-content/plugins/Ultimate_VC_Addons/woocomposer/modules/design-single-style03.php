<?php
/**
 * Add-on Name: woocomposer for WPBakery Page Builder
 * Add-on URI: http://dev.brainstormforce.com
 *
 *  @package Woocomposer design single style 03
 */

/**
 * For the design shortcode atts.
 *
 * @since ----
 * @param array $atts represts module attribuits.
 * @access public
 */
function woocomposer_single_style03( $atts ) {
		$ult_design_single03 = shortcode_atts(
			array(
				'product_id'            => '',
				'product_style'         => 'style01',
				'display_elements'      => '',
				'label_on_sale'         => 'Sale!',
				'text_align'            => 'left',
				'img_animate'           => 'rotate-clock',
				'color_heading'         => '',
				'color_categories'      => '',
				'color_price'           => '',
				'color_rating'          => '',
				'color_rating_bg'       => '',
				'color_quick_bg'        => '',
				'color_quick'           => '',
				'color_cart_bg'         => '',
				'color_on_sale_bg'      => '',
				'color_on_sale'         => '',
				'color_cart'            => '',
				'color_product_desc'    => '',
				'color_product_desc_bg' => '',
				'size_title'            => '',
				'size_cat'              => '',
				'size_price'            => '',
				'border_style'          => '',
				'border_color'          => '',
				'border_size'           => '',
				'border_radius'         => '',
				'sale_price'            => '',
				'on_sale_style'         => 'wcmp-sale-circle',
				'on_sale_alignment'     => 'wcmp-sale-right',
				'product_img_disp'      => 'single',
			),
			$atts
		);
	$output                  = '';
	$heading_style           = '';
	$cat_style               = '';
	$price_style             = '';
	$cart_style              = '';
	$cart_bg_style           = '';
	$view_style              = '';
	$view_bg_style           = '';
	$rating_style            = '';
	$desc_style              = '';
	$label_style             = '';
	$border                  = '';
	$desc_style              = '';
	$sale_price_size         = '';
	$image_size              = apply_filters( 'single_product_large_thumbnail_size', 'shop_single' );
	if ( '' !== $ult_design_single03['sale_price'] ) {
		$sale_price_size = 'font-size:' . $ult_design_single03['sale_price'] . 'px;';
	}
	$ult_design_single03['img_animate'] = 'wcmp-img-' . $ult_design_single03['img_animate'];

	if ( '' !== $ult_design_single03['border_style'] ) {
		$border .= 'border:' . $ult_design_single03['border_size'] . 'px ' . $ult_design_single03['border_style'] . ' ' . $ult_design_single03['border_color'] . ';';
		$border .= 'border-radius:' . $ult_design_single03['border_radius'] . 'px;';
	}
	if ( '' !== $ult_design_single03['color_product_desc_bg'] ) {
		$desc_style .= 'background:' . $ult_design_single03['color_product_desc_bg'] . ';';
	}
	if ( '' !== $ult_design_single03['color_product_desc'] ) {
		$desc_style .= 'color:' . $ult_design_single03['color_product_desc'] . ';';
	}
	if ( '' !== $ult_design_single03['color_heading'] ) {
		$heading_style = 'color:' . $ult_design_single03['color_heading'] . ';';
	}
	if ( '' !== $ult_design_single03['size_title'] ) {
		$heading_style .= 'font-size:' . $ult_design_single03['size_title'] . 'px;';
	}
	if ( '' !== $ult_design_single03['color_categories'] ) {
		$cat_style = 'color:' . $ult_design_single03['color_categories'] . ';';
	}
	if ( '' !== $ult_design_single03['size_cat'] ) {
		$cat_style .= 'font-size:' . $ult_design_single03['size_cat'] . 'px;';
	}
	if ( '' !== $ult_design_single03['color_price'] ) {
		$price_style = 'color:' . $ult_design_single03['color_price'] . ';';
	}
	if ( '' !== $ult_design_single03['size_price'] ) {
		$price_style .= 'font-size:' . $ult_design_single03['size_price'] . 'px;';
	}
	if ( '' !== $ult_design_single03['color_rating'] ) {
		$rating_style .= 'color:' . $ult_design_single03['color_rating'] . ';';
	}
	if ( '' !== $ult_design_single03['color_rating_bg'] ) {
		$rating_style .= 'background:' . $ult_design_single03['color_rating_bg'] . ';';
	}
	if ( '' !== $ult_design_single03['color_quick_bg'] ) {
		$view_bg_style = 'background:' . $ult_design_single03['color_quick_bg'] . ';';
	}
	if ( '' !== $ult_design_single03['color_quick'] ) {
		$view_style = 'color:' . $ult_design_single03['color_quick'] . ';';
	}
	if ( '' !== $ult_design_single03['color_cart_bg'] ) {
		$cart_bg_style = 'background:' . $ult_design_single03['color_cart_bg'] . ';';
	}
	if ( '' !== $ult_design_single03['color_cart'] ) {
		$cart_style = 'color:' . $ult_design_single03['color_cart'] . ';';
	}
	if ( '' !== $ult_design_single03['color_on_sale_bg'] ) {
		$label_style = 'background:' . $ult_design_single03['color_on_sale_bg'] . ';';
	}
	if ( '' !== $ult_design_single03['color_on_sale'] ) {
		$label_style .= 'color:' . $ult_design_single03['color_on_sale'] . ';';
	}

	$elemets = explode( ',', $ult_design_single03['display_elements'] );

	$product_title  = get_the_title( $ult_design_single03['product_id'] );
	$post           = get_post( $ult_design_single03['product_id'] );
	$product_desc   = get_post( $ult_design_single03['product_id'] )->post_excerpt;
	$product_img    = wp_get_attachment_image_src( get_post_thumbnail_id( $ult_design_single03['product_id'] ), $image_size );
	$product        = new WC_Product( $ult_design_single03['product_id'] );
	$attachment_ids = $product->get_gallery_attachment_ids();
	$price          = $product->get_price_html();
	$rating         = $product->get_rating_html();
	$attributes     = $product->get_attributes();
	$stock          = $product->is_in_stock() ? 'InStock' : 'OutOfStock';
	if ( $product->is_on_sale() ) :
		$on_sale = apply_filters( 'woocommerce_sale_flash', $ult_design_single03['label_on_sale'], $post, $product );
	else :
		$on_sale = '';
	endif;
	$cat_count  = count( get_the_terms( $ult_design_single03['product_id'], 'product_cat' ) );
	$tag_count  = count( get_the_terms( $ult_design_single03['product_id'], 'product_tag' ) );
	$categories = $product->get_categories( ', ', '<span class="posted_in">' . _n( '', '', $cat_count, 'woocommerce' ) . ' ', '.</span>' );// @codingStandardsIgnoreLine.
	$tags       = $product->get_tags( ', ', '<span class="tagged_as">' . _n( '', '', $tag_count, 'woocommerce' ) . ' ', '.</span>' );// @codingStandardsIgnoreLine.
	$output    .= "\n" . '<div class="wcmp-product woocommerce wcmp-' . $ult_design_single03['product_style'] . ' ' . $ult_design_single03['img_animate'] . '" style="' . $border . ' ' . $desc_style . '">';

		$output .= "\n\t" . '<div class="wcmp-product-image">';
	if ( ! empty( $attachment_ids ) && count( $attachment_ids ) > 1 && 'carousel' == $ult_design_single03['product_img_disp'] ) {
		$uniqid      = uniqid();
		$output     .= '<div class="wcmp-single-image-carousel">';
		$product_img = wp_get_attachment_image_src( get_post_thumbnail_id( $ult_design_single03['product_id'] ), $image_size );
		$src         = $product_img[0];
		$output     .= '<div><div class="wcmp-image"><img class="wcmp-img" src="' . $src . '" data-src="' . $product_img[0] . '"/></div></div>';
		foreach ( $attachment_ids as $attachment_id ) {
			$product_img = wp_get_attachment_image_src( $attachment_id, $image_size );
			$output     .= '<div><div class="wcmp-image"><img class="wcmp-img" src="' . $src . '" data-src="' . $product_img[0] . '"/></div></div>';
		}
		$output .= '</div>';
	} else {
		$product_img = wp_get_attachment_image_src( get_post_thumbnail_id( $ult_design_single03['product_id'] ), $image_size );
		$src         = $product_img[0];
		$output     .= '<a href="' . get_permalink( $ult_design_single03['product_id'] ) . '"><img class="wcmp-img" src="' . $src . '" data-src="' . $product_img[0] . '"/></a>';
	}
	if ( '' !== $on_sale ) {
		$output .= "\n" . '<div class="wcmp-onsale ' . $ult_design_single03['on_sale_alignment'] . ' ' . $ult_design_single03['on_sale_style'] . '"><span class="onsale" style="' . $label_style . ' ' . $sale_price_size . '">' . $on_sale . '</span></div>';
	}
	if ( 'OutOfStock' == $stock ) {
		$output .= "\n" . '<span class="wcmp-out-stock">' . __( 'Out Of Stock!', 'woocomposer' ) . '</span>';
	}

		$output .= '</div>';

		$output     .= "\n\t" . '<div class="wcmp-product-desc">';
			$output .= '<a href="' . get_permalink( $ult_design_single03['product_id'] ) . '">';
			$output .= "\n\t\t" . '<h2 style="' . $heading_style . '">' . $product_title . '</h2>';
			$output .= '</a>';

	if ( in_array( 'category', $elemets ) ) {
		$output .= '<h5 style="' . $cat_style . '">';
		if ( '' !== $categories ) {
			$output .= $categories;
			$output .= $tags;
		}
		$output .= '</h5>';
	}

			$output .= "\n\t\t" . '<div class="wcmp-price"><span class="price" style="' . $price_style . '">' . $price . '</span></div>';
			$output .= '<div class="wcmp-style3-cart-block">';/*Class Start wcmp-style3-cart-block*/

			$output .= '<div class="wcmp-add-to-cart" style="' . $cart_bg_style . '"><a style="' . $cart_style . '" title="Add to Cart" href="?add-to-cart=' . $ult_design_single03['product_id'] . '" rel="nofollow" data-product_id="' . $ult_design_single03['product_id'] . '" data-product_sku="" class="button add_to_cart_button product_type_simple">Add to Cart</a></div>';

	if ( in_array( 'reviews', $elemets ) ) {
		$output .= "\n" . '<div class="wcmp-star-ratings" style="' . $rating_style . '">' . $rating . '</div>';
	}

	if ( in_array( 'quick', $elemets ) ) {
		$output .= '<div class="wcmp-quick-view quick-view-single" style="' . $view_bg_style . '"><a style="' . $view_style . '" title="Quick View" href="' . get_permalink( $ult_design_single03['product_id'] ) . '"><i class="wooicon-plus32"></i></a></div>';
	}

			$output .= '</div>';/*Class End wcmp-style3-cart-block*/

	if ( in_array( 'description', $elemets ) ) {
		$output .= "\n\t\t" . '<div class="wcmp-product-content" style="' . $desc_style . '">' . $product_desc . '</div>';
	}

	if ( in_array( 'quick', $elemets ) ) {
		$query = new WP_Query(
			array(
				'post_type' => 'product',
				'post__in'  => array( $ult_design_single03['product_id'] ),
			)
		);
		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) :
				$query->the_post();
				$output     .= '<div class="wcmp-quick-view-wrapper">';
					$output .= '<div class="wcmp-quick-view-wrapper woocommerce">';
					$output .= '<div class="wcmp-close-single"><i class="wooicon-cross2"></i></div>';
				if ( ! empty( $attachment_ids ) && count( $attachment_ids ) > 1 ) {
					$uniqid  = uniqid();
					$output .= '<div class="wcmp-image-carousel wcmp-carousel-' . $uniqid . '" data-class="wcmp-carousel-' . $uniqid . '">';
					foreach ( $attachment_ids as $attachment_id ) {
								$product_img = wp_get_attachment_image_src( $attachment_id, $image_size );
								$output     .= '<div><div class="wcmp-image"><img src="' . $product_img[0] . '"/></div></div>';
					}
							$output .= '</div>';
				} else {
					$product_img = wp_get_attachment_image_src( get_post_thumbnail_id( $ult_design_single03['product_id'] ), $image_size );
					$output     .= '<div class="wcmp-single-image"><img src="' . $product_img[0] . '"/></div>';
				}
						$output .= '<div class="wcmp-product-content-single">';
						ob_start();
						do_action( 'woocommerce_single_product_summary' );
						$output .= ob_get_clean();
						$output .= '</div>';
						$output .= '<div class="clear"></div>';
					$output     .= '</div>';
					$output     .= '</div>';
				endwhile;
			endif;
	}
		$output .= "\n\t" . '</div>';
	$output     .= "\n\t" . '</div>';
	return $output;
}
