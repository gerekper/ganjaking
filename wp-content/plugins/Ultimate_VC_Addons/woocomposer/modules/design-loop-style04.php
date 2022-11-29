<?php
/**
 * Design Loop Style - 04.
 *
 * @package Design Loop Style - 04.
 */

/**
 * Woocomposer_loop_style04
 *
 * @param array $atts Attributes.
 * @param array $element Element.
 */
function woocomposer_loop_style04( $atts, $element ) {
	global $woocommerce;
	$output                             = '';
	$ult_dls_04_settings                = shortcode_atts(
		array(
			'disp_type'             => '',
			'category'              => '',
			'shortcode'             => '',
			'product_style'         => 'style01',
			'display_elements'      => '',
			'quick_view_style'      => 'expandable',
			'label_on_sale'         => 'Sale!',
			'text_align'            => 'left',
			'img_animate'           => 'rotate-clock',
			'pagination'            => '',
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
			'product_animation'     => '',
			'lazy_images'           => '',
			'advanced_opts'         => '',
			'sale_price'            => '',
			'on_sale_style'         => 'wcmp-sale-circle',
			'on_sale_alignment'     => 'wcmp-sale-right',
			'product_img_disp'      => 'single',
		),
		$atts
	);
	$output                             = '';
	$heading_style                      = '';
	$cat_style                          = '';
	$price_style                        = '';
	$cart_style                         = '';
	$cart_bg_style                      = '';
	$view_style                         = '';
	$view_bg_style                      = '';
	$rating_style                       = '';
	$desc_style                         = '';
	$label_style                        = '';
	$on_sale                            = '';
	$class                              = '';
	$style                              = '';
	$border                             = '';
	$desc_style                         = '';
	$sale_price_size                    = '';
	$image_size                         = apply_filters( 'single_product_large_thumbnail_size', 'shop_single' );
	$ult_dls_04_settings['img_animate'] = 'wcmp-img-' . $ult_dls_04_settings['img_animate'];
	if ( '' !== $ult_dls_04_settings['sale_price'] ) {
		$sale_price_size = 'font-size:' . $ult_dls_04_settings['sale_price'] . 'px;';
	}
	if ( '' !== $ult_dls_04_settings['border_style'] ) {
		$border .= 'border:' . $ult_dls_04_settings['border_size'] . 'px ' . $ult_dls_04_settings['border_style'] . ' ' . $ult_dls_04_settings['border_color'] . ';';
		$border .= 'border-radius:' . $ult_dls_04_settings['border_radius'] . 'px;';
	}
	if ( '' !== $ult_dls_04_settings['color_product_desc_bg'] ) {
		$desc_style .= 'background:' . $ult_dls_04_settings['color_product_desc_bg'] . ';';
	}
	if ( '' !== $ult_dls_04_settings['color_product_desc'] ) {
		$desc_style .= 'color:' . $ult_dls_04_settings['color_product_desc'] . ';';
	}
	$columns      = 3;
	$display_type = $ult_dls_04_settings['disp_type'];
	if ( '' !== $ult_dls_04_settings['color_heading'] ) {
		$heading_style = 'color:' . $ult_dls_04_settings['color_heading'] . ';';
	}
	if ( '' !== $ult_dls_04_settings['size_title'] ) {
		$heading_style .= 'font-size:' . $ult_dls_04_settings['size_title'] . 'px;';
	}
	if ( '' !== $ult_dls_04_settings['color_categories'] ) {
		$cat_style = 'color:' . $ult_dls_04_settings['color_categories'] . ';';
	}
	if ( '' !== $ult_dls_04_settings['size_cat'] ) {
		$cat_style .= 'font-size:' . $ult_dls_04_settings['size_cat'] . 'px;';
	}
	if ( '' !== $ult_dls_04_settings['color_price'] ) {
		$price_style = 'color:' . $ult_dls_04_settings['color_price'] . ';';
	}
	if ( '' !== $ult_dls_04_settings['size_price'] ) {
		$price_style .= 'font-size:' . $ult_dls_04_settings['size_price'] . 'px;';
	}
	if ( '' !== $ult_dls_04_settings['color_rating'] ) {
		$rating_style .= 'color:' . $ult_dls_04_settings['color_rating'] . ';';
	}
	if ( '' !== $ult_dls_04_settings['color_rating_bg'] ) {
		$rating_style .= 'background:' . $ult_dls_04_settings['color_rating_bg'] . ';';
	}
	if ( '' !== $ult_dls_04_settings['color_quick_bg'] ) {
		$view_bg_style = 'background:' . $ult_dls_04_settings['color_quick_bg'] . ';';
	}
	if ( '' !== $ult_dls_04_settings['color_quick'] ) {
		$view_style = 'color:' . $ult_dls_04_settings['color_quick'] . ';';
	}
	if ( '' !== $ult_dls_04_settings['color_cart_bg'] ) {
		$cart_bg_style = 'background:' . $ult_dls_04_settings['color_cart_bg'] . ';';
	}
	if ( '' !== $ult_dls_04_settings['color_cart'] ) {
		$cart_style = 'color:' . $ult_dls_04_settings['color_cart'] . ';';
	}
	if ( '' !== $ult_dls_04_settings['color_on_sale_bg'] ) {
		$label_style = 'background:' . $ult_dls_04_settings['color_on_sale_bg'] . ';';
	}
	if ( '' !== $ult_dls_04_settings['color_on_sale'] ) {
		$label_style .= 'color:' . $ult_dls_04_settings['color_on_sale'] . ';';
	}
	$elemets = explode( ',', $ult_dls_04_settings['display_elements'] );
	if ( 'grid' == $element ) {
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	} else {
		$paged = 1;
	}
	$post_count = '12';
	if ( '' !== $ult_dls_04_settings['shortcode'] ) {
		$new_shortcode = rawurldecode( base64_decode( wp_strip_all_tags( $ult_dls_04_settings['shortcode'] ) ) ); //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
	}
	$pattern       = get_shortcode_regex();
	$shortcode_str = '';
	$short_atts    = '';
	preg_match_all( '/' . $pattern . '/', $new_shortcode, $matches );
	$shortcode_str = str_replace( '"', '', str_replace( ' ', '&', trim( $matches[3][0] ) ) );
	parse_str( $shortcode_str, $short_atts );
	if ( isset( $matches[2][0] ) ) :
		$display_type = $matches[2][0];
else :
	$display_type = '';
endif;
if ( ! isset( $columns ) ) :
	$columns = '4';
endif;
if ( isset( $per_page ) ) :
	$post_count = $per_page;
endif;
if ( isset( $number ) ) :
	$post_count = $number;
endif;
if ( ! isset( $order ) ) :
	$order = 'asc';
endif;
if ( ! isset( $orderby ) ) :
	$orderby = 'date';
endif;
if ( ! isset( $ult_dls_04_settings['category'] ) ) :
	$ult_dls_04_settings['category'] = '';
endif;
if ( ! isset( $ids ) ) :
	$ids = '';
endif;
if ( $ids ) {
	$ids = explode( ',', $ids );
	$ids = array_map( 'trim', $ids );
}
	$col = $columns;
if ( '2' == $columns ) {
	$columns = 6;
} elseif ( '3' == $columns ) {
	$columns = 4;
} elseif ( '4' == $columns ) {
	$columns = 3;
}
	$meta_query = '';
if ( 'recent_products' == $display_type ) {
	$meta_query = WC()->query->get_meta_query();
}
if ( 'featured_products' == $display_type ) {
	$meta_query = array(
		array(
			'key'     => '_visibility',
			'value'   => array( 'catalog', 'visible' ),
			'compare' => 'IN',
		),
		array(
			'key'   => '_featured',
			'value' => 'yes',
		),
	);
}
if ( 'top_rated_products' == $display_type ) {
	add_filter( 'posts_clauses', array( WC()->query, 'order_by_rating_post_clauses' ) );
	$meta_query = WC()->query->get_meta_query();
}
	$args = array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => $post_count,
		'orderby'             => $orderby,
		'order'               => $order,
		'paged'               => $paged,
		'meta_query'          => $meta_query,
	);
	if ( 'sale_products' == $display_type ) {
		$product_ids_on_sale = woocommerce_get_product_ids_on_sale();
		$meta_query          = array();
		$meta_query[]        = $woocommerce->query->visibility_meta_query();
		$meta_query[]        = $woocommerce->query->stock_status_meta_query();
		$args['meta_query']  = $meta_query;
		$args['post__in']    = $product_ids_on_sale;
	}
	if ( 'best_selling_products' == $display_type ) {
		$args['meta_key']   = 'total_sales';
		$args['orderby']    = 'meta_value_num';
		$args['meta_query'] = array(
			array(
				'key'     => '_visibility',
				'value'   => array( 'catalog', 'visible' ),
				'compare' => 'IN',
			),
		);
	}
	if ( 'product_category' == $display_type ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'product_cat',
				'terms'    => array( esc_attr( $ult_dls_04_settings['category'] ) ),
				'field'    => 'slug',
				'operator' => 'IN',
			),
		);
	}
	if ( 'product_categories' == $display_type ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'product_cat',
				'terms'    => $ids,
				'field'    => 'term_id',
				'operator' => 'IN',
			),
		);
	}
	$test = '';
	if ( vc_is_inline() ) {
		$test = 'wcmp_vc_inline';
	}
	if ( '' == $ult_dls_04_settings['product_animation'] ) {
		$ult_dls_04_settings['product_animation'] = 'no-animation';
	} else {
		$style .= 'opacity:1;';
	}
	if ( 'grid' == $element ) {
		$class = 'vc_span' . $columns . ' ';
	}
	$output .= '<div class="woocomposer ' . $test . '" data-columns="' . $col . '">';
	$query   = new WP_Query( $args );
	if ( $query->have_posts() ) :
		while ( $query->have_posts() ) :
			$query->the_post();
			$product_id = get_the_ID();
			$uid        = uniqid();
			$output    .= '<div id="product-' . $uid . '" style="' . $style . '" class="' . $class . ' wpb_column column_container wooproduct" data-animation="animated ' . $ult_dls_04_settings['product_animation'] . '">';
			if ( 'carousel' == $element ) {
				$output .= '<div class="wcmp-carousel-item">';
			}
			$product_title  = get_the_title( $product_id );
			$post           = get_post( $product_id );
			$product_desc   = get_post( $product_id )->post_excerpt;
			$product_img    = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), $image_size );
			$product        = new WC_Product( $product_id );
			$attachment_ids = $product->get_gallery_attachment_ids();
			$price          = $product->get_price_html();
			$rating         = $product->get_rating_html();
			$attributes     = $product->get_attributes();
			$stock          = $product->is_in_stock() ? 'InStock' : 'OutOfStock';
			if ( $product->is_on_sale() ) :
				$on_sale = apply_filters( 'woocommerce_sale_flash', $ult_dls_04_settings['label_on_sale'], $post, $product );
			else :
				$on_sale = '';
			endif;
			if ( 'expandable' == $ult_dls_04_settings['quick_view_style'] ) {
				$quick_view_class = 'quick-view-loop';
			} else {
				$quick_view_class = 'quick-view-loop-popup';
			}
			$cat_count   = count( get_the_terms( $product_id, 'product_cat' ) );
			$tag_count   = count( get_the_terms( $product_id, 'product_tag' ) );
			$categories  = $product->get_categories( ', ', '<span class="posted_in">' . $cat_count . ' ', '.</span>' );
			$tags        = $product->get_tags( ', ', '<span class="tagged_as">' . $tag_count . ' ', '.</span>' );
			$output     .= "\n" . '<div class="wcmp-product woocommerce wcmp-' . $ult_dls_04_settings['product_style'] . ' ' . $ult_dls_04_settings['img_animate'] . '" style="' . $border . ' ' . $desc_style . '">';
				$output .= "\n\t" . '<div class="wcmp-product-image">';
			if ( empty( $attachment_ids ) && count( $attachment_ids ) > 1 && 'carousel' == $ult_dls_04_settings['product_img_disp'] ) {
				$uniqid      = uniqid();
				$output     .= '<div class="wcmp-single-image-carousel carousel-in-loop">';
				$product_img = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), $image_size );
				if ( 'enable' == $ult_dls_04_settings['lazy_images'] ) {
					$src = UAVC_URL . 'assets/img/loader.gif';
				} else {
					$src = $product_img[0];
				}
				$output .= '<div><div class="wcmp-image"><img class="wcmp-img" src="' . $src . '" data-src="' . $product_img[0] . '"/></div></div>';
				foreach ( $attachment_ids as $attachment_id ) {
					$product_img = wp_get_attachment_image_src( $attachment_id, $image_size );
					if ( 'enable' == $ult_dls_04_settings['lazy_images'] ) {
						$src = UAVC_URL . 'assets/img/loader.gif';
					} else {
						$src = $product_img[0];
					}
					$output .= '<div><div class="wcmp-image"><img class="wcmp-img" src="' . $src . '" data-src="' . $product_img[0] . '"/></div></div>';
				}
				$output .= '</div>';
			} else {
				$product_img = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), $image_size );
				if ( 'enable' == $ult_dls_04_settings['lazy_images'] ) {
					$src = UAVC_URL . 'assets/img/loader.gif';
				} else {
					$src = $product_img[0];
				}
				$output .= '<a href="' . get_permalink( $product_id ) . '"><img class="wcmp-img" src="' . $src . '" data-src="' . $product_img[0] . '"/></a>';
			}
			if ( 'OutOfStock' == $stock ) {
				$output .= "\n" . '<span class="wcmp-out-stock">' . __( 'Out Of Stock!', 'woocomposer' ) . '</span>';
			}
			if ( '' !== $on_sale ) {
				$output .= "\n" . '<div class="wcmp-onsale ' . $ult_dls_04_settings['on_sale_alignment'] . ' ' . $ult_dls_04_settings['on_sale_style'] . '"><span class="onsale" style="' . $label_style . ' ' . $sale_price_size . '">' . $on_sale . '</span></div>';
			}
					$output .= '<div class="wcmp-add-to-cart" style="' . $cart_bg_style . '"><a style="' . $cart_style . '" title="Add to Cart" href="?add-to-cart=' . $product_id . '" rel="nofollow" data-product_id="' . $product_id . '" data-product_sku="" class="add_to_cart_button product_type_simple"><i class="wooicon-cart4"></i></a></div>';
			if ( in_array( 'quick', $elemets ) ) {
				$output .= '<div class="wcmp-quick-view ' . $quick_view_class . '" style="' . $view_bg_style . '"><a style="' . $view_style . '" title="Quick View" href="' . get_permalink( $product_id ) . '"><i class="wooicon-plus32"></i></a></div>';
			}
			if ( in_array( 'reviews', $elemets ) ) {
				$output .= "\n" . '<div class="wcmp-star-ratings" style="' . $rating_style . '">' . $rating . '</div>';
			}
				$output     .= '</div>';
				$output     .= "\n\t" . '<div class="wcmp-product-desc">';
					$output .= '<a href="' . get_permalink( $product_id ) . '">';
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
			if ( in_array( 'description', $elemets ) ) {
				$output .= "\n\t\t" . '<div class="wcmp-product-content" style="' . $desc_style . '">' . $product_desc . '</div>';
			}
				$output .= "\n\t" . '</div>';
			$output     .= "\n\t" . '</div>';
			if ( in_array( 'quick', $elemets ) ) {
				$output .= '<div class="wcmp-quick-view-wrapper woocommerce" data-columns="' . $col . '">';
				if ( 'expandable' !== $ult_dls_04_settings['quick_view_style'] ) {
					$output .= '<div class="wcmp-quick-view-wrapper woocommerce product">';
					$output .= '<div class="wcmp-close-single"><i class="wooicon-cross2"></i></div>';
				}
					$product_img = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), $image_size );
				if ( 'enable' == $ult_dls_04_settings['lazy_images'] ) {
					$src = UAVC_URL . 'assets/img/loader.gif';
				} else {
					$src = $product_img[0];
				}
					$output .= '<div class="wcmp-single-image wcmp-quickview-img images"><img class="wcmp-img" src="' . $src . '" data-src="' . $product_img[0] . '"/></div>';
				if ( 'expandable' !== $ult_dls_04_settings['quick_view_style'] ) {
					$output .= '<div class="wcmp-product-content-single">';
				} else {
					$output .= '<div class="wcmp-product-content">';
				}
					ob_start();
					do_action( 'woocommerce_single_product_summary' );
					$output .= ob_get_clean();
					$output .= '</div>';
					$output .= '<div class="clear"></div>';
				if ( 'expandable' !== $ult_dls_04_settings['quick_view_style'] ) {
					$output .= '</div>';
				}
				$output .= '</div>';
			}
			$output .= "\n" . '</div>';
			if ( 'carousel' == $element ) {
				$output .= "\n\t" . '</div>';
			}
		endwhile;
	endif;
	if ( 'enable' == $ult_dls_04_settings['pagination'] ) {
		$output .= '<div class="wcmp-paginate">';
		$output .= woocomposer_pagination( $query->max_num_pages );
		$output .= '</div>';
	}
	$output .= '</div>';
	if ( 'top_rated_products' == $display_type ) {
		remove_filter( 'posts_clauses', array( WC()->query, 'order_by_rating_post_clauses' ) );
	}
	wp_reset_postdata();
	return $output;
}
