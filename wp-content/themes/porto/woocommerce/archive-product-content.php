<?php

global $porto_settings;

$js_wc_prdctfltr = false;
if ( class_exists( 'WC_Prdctfltr' ) ) {
	$porto_settings['category-ajax'] = false;
}

if ( ! empty( $porto_settings['category-ajax'] ) ) {
	// fix price slider issue
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	wp_register_script( 'wc-jquery-ui-touchpunch', WC()->plugin_url() . '/assets/js/jquery-ui-touch-punch/jquery-ui-touch-punch' . $suffix . '.js', array( 'jquery-ui-slider' ), WC_VERSION, true );
	wp_register_script( 'wc-price-slider', WC()->plugin_url() . '/assets/js/frontend/price-slider' . $suffix . '.js', array( 'jquery-ui-slider', 'wc-jquery-ui-touchpunch' ), WC_VERSION, true );
	wp_enqueue_script( 'wc-price-slider' );
}
?>

<?php
	/**
	 * Hook: woocommerce_before_main_content.
	 *
	 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
	 * @hooked woocommerce_breadcrumb - 20
	 * @hooked WC_Structured_Data::generate_website_data() - 30
	 */
	do_action( 'woocommerce_before_main_content' );
?>

<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>

	<h2 class="page-title"><?php woocommerce_page_title(); ?></h2>

<?php endif; ?>

<?php
	$builder_id = porto_check_builder_condition( 'shop' );

if ( woocommerce_product_loop() ) {
	global $woocommerce_loop;

	if ( ! ( isset( $woocommerce_loop['category-view'] ) && $woocommerce_loop['category-view'] ) ) {
		if ( ! is_array( $woocommerce_loop ) ) {
			$woocommerce_loop = array();
		}
		$woocommerce_loop['category-view'] = isset( $porto_settings['category-view-mode'] ) ? $porto_settings['category-view-mode'] : '';

		$term = get_queried_object();
		if ( $term && isset( $term->taxonomy ) && isset( $term->term_id ) ) {
			$cols = get_metadata( $term->taxonomy, $term->term_id, 'product_cols', true );
			if ( ! $cols ) {
				$cols = isset( $porto_settings['product-cols'] ) ? $porto_settings['product-cols'] : 3;
			}

			$addlinks_pos = get_metadata( $term->taxonomy, $term->term_id, 'addlinks_pos', true );
			if ( ! $addlinks_pos ) {
				$addlinks_pos = isset( $porto_settings['category-addlinks-pos'] ) ? $porto_settings['category-addlinks-pos'] : 'default';
			}

			$view_mode = get_metadata( $term->taxonomy, $term->term_id, 'view_mode', true );

			$woocommerce_loop['columns']        = $cols;
			$woocommerce_loop['columns_mobile'] = isset( $porto_settings['product-cols-mobile'] ) ? $porto_settings['product-cols-mobile'] : '2';
			$woocommerce_loop['addlinks_pos']   = $addlinks_pos;
			if ( $view_mode ) {
				$woocommerce_loop['category-view'] = $view_mode;
			}
		}
	}
}

