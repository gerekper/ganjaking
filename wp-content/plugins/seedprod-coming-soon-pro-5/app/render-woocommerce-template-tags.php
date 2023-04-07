<?php


/**
 * Render WooCommerce Template Tags.
 */

// Add [seedprod_wc] shortcode.
add_shortcode( 'seedprod_wc', 'seedprod_pro_render_wc_template_tags_shortcode' );

/**
 * Render SeedProd WC Shortcode[seedprod_wc].
 *
 * @return void|string
 */
function seedprod_pro_render_wc_template_tags_shortcode( $atts ) {
	$a = shortcode_atts(
		array(
			'tag'  => '',
			'echo' => false,
		),
		$atts
	);

	$tag_allow_list = array(
		'the_title',
		'the_post_thumbnail',
		'price_html',
		'the_content',
		'the_excerpt',
		'short_description',
	);

	// If tag not allowed return empty string.
	if ( ! in_array( $a['tag'], $tag_allow_list ) ) {
		return;
	}

	// Check if the WC Instance exists.
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		return;
	}

	global $product;

	// If the WC_product Object is not defined globally
	if ( ! is_a( $product, 'WC_Product' ) ) {
		$product = wc_get_product( get_the_id() );
	}

	// Render WC Template Tags.
	if ( ! empty( $a['tag'] ) ) {
		$values  = null;
		$values2 = null;

		if ( ! $product ) {
			return;
		}

		if ( strpos( $a['tag'], '(' ) !== false ) {
			preg_match( '#\((.*?)\)#', $a['tag'], $match );
			$a['tag'] = str_replace( $match[0], '', $a['tag'] );
			$values   = $match[1];
		}
		if ( 'the_post_thumbnail' === $a['tag'] ) {
			remove_all_filters( 'post_thumbnail_html' );
			$values2 = array( 'alt' => get_the_title() );
		}

		ob_start();
		if ( 'get_post_custom_values' === $a['tag'] ) {
			$output = @call_user_func( $a['tag'], $values, $values2 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			if ( ! empty( $output[0] ) ) {
				$output = $output[0];
			}
			echo wp_kses_post( $output );
		} elseif ( 'price_html' == $a['tag'] ) {
			$output = $product->get_price_html();
			echo wp_kses_post( $output );
		} elseif ( 'the_title' == $a['tag'] ) {
			$output = $product->get_name();
			echo wp_kses_post( $output );
		} elseif ( 'short_description' == $a['tag'] ) {
			$output = $product->get_short_description();
			echo wp_kses_post( $output );
		} elseif ( ! empty( $a['echo'] ) ) {
			$output = @call_user_func( $a['tag'], $values, $values2 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			echo wp_kses_post( $output );
		} else {
			if ( 'none' == $values ) {
				$output = @call_user_func( $a['tag'] ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			} else {
				@call_user_func( $a['tag'], $values, $values2 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			}
		}

		$render = ob_get_clean();
		return $render;
	}
}

// Add [sp_menu_cart] shortcode.
add_shortcode( 'sp_menu_cart', 'seedprod_pro_woocommerce_template_tags_menu_cart_shortcode' );

/**
 * Render menu cart.
 *
 * @return void|string
 */
function seedprod_pro_woocommerce_template_tags_menu_cart_shortcode( $atts ) {
	// Check if the WC Instance exists.
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		return;
	}

	$shortcode_args = shortcode_atts(
		array(
			'hide_on_empty' => 'false',
			'show_subtotal' => 'true',
		),
		$atts
	);

	// Get data.
	$product_count = WC()->cart->get_cart_contents_count();
	$sub_total     = WC()->cart->get_cart_subtotal();
	$cart_link     = wc_get_cart_url();
	$render        = '';

	if ( ( 'true' === $shortcode_args['hide_on_empty'] ) ? 0 < $product_count : true ) {
		$render .= '<a href="' . esc_attr( $cart_link ) . '">';
		if ( 'true' === $shortcode_args['show_subtotal'] ) {
			$render .= '<span class="sp-menu-cart-button-text">' . $sub_total . '</span>';
		}
		$render .= '
			<span class="sp-relative sp-inline-block sp-menu-cart-icon-badge">
				<i class="fas fa-shopping-cart"></i>
				<span class="sp-text-xs sp-text-white sp-bg-red sp-menu-cart-item-count">' . $product_count . '</span>
			</span>
		';
		$render .= '</a>';
	}

	return wp_kses_post( $render );
}

// Add [sp_add_to_cart] shortcode.
add_shortcode( 'sp_add_to_cart', 'seedprod_pro_woocommerce_template_tags_add_to_cart_shortcode' );

/**
 * Add To Cart Shortcode function.
 *
 * @param array $atts Shortcode attributes.
 * @return string|void $render Add To Cart html render.
 */
function seedprod_pro_woocommerce_template_tags_add_to_cart_shortcode( $atts ) {
	// Set default shortcode args.
	$shortcode_args = shortcode_atts(
		array(
			'btn_txt'     => 'Add To Cart',
			'before_icon' => '',
			'after_icon'  => '',
		),
		$atts
	);

	// Check if the WC Instance exists.
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		return;
	}

	// Get current product.
	$render = '';

	global $product;

	// If the WC_product Object is not defined globally
	if ( ! is_a( $product, 'WC_Product' ) ) {
		$product = wc_get_product( get_the_id() );
	}

	if ( ! $product ) {
		return;
	}

	$callback_btn_text              = new ButtonTextCallback();
	$callback_btn_text->btn_txt     = $shortcode_args['btn_txt'];
	$callback_btn_text->before_icon = $shortcode_args['before_icon'];
	$callback_btn_text->after_icon  = $shortcode_args['after_icon'];

	$unescape_html = function( $safe_text, $text ) {
		return $text;
	};

	add_filter( 'woocommerce_get_stock_html', '__return_empty_string' );
	add_filter( 'woocommerce_product_single_add_to_cart_text', array( $callback_btn_text, 'get_text' ) );
	add_filter( 'esc_html', $unescape_html, 10, 2 );

	ob_start();
	/**
	 * Hook: woocommerce_before_single_product.
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 */
	do_action( 'woocommerce_before_single_product' );
	woocommerce_template_single_add_to_cart();
	$form   = ob_get_clean();
	$form   = str_replace( 'single_add_to_cart_button button', 'single_add_to_cart_button sp-button', $form );
	$render = $form;

	remove_filter( 'woocommerce_product_single_add_to_cart_text', array( $callback_btn_text, 'get_text' ) );
	remove_filter( 'woocommerce_get_stock_html', '__return_empty_string' );
	remove_filter( 'esc_html', $unescape_html );

	return $render;
}

/**
 * Button Text Callback using object that carries variable in it's state.
 *
 */
class ButtonTextCallback {
	/**
	 * Button text.
	 *
	 * @var string
	 */
	public $btn_txt;

	/**
	 * Before Icon
	 *
	 * @var string
	 */
	public $before_icon;

	/**
	 * After Icon
	 *
	 * @var string
	 */
	public $after_icon;

	/**
	 * Get text.
	 *
	 * @return string
	 */
	public function get_text() {
		return esc_html( '<i class="sp-mr-2 ' . esc_attr( $this->before_icon ) . '"></i> ' . esc_html( $this->btn_txt ) . ' <i class="sp-ml-2 ' . esc_attr( $this->after_icon ) . '"></i>' );
	}
}

// Add [sp_product_meta] shortcode.
add_shortcode( 'sp_product_meta', 'seedprod_pro_woocommerce_template_tags_product_meta_shortcode' );

/**
 * Product Meta Shortcode function.
 *
 * @param array $atts Shortcode attributes.
 * @return string|void $render Product Meta html render.
 */
function seedprod_pro_woocommerce_template_tags_product_meta_shortcode( $atts ) {
	// Set default shortcode args.
	$shortcode_args = shortcode_atts(
		array(
			'divider' => '',
		),
		$atts
	);

	// Check if the WC Instance exists.
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		return;
	}

	// Get current product.
	$render = '';

	global $product;

	// If the WC_product Object is not defined globally
	if ( ! is_a( $product, 'WC_Product' ) ) {
		$product = wc_get_product( get_the_id() );
	}

	if ( ! $product ) {
		return;
	}

	$sku = $product->get_sku();
	$sku = $sku ? $sku : __( 'N/A', 'seedprod-pro' );

	$categories_count = count( $product->get_category_ids() );
	$categories_label = 1 < $categories_count ? __( 'Categories', 'seedprod-pro' ) : __( 'Category', 'seedprod-pro' );

	$tags_count = count( $product->get_tag_ids() );
	$tags_label = 1 < $tags_count ? __( 'Tags', 'seedprod-pro' ) : __( 'Tag', 'seedprod-pro' );

	$render .= '<div class="sp-product-meta-container">';

	do_action( 'woocommerce_product_meta_start' );

	if ( wc_product_sku_enabled() && ( $sku || $product->is_type( 'variable' ) ) ) {
		$render .= '<span class="sp-sku-wrapper sp-detail-container"><span class="detail-label">' . esc_html__( 'SKU', 'seedprod-pro' ) . '</span> <span class="sp-sku-detail">' . $sku . '</span>';

		if ( count( $product->get_category_ids() ) || count( $product->get_tag_ids() ) ) {
			$render .= '<span class="sp-product-meta-divider">' . $shortcode_args['divider'] . '</span>';
		}

		$render .= '</span>';
	}

	if ( count( $product->get_category_ids() ) ) {
		$render .= '<span class="sp-product-category sp-detail-container"><span class="detail-label">' . esc_html( $categories_label ) . '</span> <span class="sp-product-detail sp-detail-content">' . get_the_term_list( $product->get_id(), 'product_cat', '', ', ' ) . '</span>';

		if ( count( $product->get_tag_ids() ) ) {
			$render .= '<span class="sp-product-meta-divider">' . $shortcode_args['divider'] . '</span>';
		}

		$render .= '</span>';
	}

	if ( count( $product->get_tag_ids() ) ) {
		$render .= '<span class="sp-product-tag sp-detail-container"><span class="detail-label">' . esc_html( $tags_label ) . '</span> <span class="sp-tag-detail sp-detail-content">' . get_the_term_list( $product->get_id(), 'product_tag', '', ', ' ) . '</span></span>';
	}

	do_action( 'woocommerce_product_meta_end' );

	$render .= '</div>';

	return wp_kses_post( $render );
}

