<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_ShopList $widget */

$settings = $widget->get_settings();
global $woocommerce_loop;

$classes = '';
if ( ! empty( $settings['infinite_scroll'] ) && $settings['infinite_scroll'] !== 'none' ) {
	$classes = 'infinite_scroll-'.$settings['infinite_scroll'];
	wp_enqueue_script( 'gt3-infinite-scroll');
	wp_enqueue_script( 'gt3-appear');
}

// Woo Category render
$gt3_tax_query = array();
if ( ! empty( $settings['woo_category'] ) ) {
	$categories = $settings['woo_category'];
	if (is_string($settings['woo_category'])){
		$categories    = explode( ',', $settings['woo_category'] );
	}
	$gt3_tax_query = array(
		array(
			'taxonomy' => 'product_cat',
			'terms'    => $categories,
			'field'    => 'slug',
			'operator' => 'IN'
		)
	);
}

$product_visibility_terms  = wc_get_product_visibility_term_ids();
$product_visibility_not_in = $product_visibility_terms['exclude-from-catalog'];
if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
	$gt3_tax_query[] = array(
		'taxonomy' => 'product_visibility',
		'field'    => 'name',
		'terms'    => array( 'outofstock', 'exclude-from-catalog' ),
		'operator' => 'NOT IN',
	);
} else {
	$gt3_tax_query[] = array(
		'taxonomy' => 'product_visibility',
		'field'    => 'term_taxonomy_id',
		'terms'    => $product_visibility_not_in,
		'operator' => 'NOT IN',
	);
}
$gt3_tax_query['relation'] = 'AND';

// Select filter sortby
$orderby = $settings['orderby'];
$order   = $settings['order'];
if ( isset( $_GET['orderby'] ) ) {
	$orderby_value = explode( '-', $_GET['orderby'] );
	$orderby       = esc_attr( $orderby_value[0] );
	$order         = ! empty( $orderby_value[1] ) ? $orderby_value[1] : $order;
	if ( $_GET['orderby'] == 'price' ) {
		$order = 'ASC';
	}
}

$ordering_args = WC()->query->get_catalog_ordering_args( $orderby, $order );
$meta_query    = WC()->query->get_meta_query();

// Pagination setup
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

// Select how many products to show
$per_page = $settings['prod_per_row'] * $settings['rows_per_page'];
if ( isset( $_GET['show'] ) ) {
	if ( $_GET['show'] === 'all' ) {
		$per_page = apply_filters( 'gt3_products_per_page', '-1' );
	} else {
		$per_page = $_GET['show'];
	}
}

$args = array(
	'post_type'           => 'product',
	'post_status'         => 'publish',
	'ignore_sticky_posts' => 1,
	'orderby'             => $ordering_args['orderby'],
	'order'               => $ordering_args['order'],
	'meta_key'            => $ordering_args['meta_key'],
	'posts_per_page'      => $per_page,
	'paged'               => $paged,
	'meta_query'          => $meta_query,
	'tax_query'           => $gt3_tax_query
);

global $products;
$products = new \WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $settings ) );

if ( !$products->have_posts() && $products->get( 'paged' ) > 1 ) {
	$products->set( 'paged', 1 );
	set_query_var('paged', 1);
	$products->get_posts();
}

$gap = ! empty( $settings['grid_gap'] ) && is_array( $settings['grid_gap'] ) ? 'width:' . $settings['grid_gap']['size'] . $settings['grid_gap']['unit'] : '';

ob_start();

if ( $products->have_posts() ) {
	$columns  = ! empty( $settings['prod_per_row'] ) ? $settings['prod_per_row'] : 4;
	$view_all = isset($_COOKIE['gt3-show_all']) ? $_COOKIE['gt3-show_all'] : NULL;
	if ( (bool) $settings['shop_grid_list'] ||
         ( ! empty( $settings['dropdown_prod_per_page'] ) && (bool) $settings['dropdown_prod_per_page']) ||
	     (bool) $settings['dropdown_prod_orderby'] ||
	     $settings['pagination'] == 'top' ||
	     $settings['pagination'] == 'bottom_top' ) : ?>
        <div class="gt3-products-header"><?php

			if ( ( $settings['pagination'] == 'top' || $settings['pagination'] == 'bottom_top' ) && $settings['infinite_scroll'] !== 'always' && $view_all !== 'true'  ) {
                echo '<div class="gt3-pagination_nav">';
                gt3_get_woo_template( 'gt3-templates/pagination' );
                echo '</div>';
			}
			if ( ! empty( $settings['dropdown_prod_per_page'] ) && (bool) $settings['dropdown_prod_per_page'] && $settings['infinite_scroll'] !== 'always' ) {
				$settings['elementor'] = true;
				gt3_get_woo_template( 'gt3-templates/loop/product-show', $settings ); // Product show
			}
			if ( (bool) $settings['dropdown_prod_orderby'] ) {
				gt3_get_woo_template( 'gt3-templates/loop/orderby' ); // Orderby
			}
			if ( class_exists( 'GT3_GridList_WOO' ) && (bool) $settings['shop_grid_list'] ) {
				\GT3_GridList_WOO::gt3_enqueue_scripts();
				\GT3_GridList_WOO::toggle_button();
			}


			?></div> <!-- gt3-products-header -->
	<?php endif; ?>

	<?php do_action( 'gt3_woocommerce_before_shop_loop' ); ?>

    <ul class="products columns-<?php echo esc_attr( $columns ); ?>">
		<?php
//        $widget->add_products_post_class_filter();
		while ( $products->have_posts() ) {
			$products->the_post();
			wc_get_template_part( 'content', 'product' );

		} // end of the loop.
//        $widget->remove_products_post_class_filter();
        ?>
    </ul>

	<?php do_action( 'gt3_woocommerce_after_shop_loop' );

	if ( $settings['pagination'] == "bottom_top" || $settings['pagination'] == "bottom" || $settings['infinite_scroll'] !== 'none' ) : ?>
        <div class="gt3-products-bottom">
			<?php gt3_get_woo_template( 'gt3-templates/default-pagination' ); ?>
        </div>
	<?php
	endif;
}
wp_reset_postdata();

echo '<div class="woocommerce gt3_theme_core gt3-shop-list '.esc_attr($classes).'">' . ob_get_clean() . '</div>';