if ( ! $builder_id ) {
	/**
	 * Hook: woocommerce_archive_description.
	 *
	 * @hooked woocommerce_taxonomy_archive_description - 10
	 * @hooked woocommerce_product_archive_description - 10
	 */
	do_action( 'woocommerce_archive_description' );

	if ( woocommerce_product_loop() ) {
		/**
		 * Hook: woocommerce_before_shop_loop.
		 *
		 * @hooked woocommerce_output_all_notices - 10
		 * @hooked woocommerce_result_count - 20
		 * @hooked woocommerce_catalog_ordering - 30
		 */
		do_action( 'woocommerce_before_shop_loop' );

		global $woocommerce_loop;
		if ( is_shop() && ! is_product_category() ) {
			$woocommerce_loop['columns']        = isset( $porto_settings['shop-product-cols'] ) ? $porto_settings['shop-product-cols'] : '3';
			$woocommerce_loop['columns_mobile'] = isset( $porto_settings['shop-product-cols-mobile'] ) ? $porto_settings['shop-product-cols-mobile'] : '2';
		}

		echo '<div class="archive-products">';
		$skeleton_lazyload = apply_filters( 'porto_skeleton_lazyload', ! empty( $porto_settings['show-skeleton-screen'] ) && in_array( 'shop', $porto_settings['show-skeleton-screen'] ) && ! porto_is_ajax(), 'archive-product' );
		if ( $skeleton_lazyload ) {
			global $porto_woocommerce_loop;
			if ( ! $porto_woocommerce_loop ) {
				$porto_woocommerce_loop = array();
			}
			if ( ! isset( $porto_woocommerce_loop['el_class'] ) || empty( $porto_woocommerce_loop['el_class'] ) ) {
				$porto_woocommerce_loop['el_class'] = 'skeleton-loading';
			} else {
				$porto_woocommerce_loop['el_class'] .= ' skeleton-loading';
			}
			$porto_settings['skeleton_lazyload'] = true;

			remove_filter( 'woocommerce_product_loop_start', 'woocommerce_maybe_show_product_subcategories' );
		}
		woocommerce_product_loop_start();

		if ( $skeleton_lazyload ) {
			$porto_woocommerce_loop['el_class'] = str_replace( 'skeleton-loading', 'skeleton-body', $porto_woocommerce_loop['el_class'] );
			$skeleton_body_start                = woocommerce_product_loop_start( false );

			$sp_class = 'product product-col';
			if ( ( function_exists( 'wc_get_loop_prop' ) && ! wc_get_loop_prop( 'is_paginated' ) ) || isset( $porto_woocommerce_loop['view'] ) || ! isset( $_COOKIE['gridcookie'] ) || 'list' != $_COOKIE['gridcookie'] ) {
				if ( isset( $woocommerce_loop['addlinks_pos'] ) && 'quantity' == $woocommerce_loop['addlinks_pos'] ) {
					$sp_class .= ' product-wq_onimage';
				} elseif ( isset( $woocommerce_loop['addlinks_pos'] ) ) {
					if ( 'outimage_aq_onimage2' == $woocommerce_loop['addlinks_pos'] ) {
						$sp_class .= ' product-outimage_aq_onimage with-padding';
					} elseif ( 'onhover' == $woocommerce_loop['addlinks_pos'] ) {
						$sp_class .= ' product-default show-links-hover';
					} else {
						$sp_class .= ' product-' . $woocommerce_loop['addlinks_pos'];
					}
				}
			}

			ob_start();
			echo woocommerce_maybe_show_product_subcategories();
			$products_count = 0;
		}
		?>
		<?php
		if ( ! function_exists( 'wc_get_loop_prop' ) || wc_get_loop_prop( 'total' ) ) {
			while ( have_posts() ) {
				the_post();

				/**
				 * Hook: woocommerce_shop_loop.
				 */
				do_action( 'woocommerce_shop_loop' );

				wc_get_template_part( 'content', 'product' );
				if ( $skeleton_lazyload ) {
					$products_count++;
				}
			}
		}
		if ( $skeleton_lazyload ) {
			$archive_content = ob_get_clean();
			echo '<script type="text/template">' . json_encode( $archive_content ) . '</script>';
		}
			woocommerce_product_loop_end();
		if ( $skeleton_lazyload ) {
			if ( $products_count < 1 ) {
				global $porto_products_cols_lg;
				$products_count = $porto_products_cols_lg;
			}
			echo porto_filter_output( $skeleton_body_start );
			for ( $i = 0; $i < $products_count; $i++ ) {
				echo '<li class="' . esc_attr( $sp_class ) . '"></li>';
			}
			woocommerce_product_loop_end();

			add_filter( 'woocommerce_product_loop_start', 'woocommerce_maybe_show_product_subcategories' );
		}
		echo '</div>';

		/**
		 * Hook: woocommerce_after_shop_loop.
		 *
		 * @hooked woocommerce_pagination - 10
		 */
		do_action( 'woocommerce_after_shop_loop' );
	} else {

		global $porto_shop_filter_layout;
		if ( isset( $porto_shop_filter_layout ) && 'horizontal2' == $porto_shop_filter_layout ) {
			do_action( 'woocommerce_before_shop_loop' );
		} else {
			?>
		<div class="shop-loop-before" style="display:none;"> </div>
	<?php } ?>

		<div class="archive-products">
		<?php
			/**
			 * Hook: woocommerce_no_products_found.
			 *
			 * @hooked wc_no_products_found - 10
			 */
			do_action( 'woocommerce_no_products_found' );
		?>
		</div>

		<div class="shop-loop-after clearfix" style="display:none;"> </div>

		<?php
	}
} else {
	echo do_shortcode( '[porto_block id="' . esc_attr( $builder_id ) . '"]' );
}

	/**
	 * Hook: woocommerce_after_main_content.
	 *
	 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
	 */
	do_action( 'woocommerce_after_main_content' );
?>

<?php
	/**
	 * Hook: woocommerce_sidebar.
	 *
	 * @hooked woocommerce_get_sidebar - 10
	 */
	do_action( 'woocommerce_sidebar' );
?>