// Add [sp_product_data_tabs] shortcode.
add_shortcode( 'sp_product_data_tabs', 'seedprod_pro_woocommerce_template_tags_product_data_tabs_shortcode' );

/**
 * Product Data Tabs Shortcode function.
 *
 * @param array $atts Shortcode attributes.
 * @return string|void $render Html render.
 */
function seedprod_pro_woocommerce_template_tags_product_data_tabs_shortcode( $atts ) {
	// Set default shortcode args.
	$shortcode_args = shortcode_atts(
		array(),
		$atts
	);

	// Check if the WC Instance exists.
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		return;
	}

	// Get current product.
	$render = '';

	global $product;

	// If the WC_product Object is not defined globally
	if ( ! is_a( $product, 'WC_Product' ) ) {
		$product = wc_get_product( get_the_id() );
	}

	if ( ! $product ) {
		return;
	}

	// Enable comments.
	global $withcomments;
	$withcomments = true; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

	wp_enqueue_script( 'wc-single-product' );

	ob_start();
	setup_postdata( $product->get_id() );
	wc_get_template( 'single-product/tabs/tabs.php' );
	$render = ob_get_clean();

	return $render;
}

// Add [sp_product_gallery_images] shortcode.
add_shortcode( 'sp_product_gallery_images', 'seedprod_pro_woocommerce_template_tags_product_gallery_images_shortcode' );

