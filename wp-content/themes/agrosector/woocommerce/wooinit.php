<?php

// declare woocomerece custom theme stylesheets and js
function wp_enqueue_woocommerce_style() {
    wp_register_style( 'woocommerce', get_template_directory_uri() . '/woocommerce/css/woocommerce.css' );
    wp_enqueue_style( 'woocommerce' );
}
add_action( 'wp_enqueue_scripts', 'wp_enqueue_woocommerce_style' );

function css_js_woocomerce() {
	wp_enqueue_script( 'imagesloaded' );
	if ( class_exists( 'WC_List_Grid' ) ) {
		global $WC_List_Grid;
		add_action( 'wp_enqueue_scripts', array( $WC_List_Grid, 'setup_scripts_styles' ), 20 );
	}
	wp_enqueue_script( 'gt3-main-woo', get_template_directory_uri() . '/woocommerce/js/theme-woo.js', array(), false, true );

	wp_enqueue_script( 'gt3-appear', get_template_directory_uri() . '/js/jquery.appear.min.js', array('gt3-infinite-scroll'), false, true );
	wp_register_script( 'gt3-infinite-scroll', get_template_directory_uri() . '/woocommerce/js/infinite-scroll.pkgd.min.js', array( 'jquery' ), '3.0.5', true );

	$products_infinite_scroll = gt3_option( 'products_infinite_scroll' );
	if ( ! empty( $products_infinite_scroll ) && $products_infinite_scroll !== 'none' ) {
		wp_enqueue_script( 'gt3-infinite-scroll');
	}
}
add_action('wp_enqueue_scripts', 'css_js_woocomerce');
// end of declare woocomerece custom theme stylesheets and js

function gt3_get_template ($tmpl, $extension = NULL) {
    get_template_part( 'woocommerce/gt3-templates/' . $tmpl, $extension );
}

remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

function gt3_product_title_wrapper () {
    echo '<h3 class="gt3-product-title">'.get_the_title().'</h3>';
}

function gt3_product_image_wrap_open () {
    echo '<div class="gt3-product-image-wrapper">';
}

function gt3_product_image_wrap_close () {
    echo '</div>';
}

function gt3_add_label_outofstock () {
    global $product;
    if (!($product->is_in_stock())) {
        echo '<div class="gt3-product-outofstock"><span class="gt3-product-outofstock__inner">'.esc_html__('Out Of Stock', 'agrosector').'</span></div>';
    }
}
add_action('woocommerce_before_shop_loop_item_title', 'gt3_add_label_outofstock', 6);

// Remove woocommerce breadcrumb
remove_action('woocommerce_before_main_content','woocommerce_breadcrumb', 20);
//add breadcrumb to single product
if (gt3_option('shop_title_conditional') != '1' && gt3_option('page_title_breadcrumbs_conditional') == '1' && gt3_option('page_title_conditional') == '1' ) {
    add_action('woocommerce_single_product_summary','woocommerce_breadcrumb', 4);
}

add_action( 'yith_wcqv_product_image', 'gt3_product_image_wrap_open', 9 );
add_action( 'yith_wcqv_product_image', 'gt3_product_image_wrap_close', 21 );

