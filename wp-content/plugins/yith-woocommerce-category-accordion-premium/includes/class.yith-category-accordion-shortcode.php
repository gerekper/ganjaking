<?php // phpcs:ignore WordPress.NamingConventions
/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH\CategoryAccordion
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Category_Accordion_Shortcode' ) ) {

	/**
	 * YITH_Category_Accordion_Shortcode
	 */
	class YITH_Category_Accordion_Shortcode {

		/**
		 * Print_shortcode
		 *
		 * @param mixed $atts atts.
		 * @param mixed $content content.
		 *
		 * @return string
		 */
		public static function print_shortcode( $atts, $content = null ) {

			$default = array(
				'how_show'       => 'wc',
				'show_sub_cat'   => 'yes',
				'show_wc_img'    => 'no',
				'show_last_post' => 'no',
				'post_limit'     => '-1',
				'menu_ids'       => '',
				'exclude_post'   => '',
                'exclude_specific_cat' => 'no',
				'hide_pages_posts'   => 'no',
				'exclude_wp_cat_select'   => 'no',
				'exclude_page'   => '',
				'exclude_cat'    => '',
				'acc_style'      => '',
				'title'          => 'Categories',
				'highlight'      => 'yes',
				'orderby'        => 'name',
				'order'          => 'asc',
				'show_count'     => 'no',
				'tag_wc'         => 'no',
				'tag_wp'         => 'no',
				'name_wc_tag'    => __( 'WooCommerce TAGS', 'yith-woocommerce-category-accordion' ),
				'name_wp_tag'    => __( 'WordPress TAGS', 'yith-woocommerce-category-accordion' ),
			);

			$atts    = shortcode_atts( $default, $atts );

			extract( $atts ); //phpcs:ignore WordPress.PHP.DontExtract

			$args = array();

			$args['orderby']    = $orderby;
			$args['order']      = strtoupper( $order );

			$args['show_count'] = 'yes' === $show_count ? 1 : false;

			if(strpos($atts['acc_style'], 'style') > -1){
				$style = $atts['acc_style'];
				$style_id = get_option(str_replace('_', '', $style)."_id", ' ');

			}elseif ('' === $atts['acc_style']){

				$args       = array(
					'numberposts' => 1,
					'post_type'   => 'yith_cacc',
				);
				$first_post = get_posts( $args );
				$style_id   = $first_post[0]->ID;

			} else {
				$style_id = $atts['acc_style'];
			}

			$args['depth']    = get_option( 'ywcca_level_depth_acc' ) === 'all' ? 0 : get_option( 'ywcca_max_level_depth' );
			$limit            = get_option( 'ywcca_show_cat_acc' );
			$limit_max_option = get_option( 'ywcca_amount_max_acc' );

			$args['style_count']  = get_post_meta( $style_id, '_ywcacc_count_style', true );
			$args['hide_empty']   = get_option( 'ywcca_hide_empty_cat' ) === 'yes';
			$args['hierarchical'] = true;
			$args['pad_counts']   = true;
			$args['title_li']     = '';
            $args['style_id']     = $style_id;
			$args['wc_image']     = $show_wc_img;

			if ( apply_filters( 'ywcca_hide_category_title', false ) ) {
				$args['use_desc_for_title'] = 0;
			}

			if ( is_singular( 'product' ) ) {
				global $post;

				$product_categories = wc_get_product_terms(
					$post->ID,
					'product_cat',
					apply_filters(
						'woocommerce_product_categories_widget_product_terms_args',
						array(
							'orderby' => 'parent',
							'order'   => 'DESC',
						)
					)
				);
				$product_category   = isset( $product_categories[0] ) ? $product_categories[0] : '';
				if ( ! empty( $product_category ) ) {
					$current_category         = apply_filters( 'yith_category_accordion_current_category', $product_category->term_id, $product_categories, $post->ID );
					$args['current_category'] = $current_category;

				}
			}

			/*Check if current post or page is excluded*/
			$id_pages = array();
			$id_posts = array();
			$shop_id  = wc_get_page_id( 'shop' );

			if ( ! empty( $exclude_page ) && 'yes' === $hide_pages_posts ) {
				$id_pages = explode( ',', $exclude_page );
			}

			if ( ! empty( $exclude_post && 'yes' === $hide_pages_posts ) ) {
				$id_posts = explode( ',', $exclude_post );
			}

			if ( ( is_shop() && in_array( $shop_id, $id_pages, true ) ) ) {
				return;
			}

			if ( is_page() ) {

				$page_id = get_queried_object_id();

				if ( in_array( $page_id, $id_pages ) ) {

					return;
				}
			}
			if ( is_single() ) {
				$post_id = get_queried_object_id();

				if ( in_array( $post_id, $id_posts ) ) {
					return;
				}
			}

			global $wpdb;

			require_once YWCCA_INC . 'functions.yith-category-accordion-generate-styles.php';
			$css_inline = ywcca_generate_style_from_post( $style_id );
			ob_start();

			if(isset($_REQUEST['elementor-preview']) || isset($_REQUEST['action']) && ('elementor' === $_REQUEST['action']  || 'elementor_ajax' === $_REQUEST['action']) ){
				echo '<style>' . $css_inline. ' </style>';
			}else{
				wp_add_inline_style( 'ywcca_accordion_style', $css_inline );
			}

			wp_enqueue_style( 'ywcca_accordion_style' );
			wp_enqueue_script( 'ywcca_accordion' );
			wp_enqueue_script( 'hover_intent' );

			$icon_position = get_post_meta( $style_id, '_ywcacc_toggle_icon_position', true );
			echo '<div class="ywcca_container ywcca_widget_container_' . esc_attr( $style_id ) . '">';
			echo '<h3 class="ywcca_widget_title">' . esc_attr( $title ) . '</h3>';
			$content             = '<ul class="ywcca_category_accordion_widget %s" data-highlight_curr_cat="%s" data-ywcca_style="%s" data-ywcca_orderby="%s" data-ywcca_order="%s" data-ywcca_icon_position="%s">';
			$general_content     = sprintf( $content, 'category_accordion', $highlight, $style_id, $orderby, $order, $icon_position);
			$end_general_content = '</ul>';

			switch ( $how_show ) {

				case 'wc':
					include_once YWCCA_INC . '/walkers/class.yith-category-accordion-walker.php';
					$args['taxonomy'] = 'product_cat';
					$args['walker']   = new YITH_Category_Accordion_Walker();
					$args['exclude']  = empty( $exclude_cat ) || 'no' === $exclude_specific_cat ? '' : explode( ',', $exclude_cat);

					if ( 'no' === $show_sub_cat ) {
						$args['parent'] = 0;
					}

					if ( 'menu_order' !== $orderby ) {
						$args['orderby'] = 'name' === $orderby ? 'title' : $orderby;
					} else {
						$args['menu_order'] = 'asc';
						unset( $args['orderby'] );

					}

					/*if a limit is set*/
					if ( 'amount' === $limit ) {

						$args_cat = array(
							'orderby'  => 'name' === $orderby ? 'title' : $orderby,
							'order'    => $order,
							'parent'   => 0,
							'number'   => $limit_max_option['number_categories'],
							'taxonomy' => 'product_cat',
							'exclude'  => ! empty ( $args['exclude'] ) ?  $args['exclude'] : '',

						);

						/*Get category parent */
						$categories = get_categories( $args_cat );

						if ( ! empty( $categories ) ) {
							$include = array();

							foreach ( $categories as $category ) {
								$include[] = $category->term_id;
							}

							// Get the child category !
							$children   = $wpdb->get_col( "SELECT `term_id` FROM {$wpdb->term_taxonomy } WHERE `parent` IN (" . implode( ',', $include ) . ')' ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.NotPrepared
							$exclusions =  !empty( $args['exclude'] ) ? $args['exclude'] : array();
							$result     = array_filter( $children, function( $child ) use ( $exclusions ) {
									if ( ! in_array( $child, $exclusions) ) {
										return $child;
									}
							});
							$args['include'] = implode( ',', $include ) . ',' . implode( ',', $result );
						}
					}

					echo $general_content; //phpcs:ignore WordPress.Security.EscapeOutput
					wp_list_categories( apply_filters( 'ywcca_wc_product_categories_widget_args', $args ) );
					echo $end_general_content; //phpcs:ignore WordPress.Security.EscapeOutput

					break;
				case 'wp':

					include_once YWCCA_INC . '/walkers/class.yith-category-accordion-walker.php';

					$args['taxonomy']       = 'category';
					$args['walker']         = new YITH_Category_Accordion_Walker();
					$args['exclude']        = empty( $exclude_cat ) || 'no' === $exclude_wp_cat_select ? '' : explode( ',', $exclude_cat );
					$args['show_last_post'] = 'yes' === $show_last_post;
					$args['post_limit']     = $post_limit;
					$args['pad_counts']     = false;

					/*if a limit is set*/

					if ( 'amount' === $limit ) {
						$args_cat = array(
							'orderby'  => $orderby,
							'order'    => $order,
							'parent'   => 0,
							'number'   => $limit_max_option['number_categories'],
							'taxonomy' => 'category',
						);

						/*Get category parent */
						$categories = get_categories( $args_cat );

						$include = array();

						foreach ( $categories as $category ) {
							$include[] = $category->term_id;
						}

						// Get the child category !
						$children        = $wpdb->get_col( "SELECT `term_id` FROM {$wpdb->term_taxonomy } WHERE `parent` IN (" . implode( ',', $include ) . ')' ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.NotPrepared
						$args['include'] = implode( ',', $include ) . ',' . implode( ',', $children );

					}

					echo $general_content; //phpcs:ignore WordPress.Security.EscapeOutput
					echo wp_list_categories( apply_filters( 'ywcca_wc_product_categories_widget_args', $args ) );
					echo $end_general_content; //phpcs:ignore WordPress.Security.EscapeOutput

					break;

				case 'tag':
					include_once YWCCA_INC . '/walkers/class.yith-category-accordion-walker.php';

					$args['walker'] = new YITH_Category_Accordion_Walker();
					// Get the tag!
					$tags                     = $wpdb->get_col( "SELECT `term_id` FROM {$wpdb->term_taxonomy } WHERE `taxonomy` IN ( 'post_tag','product_tag' )" ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery
					$args['include']          = implode( ',', $tags );
					$args['show_option_none'] = __( 'No Tags', 'yith-woocommerce-category-accordion' );

					echo $general_content; //phpcs:ignore WordPress.Security.EscapeOutput
					if ( 'yes' === $tag_wc ) {
						echo '<li class="cat-item"><a href="#">' . esc_html( $name_wc_tag ) . '</a>';
						echo '<ul class="yith-children">';
						wp_list_categories( array_merge( array( 'taxonomy' => 'product_tag' ), $args ) );
						echo '</ul>';
						echo '</li>';
					}
					if ( 'yes' === $tag_wp ) {

						echo '<li class="cat-item"><a href="#">' . esc_html( $name_wp_tag ) . '</a>';
						echo '<ul class="yith-children">';
						wp_list_categories( array_merge( array( 'taxonomy' => 'post_tag' ), $args ) );
						echo '</ul>';
						echo '</li>';
					}
					echo $end_general_content; //phpcs:ignore WordPress.Security.EscapeOutput
					break;

				case 'menu':
					$menu_ids          = explode( ',', $menu_ids );
					$args['container'] = false;

					$general_content = sprintf( $content, 'category_menu_accordion', $highlight, $acc_style, '', '', $icon_position );

					echo $general_content; //phpcs:ignore WordPress.Security.EscapeOutput
					if ( ! empty( $menu_ids ) ) {
						add_filter( 'nav_menu_submenu_css_class', 'ywcca_change_submenu_css_class', 20, 3 );
						foreach ( $menu_ids as $menu_id ) {
							wp_nav_menu(
								array_merge(
									array(
										'menu'       => $menu_id,
										'menu_class' => 'ywcca-menu',
									),
									$args
								)
							);
						}
						remove_filter( 'nav_menu_submenu_css_class', 'ywcca_change_submenu_css_class', 20 );

					}
					echo $end_general_content; //phpcs:ignore WordPress.Security.EscapeOutput
					break;
			}

			echo '</div>';

			$template = ob_get_contents();

			ob_end_clean();

			return $template;

		}
	}

}

add_shortcode( 'yith_wcca_category_accordion', array( 'YITH_Category_Accordion_Shortcode', 'print_shortcode' ) );

/**
 * Ywcca_change_submenu_css_class
 *
 * @param array $classes classes.
 * @param array $args args.
 * @param mixed $depth depth.
 */
function ywcca_change_submenu_css_class( $classes, $args, $depth ) {

	/**
	 * The arg.
	 *
	 * @var object $args
	 */
	if ( ! empty( $args->menu_class ) && 'ywcca-menu' === $args->menu_class ) {

		$key = array_search( 'sub-menu', $classes, true );

		if ( false !== $key ) {
			$classes[ $key ] = 'ywcca-sub-menu';
		}
	}

	return $classes;
}