/**
 * Product Gallery Images Shortcode function.
 *
 * @param array $atts Shortcode attributes.
 * @return string|void $render Html render.
 */
function seedprod_pro_woocommerce_template_tags_product_gallery_images_shortcode( $atts ) {
	// Set default shortcode args.
	$shortcode_args = shortcode_atts(
		array(),
		$atts
	);

	// Check if the WC Instance exists.
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		return;
	}

	// Get current product.
	$render = '';

	global $product;

	// If the WC_product Object is not defined globally
	if ( ! is_a( $product, 'WC_Product' ) ) {
		$product = wc_get_product( get_the_id() );
	}

	if ( ! $product ) {
		return;
	}

	// Enqueue scripts.
	wp_enqueue_script( 'woocommerce' );

	// Load gallery scripts on product pages only if supported.
	if ( $product ) {
		if ( current_theme_supports( 'wc-product-gallery-zoom' ) ) {
			wp_enqueue_script( 'zoom' );
		}
		if ( current_theme_supports( 'wc-product-gallery-slider' ) ) {
			wp_enqueue_script( 'flexslider' );
		}
		if ( current_theme_supports( 'wc-product-gallery-lightbox' ) ) {
			wp_enqueue_script( 'photoswipe-ui-default' );
			wp_enqueue_script( 'photoswipe-default-skin' );
			add_action( 'wp_footer', 'woocommerce_photoswipe' );
		}
		wp_enqueue_script( 'wc-single-product' );
	}

	ob_start();
	setup_postdata( $product->get_id() );
	// Output the product image.
	wc_get_template( 'single-product/product-image.php' );
	$render = ob_get_clean();

	return $render;
}