function gt3_add_thumb_wcqv () {
    add_action('woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 2);
}
add_action( 'wp_ajax_yith_load_product_quick_view', "gt3_add_thumb_wcqv", 1);
add_action( 'wp_ajax_nopriv_yith_load_product_quick_view', 'gt3_add_thumb_wcqv',1 );

remove_action( 'yith_wcqv_product_summary', 'woocommerce_template_single_excerpt', 20 );

function gt3_page_template () {
	$id = gt3_get_queried_object_id();
    switch (is_single()) {
        case true:
            $layout = gt3_option('product_sidebar_layout');
            $sidebar = gt3_option('product_sidebar_def');
            break;
        case false:
            $layout = gt3_option('products_sidebar_layout');
            $sidebar = gt3_option('products_sidebar_def');
            break;
        default:
            $layout = gt3_option('products_sidebar_layout');
            $sidebar = gt3_option('products_sidebar_def');
    }
    if (class_exists( 'RWMB_Loader' ) && $id !== 0 && !(class_exists('WooCommerce') && is_product_category())) {
        $mb_layout = rwmb_meta('mb_page_sidebar_layout', array(), $id);
        if (!empty($mb_layout) && $mb_layout != 'default') {
            $layout = $mb_layout;
            $sidebar = rwmb_meta('mb_page_sidebar_def', array(), $id);
        }
    }
    if ( ($layout == 'left' || $layout == 'right') && is_active_sidebar( $sidebar )  ) {
        $column = 9;
    }else{
        $column = 12;
    }
    $row_class = ' sidebar_'.esc_attr($layout);

    $container_style = 'container';
    if ( !is_single() && get_post_type() == 'product') {
        $container_style = gt3_option('products_layout');
    } elseif (class_exists( 'RWMB_Loader' ) && is_single() && get_post_type() == 'product') {
        if (rwmb_meta('mb_single_product', array(), $id) === 'custom' ) {
            $container_style = rwmb_meta('mb_product_container', array(), $id);
        } else {
            $container_style = gt3_option('product_container');
        }
    }
    switch ($container_style) {
        case 'container':
            $container_class = 'container';
            break;
        case 'full_width':
            $container_class = 'fullwidth-wrapper';
            break;
        default:
            $container_class = 'container';
    }
    ?>

    <div class="<?php echo esc_html($container_class) ?>">
        <div class="row<?php echo esc_attr($row_class); ?>">

            <div class="content-container span<?php echo (int)$column; ?>">
                <section id='main_content'>
    <?php
}
add_action('woocommerce_before_main_content', 'gt3_page_template', 9);

// add bottom part of page template
function gt3_page_template_close () {
	$id = gt3_get_queried_object_id();
    switch (is_single()) {
        case true:
            $layout = gt3_option('product_sidebar_layout');
            $sidebar = gt3_option('product_sidebar_def');
            break;
        case false:
            $layout = gt3_option('products_sidebar_layout');
            $sidebar = gt3_option('products_sidebar_def');
            break;
        default:
            $layout = gt3_option('products_sidebar_layout');
            $sidebar = gt3_option('products_sidebar_def');
    }
    if (class_exists( 'RWMB_Loader' ) && $id !== 0 && !(class_exists('WooCommerce') && is_product_category())) {
        $mb_layout = rwmb_meta('mb_page_sidebar_layout', array(), $id);
        if (!empty($mb_layout) && $mb_layout != 'default') {
            $layout = $mb_layout;
            $sidebar = rwmb_meta('mb_page_sidebar_def', array(), $id);
        }
    }

    if ( ($layout == 'left' || $layout == 'right') && is_active_sidebar( $sidebar )  ) {
        $column = 9;
    }else{
        $column = 12;
        $sidebar = '';
    }
    ?>
                </section>
            </div>
            <?php
            if ($layout == 'left' || $layout == 'right') {
                echo '<div class="sidebar-container span'.(12 - (int)$column).'">';
                    if (is_active_sidebar( $sidebar )) {
                        echo "<aside class='sidebar'>";
                        dynamic_sidebar( $sidebar );
                        echo "</aside>";
                    }
                echo "</div>";
            }
            ?>
        </div>
    </div>
    <?php
}
add_action('woocommerce_after_main_content', 'gt3_page_template_close', 11);

// add sidebar to bottom on Shop page
function gt3_woo_bottom_products_sidebar_top(){
    $gt3_recently_viewed = gt3_option('woocommerce_recently_viewed');
    if ( !(bool)$gt3_recently_viewed ) return;
    if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {
        gt3_get_template('gt3-recently-viewed');
    }
}
add_action('woocommerce_after_shop_loop', 'gt3_woo_bottom_products_sidebar_top', 50);

if ( isset( $_GET['show'] ) ) {
	function gt3_products_per_page() {
		return $_GET['show'];
	}

	add_filter( 'loop_shop_per_page', 'gt3_products_per_page', 20 );
}
//Track product views.
function gt3_track_product_view() {
    $gt3_recently_viewed = gt3_option('woocommerce_recently_viewed');
    if ( !is_singular('product') || !(bool)$gt3_recently_viewed ) return;

    $viewed_products = empty($_COOKIE['gt3_product_recently_viewed']) ? array() : (array)explode('|',$_COOKIE['gt3_product_recently_viewed']);

    global $post;
    if ( ! in_array( $post->ID, $viewed_products ) ) {
        $viewed_products[] = $post->ID;
    }
    if ( sizeof( $viewed_products ) > 15 ) {
        array_shift( $viewed_products );
    }

    // Store for session only
    wc_setcookie( 'gt3_product_recently_viewed', implode( '|', $viewed_products ) );
}
add_action( 'template_redirect', 'gt3_track_product_view', 20 );

/* Products Page filter bar Top */
function gt3_woo_header_products_open () {
	echo '<div class="gt3-products-header">';
}

function gt3_woo_header_pagination() {
	$woocommerce_pagination   = gt3_option( 'woocommerce_pagination' );
	$products_infinite_scroll = gt3_option( 'products_infinite_scroll' );
	$view_all                 = isset($_COOKIE['gt3-show_all']) ? $_COOKIE['gt3-show_all'] : NULL;
	if ( ( $woocommerce_pagination == 'top' || $woocommerce_pagination == 'top_bottom' ) && $products_infinite_scroll !== 'always' && $view_all !== 'true' ) {
		echo '<div class="gt3-pagination_nav">';
		woocommerce_pagination();
		echo '</div>';
	}
}
function gt3_woo_header_products_per_page(){
	$filter_number = gt3_option('products_per_page_frontend');
	$products_infinite_scroll = gt3_option( 'products_infinite_scroll' );
	if ( (bool) $filter_number && $products_infinite_scroll !== 'always' ) {
		gt3_get_template('loop/product-show'); // Result Count
	}
}
function gt3_woo_header_catalog_ordering(){
	$products_sorting = gt3_option('products_sorting_frontend');
	if ( (bool)$products_sorting ) {
		gt3_get_template('loop/orderby');
	}
}
function gt3_woo_header_products_close () {
	echo '</div>';
}

function gt3_products_infinite_scroll_open() {
	$products_infinite_scroll = gt3_option( 'products_infinite_scroll' );
	echo '<div class="infinite_scroll-' . ( ! empty( $products_infinite_scroll ) ? esc_attr( $products_infinite_scroll ) : 'none' ) . '">';
}
function gt3_products_infinite_scroll_close() {
	echo '</div>';
}
add_action( 'woocommerce_before_shop_loop', 'gt3_products_infinite_scroll_open', 5 );
add_action( 'woocommerce_after_shop_loop', 'gt3_products_infinite_scroll_close', 45 );

add_action('woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 5);
add_action('woocommerce_before_shop_loop', 'gt3_woo_header_products_open', 9);
add_action('woocommerce_before_shop_loop', 'gt3_woo_header_pagination', 13); // GT3 Pagination
add_action('woocommerce_before_shop_loop', 'gt3_woo_header_products_per_page', 20); // Show
add_action('woocommerce_before_shop_loop', 'gt3_woo_header_catalog_ordering', 25); // Ordering
add_action('woocommerce_before_shop_loop', 'gt3_woo_header_products_close', 35);

remove_action('woocommerce_before_shop_loop','woocommerce_output_all_notices',10);
remove_action('woocommerce_before_shop_loop','woocommerce_result_count',20);
remove_action('woocommerce_before_shop_loop','woocommerce_catalog_ordering',30);
/* Products Page filter bar Top end */

/* Products Page filter bar Bottom */
function gt3_woo_products_bottom () {
	if ( ! wc_get_loop_prop( 'is_paginated' ) || ! woocommerce_products_will_display() ) {
		return;
	}
	$woocommerce_pagination = gt3_option( 'woocommerce_pagination' );
	$products_infinite_scroll = gt3_option( 'products_infinite_scroll' );
	if ( $woocommerce_pagination == 'bottom' || $woocommerce_pagination == 'top_bottom' || $products_infinite_scroll !== 'none' ) {
        echo '<div class="gt3-products-bottom">';
		    gt3_get_woo_template( 'loop/default-pagination' );
        echo '</div>';
    }
}
add_action('woocommerce_after_shop_loop', 'gt3_woo_products_bottom', 15);
remove_action('woocommerce_after_shop_loop','woocommerce_pagination',10);

function gt3_products_bubblings(){?>
    <div class="spinner infinite-scroll">
        <div class="infinite-scroll-request">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
    </div>
	<?php
}
add_action('woocommerce_after_shop_loop', 'gt3_products_bubblings', 11);
add_action('gt3_woocommerce_after_shop_loop', 'gt3_products_bubblings', 11);

function gt3_wrap_single_product_open () {
    echo '<div class="gt3-single-content-wrapper">';
}
function gt3_wrap_single_product_close () {
    echo '</div>';
}

function gt3_add_sticky_parent_open() {
	$thumb_direction = gt3_option( 'product_layout' );
	$id              = gt3_get_queried_object_id();
	if ( class_exists( 'RWMB_Loader' ) ) {
		$mb_single_product = rwmb_meta( 'mb_single_product', array(), $id );
		if ( $mb_single_product === 'custom' ) {
			$thumb_direction = rwmb_meta( 'mb_thumbnails_layout', array(), $id );
		}
	}
	echo '<div class="gt3-single-product-sticky gt3_thumb_sticky_' . $thumb_direction . '">';
}

function gt3_add_sticky_parent_close() {
	echo '</div>';
}

// Add theme support for single product
function gt3_add_single_product_opts () {
    add_theme_support('woocommerce', array(
	    'gallery_thumbnail_image_width' => 800,
    ) );
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-slider');
    add_theme_support('wc-product-gallery-lightbox');
}
add_action('after_setup_theme','gt3_add_single_product_opts');

add_filter( 'woocommerce_add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment' );
function woocommerce_header_add_to_cart_fragment( $fragments ) {
  global $woocommerce;
  ob_start();
    ?>
      <i class='woo_mini-count'><?php echo ((WC()->cart->cart_contents_count > 0) ? '<span>' . esc_html( WC()->cart->cart_contents_count ) .'</span>' : '') ?></i>
    <?php
    $fragments['.woo_mini-count'] = ob_get_clean();

    ob_start();
    echo '<div class="gt3_header_builder_cart_component__cart-container">';
    woocommerce_mini_cart();
    echo '</div>';
    $fragments['.gt3_header_builder_cart_component__cart-container'] = ob_get_clean();

    return $fragments;
}

remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
add_action('woocommerce_product_thumbnails', 'woocommerce_show_product_sale_flash', 25);


// add vertical thumbnails options
function gt3_option_thumbnail_slider() {
	return array(
		'rtl'            => is_rtl(),
		'animation'      => "fade",
		'smoothHeight'   => false,
		'directionNav'   => false,
		'controlNav'     => 'thumbnails',
		'slideshow'      => false,
		'animationSpeed' => 500,
		'animationLoop'  => false, // Breaks photoswipe pagination if true.
	);
}
add_filter( 'woocommerce_single_product_carousel_options', 'gt3_option_thumbnail_slider' );
// Remove script in single
function gt3_dequeue_script() {
	$id = gt3_get_queried_object_id();
	if ( class_exists( 'RWMB_Loader' ) && rwmb_meta( 'mb_single_product', array(), $id ) === 'custom' ) {
		$gt3_single_layout = rwmb_meta( 'mb_thumbnails_layout', array(), $id );
		$gt3_sticky_thumb  = rwmb_meta( 'mb_sticky_thumb', array(), $id );
	} else {
		$gt3_single_layout = gt3_option( 'product_layout' );
		$gt3_sticky_thumb  = gt3_option( 'sticky_thumb' );
	}

	if ( $gt3_single_layout === "thumb_grid" || $gt3_single_layout === "thumb_vertical" ) {
		wp_dequeue_script( 'zoom' );
		wp_dequeue_script( 'flexslider' );
	}

	if ( $gt3_sticky_thumb ) {
		remove_action( 'woocommerce_before_single_product', 'wc_print_notices', 10 );

		add_action( 'woocommerce_before_single_product_summary', 'gt3_add_sticky_parent_open', 1 );
		add_action( 'woocommerce_after_single_product_summary', 'gt3_add_sticky_parent_close', 12 );

		add_action( 'woocommerce_before_single_product_summary', 'gt3_wrap_single_product_open', 30 );
		add_action( 'woocommerce_before_single_product_summary', 'wc_print_notices', 35 );
		add_action( 'woocommerce_after_single_product_summary', 'gt3_wrap_single_product_close', 11 );
	}

	if ( $gt3_single_layout === "thumb_vertical" ) {
		remove_action( 'woocommerce_before_single_product', 'wc_print_notices', 10 );

		add_action( 'woocommerce_before_single_product_summary', 'gt3_add_sticky_parent_open', 1 );
		add_action( 'woocommerce_after_single_product_summary', 'gt3_add_sticky_parent_close', 12 );

		add_action( 'woocommerce_before_single_product_summary', 'gt3_wrap_single_product_open', 30 );
		add_action( 'woocommerce_before_single_product_summary', 'wc_print_notices', 35 );
		add_action( 'woocommerce_after_single_product_summary', 'gt3_wrap_single_product_close', 11 );
	}
}

add_action( 'wp_print_scripts', 'gt3_dequeue_script', 100 );

// Add class to thumbnails wrapper's
function gt3_thumb_class_view( $content ) {
	$thumb_direction  = gt3_option( 'product_layout' );
	$gt3_sticky_thumb = gt3_option( 'sticky_thumb' );
	$carousel_thumb   = gt3_option( 'activate_carousel_thumb' );

	$id = gt3_get_queried_object_id();
	if ( class_exists( 'RWMB_Loader' ) ) {
		$mb_single_product = rwmb_meta( 'mb_single_product', array(), $id );
		if ( $mb_single_product === 'custom' ) {
			$thumb_direction  = rwmb_meta( 'mb_thumbnails_layout', array(), $id );
			$gt3_sticky_thumb = rwmb_meta( 'mb_sticky_thumb', array(), $id );
		}
	}

	switch ( $thumb_direction ) {
		case 'vertical':
			array_push( $content, 'gt3_thumb_vertical' );
			if ( $carousel_thumb ) {
				array_push( $content, 'gt3_carousel_thumb' );
			}else{
				array_push( $content, 'gt3_carousel_none' );
            }
			break;
		case 'horizontal':
			array_push( $content, 'gt3_thumb_horizontal' );
			break;
		case 'thumb_grid':
			array_push( $content, 'gt3_thumb_grid' );
			break;
		case 'thumb_vertical':
			array_push( $content, 'gt3_thumb_grid_vertical' );
			break;
		default:
			array_push( $content, 'gt3_thumb_horizontal' );
			break;
	}
	if ( $gt3_sticky_thumb && $thumb_direction !== 'thumb_vertical' ) {
		array_push( $content, 'gt3_sticky_thumb' );
	}

	global $product;
	$attachment_ids = $product->get_gallery_image_ids();
	if ( ! empty( $attachment_ids ) ) {
		array_push( $content, 'gt3_gallery_attached' );
	}

	return $content;
}
add_filter( 'woocommerce_single_product_image_gallery_classes', 'gt3_thumb_class_view' );


/* Add size guide button on single product */
function gt3_size_guide() {
	$id = gt3_get_queried_object_id();
	$shop_size_guide = gt3_option( 'shop_size_guide' );
	if ( $shop_size_guide == 1 ) {
		$size_guide     = gt3_option( 'size_guide' );
		$size_guide_url = ! empty( $size_guide['url'] ) ? $size_guide['url'] : '';
	} else {
		$size_guide_url = '';
	}
	if ( class_exists( 'RWMB_Loader' ) ) {
		$mb_img_size_guide = rwmb_meta( 'mb_img_size_guide', array(), $id );
		switch ( $mb_img_size_guide ) {
			case 'custom':
				$size_guide_url = rwmb_meta( 'mb_size_guide', 'size=full', $id );
				break;
			case 'none':
				$size_guide_url = '';
				break;
			default:
				break;
		}
	}
    if (!empty($size_guide_url)) {
        echo '<div class="gt3_block_size_popup"><a href="#" class="image_size_popup_button theme_icon-home-repair">'.esc_html__('Size Guide', 'agrosector').'</a></div><!-- gt3_block_size_popup -->';
    }
}
add_action('woocommerce_single_product_summary', 'gt3_size_guide', 31);
function gt3_popup_image_guide() {
	$shop_size_guide = gt3_option( 'shop_size_guide' );
	$id = gt3_get_queried_object_id();

	if ( $shop_size_guide == 1 ) {
		$size_guide     = gt3_option( 'size_guide' );
		$size_guide_url = ! empty( $size_guide['url'] ) ? $size_guide['url'] : '';
		$size_guide_id  = ! empty( $size_guide['id'] ) ? get_the_title($size_guide['id']) : '';
	} else {
		$size_guide_url = $size_guide_id = '';
	}
	if ( class_exists( 'RWMB_Loader' ) ) {
		$mb_img_size_guide = rwmb_meta( 'mb_img_size_guide', array(), $id );
		switch ( $mb_img_size_guide ) {
			case 'custom':
				$mb_size_guide = rwmb_meta( 'mb_size_guide', 'size=full', $id );
				if ( ! empty( $mb_size_guide ) ) {
					$size_guide_image_src = array_values( $mb_size_guide );
					$size_guide_url       = ! empty( $size_guide_image_src ) ? $size_guide_image_src[0]['full_url'] : '';
				} else {
					$size_guide_url = '';
				}
				break;

			case 'none':
				$size_guide_url = '';
				break;
			default:
				break;
		}
	}
	if ( ! empty( $size_guide_url ) ) {
		echo '<div class="image_size_popup">
            <div class="layer"></div>
            <div class="size_guide_block"><div class="wrapper_size_guide">
                <span class="close"></span>
                <a href="'.esc_url( $size_guide_url ).'" target="_blank">
                    <img src="'.esc_url( $size_guide_url ).'" alt="'.esc_attr($size_guide_id).'">
                </a>
            </div></div>
          </div>';
	}
}
add_action('gt3_footer_action', 'gt3_popup_image_guide', 20);  // footer.php

/* Add next/prev buttons on single product */
if ( (bool) gt3_option( 'next_prev_product' ) && class_exists( 'GT3_WooCommerce_Adjacent_Products' ) ) {
	add_action( 'woocommerce_after_single_product_summary', 'gt3_prev_next_product', 17 );
	function gt3_prev_next_product() {
		// Show only products in the same category?
		$in_same_term   = apply_filters( 'gt3_single_product_pagination_same_category', true );
		$excluded_terms = apply_filters( 'gt3_single_product_pagination_excluded_terms', '' );
		$taxonomy       = apply_filters( 'gt3_single_product_pagination_taxonomy', 'product_cat' );

		$previous_product = gt3_get_previous_product( $in_same_term, $excluded_terms, $taxonomy );
		$next_product     = gt3_get_next_product( $in_same_term, $excluded_terms, $taxonomy );

		if ( ! $previous_product && ! $next_product ) {
			return;
		}

		?>
        <ul class='gt3_product_list_nav'>
		<?php if ( $previous_product ) : ?>
            <li>
                <a href="<?php echo esc_url( $previous_product->get_permalink() ); ?>" rel="prev">
					<?php
					if ( apply_filters( 'gt3_next_prev_product_img', false ) ) {
						echo '<div class="product_list_nav_thumbnail">';
						echo wp_kses_post( $previous_product->get_image() );
						echo '</div>';
					}

					echo '<div class="product_list_nav_text">';
					echo '<span class="nav_title">';
					echo wp_kses_post( $previous_product->get_name() );
					echo '</span>';
					echo '<span class="nav_text">'.esc_html__('PREV', 'agrosector').'</span>';
					echo '<span class="nav_price">'. wp_kses_post( $previous_product->get_price_html() ).'</span>';
					echo '</div>';
					?>
                </a>
            </li>
		<?php endif; ?>

		<?php if ( $next_product ) : ?>
            <li>
                <a href="<?php echo esc_url( $next_product->get_permalink() ); ?>" rel="next">
					<?php
					if ( apply_filters( 'gt3_next_prev_product_img', false ) ) {
						echo '<div class="product_list_nav_thumbnail">';
						echo wp_kses_post( $next_product->get_image() );
						echo '</div>';
					}

					echo '<div class="product_list_nav_text">';
					echo '<span class="nav_title">';
					echo wp_kses_post( $next_product->get_name() );
					echo '</span>';
					echo '<span class="nav_text">'.esc_html__('NEXT', 'agrosector').'</span>';
					echo '<span class="nav_price">'. wp_kses_post( $next_product->get_price_html() ).'</span>';
					echo '</div>';
					?>
                </a>
            </li>
		<?php endif; ?>
        </ul><?php
	}
}
function gt3_get_previous_product( $in_same_term = false, $excluded_terms = '', $taxonomy = 'product_cat' ) {
	$product = new GT3_WooCommerce_Adjacent_Products( $in_same_term, $excluded_terms, $taxonomy, true );
	return $product->get_product();
}
function gt3_get_next_product( $in_same_term = false, $excluded_terms = '', $taxonomy = 'product_cat' ) {
	$product = new GT3_WooCommerce_Adjacent_Products( $in_same_term, $excluded_terms, $taxonomy );
	return $product->get_product();
}

// Wishlist button wrap in
function gt3_output_wishlist_button_listing() {
    if ( class_exists( 'YITH_WCWL_Shortcode' ) && get_option('yith_wcwl_enabled') == true ) {
        echo '<div class="gt3_add_to_wishlist">'.do_shortcode( '[yith_wcwl_add_to_wishlist]' ).'</div>';
    }
}
// Quick View button wrap in
function gt3_output_quick_view_button_listing() {
    if ( class_exists('YITH_WCQV_Frontend') && get_option('yith-wcqv-enable') ) {
        global $product;
        echo '<div class="gt3_quick_view">'.do_shortcode( '[yith_quick_view product_id="'.$product->get_id().'"]' ).'</div>';
    }
}

// Add 'Hot' and 'New' labels for products
add_action( 'woocommerce_product_options_general_product_data', 'woo_add_custom_general_field' );
add_action( 'woocommerce_process_product_meta', 'woo_add_custom_general_fields_save' );
function woo_add_custom_general_field() {
    global $woocommerce, $post;

    echo '<div class="options_group">';
    woocommerce_wp_checkbox( array(
        'id'            => '_checkbox_hot',
        'label'         => esc_html__( 'Hot Product', 'agrosector' ),
        'description'   => esc_html__( 'Check for Hot Product', 'agrosector' )
    ) );
    woocommerce_wp_checkbox( array(
        'id'            => '_checkbox_new',
        'label'         => esc_html__( 'New Product', 'agrosector' ),
        'description'   => esc_html__( 'Check for New Product', 'agrosector' )
    ) );
    echo '</div>';
}
function woo_add_custom_general_fields_save( $post_id ){
    $woocommerce_checkbox = isset( $_POST['_checkbox_hot'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_checkbox_hot', $woocommerce_checkbox );

    $woocommerce_checkbox = isset( $_POST['_checkbox_new'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_checkbox_new', $woocommerce_checkbox );
}

add_action('woocommerce_product_thumbnails','gt3_hot_new_product', 30);
add_action('woocommerce_before_shop_loop_item_title','gt3_hot_new_product', 15);
function gt3_hot_new_product(){
    global $product;

    $is_hot = get_post_meta( $product->get_id(), '_checkbox_hot', true );
    if ( 'yes' == $is_hot ) {
        echo '<span class="onsale hot-product">'.esc_html__('Hot','agrosector').'</span>';
    }

    $is_new = get_post_meta( $product->get_id(), '_checkbox_new', true );
    if ( 'yes' == $is_new ) {
        echo '<span class="onsale new-product">'.esc_html__('New','agrosector').'</span>';
    }
}

// Add name of variation to option
add_filter( 'woocommerce_dropdown_variation_attribute_options_args', 'gt3_variable_choose_an_option_rename', 10);
function gt3_variable_choose_an_option_rename( $args ){
    $attr = get_taxonomy( $args['attribute'] ); //Select the attribute from the taxonomy
    if (is_object($attr)) {
        $fix = $attr->name;
        $fix = wc_attribute_label( $fix );
    }else{
        $fix = esc_html__('an option','agrosector');
    }
    $args['show_option_none'] = esc_html__('Choose ','agrosector' ).$fix;
    return $args; //Returns "Select a size" or "Select a color" depending on what your attribute name is.
}
// !Add name of variation to option

function gt3_open_control_tag () {
    echo '<div class="gt3_woocommerce_open_control_tag">';
}
function gt3_close_control_tag () {
    echo '</div>';
}

add_action('woocommerce_after_shop_loop_item', 'gt3_open_control_tag', 9);
add_action('woocommerce_after_shop_loop_item', 'gt3_close_control_tag', 35);

remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
add_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 15);

function gt3_animation_wrapper_product_open(){
	echo '<div class="gt3-animation-wrapper gt3-anim-product">';
}
function gt3_animation_wrapper_product_close(){
	echo '</div><!-- .gt3-anim-product -->';
}
add_action( 'woocommerce_before_shop_loop_item', 'gt3_animation_wrapper_product_open', 5 );
add_action( 'woocommerce_after_shop_loop_item', 'gt3_animation_wrapper_product_close', 45 );

function gt3_wrapper_product_thumbnail_open() {
	echo '<div class="gt3-product-thumbnail-wrapper">';
}
function gt3_wrapper_product_thumbnail_close() {
	echo '</div>';
}
add_action('woocommerce_before_shop_loop_item', 'gt3_wrapper_product_thumbnail_open', 9);
add_action('woocommerce_before_shop_loop_item_title', 'gt3_wrapper_product_thumbnail_close', 17);

// title wrapper
add_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_open', 8 );
add_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 12 );

// replace star-rating
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
add_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);

if (!function_exists('gt3_search_category')) {
    function gt3_search_category() {

        $search_cat_text_search     = gt3_option('search_cat_text_search');
        $search_cat_text_dropdown   = gt3_option('search_cat_text_dropdown');
        $search_cat_hide_empty      = gt3_option('search_cat_hide_empty');
        $search_cat_exclude_child   = gt3_option('search_cat_exclude_child');
        $search_cat_exclude_mobile  = gt3_option('search_cat_exclude_mobile');

        if (empty($search_cat_hide_empty)) $search_cat_hide_empty = '0'; // 0 means false
        if (empty($search_cat_exclude_child)) $search_cat_exclude_child = '0'; // 0 means false
        if (empty($search_cat_exclude_mobile)) $search_cat_exclude_mobile = 'hide';

        $gt3_product_cat = !empty($_GET['gt3_product_cat']) ? $_GET['gt3_product_cat'] : 0;

        $settings = array('show_option_all'     => $search_cat_text_dropdown,
                            'show_option_none'  => '',
                            'orderby'           => 'name',
                            'order'             => 'ASC',
                            'show_last_update'  => 0,
                            'show_count'        => 0,
                            'hide_empty'        => $search_cat_hide_empty,
                            'child_of'          => 0,
                            'echo'              => 0,
                            'selected'          => $gt3_product_cat,
                            'hierarchical'      => 1,
                            'name'              => 'gt3_product_cat',
                            'taxonomy'          => 'gt3_product_cat',
                            'class'             => 'postform',
                            'depth'             => $search_cat_exclude_child);
        $list = wp_dropdown_categories($settings);

        $form = '<div id="gt3_cat_search" class="search_cat_mobile_'.esc_attr($search_cat_exclude_mobile).'"> 
            <form action="' . get_permalink( wc_get_page_id( "shop" ) ) . '" method="GET" role="search">
                <input type="hidden" name="post_type" value="product" />
                <input placeholder="'.esc_attr__('Enter your keyworld...', 'agrosector').'" type="text" value="'.get_search_query().'" name="s" id="s" onblur="if (this.value == \'\') {this.value = \''.esc_attr($search_cat_text_search).'\';}"  onfocus="if (this.value == \''.esc_attr($search_cat_text_search).'\') {this.value = \'\';}" />
                <span class="gt3-search_cat-line"></span>
                <div class="gt3-search_cat-select">'.$list.'</div>
                <button type="submit" id="sbc-submit" class="theme_icon-search" value="" ></button>
            </form> 
        </div>';

        return $form;
    }
}

function advanced_search_query($query) {
    if($query->is_search()) {
        // category terms search.
        if (!$query->is_main_query()) return;
        $gt3_product_cat = !empty($_GET['gt3_product_cat']) ? esc_attr($_GET['gt3_product_cat']) : '';

        $query_args = array();
        $query_args['post_type']    = 'product';
        $query_args['post_status']  = 'publish';

        $search_keyword = esc_attr($_GET['s']);
        $query_args['s'] = $search_keyword && strlen($search_keyword) > 0 ? $search_keyword : '';

        if (!empty($gt3_product_cat) && $gt3_product_cat != '0' && $gt3_product_cat != '') {
            $query_args['tax_query']['relation'] = 'OR';
            $query_args['tax_query'][] = array(
                'taxonomy'  => 'gt3_product_cat',
                'field'     => 'slug',
                'terms'     => get_cat_name($gt3_product_cat),
            );
        }

        // Set query variables
        foreach ($query_args as $key => $value) {
            $query->set($key, $value);
        }
    }
    return $query;
}
add_action('pre_get_posts', 'advanced_search_query', 1000);

//remove woocommerce_taxonomy_archive_description from category page
remove_action('woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10);

add_filter( 'woocommerce_show_page_title', function () { return false; } );

function gt3_woocommerce_output_related_products_args($args){
	$layout_single  = gt3_option( 'product_sidebar_layout' );
	$layout_shop    = gt3_option( 'products_sidebar_layout' );
	$id = gt3_get_queried_object_id();
	if ( class_exists( 'RWMB_Loader' ) && $id !== 0 && ! ( class_exists( 'WooCommerce' ) && is_product_category() ) ) {
		$mb_layout = rwmb_meta( 'mb_page_sidebar_layout', array(), $id );
		if ( ! empty( $mb_layout ) && $mb_layout != 'default' ) {
			$layout_single  = $mb_layout;
		}
	}

	$columns = wc_get_default_products_per_row();
	if ( ($layout_single === 'left' || $layout_single === 'right') && ($layout_shop !== 'left' && $layout_shop !== 'right') && (int)$columns > 1) {
		$columns = (int)$columns - 1;
	} elseif ( ($layout_single !== 'left' && $layout_single !== 'right') && ($layout_shop === 'left' || $layout_shop === 'right') ) {
		$columns = (int)$columns + 1;
	}

	$args['posts_per_page'] = $columns;
	$args['columns']        = $columns;

	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'gt3_woocommerce_output_related_products_args' );

function gt3_woocommerce_cart_item_remove_link($string, $cart_item_key) {
    $string = str_replace('class="remove"', '', $string);
    return str_replace('&times;', '', $string);
}
add_filter( 'woocommerce_cart_item_remove_link', 'gt3_woocommerce_cart_item_remove_link', 10, 2 );

function gt3_woocommerce_product_gallery_trigger(){
	echo '<div class="woocommerce-product-gallery__trigger">'.esc_html__('Fullscreen','agrosector').'</div>';
}
add_action('woocommerce_before_single_product_summary','gt3_woocommerce_product_gallery_trigger', 1);

function gt3_woocommerce_product_thumbnails_columns(){
    return 3;
}
add_filter('woocommerce_product_thumbnails_columns', 'gt3_woocommerce_product_thumbnails_columns');

add_filter( 'posts_results', function ( $results, $args ) {
	/** @var \WP_Query $args */
	if ( !is_null(get_queried_object()) && ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) && ! count( $results ) && $args->get( 'paged' ) > 1 ) {
		$args->set( 'paged', 1 );
		$gt3_posts_results = $args->get_posts();
		if ( count( $gt3_posts_results ) > 0 ) {
			return $gt3_posts_results;
		}
	}

	return $results;
}, 10, 2 );