// Add [sp_additional_info] shortcode.
add_shortcode( 'sp_additional_info', 'seedprod_pro_woocommerce_template_tags_additional_information_shortcode' );

/**
 * Additional Information Shortcode function.
 *
 * @param array $atts Shortcode attributes.
 * @return string|void $render Html render.
 */
function seedprod_pro_woocommerce_template_tags_additional_information_shortcode( $atts ) {
	// Set default shortcode args.
	$shortcode_args = shortcode_atts(
		array(),
		$atts
	);

	// Check if the WC Instance exists.
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		return;
	}

	// Get current product.
	$render = '';

	global $product;

	// If the WC_product Object is not defined globally
	if ( ! is_a( $product, 'WC_Product' ) ) {
		$product = wc_get_product( get_the_id() );
	}

	if ( ! $product ) {
		return;
	}

	ob_start();
	setup_postdata( $product->get_id() );
	wc_get_template( 'single-product/tabs/additional-information.php' );
	$render = ob_get_clean();

	return wp_kses_post( $render );
}

// Add [sp_product_related] shortcode.
add_shortcode( 'sp_product_related', 'seedprod_pro_woocommerce_template_tags_product_related_shortcode' );

/**
 * Product Related Shortcode function.
 *
 * @param array $atts Shortcode attributes.
 * @return string|void $render Html render.
 */
function seedprod_pro_woocommerce_template_tags_product_related_shortcode( $atts ) {
	// Set default shortcode args.
	$shortcode_args = shortcode_atts(
		array(
			'columns'  => 4,
			'paginate' => false,
			'limit'    => '-1',
			'orderby'  => '',
			'order'    => '',
		),
		$atts
	);

	// Check if the WC Instance exists.
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		return;
	}

	// Get current product.
	$render = '';

	global $product;

	// If the WC_product Object is not defined globally
	if ( ! is_a( $product, 'WC_Product' ) ) {
		$product = wc_get_product( get_the_id() );
	}

	if ( ! $product ) {
		return;
	}

	$related_args = array(
		'posts_per_page' => $shortcode_args['limit'],
		'columns'        => $shortcode_args['columns'],
		'orderby'        => $shortcode_args['orderby'],
		'order'          => $shortcode_args['order'],
	);

	// Get visible related products then sort them at random.
	$related_args['related_products'] = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product->get_id(), $related_args['posts_per_page'], $product->get_upsell_ids() ) ), 'wc_products_array_filter_visible' );

	// Handle orderby.
	$related_args['related_products'] = wc_products_array_orderby( $related_args['related_products'], $related_args['orderby'], $related_args['order'] );

	wc_set_loop_prop( 'name', 'related' );
	wc_set_loop_prop( 'columns', $related_args['columns'] );

	ob_start();
	setup_postdata( $product->get_id() );
	wc_get_template( 'single-product/related.php', $related_args );
	$render = ob_get_clean();
	$render = str_replace( 'related', 'woocommerce related', $render );

	return wp_kses_post( $render );
}

// Add [sp_product_upsells] shortcode.
add_shortcode( 'sp_product_upsells', 'seedprod_pro_woocommerce_template_tags_upsells_shortcode' );

/**
 * Upsells Shortcode function.
 *
 * @param array $atts Shortcode attributes.
 * @return string|void $render Html render.
 */
function seedprod_pro_woocommerce_template_tags_upsells_shortcode( $atts ) {
	// Set default shortcode args.
	$shortcode_args = shortcode_atts(
		array(
			'columns' => 1,
			'limit'   => '-1',
			'orderby' => '',
			'order'   => '',
		),
		$atts
	);

	// Check if the WC Instance exists.
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		return;
	}

	// Get current product.
	$render = '';

	global $product;

	// If the WC_product Object is not defined globally
	if ( ! is_a( $product, 'WC_Product' ) ) {
		$product = wc_get_product( get_the_id() );
	}

	if ( ! $product ) {
		return;
	}

	ob_start();
	setup_postdata( $product->get_id() );
	woocommerce_upsell_display( $shortcode_args['limit'], $shortcode_args['columns'], $shortcode_args['orderby'], $shortcode_args['order'] );
	$render = ob_get_clean();
	$render = str_replace( 'up-sells', 'woocommerce up-sells', $render );

	wp_reset_postdata();

	return wp_kses_post( $render );
}

// Add [sp_product_rating] shortcode.
add_shortcode( 'sp_product_rating', 'seedprod_pro_woocommerce_template_tags_product_rating_shortcode' );

/**
 * Product Rating Shortcode function.
 *
 * @param array $atts Shortcode attributes.
 * @return string|void $render Html render.
 */
function seedprod_pro_woocommerce_template_tags_product_rating_shortcode( $atts ) {
	// Set default shortcode args.
	$shortcode_args = shortcode_atts(
		array(),
		$atts
	);

	// Check if the WC Instance exists.
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		return;
	}

	// Get current product.
	$render = '';

	global $product;

	// If the WC_product Object is not defined globally
	if ( ! is_a( $product, 'WC_Product' ) ) {
		$product = wc_get_product( get_the_id() );
	}

	if ( ! $product ) {
		return;
	}

	wp_enqueue_script( 'wc-single-product' );

	ob_start();
	setup_postdata( $product->get_id() );
	wc_get_template( 'single-product/rating.php' );
	$render = ob_get_clean();
	$render = str_replace( 'woocommerce-product-rating', 'woocommerce woocommerce-product-rating', $render );

	return wp_kses_post( $render );
}

// Add [sp_product_stock] shortcode.
add_shortcode( 'sp_product_stock', 'seedprod_pro_woocommerce_template_tags_product_stock_shortcode' );

/**
 * Product Stock Shortcode function.
 *
 * @param array $atts Shortcode attributes.
 * @return string|void $render Html render.
 */
function seedprod_pro_woocommerce_template_tags_product_stock_shortcode( $atts ) {
	// Set default shortcode args.
	$shortcode_args = shortcode_atts(
		array(),
		$atts
	);

	// Check if the WC Instance exists.
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		return;
	}

	// Get current product.
	$render = '';

	global $product;

	// If the WC_product Object is not defined globally
	if ( ! is_a( $product, 'WC_Product' ) ) {
		$product = wc_get_product( get_the_id() );
	}

	if ( ! $product ) {
		return;
	}

	$render = wc_get_stock_html( $product );

	return wp_kses_post( $render );
}


